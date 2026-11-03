<?php

namespace App\Services;

use App\Models\Event;
use App\Models\EventGroup;
use App\Models\Peserta;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class PesertaParticipationService
{
    public const SESSION_GROUP_KEY = 'peserta_event_group_key';

    public const SESSION_ACTIVE_PESERTA_ID = 'active_peserta_id';

    public function groupKeyForEvent(Event $event): string
    {
        if ($event->event_group_id) {
            return 'group_' . $event->event_group_id;
        }

        return 'solo_' . $event->id;
    }

    public function findActiveParticipationsByIdNumber(string $idNumber): Collection
    {
        return $this->findParticipationsByIdNumber($idNumber, onlyOngoingEvents: true);
    }

    /**
     * Semua partisipasi aktif peserta (termasuk event admin status Selesai) — untuk dashboard.
     */
    public function findParticipationsByIdNumberForDisplay(string $idNumber): Collection
    {
        return $this->findParticipationsByIdNumber($idNumber, onlyOngoingEvents: false);
    }

    private function findParticipationsByIdNumber(string $idNumber, bool $onlyOngoingEvents): Collection
    {
        $length = strlen($idNumber);

        $query = Peserta::query()
            ->with(['event.metodeTes', 'event.eventGroup'])
            ->where('is_active', 'true');

        if ($onlyOngoingEvents) {
            $query->whereHas('event', function ($query) {
                $query->where('is_finished', 'false');
            });
        }

        if ($length === 18) {
            $query->where('jenis_peserta_id', 1)->where('nip', $idNumber);
        } elseif ($length === 16) {
            $query->where('jenis_peserta_id', 2)->where('nik', $idNumber);
        } else {
            return collect();
        }

        return $query->orderBy('event_id')->get()->sortBy(fn (Peserta $p) => $p->event->metode_tes_id)->values();
    }

    public function participationsMatchingPassword(Collection $participations, string $password): Collection
    {
        return $participations->filter(function (Peserta $peserta) use ($password) {
            return \Illuminate\Support\Facades\Hash::check($password, $peserta->password);
        });
    }

    public function resolveLoginParticipation(Collection $participations): ?Peserta
    {
        if ($participations->isEmpty()) {
            return null;
        }

        $anchor = $participations->sortByDesc('event_id')->first();
        $groupKey = $this->groupKeyForEvent($anchor->event);

        session([self::SESSION_GROUP_KEY => $groupKey]);
        session()->forget(self::SESSION_ACTIVE_PESERTA_ID);

        return $anchor;
    }

    public function getCurrentGroupParticipations(): Collection
    {
        $authPeserta = Auth::guard('peserta')->user();

        if (! $authPeserta) {
            return collect();
        }

        $groupKey = session(self::SESSION_GROUP_KEY);

        if (! $groupKey) {
            $groupKey = $this->groupKeyForEvent($authPeserta->event);
            session([self::SESSION_GROUP_KEY => $groupKey]);
        }

        $idNumber = $authPeserta->nip ?: $authPeserta->nik;

        $participations = $this->findParticipationsByIdNumberForDisplay($idNumber);

        return $this->filterByGroupKey($participations, $groupKey);
    }

    public function filterByGroupKey(Collection $participations, string $groupKey): Collection
    {
        if (str_starts_with($groupKey, 'solo_')) {
            $eventId = (int) str_replace('solo_', '', $groupKey);

            return $participations->where('event_id', $eventId)->values();
        }

        if (str_starts_with($groupKey, 'group_')) {
            $groupId = (int) str_replace('group_', '', $groupKey);

            return $participations
                ->filter(fn (Peserta $p) => $p->event->event_group_id === $groupId)
                ->values();
        }

        return $participations;
    }

    public function setActivePesertaContext(int $pesertaId): void
    {
        $allowed = $this->getCurrentGroupParticipations()->pluck('id');

        if (! $allowed->contains($pesertaId)) {
            abort(403, 'Partisipasi tes tidak valid.');
        }

        session([self::SESSION_ACTIVE_PESERTA_ID => $pesertaId]);
    }

    public function clearActivePesertaContext(): void
    {
        session()->forget(self::SESSION_ACTIVE_PESERTA_ID);
    }

    public function activePeserta(): ?Peserta
    {
        $authPeserta = Auth::guard('peserta')->user();

        if (! $authPeserta) {
            return null;
        }

        $activeId = session(self::SESSION_ACTIVE_PESERTA_ID);

        if ($activeId) {
            $active = $this->getCurrentGroupParticipations()->firstWhere('id', $activeId);

            if ($active) {
                return $active;
            }
        }

        return $authPeserta;
    }

    public function activeEventId(): int
    {
        return (int) $this->activePeserta()->event_id;
    }

    public function activePesertaId(): int
    {
        return (int) $this->activePeserta()->id;
    }

    public function routeForMetodeTes(int $metodeTesId): string
    {
        return match ($metodeTesId) {
            1 => route('peserta.portofolio'),
            2 => route('peserta.tes-intelektual'),
            3 => route('peserta.tes-cakap-digital'),
            4 => route('peserta.tes-kompetensi-teknis'),
            5, 6, 7, 8 => route('peserta.tes-pspk'),
            default => route('peserta.dashboard'),
        };
    }

    public function routeForParticipation(Peserta $peserta): string
    {
        $metodeTesId = (int) $peserta->event->metode_tes_id;
        $eventId = $peserta->event_id;
        $pesertaId = $peserta->id;

        if ($metodeTesId === 2) {
            $intelektualSelesai = collect(getFinishedTesIntelektual($eventId, $pesertaId))
                ->every(fn ($selesai) => $selesai === true);

            if (! $intelektualSelesai) {
                return session()->has('exam_pin')
                    ? route('peserta.tes-intelektual.home')
                    : route('peserta.tes-intelektual');
            }

            $potensiSelesai = collect(getFinishedTes($eventId, $pesertaId))
                ->every(fn ($selesai) => $selesai === true);

            if (! $potensiSelesai) {
                return session()->has('exam_pin')
                    ? route('peserta.tes-potensi.home')
                    : route('peserta.tes-potensi');
            }

            return route('peserta.dashboard');
        }

        return $this->routeForMetodeTes($metodeTesId);
    }

    public function intelektualSelesai(?int $eventId = null, ?int $pesertaId = null): bool
    {
        return collect(getFinishedTesIntelektual($eventId, $pesertaId))
            ->every(fn ($selesai) => $selesai === true);
    }

    public function metodeTesLabel(int $metodeTesId): string
    {
        return match ($metodeTesId) {
            1 => 'Assessment Center (Portofolio)',
            2 => 'Tes Potensi Online',
            3 => 'Tes Cakap Digital',
            4 => 'Tes Kompetensi Teknis',
            5 => 'Tes PSPK Level 1',
            6 => 'Tes PSPK Level 2',
            7 => 'Tes PSPK Level 3',
            8 => 'Tes PSPK Level 4',
            default => 'Tes',
        };
    }

    public function isTestFinished(Peserta $peserta): bool
    {
        $eventId = $peserta->event_id;
        $pesertaId = $peserta->id;
        $metodeTesId = $peserta->event->metode_tes_id;

        return match ($metodeTesId) {
            1 => false,
            2 => collect(getFinishedTesIntelektual($eventId, $pesertaId))->every(fn ($v) => $v === true)
                && collect(getFinishedTes($eventId, $pesertaId))->every(fn ($v) => $v === true),
            3 => collect(getFinishedTesCakapDigital($eventId, $pesertaId))->contains(true),
            4 => collect(getFinishedTesKompetensiTeknis($eventId, $pesertaId))->contains(true),
            5, 6 => collect(getFinishedTesPspk($eventId, $pesertaId))->contains(true),
            7 => collect(getFinishedTesPspkLv3($eventId, $pesertaId))->contains(true),
            8 => collect(getFinishedTesPspkLv4($eventId, $pesertaId))->contains(true),
            default => false,
        };
    }

    public function canStartTest(Peserta $peserta): bool
    {
        if ($peserta->event->is_finished === 'true') {
            return false;
        }

        if ($this->isTestFinished($peserta)) {
            return false;
        }

        if ((int) $peserta->event->metode_tes_id === 1 && $peserta->event->is_open === 'false') {
            return false;
        }

        return true;
    }

    /**
     * State tombol kartu dashboard (tanpa urutan antar tes).
     *
     * @return array{type: string, label: string, hint: ?string, btn_class: string}
     */
    public function dashboardButtonState(Peserta $peserta): array
    {
        if ($this->isTestFinished($peserta)) {
            return [
                'type' => 'finished',
                'label' => 'Sudah Selesai',
                'hint' => null,
                'btn_class' => 'btn-success',
            ];
        }

        if ($peserta->event->is_finished === 'true') {
            return [
                'type' => 'event_closed',
                'label' => 'Tes Tidak Dapat Dimulai',
                'hint' => 'Event assessment ini sudah ditutup oleh admin. Anda tidak dapat memulai atau melanjutkan tes ini.',
                'btn_class' => 'btn-secondary',
            ];
        }

        if ((int) $peserta->event->metode_tes_id === 1 && $peserta->event->is_open === 'false') {
            return [
                'type' => 'portofolio_closed',
                'label' => 'Portofolio Ditutup',
                'hint' => 'Pengisian portofolio untuk event ini sedang ditutup. Silakan hubungi panitia jika memerlukan bantuan.',
                'btn_class' => 'btn-info',
            ];
        }

        if ($this->canStartTest($peserta)) {
            return [
                'type' => 'start',
                'label' => 'Mulai Tes',
                'hint' => null,
                'btn_class' => 'btn-primary',
            ];
        }

        return [
            'type' => 'unavailable',
            'label' => 'Tidak Tersedia',
            'hint' => 'Tes belum dapat diakses saat ini.',
            'btn_class' => 'btn-secondary',
        ];
    }

    public function createGroupForEvent(string $nama, ?string $periode = null): EventGroup
    {
        return EventGroup::create([
            'nama' => $nama,
            'periode' => $periode,
            'is_active' => 'true',
        ]);
    }
}
