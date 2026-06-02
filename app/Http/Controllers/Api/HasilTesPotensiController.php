<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\NilaiJpm;
use App\Models\Peserta;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class HasilTesPotensiController extends Controller
{
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

        $pesertaList = Peserta::query()
            ->where('is_active', 'true')
            ->when($nipGiven, fn ($q) => $q->where('nip', $validated['nip'])->where('jenis_peserta_id', 1))
            ->when(! $nipGiven, fn ($q) => $q->where('nik', $validated['nik'])->where('jenis_peserta_id', 2))
            ->when(! empty($validated['tahun']), fn ($q) => $q->whereYear('test_started_at', $validated['tahun']))
            ->when(! empty($validated['tanggal_tes']), function ($q) use ($validated) {
                $q->whereDate('test_started_at', Carbon::parse($validated['tanggal_tes'])->format('Y-m-d'));
            })
            ->whereHas('ujianInterpersonal', fn ($q) => $q->where('is_finished', 'true'))
            ->whereHas('ujianKesadaranDiri', fn ($q) => $q->where('is_finished', 'true'))
            ->whereHas('ujianBerpikirKritis', fn ($q) => $q->where('is_finished', 'true'))
            ->whereHas('ujianPengembanganDiri', fn ($q) => $q->where('is_finished', 'true'))
            ->whereHas('ujianProblemSolving', fn ($q) => $q->where('is_finished', 'true'))
            ->whereHas('ujianKecerdasanEmosi', fn ($q) => $q->where('is_finished', 'true'))
            ->whereHas('ujianMotivasiKomitmen', fn ($q) => $q->where('is_finished', 'true'))
            ->whereHas('ujianIntelektualSubTes1', fn ($q) => $q->where('is_finished', 'true'))
            ->whereHas('ujianIntelektualSubTes2', fn ($q) => $q->where('is_finished', 'true'))
            ->whereHas('ujianIntelektualSubTes3', fn ($q) => $q->where('is_finished', 'true'))
            ->with([
                'event.nomorLaporan',
                'hasilIntelektual',
                'hasilInterpersonal',
                'hasilKesadaranDiri',
                'hasilBerpikirKritis',
                'hasilProblemSolving',
                'hasilPengembanganDiri',
                'hasilKecerdasanEmosi',
                'hasilMotivasiKomitmen',
                'nilaiJpm',
            ])
            ->orderByDesc('id')
            ->get();

        if ($pesertaList->isEmpty()) {
            return response()->json([
                'message' => 'Data tidak ditemukan',
                'status' => 'error',
                'code' => 404,
            ], 404);
        }

        $rows = $pesertaList
            ->map(fn (Peserta $peserta) => $this->formatBaris($peserta))
            ->filter()
            ->values();

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

    private function formatBaris(Peserta $peserta): ?array
    {
        if (! $this->hasilLengkap($peserta)) {
            return null;
        }

        $capaian = $this->buildCapaianLevel($peserta);

        $jpm = countJpm($capaian);
        $kategoriTeks = getKategori($jpm) ?? null;
        $pjf = round($jpm * 100, 2);

        NilaiJpm::updateOrCreate(
            ['event_id' => $peserta->event_id, 'peserta_id' => $peserta->id],
            ['jpm' => $pjf, 'kategori' => $kategoriTeks]
        );

        $tahunTes = null;
        if ($peserta->test_started_at) {
            $tahunTes = (int) Carbon::parse($peserta->test_started_at)->format('Y');
        } elseif ($peserta->event) {
            $rawTgl = $peserta->event->getRawOriginal('tgl_mulai');
            $tahunTes = $rawTgl ? (int) Carbon::parse($rawTgl)->format('Y') : null;
        }

        return [
            'nama' => $peserta->nama,
            'nip' => $peserta->nip,
            'nik' => $peserta->nik,
            'tahun_tes' => $tahunTes,
            'nomor_laporan' => $this->resolveNomorLaporan($peserta, $peserta->event?->nomorLaporan ?? collect()),
            'tgl_tes' => $peserta->test_started_at,
            'tujuan_penkom' => 1,
            'level_intelektual' => $this->levelUntukApi($capaian['intelektual']),
            'level_interpersonal' => $this->levelUntukApi($capaian['interpersonal']),
            'level_kesadaran_diri' => $this->levelUntukApi($capaian['kesadaran_diri']),
            'level_berpikir_kritis' => $this->levelUntukApi($capaian['berpikir_kritis']),
            'level_problem_solving' => $this->levelUntukApi($capaian['problem_solving']),
            'level_kecerdasan_emosi' => $this->levelUntukApi($capaian['kecerdasan_emosi']),
            'level_pengembangan_diri' => $this->levelUntukApi($capaian['pengembangan_diri']),
            'level_motivasi_komitmen' => $this->levelUntukApi($capaian['motivasi_komitmen']),
            'pjf' => $pjf,
            'kategori_teks' => $kategoriTeks,
            'kategori_angka' => $this->kategoriKeAngka($kategoriTeks),
        ];
    }

    private function hasilLengkap(Peserta $peserta): bool
    {
        $levelFields = [
            $peserta->hasilIntelektual?->level,
            $peserta->hasilInterpersonal?->level_total,
            $peserta->hasilKesadaranDiri?->level_total,
            $peserta->hasilBerpikirKritis?->level_total,
            $peserta->hasilProblemSolving?->level_total,
            $peserta->hasilKecerdasanEmosi?->level_total,
            $peserta->hasilPengembanganDiri?->level_total,
            $peserta->hasilMotivasiKomitmen?->level_total,
        ];

        foreach ($levelFields as $level) {
            if ($level === null || $level === '') {
                return false;
            }
        }

        return true;
    }

    /**
     * Capaian level 1–5 tanpa +/-, selaras kolom "Capaian Level" pada laporan PDF.
     */
    private function levelUntukApi(mixed $capaianLevel): ?int
    {
        if ($capaianLevel === null || $capaianLevel === '') {
            return null;
        }

        return (int) $capaianLevel;
    }

    /**
     * @return array<string, string|int|null>
     */
    private function buildCapaianLevel(Peserta $peserta): array
    {
        return [
            'intelektual' => capaianLevel($peserta->hasilIntelektual?->level),
            'interpersonal' => capaianLevel($peserta->hasilInterpersonal?->level_total),
            'kesadaran_diri' => capaianLevel($peserta->hasilKesadaranDiri?->level_total),
            'berpikir_kritis' => capaianLevel($peserta->hasilBerpikirKritis?->level_total),
            'problem_solving' => capaianLevel($peserta->hasilProblemSolving?->level_total),
            'kecerdasan_emosi' => capaianLevel($peserta->hasilKecerdasanEmosi?->level_total),
            'pengembangan_diri' => capaianLevel($peserta->hasilPengembanganDiri?->level_total),
            'motivasi_komitmen' => capaianLevel($peserta->hasilMotivasiKomitmen?->level_total),
        ];
    }

    /**
     * @param  \Illuminate\Support\Collection<int, \App\Models\NomorLaporan>|iterable  $nomorLaporans
     */
    private function resolveNomorLaporan($peserta, $nomorLaporans): ?string
    {
        if (! $peserta || collect($nomorLaporans)->isEmpty()) {
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
     * Tinggi = 1, Menengah = 2, Rendah = 3 (selaras label Person Job Fit pada laporan potensi).
     */
    private function kategoriKeAngka(?string $kategori): ?int
    {
        if ($kategori === null || $kategori === '') {
            return null;
        }

        return match (strtolower(trim($kategori))) {
            'tinggi' => 1,
            'menengah' => 2,
            'rendah' => 3,
            default => null,
        };
    }
}
