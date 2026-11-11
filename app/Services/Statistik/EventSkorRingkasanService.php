<?php

namespace App\Services\Statistik;

use App\Models\Event;
use App\Models\LiterasiDigital\HasilLiterasiDigital;
use App\Models\Peserta;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

final class EventSkorRingkasanService
{
    public function ringkasanPotensi(Event $event, ?string $search, ?string $tanggalTes): EventSkorRingkasanDto
    {
        $eventId = (int) $event->id;
        $filterAktif = $this->filterAktif($search, $tanggalTes);

        $populasi = PotensiPesertaSelesaiQuery::forEvent($event, $search, $tanggalTes);
        $totalPopulasi = (clone $populasi)->count('peserta.id');

        $denganSkor = (clone $populasi)
            ->join('nilai_jpm', function ($join) use ($eventId) {
                $join->on('nilai_jpm.peserta_id', '=', 'peserta.id')
                    ->where('nilai_jpm.event_id', '=', $eventId)
                    ->whereNotNull('nilai_jpm.jpm');
            })
            ->select('peserta.id', 'peserta.nama', 'nilai_jpm.jpm', 'nilai_jpm.kategori');

        $skorQuery = $denganSkor->getQuery();

        $agg = $this->tanpaKolom($skorQuery)
            ->selectRaw('COUNT(*) as total')
            ->selectRaw('ROUND(AVG(nilai_jpm.jpm), 2) as rata')
            ->first();

        $totalDenganSkor = (int) ($agg->total ?? 0);
        $rata = $agg->rata !== null ? (float) $agg->rata : null;

        $distribusi = $this->buildDistribusi(
            $this->tanpaKolom($skorQuery)
                ->selectRaw('nilai_jpm.kategori as kategori, COUNT(*) as jumlah')
                ->groupBy('nilai_jpm.kategori')
                ->orderByDesc('jumlah')
                ->get(),
            $totalDenganSkor
        );

        $terendah = $this->ekstremPeserta(clone $skorQuery, 'nilai_jpm.jpm', 'asc');
        $tertinggi = $this->ekstremPeserta(clone $skorQuery, 'nilai_jpm.jpm', 'desc');

        $peringatan = null;
        if ($totalPopulasi > $totalDenganSkor) {
            $selisih = $totalPopulasi - $totalDenganSkor;
            $peringatan = "{$selisih} peserta selesai belum memiliki nilai PJF tersimpan (buka laporan hasil atau sinkronisasi API).";
        }

        return new EventSkorRingkasanDto(
            labelSkor: 'PJF',
            totalPesertaPopulasi: $totalPopulasi,
            totalDenganSkor: $totalDenganSkor,
            rata: $rata,
            terendah: $terendah,
            tertinggi: $tertinggi,
            distribusi: $distribusi,
            filterAktif: $filterAktif,
            peringatan: $peringatan,
        );
    }

    public function ringkasanPspk(Event $event, ?string $search, ?string $tanggalTes): EventSkorRingkasanDto
    {
        $eventId = (int) $event->id;
        $filterAktif = $this->filterAktif($search, $tanggalTes);
        $pesertaIds = $event->pesertaIdTesPspk->pluck('peserta_id');

        $base = $this->queryPspkBase($eventId, $pesertaIds)
            ->where('ujian_pspk.is_finished', 'true')
            ->whereNotNull('hasil_pspk.jpm');

        $this->applyPspkSearch($base, $search);
        $this->applyTanggalTesPeserta($base, $tanggalTes);

        return $this->ringkasanDariQuery($base, 'hasil_pspk.jpm', 'hasil_pspk.kategori', 'JPM', $filterAktif);
    }

    public function ringkasanLiterasiDigital(Event $event, ?string $search, ?string $tanggalTes): EventSkorRingkasanDto
    {
        $eventId = (int) $event->id;
        $filterAktif = $this->filterAktif($search, $tanggalTes);

        $hasilTerbaru = HasilLiterasiDigital::query()
            ->selectRaw('MAX(hasil_literasi_digital.id) as id')
            ->join('ujian_literasi_digital as ujian_hasil', function ($join) use ($eventId) {
                $join->on('ujian_hasil.id', '=', 'hasil_literasi_digital.ujian_id')
                    ->where('ujian_hasil.event_id', $eventId);
            })
            ->where('hasil_literasi_digital.event_id', $eventId)
            ->groupBy('hasil_literasi_digital.peserta_id');

        $base = Peserta::query()
            ->join('hasil_literasi_digital', 'hasil_literasi_digital.peserta_id', '=', 'peserta.id')
            ->whereIn('hasil_literasi_digital.id', $hasilTerbaru)
            ->join('ujian_literasi_digital', function ($join) use ($eventId) {
                $join->on('ujian_literasi_digital.id', '=', 'hasil_literasi_digital.ujian_id')
                    ->where('ujian_literasi_digital.event_id', $eventId);
            })
            ->where('hasil_literasi_digital.event_id', $eventId)
            ->where('peserta.event_id', $eventId)
            ->where('ujian_literasi_digital.is_finished', 'true')
            ->whereNotNull('hasil_literasi_digital.jpm')
            ->select('peserta.id', 'peserta.nama', 'hasil_literasi_digital.jpm', 'hasil_literasi_digital.kategori');

        $this->applyLiterasiSearch($base, $search);
        $this->applyTanggalTesPeserta($base, $tanggalTes, 'peserta');

        return $this->ringkasanDariQuery($base, 'hasil_literasi_digital.jpm', 'hasil_literasi_digital.kategori', 'JPM', $filterAktif);
    }

    /**
     * @param  \Illuminate\Support\Collection<int, object{kategori: string, jumlah: int}>  $rows
     * @return array<int, array{kategori: string, jumlah: int, persen: float}>
     */
    private function buildDistribusi($rows, int $total): array
    {
        if ($total === 0) {
            return [];
        }

        $out = [];
        foreach ($rows as $row) {
            $kategori = (string) ($row->kategori ?? '—');
            $jumlah = (int) $row->jumlah;
            $out[] = [
                'kategori' => $kategori,
                'jumlah' => $jumlah,
                'persen' => round($jumlah / $total * 100, 1),
            ];
        }

        return $out;
    }

    /**
     * @param  Builder<\App\Models\Peserta>  $query
     */
    private function ringkasanDariQuery(
        Builder $query,
        string $kolomJpm,
        string $kolomKategori,
        string $labelSkor,
        bool $filterAktif
    ): EventSkorRingkasanDto {
        $agg = $this->tanpaKolom($query)
            ->selectRaw('COUNT(*) as total')
            ->selectRaw("ROUND(AVG({$kolomJpm}), 2) as rata")
            ->first();

        $totalDenganSkor = (int) ($agg->total ?? 0);
        $rata = $agg->rata !== null ? (float) $agg->rata : null;

        $distribusi = $this->buildDistribusi(
            $this->tanpaKolom($query)
                ->selectRaw("{$kolomKategori} as kategori, COUNT(*) as jumlah")
                ->groupBy($kolomKategori)
                ->orderByDesc('jumlah')
                ->get(),
            $totalDenganSkor
        );

        $terendah = $this->ekstremPeserta(clone $query, $kolomJpm, 'asc');
        $tertinggi = $this->ekstremPeserta(clone $query, $kolomJpm, 'desc');

        return new EventSkorRingkasanDto(
            labelSkor: $labelSkor,
            totalPesertaPopulasi: $totalDenganSkor,
            totalDenganSkor: $totalDenganSkor,
            rata: $rata,
            terendah: $terendah,
            tertinggi: $tertinggi,
            distribusi: $distribusi,
            filterAktif: $filterAktif,
        );
    }

    /**
     * @return array{nilai: float, nama: string}|null
     */
    private function ekstremPeserta(Builder $query, string $kolomJpm, string $arah): ?array
    {
        $row = $query
            ->select('peserta.nama', DB::raw("{$kolomJpm} as nilai_skor"))
            ->orderBy($kolomJpm, $arah)
            ->limit(1)
            ->first();

        if ($row === null || $row->nilai_skor === null) {
            return null;
        }

        return [
            'nilai' => round((float) $row->nilai_skor, 2),
            'nama' => (string) $row->nama,
        ];
    }

    /**
     * Salinan query tanpa kolom SELECT bawaan, agar agregat tidak bentrok ONLY_FULL_GROUP_BY.
     *
     * @param  Builder<\App\Models\Peserta>  $query
     * @return Builder<\App\Models\Peserta>
     */
    private function tanpaKolom(Builder $query): Builder
    {
        $clone = clone $query;
        $clone->getQuery()->columns = [];
        $clone->getQuery()->bindings['select'] = [];

        return $clone;
    }

    private function filterAktif(?string $search, ?string $tanggalTes): bool
    {
        return filled($search) || filled($tanggalTes);
    }

    /**
     * @param  \Illuminate\Support\Collection<int, int|string>  $pesertaIds
     * @return Builder<\App\Models\Peserta>
     */
    private function queryPspkBase(int $eventId, $pesertaIds): Builder
    {
        return Peserta::query()
            ->join('hasil_pspk', 'hasil_pspk.peserta_id', '=', 'peserta.id')
            ->join('ujian_pspk', 'ujian_pspk.peserta_id', '=', 'peserta.id')
            ->whereIn('peserta.id', $pesertaIds)
            ->where('hasil_pspk.event_id', $eventId)
            ->where('ujian_pspk.event_id', $eventId)
            ->select('peserta.id', 'peserta.nama', 'hasil_pspk.jpm', 'hasil_pspk.kategori');
    }

    private function applyPspkSearch(Builder $query, ?string $search): void
    {
        if ($search === null || $search === '') {
            return;
        }

        $term = '%'.$search.'%';
        $query->where(function ($q) use ($term) {
            $q->where('peserta.nama', 'like', $term)
                ->orWhere('peserta.nip', 'like', $term)
                ->orWhere('peserta.nik', 'like', $term)
                ->orWhere('peserta.jabatan', 'like', $term)
                ->orWhere('peserta.instansi', 'like', $term);
        });
    }

    private function applyLiterasiSearch(Builder $query, ?string $search): void
    {
        if ($search === null || $search === '') {
            return;
        }

        $term = '%'.$search.'%';
        $query->where(function ($q) use ($term) {
            $q->where('peserta.nama', 'like', $term)
                ->orWhere('peserta.nip', 'like', $term)
                ->orWhere('peserta.nik', 'like', $term)
                ->orWhere('peserta.jabatan', 'like', $term)
                ->orWhere('peserta.instansi', 'like', $term);
        });
    }

    private function applyTanggalTesPeserta(Builder $query, ?string $tanggalTes, string $tabelPeserta = 'peserta'): void
    {
        if ($tanggalTes === null || $tanggalTes === '') {
            return;
        }

        $query->whereDate("{$tabelPeserta}.test_started_at", date('Y-m-d', strtotime($tanggalTes)));
    }
}
