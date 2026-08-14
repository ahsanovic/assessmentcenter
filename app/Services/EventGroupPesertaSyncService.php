<?php

namespace App\Services;

use App\Models\Event;
use App\Models\Peserta;
use Illuminate\Support\Collection;

class EventGroupPesertaSyncService
{
    /**
     * Salin peserta ke semua event lain dalam grup assessment yang sama.
     */
    public function replicateFromPeserta(Peserta $source): int
    {
        return $this->syncAttributesToSiblingEvents(
            (int) $source->event_id,
            $this->extractSyncableAttributes($source)
        );
    }

    /**
     * Perbarui peserta serupa (NIP/NIK sama) di event lain dalam grup.
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
     * Hapus peserta serupa di event lain dalam grup (tidak menghapus $source).
     */
    public function syncDeleteFromPeserta(Peserta $source, ?string $matchNip = null, ?string $matchNik = null): int
    {
        $sourceEvent = Event::find($source->event_id);

        if (! $sourceEvent?->event_group_id) {
            return 0;
        }

        $siblingEventIds = $this->siblingEventIds($sourceEvent);
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
     * @param  array<string, mixed>  $attributes
     */
    public function syncAttributesToSiblingEvents(int $sourceEventId, array $attributes): int
    {
        $sourceEvent = Event::find($sourceEventId);

        if (! $sourceEvent?->event_group_id) {
            return 0;
        }

        $siblingEventIds = $this->siblingEventIds($sourceEvent);

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
}
