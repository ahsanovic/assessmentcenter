<?php

namespace App\Services\Rekap;

use App\Models\Event;
use App\Models\Peserta;
use Illuminate\Support\Collection;

class RekapPesertaQueryService
{
    public function getFinishedPesertaForEvent(Event $event, ?string $tanggalTes = null): Collection
    {
        $query = Peserta::query()
            ->with($this->relationsForMetode((int) $event->metode_tes_id))
            ->where('event_id', $event->id)
            ->when($tanggalTes, function ($query) use ($tanggalTes) {
                $query->whereDate('test_started_at', $tanggalTes);
            });

        $this->applyFinishedScope($query, (int) $event->metode_tes_id);

        return $query->get();
    }

    private function relationsForMetode(int $metodeTesId): array
    {
        return match ($metodeTesId) {
            2 => [
                'event',
                'hasilIntelektual',
                'hasilInterpersonal',
                'hasilKesadaranDiri',
                'hasilBerpikirKritis',
                'hasilPengembanganDiri',
                'hasilProblemSolving',
                'hasilKecerdasanEmosi',
                'hasilMotivasiKomitmen',
                'nilaiJpm',
            ],
            3 => ['event', 'hasilCakapDigital'],
            4 => ['event', 'hasilKompetensiTeknis'],
            5, 6, 7, 8 => ['event', 'hasilPspk'],
            default => ['event'],
        };
    }

    private function applyFinishedScope($query, int $metodeTesId): void
    {
        match ($metodeTesId) {
            2 => $query
                ->whereHas('ujianInterpersonal', fn ($q) => $q->where('is_finished', 'true'))
                ->whereHas('ujianKesadaranDiri', fn ($q) => $q->where('is_finished', 'true'))
                ->whereHas('ujianBerpikirKritis', fn ($q) => $q->where('is_finished', 'true'))
                ->whereHas('ujianPengembanganDiri', fn ($q) => $q->where('is_finished', 'true'))
                ->whereHas('ujianProblemSolving', fn ($q) => $q->where('is_finished', 'true'))
                ->whereHas('ujianKecerdasanEmosi', fn ($q) => $q->where('is_finished', 'true'))
                ->whereHas('ujianMotivasiKomitmen', fn ($q) => $q->where('is_finished', 'true')),
            3 => $query->whereHas('ujianCakapDigital', fn ($q) => $q->where('is_finished', 'true')),
            4 => $query->whereHas('ujianKompetensiTeknis', fn ($q) => $q->where('is_finished', 'true')),
            5, 6, 7, 8 => $query->whereHas('ujianPspk', fn ($q) => $q->where('is_finished', 'true')),
            default => $query,
        };
    }
}
