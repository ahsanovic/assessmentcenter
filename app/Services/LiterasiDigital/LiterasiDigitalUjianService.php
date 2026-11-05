<?php

namespace App\Services\LiterasiDigital;

use App\Models\LiterasiDigital\HasilLiterasiDigital;
use App\Models\LiterasiDigital\JawabanLiterasiDigital;
use App\Models\LiterasiDigital\RefPilarLiterasiDigital;
use App\Models\LiterasiDigital\SoalLiterasiDigital;
use App\Models\LiterasiDigital\UjianLiterasiDigital;
use App\Models\SettingWaktuTes;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class LiterasiDigitalUjianService
{
    public const SOAL_PER_PILAR = 15;

    public const JENIS_TES_ID = 2;

    public const JPM_PENYEBUT = 12;

    public const KODE_PILAR = ['budaya', 'cakap', 'keamanan', 'etika'];

    private const KOLOM_SKOR = [
        'budaya' => 'skor_budaya',
        'cakap' => 'skor_cakap',
        'keamanan' => 'skor_keamanan',
        'etika' => 'skor_etika',
    ];

    private const KOLOM_LEVEL = [
        'budaya' => 'level_budaya',
        'cakap' => 'level_cakap',
        'keamanan' => 'level_keamanan',
        'etika' => 'level_etika',
    ];

    public function mulai(int $eventId, int $pesertaId): UjianLiterasiDigital
    {
        return DB::transaction(function () use ($eventId, $pesertaId) {
            $paket = $this->ambilPaketSoal();
            $menit = $this->durasiMenit();

            $ujian = UjianLiterasiDigital::create([
                'event_id' => $eventId,
                'peserta_id' => $pesertaId,
                'jumlah_soal' => $paket->count(),
                'waktu_tes_berakhir' => now()->addMinutes($menit),
                'is_finished' => 'false',
            ]);

            $baris = $paket->values()->map(function (SoalLiterasiDigital $soal, int $index) use ($ujian) {
                return [
                    'ujian_id' => $ujian->id,
                    'soal_id' => $soal->id,
                    'pilar_id' => $soal->pilar_id,
                    'urutan' => $index + 1,
                    'jawaban' => null,
                    'skor' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            })->all();

            JawabanLiterasiDigital::insert($baris);

            return $ujian;
        });
    }

    public function simpanJawaban(UjianLiterasiDigital $ujian, int $urutan, ?string $huruf): void
    {
        $huruf = strtoupper(trim((string) $huruf));
        if (! in_array($huruf, ['A', 'B', 'C', 'D', 'E'], true)) {
            $huruf = null;
        }

        $jawaban = JawabanLiterasiDigital::with('soal')
            ->where('ujian_id', $ujian->id)
            ->where('urutan', $urutan)
            ->firstOrFail();

        $jawaban->jawaban = $huruf;
        $jawaban->skor = $this->skorBenar($huruf, $jawaban->soal->kunci_jawaban);
        $jawaban->save();
    }

    public function selesai(UjianLiterasiDigital $ujian): HasilLiterasiDigital
    {
        return DB::transaction(function () use ($ujian) {
            $agregat = $this->hitungUlang($ujian);

            $ujian->fill($agregat);
            $ujian->is_finished = 'true';
            $ujian->save();

            return HasilLiterasiDigital::updateOrCreate(
                ['ujian_id' => $ujian->id],
                array_merge($agregat, [
                    'event_id' => $ujian->event_id,
                    'peserta_id' => $ujian->peserta_id,
                ])
            );
        });
    }

    public function durasiMenit(): int
    {
        $waktu = SettingWaktuTes::where('is_active', 'true')
            ->where('jenis_tes', self::JENIS_TES_ID)
            ->value('waktu');

        return max(1, (int) ($waktu ?: 60));
    }

    /**
     * Ambil 15 soal aktif dari tiap pilar, lalu acak seluruh paket sebagai satu kumpulan.
     */
    public function ambilPaketSoal(): Collection
    {
        $pilar = RefPilarLiterasiDigital::query()
            ->where('is_active', 'true')
            ->whereIn('kode', self::KODE_PILAR)
            ->orderBy('urutan')
            ->get();

        if ($pilar->count() !== count(self::KODE_PILAR)) {
            throw new RuntimeException('Keempat pilar Literasi Digital harus aktif sebelum tes dimulai.');
        }

        $paket = collect();

        foreach ($pilar as $item) {
            $soal = SoalLiterasiDigital::query()
                ->where('pilar_id', $item->id)
                ->where('is_active', 'true')
                ->inRandomOrder()
                ->limit(self::SOAL_PER_PILAR)
                ->get();

            if ($soal->count() < self::SOAL_PER_PILAR) {
                throw new RuntimeException("Pilar {$item->nama} baru memiliki {$soal->count()} soal aktif. Dibutuhkan ".self::SOAL_PER_PILAR.' soal.');
            }

            $paket = $paket->concat($soal);
        }

        return $paket->shuffle()->values();
    }

    /**
     * Hitung ulang JPM, kategori, dan level dari skor jawaban yang sudah tersimpan.
     * Kunci soal dan isi jawaban tidak diubah.
     *
     * @return array{berubah: bool, hasil: HasilLiterasiDigital, sebelum: array<string, mixed>}
     */
    public function hitungUlangRumus(UjianLiterasiDigital $ujian): array
    {
        return DB::transaction(function () use ($ujian) {
            $jawaban = JawabanLiterasiDigital::with('pilar')
                ->where('ujian_id', $ujian->id)
                ->get();
            $agregat = $this->agregatDariJawaban($jawaban);

            $hasilLama = HasilLiterasiDigital::where('ujian_id', $ujian->id)->first();
            $sebelum = $hasilLama
                ? $hasilLama->only(array_keys($agregat))
                : $ujian->only(array_keys($agregat));

            $ujian->fill($agregat);
            $ujian->save();

            $hasil = HasilLiterasiDigital::updateOrCreate(
                ['ujian_id' => $ujian->id],
                array_merge($agregat, [
                    'event_id' => $ujian->event_id,
                    'peserta_id' => $ujian->peserta_id,
                ])
            );

            return [
                'berubah' => $this->agregatBerubah($sebelum, $agregat),
                'hasil' => $hasil,
                'sebelum' => $sebelum,
            ];
        });
    }

    private function hitungUlang(UjianLiterasiDigital $ujian): array
    {
        $jawaban = JawabanLiterasiDigital::with(['soal', 'pilar'])
            ->where('ujian_id', $ujian->id)
            ->get();

        foreach ($jawaban as $baris) {
            $skor = $this->skorBenar($baris->jawaban, $baris->soal->kunci_jawaban);
            if ((int) $baris->skor !== $skor) {
                $baris->skor = $skor;
                $baris->save();
            }
        }

        return $this->agregatDariJawaban($jawaban);
    }

    private function agregatDariJawaban(Collection $jawaban): array
    {
        $skorPilar = array_fill_keys(self::KODE_PILAR, 0);

        foreach ($jawaban as $baris) {
            $kode = $baris->pilar?->kode;
            if (is_string($kode) && array_key_exists($kode, $skorPilar)) {
                $skorPilar[$kode] += (int) $baris->skor;
            }
        }

        $total = array_sum($skorPilar);
        $levelPilar = [];

        foreach (self::KODE_PILAR as $kode) {
            $levelPilar[$kode] = $this->levelPilar($skorPilar[$kode]);
        }

        $totalLevel = array_sum($levelPilar);
        $jpm = round(($totalLevel / self::JPM_PENYEBUT) * 100, 2);

        $agregat = [
            'jumlah_soal' => $jawaban->count(),
            'skor_total' => $total,
            'jpm' => $jpm,
            'kategori' => $this->kategoriJpm($jpm),
        ];

        foreach (self::KOLOM_SKOR as $kode => $kolom) {
            $agregat[$kolom] = $skorPilar[$kode];
            $agregat[self::KOLOM_LEVEL[$kode]] = $levelPilar[$kode];
        }

        return $agregat;
    }

    /**
     * @param  array<string, mixed>  $sebelum
     * @param  array<string, mixed>  $agregat
     */
    private function agregatBerubah(array $sebelum, array $agregat): bool
    {
        foreach ($agregat as $kolom => $nilai) {
            $lama = $sebelum[$kolom] ?? null;

            if (is_numeric($nilai) && is_numeric($lama)) {
                if (round((float) $lama, 2) !== round((float) $nilai, 2)) {
                    return true;
                }

                continue;
            }

            if ((string) $lama !== (string) $nilai) {
                return true;
            }
        }

        return false;
    }

    private function kategoriJpm(float $jpm): string
    {
        if ($jpm >= 90) {
            return 'Optimal';
        }

        if ($jpm >= 78) {
            return 'Cukup Optimal';
        }

        return 'Kurang Optimal';
    }

    private function levelPilar(int $skor): int
    {
        return match (true) {
            $skor <= 3 => 1,
            $skor <= 6 => 2,
            $skor <= 9 => 3,
            $skor <= 12 => 4,
            default => 5,
        };
    }

    private function skorBenar(?string $jawaban, ?string $kunci): int
    {
        if ($jawaban === null || $jawaban === '' || $kunci === null || $kunci === '') {
            return 0;
        }

        return strcasecmp($jawaban, $kunci) === 0 ? 1 : 0;
    }
}