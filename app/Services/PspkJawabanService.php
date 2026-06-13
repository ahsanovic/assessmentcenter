<?php

namespace App\Services;

use App\Models\Pspk\SoalPspk;
use Illuminate\Support\Collection;

class PspkJawabanService
{
    public const METODE_TO_LEVEL = [
        5 => 1,
        6 => 2,
        7 => 3,
        8 => 4,
    ];

    public const PSPK_METODE_IDS = [5, 6, 7, 8];

    public function levelFromMetode(int $metodeTesId): ?int
    {
        return self::METODE_TO_LEVEL[$metodeTesId] ?? null;
    }

    public function metodeFromLevel(int $levelPspk): ?int
    {
        $flipped = array_flip(self::METODE_TO_LEVEL);

        return $flipped[$levelPspk] ?? null;
    }

    /**
     * @return array<string, int>
     */
    public function optionScores(SoalPspk $soal): array
    {
        $scores = [];

        foreach (['a', 'b', 'c', 'd', 'e'] as $suffix) {
            $opsiField = 'opsi_'.$suffix;
            $poinField = 'poin_opsi_'.$suffix;

            if (filled($soal->{$opsiField})) {
                $scores[strtoupper($suffix)] = (int) ($soal->{$poinField} ?? 0);
            }
        }

        return $scores;
    }

    public function hasOptionScores(SoalPspk $soal): bool
    {
        return $this->optionScores($soal) !== [];
    }

    public function formatOptionScores(SoalPspk $soal): ?string
    {
        $scores = $this->optionScores($soal);

        if ($scores === []) {
            return null;
        }

        $parts = [];
        foreach ($scores as $letter => $poin) {
            $parts[] = "{$letter}: {$poin}";
        }

        return implode(', ', $parts);
    }

    public function formatJawaban(SoalPspk $soal, ?string $letter): string
    {
        if (blank($letter) || $letter === '0') {
            return '(belum dijawab)';
        }

        $letter = strtoupper($letter);
        $opsiField = 'opsi_'.strtolower($letter);
        $opsiText = $soal->{$opsiField} ?? '';

        return "{$letter} — {$opsiText}";
    }

    public function evaluateAnswer(SoalPspk $soal, ?string $jawabanPeserta, int $levelPspk): string
    {
        if (blank($jawabanPeserta) || $jawabanPeserta === '0') {
            return 'unanswered';
        }

        $jawabanPeserta = strtoupper($jawabanPeserta);

        if ($levelPspk === 2) {
            $kunci = strtoupper((string) ($soal->kunci_jawaban ?? ''));
            if ($kunci === '') {
                return 'neutral';
            }

            return $jawabanPeserta === $kunci ? 'correct' : 'wrong';
        }

        $scores = $this->optionScores($soal);
        if ($scores === []) {
            return 'neutral';
        }

        $selectedScore = $scores[$jawabanPeserta] ?? null;
        if ($selectedScore === null) {
            return 'neutral';
        }

        $maxScore = max($scores);

        return $selectedScore === $maxScore ? 'correct' : 'wrong';
    }

    public function selectedOptionScore(SoalPspk $soal, ?string $jawabanPeserta): ?int
    {
        if (blank($jawabanPeserta) || $jawabanPeserta === '0') {
            return null;
        }

        $scores = $this->optionScores($soal);

        return $scores[strtoupper($jawabanPeserta)] ?? null;
    }

    public function resolveJenisKunci(SoalPspk $soal, int $levelPspk): ?string
    {
        if ($levelPspk === 2) {
            return 'kunci_jawaban';
        }

        if (in_array($levelPspk, [1, 3, 4], true) && $this->hasOptionScores($soal)) {
            return 'skor_opsi';
        }

        return null;
    }

    public function kunciLetterForDisplay(SoalPspk $soal, int $levelPspk): ?string
    {
        if ($this->resolveJenisKunci($soal, $levelPspk) !== 'kunci_jawaban') {
            return null;
        }

        return filled($soal->kunci_jawaban) ? strtoupper($soal->kunci_jawaban) : null;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function buildRowsForUjian(
        string $soalIdCsv,
        string $jawabanCsv,
        int $levelPspk,
        ?Collection $soalMap = null
    ): array {
        $soalIds = explode(',', $soalIdCsv);
        $jawabans = explode(',', $jawabanCsv);

        if ($soalMap === null) {
            $soalMap = SoalPspk::with('aspek')
                ->whereIn('id', $soalIds)
                ->get()
                ->keyBy('id');
        }

        $rows = [];

        foreach ($soalIds as $i => $soalId) {
            $soal = $soalMap->get((int) $soalId);
            if (! $soal) {
                continue;
            }

            $jawaban = $jawabans[$i] ?? '0';
            $jenisKunci = $this->resolveJenisKunci($soal, $levelPspk);
            $status = $this->evaluateAnswer($soal, $jawaban, $levelPspk);
            $kunciLetter = $this->kunciLetterForDisplay($soal, $levelPspk);

            $rows[] = [
                'nomor' => $i + 1,
                'soal_id' => $soal->id,
                'pertanyaan' => $soal->soal,
                'jenis_soal' => (int) $soal->jenis_soal,
                'jenis_kunci' => $jenisKunci,
                'aspek' => $soal->aspek?->nama_aspek ?? '-',
                'jawaban_peserta' => $this->formatJawaban($soal, $jawaban),
                'jawaban_peserta_letter' => $jawaban === '0' ? null : strtoupper($jawaban),
                'kunci' => $kunciLetter ? $this->formatJawaban($soal, $kunciLetter) : null,
                'kunci_letter' => $kunciLetter,
                'skor_opsi' => $this->formatOptionScores($soal),
                'skor_jawaban' => $this->selectedOptionScore($soal, $jawaban),
                'status' => $status,
            ];
        }

        return $rows;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function buildRowsForAllUjian(Collection $ujianList, int $levelPspk): array
    {
        $allSoalIds = $ujianList
            ->flatMap(fn ($ujian) => explode(',', (string) $ujian->soal_id))
            ->filter()
            ->unique()
            ->values()
            ->all();

        $soalMap = SoalPspk::with('aspek')
            ->whereIn('id', $allSoalIds)
            ->get()
            ->keyBy('id');

        $result = [];

        foreach ($ujianList as $ujian) {
            $result[$ujian->id] = $this->buildRowsForUjian(
                (string) $ujian->soal_id,
                (string) $ujian->jawaban,
                $levelPspk,
                $soalMap
            );
        }

        return $result;
    }
}
