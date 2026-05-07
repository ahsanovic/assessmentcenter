<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Peserta;
use App\Models\Pspk\HasilPspk;
use App\Models\RefAspekPspk;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class HasilPspkController extends Controller
{
    /** Urutan output kode aspek (huruf kecil), selaras referensi bisnis. */
    private const ASPEK_URUTAN = ['int', 'ks', 'kom', 'oph', 'pp', 'pdol', 'mp', 'pk', 'pb'];

    public function index(Request $request)
    {
        $validated = $request->validate([
            'nip' => ['nullable', 'string'],
            'nik' => ['nullable', 'string'],
            'tahun' => ['nullable', 'integer', 'min:1990', 'max:2100'],
            'tanggal_tes' => ['nullable', 'date'],
        ]);

        $nipGiven = filled($validated['nip'] ?? null);
        $nikGiven = filled($validated['nik'] ?? null);

        if (! ($nipGiven xor $nikGiven)) {
            throw ValidationException::withMessages([
                'nip' => ['Wajib mengisi tepat satu: nip (peserta ASN) atau nik (peserta non-ASN).'],
            ]);
        }

        $pesertaIds = Peserta::query()
            ->where('is_active', 'true')
            ->when($nipGiven, fn ($q) => $q->where('nip', $validated['nip'])->where('jenis_peserta_id', 1))
            ->when(! $nipGiven, fn ($q) => $q->where('nik', $validated['nik'])->where('jenis_peserta_id', 2))
            ->when(! empty($validated['tahun']), fn ($q) => $q->whereYear('test_started_at', $validated['tahun']))
            ->when(! empty($validated['tanggal_tes']), function ($q) use ($validated) {
                $q->whereDate('test_started_at', Carbon::parse($validated['tanggal_tes'])->format('Y-m-d'));
            })
            ->pluck('id');

        if ($pesertaIds->isEmpty()) {
            return response()->json([
                'message' => 'Data tidak ditemukan',
                'status' => 'error',
                'code' => 404,
            ], 404);
        }

        $aspekRefs = RefAspekPspk::query()->orderBy('id')->get();

        $rows = HasilPspk::query()
            ->select([
                'id',
                'peserta_id',
                'event_id',
                'ujian_id',
                'nilai_capaian',
                'jpm',
                'kategori',
            ])
            ->whereIn('peserta_id', $pesertaIds)
            ->whereExists(function ($query) {
                $query->selectRaw('1')
                    ->from('ujian_pspk')
                    ->whereColumn('ujian_pspk.id', 'hasil_pspk.ujian_id')
                    ->where('ujian_pspk.is_finished', 'true');
            })
            ->with(['peserta', 'peserta.event', 'event.nomorLaporan'])
            ->orderByDesc('id')
            ->get()
            ->map(fn (HasilPspk $hasil) => $this->formatBaris($hasil, $aspekRefs));
        
        if ($rows->isEmpty()) {
            return response()->json([
                'message' => 'Data tidak ditemukan',
                'status' => 'error',
                'code' => 404,
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'code' => 200,
            'data' => $rows,
        ], 200);
    }

    private function formatBaris(HasilPspk $hasil, $aspekRefs): array
    {
        $peserta = $hasil->peserta;
        $event = $hasil->event ?? $peserta?->event;
        $nomorLaporans = $event?->nomorLaporan ?? collect();

        $levelPspk = match ((string) ($event->metode_tes_id ?? '')) {
            '5' => 1,
            '6' => 2,
            default => null,
        };

        $tahunTes = null;
        if ($peserta?->test_started_at) {
            $tahunTes = (int) Carbon::parse($peserta->test_started_at)->format('Y');
        } elseif ($event) {
            $rawTgl = $event->getRawOriginal('tgl_mulai');
            $tahunTes = $rawTgl ? (int) Carbon::parse($rawTgl)->format('Y') : null;
        }

        $nilaiCapaianByCode = $this->mapNilaiCapaianByKode($hasil->nilai_capaian ?? [], $aspekRefs);
        $nilaiOrdered = [];
        foreach (self::ASPEK_URUTAN as $code) {
            $nilaiOrdered[$code] = $nilaiCapaianByCode[$code] ?? null;
        }

        return [
            'nama' => $peserta?->nama,
            'nip' => $peserta?->nip,
            'nik' => $peserta?->nik,
            'tahun_tes' => $tahunTes,
            'nomor_laporan_individu' => $this->resolveNomorLaporanIndividu($peserta, $nomorLaporans),
            'level_pspk' => $levelPspk,
            'nilai' => $nilaiOrdered,
            'nilai_jpm' => $hasil->jpm !== null ? round((float) $hasil->jpm, 2) : null,
            'kategori_teks' => $hasil->kategori,
            'kategori_id' => $this->kategoriKeAngka($hasil->kategori),
        ];
    }

    /**
     * Nomor laporan individu: samakan dengan unduhan PDF bila tanggal tes = tanggal di master nomor laporan.
     * Bila tidak cocok atau tes tanpa test_started_at, fallback satu nomor per event atau baris terbaru (tanggal/id).
     *
     * @param  \Illuminate\Support\Collection<int, \App\Models\NomorLaporan>|iterable  $nomorLaporans
     */
    private function resolveNomorLaporanIndividu($peserta, $nomorLaporans): ?string
    {
        if (! $peserta || $nomorLaporans->isEmpty()) {
            return null;
        }

        $items = collect($nomorLaporans)->filter(fn ($nl) => filled($nl->nomor));
        if ($items->isEmpty()) {
            return null;
        }

        $tesDay = null;
        if ($peserta->test_started_at) {
            $tesDay = Carbon::parse($peserta->test_started_at)->startOfDay();
        }

        foreach ($items as $nl) {
            $rawTgl = $nl->getRawOriginal('tanggal');
            if ($tesDay === null || ! $rawTgl) {
                continue;
            }
            if (Carbon::parse($rawTgl)->startOfDay()->equalTo($tesDay)) {
                return $nl->nomor;
            }
        }

        if ($items->count() === 1) {
            return $items->first()->nomor;
        }

        return $items->sort(function ($a, $b) {
            $ta = $a->getRawOriginal('tanggal') ?? '';
            $tb = $b->getRawOriginal('tanggal') ?? '';
            if ($ta !== $tb) {
                return strcmp($tb, $ta);
            }

            return (string) $b->getKey() <=> (string) $a->getKey();
        })->first()?->nomor;
    }

    /**
     * Memetakan indeks nilai_capaian ke kode aspek mengikuti urutan RefAspekPspk::orderBy('id') (sama seperti laporan PDF).
     *
     * @param  array<int, float|int|string|null>  $nilaiCapaian
     * @return array<string, float|null>
     */
    private function mapNilaiCapaianByKode(array $nilaiCapaian, $aspekRefs): array
    {
        $map = [];
        foreach ($aspekRefs as $idx => $ref) {
            $code = strtolower((string) $ref->kode_aspek);
            $map[$code] = array_key_exists($idx, $nilaiCapaian)
                ? $this->normalizeNilaiCapaian($nilaiCapaian[$idx])
                : null;
        }

        return $map;
    }

    private function normalizeNilaiCapaian(mixed $val): ?float
    {
        if ($val === null || $val === '') {
            return null;
        }

        return is_numeric($val) ? (float) $val : null;
    }

    /**
     * optimal = 1, cukup optimal = 2, kurang optimal = 3 (selaras label pada tes PSPK).
     */
    private function kategoriKeAngka(?string $kategori): ?int
    {
        if ($kategori === null || $kategori === '') {
            return null;
        }

        $k = strtolower(trim($kategori));

        if (str_contains($k, 'kurang')) {
            return 3;
        }
        if (str_contains($k, 'cukup')) {
            return 2;
        }
        if (str_contains($k, 'optimal')) {
            return 1;
        }

        return null;
    }
}
