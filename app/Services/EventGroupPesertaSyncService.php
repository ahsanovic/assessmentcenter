<?php

namespace App\Services;

use App\Models\Event;
use App\Models\Peserta;
use App\Models\Pspk\UjianPspk;
use Illuminate\Support\Collection;

class EventGroupPesertaSyncService
{
    /**
     * Salin peserta ke tes bersama lain dalam grup.
     * Event PSPK level tidak pernah menjadi tujuan salinan: level diisi manual.
     */
    public function replicateFromPeserta(Peserta $source): int
    {
        return $this->syncAttributesToSiblingEvents(
            (int) $source->event_id,
            $this->extractSyncableAttributes($source)
        );
    }

    /**
     * Perbarui peserta serupa (NIP/NIK sama) di event lain dalam grup yang sudah punya baris itu.
     */
    public function syncUpdateFromPeserta(Peserta $source, ?string $matchNip = null, ?string $matchNik = null): int
    {
        $sourceEvent = Event::find($source->event_id);

        if (! $sourceEvent?->event_group_id) {
            return 0;
        }

        $siblingEventIds = $this->siblingEventIds($sourceEvent);
        $attributes = $this->extractSyncableAttributes($source);
        $updated = 0;

        foreach ($siblingEventIds as $targetEventId) {
            $sibling = $this->findPesertaInEventByIdentifier(
                (int) $targetEventId,
                $matchNip,
                $matchNik
            );

            if (! $sibling) {
                continue;
            }

            $oldData = $sibling->getOriginal();
            $sibling->fill($attributes);
            $sibling->save();

            activity_log($sibling, 'update', 'peserta (sync grup)', $oldData);
            $updated++;
        }

        return $updated;
    }

    /**
     * Hapus peserta serupa di event lain.
     * Dari event PSPK, hanya baris level itu yang dihapus (pemanggil menghapus sumber).
     * Dari tes bersama, peserta dihapus dari seluruh grup.
     */
    public function syncDeleteFromPeserta(Peserta $source, ?string $matchNip = null, ?string $matchNik = null): int
    {
        $sourceEvent = Event::find($source->event_id);

        if (! $sourceEvent?->event_group_id) {
            return 0;
        }

        $siblingEventIds = $this->deleteSiblingEventIds($sourceEvent);
        $deleted = 0;

        foreach ($siblingEventIds as $targetEventId) {
            $sibling = $this->findPesertaInEventByIdentifier(
                (int) $targetEventId,
                $matchNip,
                $matchNik
            );

            if (! $sibling) {
                continue;
            }

            $oldData = $sibling->getOriginal();
            activity_log($sibling, 'delete', 'peserta (sync grup)', $oldData);
            $sibling->delete();
            $deleted++;
        }

        return $deleted;
    }

    /**
     * Peserta sudah ada di level PSPK lain dalam grup yang sama.
     */
    public function conflictingPspkMessage(Event $event, ?string $nip, ?string $nik): ?string
    {
        if (! $event->event_group_id || ! $event->isPspkLevel()) {
            return null;
        }

        $nip = filled($nip) ? (string) $nip : null;
        $nik = filled($nik) ? (string) $nik : null;

        if ($nip === null && $nik === null) {
            return null;
        }

        $otherEvents = Event::query()
            ->with('metodeTes')
            ->where('event_group_id', $event->event_group_id)
            ->whereIn('metode_tes_id', Event::METODE_PSPK_LEVEL)
            ->where('id', '!=', $event->id)
            ->get();

        if ($otherEvents->isEmpty()) {
            return null;
        }

        $match = Peserta::query()
            ->whereIn('event_id', $otherEvents->pluck('id'))
            ->when($nip !== null, fn ($query) => $query->where('nip', $nip))
            ->when($nip === null, fn ($query) => $query->where('nik', $nik))
            ->first();

        if (! $match) {
            return null;
        }

        $label = $otherEvents->firstWhere('id', $match->event_id)?->metodeTes?->metode_tes
            ?? 'PSPK level lain';

        return "peserta sudah terdaftar di event {$label} dalam grup yang sama";
    }

    /**
     * Alasan penghapusan ditolak karena peserta sudah mulai mengerjakan PSPK.
     */
    public function deletionBlockedReason(Peserta $peserta): ?string
    {
        $event = Event::find($peserta->event_id);

        if (! $event?->event_group_id) {
            return null;
        }

        $rows = collect([$peserta]);

        if (! $event->isPspkLevel()) {
            $pspkEventIds = Event::query()
                ->where('event_group_id', $event->event_group_id)
                ->whereIn('metode_tes_id', Event::METODE_PSPK_LEVEL)
                ->pluck('id');

            if ($pspkEventIds->isEmpty()) {
                return null;
            }

            $rows = $rows->merge($this->findPesertaInEvents($pspkEventIds, $peserta->nip, $peserta->nik));
        }

        $started = UjianPspk::query()
            ->whereIn('peserta_id', $rows->pluck('id')->filter()->unique())
            ->exists();

        if (! $started) {
            return null;
        }

        if ($event->isPspkLevel()) {
            return 'Peserta sudah mulai mengerjakan PSPK level ini, sehingga tidak dapat dihapus.';
        }

        return 'Peserta sudah mulai mengerjakan PSPK, sehingga tidak dapat dihapus dari grup.';
    }

    public function groupHasPspkLevel(Event $event): bool
    {
        if (! $event->event_group_id) {
            return false;
        }

        return Event::query()
            ->where('event_group_id', $event->event_group_id)
            ->whereIn('metode_tes_id', Event::METODE_PSPK_LEVEL)
            ->exists();
    }

    /**
     * Kunci nip:/nik: peserta di halaman ini yang belum masuk ke event PSPK mana pun dalam grup.
     *
     * @param  Collection<int, Peserta>  $pesertaList
     * @return array<string, true>
     */
    public function missingPspkLevelKeys(Event $event, Collection $pesertaList): array
    {
        if (! $event->event_group_id || $event->isPspkLevel() || $pesertaList->isEmpty()) {
            return [];
        }

        $pspkEventIds = Event::query()
            ->where('event_group_id', $event->event_group_id)
            ->whereIn('metode_tes_id', Event::METODE_PSPK_LEVEL)
            ->pluck('id');

        if ($pspkEventIds->isEmpty()) {
            return [];
        }

        $nips = $pesertaList->pluck('nip')->filter()->unique()->values();
        $niks = $pesertaList->pluck('nik')->filter()->unique()->values();

        if ($nips->isEmpty() && $niks->isEmpty()) {
            return [];
        }

        $assigned = Peserta::query()
            ->whereIn('event_id', $pspkEventIds)
            ->where(function ($query) use ($nips, $niks) {
                $hasNip = $nips->isNotEmpty();
                $hasNik = $niks->isNotEmpty();

                if ($hasNip) {
                    $query->whereIn('nip', $nips);
                }

                if ($hasNik) {
                    $hasNip
                        ? $query->orWhereIn('nik', $niks)
                        : $query->whereIn('nik', $niks);
                }
            })
            ->get(['nip', 'nik']);

        $assignedKeys = [];

        foreach ($assigned as $row) {
            if (filled($row->nip)) {
                $assignedKeys['nip:'.$row->nip] = true;
            }

            if (filled($row->nik)) {
                $assignedKeys['nik:'.$row->nik] = true;
            }
        }

        $missing = [];

        foreach ($pesertaList as $peserta) {
            $key = $this->identifierKey($peserta->nip, $peserta->nik);

            if ($key !== null && ! isset($assignedKeys[$key])) {
                $missing[$key] = true;
            }
        }

        return $missing;
    }

    public function createSyncNotice(Event $event): ?string
    {
        if (! $event->event_group_id) {
            return null;
        }

        $nama = $event->eventGroup?->nama ?? 'ini';

        if ($event->isPspkLevel()) {
            return "Peserta disalin ke tes lain dalam grup {$nama} selain level PSPK. Satu orang hanya dapat mengikuti satu level PSPK. Event tes bersama harus sudah dibuat sebelum peserta ditambahkan.";
        }

        if ($this->groupHasPspkLevel($event)) {
            return "Peserta disalin ke tes bersama dalam grup {$nama}. Level PSPK tidak diisi otomatis; tambahkan peserta pada event level yang sesuai.";
        }

        return "Peserta akan otomatis disalin ke event lain dalam grup {$nama}.";
    }

    public function updateSyncNotice(Event $event): ?string
    {
        if (! $event->event_group_id) {
            return null;
        }

        $nama = $event->eventGroup?->nama ?? 'ini';

        return "Perubahan data disinkronkan ke peserta yang sama di event lain dalam grup {$nama}, hanya pada event yang sudah memiliki peserta tersebut.";
    }

    public function deleteSyncNotice(Event $event): ?string
    {
        if (! $event->event_group_id) {
            return null;
        }

        if ($event->isPspkLevel()) {
            return 'Peserta hanya dihapus dari level PSPK ini. Data pada tes lain dalam grup tetap ada. Penghapusan ditolak jika peserta sudah mulai mengerjakan PSPK.';
        }

        if ($this->groupHasPspkLevel($event)) {
            return 'Peserta akan dihapus dari event ini dan semua event dalam grup, termasuk level PSPK. Penghapusan ditolak jika peserta sudah mulai mengerjakan PSPK.';
        }

        return 'Peserta akan dihapus dari event ini dan semua event dalam grup assessment yang sama.';
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function syncAttributesToSiblingEvents(int $sourceEventId, array $attributes): int
    {
        $sourceEvent = Event::find($sourceEventId);

        if (! $sourceEvent?->event_group_id) {
            return 0;
        }

        $siblingEventIds = $this->replicateSiblingEventIds($sourceEvent);

        if ($siblingEventIds->isEmpty()) {
            return 0;
        }

        $synced = 0;

        foreach ($siblingEventIds as $targetEventId) {
            if ($this->participantExistsInEvent((int) $targetEventId, $attributes)) {
                continue;
            }

            $copy = array_merge($attributes, [
                'event_id' => (int) $targetEventId,
            ]);

            $peserta = Peserta::create($copy);
            activity_log($peserta, 'create', 'peserta (sync grup)');
            $synced++;
        }

        return $synced;
    }

    /**
     * @return Collection<int, int>
     */
    private function siblingEventIds(Event $sourceEvent): Collection
    {
        return Event::query()
            ->where('event_group_id', $sourceEvent->event_group_id)
            ->where('id', '!=', $sourceEvent->id)
            ->pluck('id');
    }

    /**
     * @return Collection<int, int>
     */
    private function replicateSiblingEventIds(Event $sourceEvent): Collection
    {
        return Event::query()
            ->where('event_group_id', $sourceEvent->event_group_id)
            ->where('id', '!=', $sourceEvent->id)
            ->whereNotIn('metode_tes_id', Event::METODE_PSPK_LEVEL)
            ->pluck('id');
    }

    /**
     * @return Collection<int, int>
     */
    private function deleteSiblingEventIds(Event $sourceEvent): Collection
    {
        if ($sourceEvent->isPspkLevel()) {
            return collect();
        }

        return $this->siblingEventIds($sourceEvent);
    }

    /**
     * @return array<string, mixed>
     */
    private function extractSyncableAttributes(Peserta $source): array
    {
        return [
            'nama' => $source->getAttributes()['nama'],
            'gelar_depan' => $source->gelar_depan,
            'gelar_belakang' => $source->gelar_belakang,
            'jenis_peserta_id' => $source->jenis_peserta_id,
            'nip' => $source->nip,
            'nik' => $source->nik,
            'jabatan' => $source->jabatan,
            'unit_kerja' => $source->unit_kerja,
            'instansi' => $source->instansi,
            'password' => $source->password,
            'is_active' => $source->is_active ?? 'true',
        ];
    }

    private function findPesertaInEventByIdentifier(int $eventId, ?string $nip, ?string $nik): ?Peserta
    {
        if ($nip) {
            return Peserta::where('event_id', $eventId)->where('nip', $nip)->first();
        }

        if ($nik) {
            return Peserta::where('event_id', $eventId)->where('nik', $nik)->first();
        }

        return null;
    }

    /**
     * @param  Collection<int, int>  $eventIds
     * @return Collection<int, Peserta>
     */
    private function findPesertaInEvents(Collection $eventIds, ?string $nip, ?string $nik): Collection
    {
        if ($eventIds->isEmpty()) {
            return collect();
        }

        if ($nip) {
            return Peserta::query()->whereIn('event_id', $eventIds)->where('nip', $nip)->get();
        }

        if ($nik) {
            return Peserta::query()->whereIn('event_id', $eventIds)->where('nik', $nik)->get();
        }

        return collect();
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function participantExistsInEvent(int $eventId, array $attributes): bool
    {
        $jenisPesertaId = (int) ($attributes['jenis_peserta_id'] ?? 0);

        if ($jenisPesertaId === 1 && ! empty($attributes['nip'])) {
            return Peserta::where('event_id', $eventId)
                ->where('nip', $attributes['nip'])
                ->exists();
        }

        if ($jenisPesertaId === 2 && ! empty($attributes['nik'])) {
            return Peserta::where('event_id', $eventId)
                ->where('nik', $attributes['nik'])
                ->exists();
        }

        return false;
    }

    private function identifierKey(?string $nip, ?string $nik): ?string
    {
        if (filled($nip)) {
            return 'nip:'.$nip;
        }

        if (filled($nik)) {
            return 'nik:'.$nik;
        }

        return null;
    }
}
