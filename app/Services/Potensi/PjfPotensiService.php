<?php

namespace App\Services\Potensi;

use App\Models\Event;
use App\Models\NilaiJpm;
use App\Models\Peserta;
use App\Services\Statistik\PotensiPesertaSelesaiQuery;

final class PjfPotensiService
{
    /**
     * Hitung dan simpan PJF untuk peserta selesai yang belum punya baris nilai_jpm.
     *
     * @return array{tersimpan: int, dilewati: int}
     */
    public function simpanYangBelumAda(Event $event): array
    {
        $eventId = (int) $event->id;

        $peserta = PotensiPesertaSelesaiQuery::forEvent($event, null, null)
            ->whereDoesntHave('nilaiJpm', function ($query) use ($eventId) {
                $query->where('event_id', $eventId)->whereNotNull('jpm');
            })
            ->with([
                'hasilIntelektual' => fn ($query) => $query->where('event_id', $eventId),
                'hasilInterpersonal' => fn ($query) => $query->where('event_id', $eventId),
                'hasilKecerdasanEmosi' => fn ($query) => $query->where('event_id', $eventId),
                'hasilPengembanganDiri' => fn ($query) => $query->where('event_id', $eventId),
                'hasilProblemSolving' => fn ($query) => $query->where('event_id', $eventId),
                'hasilMotivasiKomitmen' => fn ($query) => $query->where('event_id', $eventId),
                'hasilBerpikirKritis' => fn ($query) => $query->where('event_id', $eventId),
                'hasilKesadaranDiri' => fn ($query) => $query->where('event_id', $eventId),
            ])
            ->get();

        $tersimpan = 0;
        $dilewati = 0;

        foreach ($peserta as $item) {
            if ($this->simpanDariHasil($eventId, $item) === null) {
                $dilewati++;

                continue;
            }

            $tersimpan++;
        }

        return [
            'tersimpan' => $tersimpan,
            'dilewati' => $dilewati,
        ];
    }

    /**
     * Simpan PJF hanya bila ketujuh ujian potensi selesai dan delapan level (termasuk intelektual) terisi.
     * Melempar exception jika penulisan database gagal; pemanggil alur ujian wajib menangkapnya sendiri.
     */
    public function simpanJikaSemuaSelesai(int $eventId, int $pesertaId): ?NilaiJpm
    {
        $status = getFinishedTes($eventId, $pesertaId);
        if (in_array(false, $status, true)) {
            return null;
        }

        $peserta = Peserta::query()->find($pesertaId);
        if ($peserta === null) {
            return null;
        }

        $peserta->load([
            'hasilIntelektual' => fn ($query) => $query->where('event_id', $eventId),
            'hasilInterpersonal' => fn ($query) => $query->where('event_id', $eventId),
            'hasilKecerdasanEmosi' => fn ($query) => $query->where('event_id', $eventId),
            'hasilPengembanganDiri' => fn ($query) => $query->where('event_id', $eventId),
            'hasilProblemSolving' => fn ($query) => $query->where('event_id', $eventId),
            'hasilMotivasiKomitmen' => fn ($query) => $query->where('event_id', $eventId),
            'hasilBerpikirKritis' => fn ($query) => $query->where('event_id', $eventId),
            'hasilKesadaranDiri' => fn ($query) => $query->where('event_id', $eventId),
        ]);

        return $this->simpanDariHasil($eventId, $peserta);
    }

    public function simpanDariHasil(int $eventId, Peserta $peserta): ?NilaiJpm
    {
        $level = [
            $peserta->hasilIntelektual?->level,
            $peserta->hasilInterpersonal?->level_total,
            $peserta->hasilKecerdasanEmosi?->level_total,
            $peserta->hasilPengembanganDiri?->level_total,
            $peserta->hasilProblemSolving?->level_total,
            $peserta->hasilMotivasiKomitmen?->level_total,
            $peserta->hasilBerpikirKritis?->level_total,
            $peserta->hasilKesadaranDiri?->level_total,
        ];

        foreach ($level as $nilaiLevel) {
            if ($nilaiLevel === null || $nilaiLevel === '') {
                return null;
            }
        }

        $rasio = countJpm(array_map(fn ($nilai) => capaianLevel($nilai), $level));

        return NilaiJpm::updateOrCreate(
            [
                'event_id' => $eventId,
                'peserta_id' => $peserta->id,
            ],
            [
                'jpm' => round($rasio * 100, 2),
                'kategori' => getKategori($rasio),
            ]
        );
    }
}
