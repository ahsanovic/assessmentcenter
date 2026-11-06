<?php

namespace App\Services\TesPotensi;

use App\Models\BerpikirKritis\SoalBerpikirKritis;
use App\Models\BerpikirKritis\UjianBerpikirKritis;
use App\Models\Event;
use App\Models\Interpersonal\SoalInterpersonal;
use App\Models\Interpersonal\UjianInterpersonal;
use App\Models\KecerdasanEmosi\SoalKecerdasanEmosi;
use App\Models\KecerdasanEmosi\UjianKecerdasanEmosi;
use App\Models\KesadaranDiri\SoalKesadaranDiri;
use App\Models\KesadaranDiri\UjianKesadaranDiri;
use App\Models\MotivasiKomitmen\SoalMotivasiKomitmen;
use App\Models\MotivasiKomitmen\UjianMotivasiKomitmen;
use App\Models\PengembanganDiri\SoalPengembanganDiri;
use App\Models\PengembanganDiri\UjianPengembanganDiri;
use App\Models\Peserta;
use App\Models\ProblemSolving\SoalProblemSolving;
use App\Models\ProblemSolving\UjianProblemSolving;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TesPotensiJawabanService
{
    public const METODE_TES_IDS = [1, 2];

    public const R_LAYAK = 0.30;

    public const R_LEMAH = 0.20;

    public const ALPHA_RELIABEL = 0.80;

    public const ALPHA_CUKUP = 0.70;

    public const N_UJI_STABIL = 30;

    /**
     * @return array<string, array<string, mixed>>
     */
    public function subtestDefinitions(): array
    {
        return [
            'interpersonal' => [
                'label' => 'Kemampuan Interpersonal',
                'sheet_analysis' => 'Analisa Interpersonal',
                'sheet_jawaban' => 'Jawaban Interpersonal',
                'ujian_model' => UjianInterpersonal::class,
                'soal_model' => SoalInterpersonal::class,
                'soal_with' => ['jenisIndikator'],
                'letters' => ['A', 'B', 'C'],
            ],
            'kesadaran-diri' => [
                'label' => 'Kesadaran Diri',
                'sheet_analysis' => 'Analisa Kesadaran Diri',
                'sheet_jawaban' => 'Jawaban Kesadaran Diri',
                'ujian_model' => UjianKesadaranDiri::class,
                'soal_model' => SoalKesadaranDiri::class,
                'soal_with' => ['jenisIndikator'],
                'letters' => ['A', 'B', 'C'],
            ],
            'berpikir-kritis' => [
                'label' => 'Berpikir Kritis dan Strategis',
                'sheet_analysis' => 'Analisa Berpikir Kritis',
                'sheet_jawaban' => 'Jawaban Berpikir Kritis',
                'ujian_model' => UjianBerpikirKritis::class,
                'soal_model' => SoalBerpikirKritis::class,
                'soal_with' => ['indikator', 'aspek'],
                'letters' => ['A', 'B', 'C', 'D', 'E'],
            ],
            'problem-solving' => [
                'label' => 'Problem Solving',
                'sheet_analysis' => 'Analisa Problem Solving',
                'sheet_jawaban' => 'Jawaban Problem Solving',
                'ujian_model' => UjianProblemSolving::class,
                'soal_model' => SoalProblemSolving::class,
                'soal_with' => ['indikator', 'aspek'],
                'letters' => ['A', 'B', 'C', 'D', 'E'],
            ],
            'kecerdasan-emosi' => [
                'label' => 'Kecerdasan Emosi',
                'sheet_analysis' => 'Analisa Kecerdasan Emosi',
                'sheet_jawaban' => 'Jawaban Kecerdasan Emosi',
                'ujian_model' => UjianKecerdasanEmosi::class,
                'soal_model' => SoalKecerdasanEmosi::class,
                'soal_with' => ['jenisIndikator'],
                'letters' => ['A', 'B', 'C'],
            ],
            'pengembangan-diri' => [
                'label' => 'Belajar Cepat dan Pengembangan Diri',
                'sheet_analysis' => 'Analisa Pengembangan Diri',
                'sheet_jawaban' => 'Jawaban Pengembangan Diri',
                'ujian_model' => UjianPengembanganDiri::class,
                'soal_model' => SoalPengembanganDiri::class,
                'soal_with' => ['jenisIndikator'],
                'letters' => ['A', 'B'],
            ],
            'motivasi-komitmen' => [
                'label' => 'Motivasi dan Komitmen',
                'sheet_analysis' => 'Analisa Motivasi Komitmen',
                'sheet_jawaban' => 'Jawaban Motivasi Komitmen',
                'ujian_model' => UjianMotivasiKomitmen::class,
                'soal_model' => SoalMotivasiKomitmen::class,
                'soal_with' => ['jenisIndikator'],
                'letters' => ['A', 'B'],
            ],
        ];
    }

    public function subtestKeys(): array
    {
        return array_keys($this->subtestDefinitions());
    }

    /**
     * @return array<string, mixed>
     */
    public function definition(string $key): array
    {
        $defs = $this->subtestDefinitions();
        if (! isset($defs[$key])) {
            abort(404);
        }

        return $defs[$key];
    }

    public function eventHasPotensiAnswers(Event $event): bool
    {
        if (! in_array((int) $event->metode_tes_id, self::METODE_TES_IDS, true)) {
            return false;
        }

        foreach ($this->subtestDefinitions() as $def) {
            /** @var class-string<Model> $model */
            $model = $def['ujian_model'];
            if ($model::query()->where('event_id', $event->id)->exists()) {
                return true;
            }
        }

        return false;
    }

    public function countParticipantsWithData(int $eventId): int
    {
        $ids = collect();

        foreach ($this->subtestDefinitions() as $def) {
            /** @var class-string<Model> $model */
            $model = $def['ujian_model'];
            $ids = $ids->merge(
                $model::query()->where('event_id', $eventId)->pluck('peserta_id')
            );
        }

        return $ids->filter()->unique()->count();
    }

    /**
     * @return Collection<int, Model>
     */
    public function loadSoalMap(string $subtestKey): Collection
    {
        $def = $this->definition($subtestKey);
        /** @var class-string<Model> $soalModel */
        $soalModel = $def['soal_model'];
        $with = $def['soal_with'] ?? [];

        return $soalModel::query()
            ->with($with)
            ->orderBy('id')
            ->get()
            ->keyBy('id');
    }

    /**
     * @return array<int, int>
     */
    public function canonicalSoalOrder(Collection $soalMap): array
    {
        return $soalMap->keys()->map(fn ($id) => (int) $id)->values()->all();
    }

    /**
     * @return array<int, string>
     */
    public function answerPairsFromUjian(object $ujian, Collection $soalMap): array
    {
        $soalIds = array_map('intval', array_filter(explode(',', (string) ($ujian->soal_id ?? ''))));
        $answers = explode(',', (string) ($ujian->jawaban ?? ''));
        $pairs = [];

        foreach ($soalIds as $index => $soalId) {
            if (! $soalMap->has($soalId)) {
                continue;
            }

            $raw = $answers[$index] ?? '0';
            $pairs[$soalId] = ($raw === '' || $raw === null) ? '0' : (string) $raw;
        }

        return $pairs;
    }

    public function normalizeLetter(mixed $value): ?string
    {
        $value = strtoupper(trim((string) $value));

        if ($value === '' || $value === '0') {
            return null;
        }

        return $value;
    }

    /**
     * @param  array<int, string>  $letters
     * @return array<string, int>
     */
    public function optionScores(Model $soal, array $letters): array
    {
        $scores = [];

        foreach ($letters as $letter) {
            $suffix = strtolower($letter);
            $opsiField = 'opsi_'.$suffix;
            $poinField = 'poin_opsi_'.$suffix;

            if (filled($soal->{$opsiField} ?? null)) {
                $scores[$letter] = (int) ($soal->{$poinField} ?? 0);
            }
        }

        return $scores;
    }

    public function formatOptionScoresLine(Model $soal, array $letters): string
    {
        $scores = $this->optionScores($soal, $letters);
        $parts = [];

        foreach ($scores as $letter => $poin) {
            $parts[] = "{$letter}: {$poin}";
        }

        return $parts === [] ? '-' : implode(' | ', $parts);
    }

    public function formatJawaban(Model $soal, ?string $letter, array $letters): string
    {
        $letter = $this->normalizeLetter($letter);
        if ($letter === null) {
            return '(belum dijawab)';
        }

        if (! in_array($letter, $letters, true)) {
            return $letter;
        }

        $suffix = strtolower($letter);
        $text = trim((string) ($soal->{'opsi_'.$suffix} ?? ''));

        return $text !== '' ? "{$letter} — {$text}" : $letter;
    }

    public function scoreForLetter(Model $soal, ?string $letter, array $letters): ?int
    {
        $letter = $this->normalizeLetter($letter);
        if ($letter === null) {
            return null;
        }

        $scores = $this->optionScores($soal, $letters);

        return $scores[$letter] ?? null;
    }

    public function maxScore(Model $soal, array $letters): ?int
    {
        $scores = $this->optionScores($soal, $letters);

        return $scores === [] ? null : max($scores);
    }

    public function indikatorLabel(string $subtestKey, Model $soal): string
    {
        return match ($subtestKey) {
            'interpersonal', 'kesadaran-diri', 'kecerdasan-emosi', 'pengembangan-diri', 'motivasi-komitmen' => filled($soal->jenisIndikator?->indikator_nama)
                ? (string) $soal->jenisIndikator->indikator_nama
                : '-',
            'berpikir-kritis', 'problem-solving' => $this->indikatorBerpikirProblem($soal),
            default => '-',
        };
    }

    private function indikatorBerpikirProblem(Model $soal): string
    {
        $aspek = filled($soal->aspek?->aspek ?? null) ? (string) $soal->aspek->aspek : null;
        $indikator = filled($soal->indikator?->indikator_nama ?? null)
            ? (string) $soal->indikator->indikator_nama
            : null;

        if ($aspek && $indikator) {
            return $aspek.' — '.$indikator;
        }

        return $indikator ?? $aspek ?? '-';
    }

    /**
     * @return Collection<int, object>
     */
    public function ujianForEvent(int $eventId, string $subtestKey, ?string $search = null): Collection
    {
        $def = $this->definition($subtestKey);
        /** @var class-string<Model> $model */
        $model = $def['ujian_model'];

        return $model::query()
            ->with('peserta')
            ->where('event_id', $eventId)
            ->whereHas('peserta')
            ->when(filled($search), function (Builder $query) use ($search) {
                $term = '%'.trim((string) $search).'%';
                $query->whereHas('peserta', function (Builder $peserta) use ($term) {
                    $peserta->where(function (Builder $inner) use ($term) {
                        $inner->where('nama', 'like', $term)
                            ->orWhere('nip', 'like', $term);
                    });
                });
            })
            ->orderBy('id')
            ->get();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function buildParticipantProgressRows(Collection $ujianList, string $subtestKey, Collection $soalMap): array
    {
        $totalSoal = $soalMap->count();
        $rows = [];

        foreach ($ujianList as $ujian) {
            $pairs = $this->answerPairsFromUjian($ujian, $soalMap);
            $terjawab = collect($pairs)->filter(fn ($v) => $this->normalizeLetter($v) !== null)->count();

            $rows[$ujian->id] = [
                'ujian_id' => $ujian->id,
                'peserta' => $ujian->peserta,
                'is_finished' => in_array($ujian->is_finished, ['true', true, 1, '1'], true),
                'terjawab' => $terjawab,
                'total_soal' => $totalSoal,
            ];
        }

        return $rows;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function buildDetailRowsForUjian(object $ujian, string $subtestKey, Collection $soalMap, ?array $canonicalOrder = null): array
    {
        $def = $this->definition($subtestKey);
        $letters = $def['letters'];
        $pairs = $this->answerPairsFromUjian($ujian, $soalMap);
        $order = $canonicalOrder ?? $this->canonicalSoalOrder($soalMap);
        $rows = [];

        foreach ($order as $i => $soalId) {
            $soal = $soalMap->get($soalId);
            if (! $soal) {
                continue;
            }

            $raw = $pairs[$soalId] ?? '0';
            $score = $this->scoreForLetter($soal, $raw, $letters);
            $max = $this->maxScore($soal, $letters);

            $rows[] = [
                'nomor' => $i + 1,
                'soal_id' => $soalId,
                'indikator' => $this->indikatorLabel($subtestKey, $soal),
                'pertanyaan' => (string) $soal->soal,
                'jawaban_peserta' => $this->formatJawaban($soal, $raw, $letters),
                'jawaban_letter' => $this->normalizeLetter($raw),
                'poin' => $score,
                'poin_max' => $max,
                'skor_opsi' => $this->formatOptionScoresLine($soal, $letters),
                'status' => $this->normalizeLetter($raw) === null ? 'unanswered' : 'answered',
            ];
        }

        return $rows;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function buildItemAnalysisRows(int $eventId, string $subtestKey): array
    {
        return $this->analyzeSubtest($eventId, $subtestKey)['items'];
    }

    /**
     * @return array{items: array<int, array<string, mixed>>, conclusion: array<string, mixed>}
     */
    public function analyzeSubtest(int $eventId, string $subtestKey): array
    {
        $def = $this->definition($subtestKey);
        $letters = $def['letters'];
        $soalMap = $this->loadSoalMap($subtestKey);
        $order = $this->canonicalSoalOrder($soalMap);
        $ujianList = $this->ujianForEvent($eventId, $subtestKey);
        $nPeserta = $ujianList->count();

        $answersByUjian = [];
        foreach ($ujianList as $ujian) {
            $answersByUjian[$ujian->id] = $this->answerPairsFromUjian($ujian, $soalMap);
        }

        $completeMatrix = [];
        foreach ($ujianList as $ujian) {
            $pairs = $answersByUjian[$ujian->id];
            $row = [];
            foreach ($order as $soalId) {
                $soal = $soalMap->get($soalId);
                $score = $soal ? $this->scoreForLetter($soal, $pairs[$soalId] ?? '0', $letters) : null;
                if ($score === null) {
                    $row = null;
                    break;
                }
                $row[] = (float) $score;
            }

            if ($row !== null && $row !== []) {
                $completeMatrix[] = $row;
            }
        }

        $nUji = count($completeMatrix);
        $rTable = $this->criticalR($nUji);
        $alpha = $this->cronbachAlpha($completeMatrix);
        $xsByItem = [];
        $ysByItem = [];

        foreach ($completeMatrix as $row) {
            $total = array_sum($row);
            foreach ($order as $index => $soalId) {
                $itemScore = $row[$index];
                $xsByItem[$soalId][] = $itemScore;
                $ysByItem[$soalId][] = $total - $itemScore;
            }
        }

        $rows = [];
        foreach ($order as $i => $soalId) {
            $soal = $soalMap->get($soalId);
            if (! $soal) {
                continue;
            }

            $counts = array_fill_keys($letters, 0);
            $scores = [];
            $answered = 0;

            foreach ($ujianList as $ujian) {
                $letter = $this->normalizeLetter($answersByUjian[$ujian->id][$soalId] ?? '0');
                if ($letter === null) {
                    continue;
                }

                $answered++;
                if (isset($counts[$letter])) {
                    $counts[$letter]++;
                }

                $score = $this->scoreForLetter($soal, $letter, $letters);
                if ($score !== null) {
                    $scores[] = $score;
                }
            }

            $percentages = [];
            foreach ($letters as $letter) {
                $percentages[$letter] = $answered > 0
                    ? round(($counts[$letter] / $answered) * 100, 1)
                    : 0.0;
            }

            $r = $this->pearson($xsByItem[$soalId] ?? [], $ysByItem[$soalId] ?? []);
            $decision = $this->decideItem($r, $rTable);

            $rows[] = [
                'nomor' => $i + 1,
                'soal_id' => $soalId,
                'indikator' => $this->indikatorLabel($subtestKey, $soal),
                'pertanyaan' => (string) $soal->soal,
                'opsi_skor' => $this->formatOptionScoresLine($soal, $letters),
                'n_peserta' => $nPeserta,
                'n_terjawab' => $answered,
                'counts' => $counts,
                'percentages' => $percentages,
                'rata_poin' => $scores !== [] ? round(array_sum($scores) / count($scores), 2) : null,
                'poin_max' => $this->maxScore($soal, $letters),
                'r_item_total' => $r,
                'r_tabel' => $rTable,
                'keputusan_key' => $decision['key'],
                'keputusan_label' => $decision['label'],
            ];
        }

        return [
            'items' => $rows,
            'conclusion' => $this->buildConclusion($def['label'], $rows, $completeMatrix, $alpha, $rTable, $nUji),
        ];
    }

    /**
     * @return array{key: string, label: string}
     */
    public function decideItem(?float $r, ?float $rTable): array
    {
        if ($r === null || $rTable === null) {
            return ['key' => 'insufficient', 'label' => 'Belum cukup data'];
        }

        if ($r >= self::R_LAYAK && $r > $rTable) {
            return ['key' => 'layak', 'label' => 'Layak'];
        }

        if ($r < self::R_LEMAH) {
            return ['key' => 'lemah', 'label' => 'Lemah'];
        }

        return ['key' => 'ditinjau', 'label' => 'Ditinjau'];
    }

    public function criticalR(int $n): ?float
    {
        $df = $n - 2;
        if ($df < 1) {
            return null;
        }

        $t = $this->tCritical($df);

        return $t / sqrt(($t * $t) + $df);
    }

    /**
     * @param  array<int, array<int, float>>  $matrix
     */
    public function cronbachAlpha(array $matrix): ?float
    {
        $n = count($matrix);
        $k = count($matrix[0] ?? []);
        if ($n < 3 || $k < 2) {
            return null;
        }

        $itemVarSum = 0.0;
        for ($j = 0; $j < $k; $j++) {
            $column = [];
            foreach ($matrix as $row) {
                $column[] = (float) $row[$j];
            }
            $itemVarSum += $this->variance($column);
        }

        $totals = array_map(fn (array $row) => array_sum($row), $matrix);
        $totalVar = $this->variance($totals);
        if ($totalVar <= 0.0) {
            return null;
        }

        return ($k / ($k - 1)) * (1 - ($itemVarSum / $totalVar));
    }

    /**
     * @return array<int, array<string, string>>
     */
    public function exportParticipantRows(int $eventId, string $subtestKey): array
    {
        $def = $this->definition($subtestKey);
        $soalMap = $this->loadSoalMap($subtestKey);
        $order = $this->canonicalSoalOrder($soalMap);
        $ujianList = $this->ujianForEvent($eventId, $subtestKey);
        $rows = [];

        foreach ($ujianList as $ujian) {
            $peserta = $ujian->peserta;
            if (! $peserta) {
                continue;
            }

            $status = in_array($ujian->is_finished, ['true', true, 1, '1'], true) ? 'Selesai' : 'Berlangsung';
            $detail = $this->buildDetailRowsForUjian($ujian, $subtestKey, $soalMap, $order);

            foreach ($detail as $answer) {
                $rows[] = [
                    'nip' => $this->text($peserta->nip),
                    'nama' => $this->text($peserta->nama),
                    'jabatan' => $this->text($peserta->jabatan),
                    'unit_kerja' => $this->text($peserta->unit_kerja),
                    'status_subtes' => $status,
                    'nomor' => (string) $answer['nomor'],
                    'indikator' => $answer['indikator'],
                    'soal' => 'Soal '.$answer['nomor'].'. '.$answer['pertanyaan'],
                    'jawaban' => $answer['jawaban_peserta'],
                    'poin' => $answer['poin'] !== null ? (string) $answer['poin'] : '-',
                    'poin_max' => $answer['poin_max'] !== null ? (string) $answer['poin_max'] : '-',
                    'skor_opsi' => $answer['skor_opsi'],
                ];
            }
        }

        return $rows;
    }

    /**
     * @return array<int, array<string, string>>
     */
    public function exportItemAnalysisRows(int $eventId, string $subtestKey): array
    {
        return $this->mapAnalysisExportRows($this->buildItemAnalysisRows($eventId, $subtestKey), $subtestKey);
    }

    /**
     * @param  array<int, array<string, mixed>>  $analysis
     * @return array<int, array<string, string>>
     */
    private function mapAnalysisExportRows(array $analysis, string $subtestKey): array
    {
        $letters = $this->definition($subtestKey)['letters'];
        $rows = [];

        foreach ($analysis as $item) {
            $row = [
                'nomor' => (string) $item['nomor'],
                'indikator' => $item['indikator'],
                'soal' => 'Soal '.$item['nomor'].'. '.$item['pertanyaan'],
                'opsi_skor' => $item['opsi_skor'],
                'n_peserta' => (string) $item['n_peserta'],
                'n_terjawab' => (string) $item['n_terjawab'],
                'rata_poin' => $item['rata_poin'] !== null ? (string) $item['rata_poin'] : '-',
                'poin_max' => $item['poin_max'] !== null ? (string) $item['poin_max'] : '-',
                'r_item_total' => $item['r_item_total'] !== null ? (string) round($item['r_item_total'], 3) : '-',
                'r_tabel' => $item['r_tabel'] !== null ? (string) round((float) $item['r_tabel'], 3) : '-',
                'keputusan' => (string) ($item['keputusan_label'] ?? '-'),
            ];

            foreach ($letters as $letter) {
                $row['n_'.$letter] = (string) ($item['counts'][$letter] ?? 0);
                $row['pct_'.$letter] = (string) ($item['percentages'][$letter] ?? 0);
            }

            $rows[] = $row;
        }

        return $rows;
    }

    public function makeSpreadsheetForSubtest(string $eventName, int $eventId, string $subtestKey): Spreadsheet
    {
        $spreadsheet = new Spreadsheet;
        $spreadsheet->removeSheetByIndex(0);

        $def = $this->definition($subtestKey);
        $bundle = $this->analyzeSubtest($eventId, $subtestKey);
        $analysisRows = $this->mapAnalysisExportRows($bundle['items'], $subtestKey);
        $participantRows = $this->exportParticipantRows($eventId, $subtestKey);

        $this->fillAnalysisSheet(
            $spreadsheet->createSheet(),
            $def['sheet_analysis'],
            $eventName,
            $subtestKey,
            $analysisRows,
            (string) ($bundle['conclusion']['kalimat'] ?? '')
        );

        $this->fillParticipantSheet(
            $spreadsheet->createSheet(),
            $def['sheet_jawaban'],
            $eventName,
            $participantRows
        );

        $spreadsheet->setActiveSheetIndex(0);

        return $spreadsheet;
    }

    public function makeSpreadsheetForEvent(string $eventName, int $eventId): Spreadsheet
    {
        $spreadsheet = new Spreadsheet;
        $spreadsheet->removeSheetByIndex(0);

        foreach ($this->subtestKeys() as $key) {
            $def = $this->definition($key);
            $bundle = $this->analyzeSubtest($eventId, $key);
            $analysisRows = $this->mapAnalysisExportRows($bundle['items'], $key);
            $participantRows = $this->exportParticipantRows($eventId, $key);

            if ($analysisRows === [] && $participantRows === []) {
                continue;
            }

            $this->fillAnalysisSheet(
                $spreadsheet->createSheet(),
                $def['sheet_analysis'],
                $eventName,
                $key,
                $analysisRows,
                (string) ($bundle['conclusion']['kalimat'] ?? '')
            );

            $this->fillParticipantSheet(
                $spreadsheet->createSheet(),
                $def['sheet_jawaban'],
                $eventName,
                $participantRows
            );
        }

        if ($spreadsheet->getSheetCount() === 0) {
            $sheet = $spreadsheet->createSheet();
            $sheet->setTitle('Kosong');
            $sheet->setCellValue('A1', 'Tidak ada data');
        }

        $spreadsheet->setActiveSheetIndex(0);

        return $spreadsheet;
    }

    public function filename(string $eventName, ?string $subtestKey = null): string
    {
        $slug = Str::slug($eventName);
        if ($slug === '') {
            $slug = 'event';
        }

        if ($subtestKey) {
            return 'jawaban-tes-potensi-'.Str::slug($subtestKey).'-'.$slug.'-'.date('Y-m-d').'.xlsx';
        }

        return 'jawaban-tes-potensi-'.$slug.'-'.date('Y-m-d').'.xlsx';
    }

    /**
     * @param  array<int, array<string, string>>  $rows
     */
    private function fillAnalysisSheet(
        Worksheet $sheet,
        string $title,
        string $eventName,
        string $subtestKey,
        array $rows,
        string $subtitle = ''
    ): void {
        $def = $this->definition($subtestKey);
        $letters = $def['letters'];
        $headers = ['Nomor', 'Indikator', 'Soal', 'Skor per Opsi', 'N Peserta', 'N Terjawab'];

        foreach ($letters as $letter) {
            $headers[] = 'N '.$letter;
            $headers[] = '% '.$letter;
        }

        $headers = array_merge($headers, ['Rata Poin', 'Poin Max', 'r Item-Total', 'r Tabel', 'Keputusan']);
        $keys = ['nomor', 'indikator', 'soal', 'opsi_skor', 'n_peserta', 'n_terjawab'];

        foreach ($letters as $letter) {
            $keys[] = 'n_'.$letter;
            $keys[] = 'pct_'.$letter;
        }

        $keys = array_merge($keys, ['rata_poin', 'poin_max', 'r_item_total', 'r_tabel', 'keputusan']);

        $sheet->setTitle($this->truncateSheetTitle($title));
        $this->writeSheetHeader($sheet, $eventName, $headers, $subtitle);
        $this->writeDataRows($sheet, $keys, $rows, 4);
        $this->applyColumnWidths($sheet, count($headers));
        $sheet->freezePane('A4');
    }

    /**
     * @param  array<int, array<string, string>>  $rows
     */
    private function fillParticipantSheet(Worksheet $sheet, string $title, string $eventName, array $rows): void
    {
        $headers = [
            'NIP', 'Nama Peserta', 'Jabatan', 'Unit Kerja', 'Status Subtes',
            'Nomor', 'Indikator', 'Soal', 'Jawaban Peserta', 'Poin', 'Poin Max', 'Skor per Opsi',
        ];
        $keys = [
            'nip', 'nama', 'jabatan', 'unit_kerja', 'status_subtes',
            'nomor', 'indikator', 'soal', 'jawaban', 'poin', 'poin_max', 'skor_opsi',
        ];

        $sheet->setTitle($this->truncateSheetTitle($title));
        $this->writeSheetHeader($sheet, $eventName, $headers);
        $this->writeDataRows($sheet, $keys, $rows, 4);
        $this->applyColumnWidths($sheet, count($headers));
        $sheet->freezePane('A4');
    }

    /**
     * @param  array<int, string>  $headers
     */
    private function writeSheetHeader(Worksheet $sheet, string $eventName, array $headers, string $subtitle = ''): void
    {
        $lastCol = $this->columnLetter(count($headers) - 1);
        $sheet->mergeCells("A1:{$lastCol}1");
        $sheet->setCellValueExplicit('A1', $eventName !== '' ? $eventName : 'Event', DataType::TYPE_STRING);
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        if ($subtitle !== '') {
            $sheet->mergeCells("A2:{$lastCol}2");
            $sheet->setCellValueExplicit('A2', $subtitle, DataType::TYPE_STRING);
            $sheet->getStyle('A2')->getAlignment()->setWrapText(true)->setVertical(Alignment::VERTICAL_CENTER);
            $sheet->getRowDimension(2)->setRowHeight(36);
        }

        foreach ($headers as $colIndex => $header) {
            $sheet->setCellValueExplicit($this->columnLetter($colIndex).'3', $header, DataType::TYPE_STRING);
        }

        $sheet->getStyle("A3:{$lastCol}3")->getFont()->setBold(true);
        $sheet->getStyle("A3:{$lastCol}3")->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFE2E8F0');
    }

    /**
     * @param  array<int, string>  $keys
     * @param  array<int, array<string, string>>  $rows
     */
    private function writeDataRows(Worksheet $sheet, array $keys, array $rows, int $startRow): void
    {
        $rowNumber = $startRow;

        foreach ($rows as $row) {
            foreach ($keys as $colIndex => $key) {
                $sheet->setCellValueExplicit(
                    $this->columnLetter($colIndex).$rowNumber,
                    (string) ($row[$key] ?? '-'),
                    DataType::TYPE_STRING
                );
            }

            $sheet->getRowDimension($rowNumber)->setRowHeight(-1);
            $rowNumber++;
        }

        if ($rows !== []) {
            $lastCol = $this->columnLetter(count($keys) - 1);
            $lastRow = $rowNumber - 1;
            $sheet->getStyle("A{$startRow}:{$lastCol}{$lastRow}")->getAlignment()
                ->setWrapText(true)
                ->setVertical(Alignment::VERTICAL_TOP);
        }
    }

    private function applyColumnWidths(Worksheet $sheet, int $columnCount): void
    {
        for ($i = 0; $i < $columnCount; $i++) {
            $letter = $this->columnLetter($i);
            $width = match ($i) {
                0 => 8,
                1 => 28,
                2 => 55,
                default => 16,
            };
            $sheet->getColumnDimension($letter)->setWidth($width);
        }
    }

    private function truncateSheetTitle(string $title): string
    {
        return Str::limit($title, 31, '');
    }

    /**
     * @param  array<int, array<string, mixed>>  $rows
     * @param  array<int, array<int, float>>  $matrix
     * @return array<string, mixed>
     */
    private function buildConclusion(string $label, array $rows, array $matrix, ?float $alpha, ?float $rTable, int $nUji): array
    {
        $counts = ['layak' => 0, 'ditinjau' => 0, 'lemah' => 0, 'insufficient' => 0];
        foreach ($rows as $row) {
            $key = $row['keputusan_key'] ?? 'insufficient';
            if (! isset($counts[$key])) {
                $key = 'insufficient';
            }
            $counts[$key]++;
        }

        $reliability = $this->reliabilityLabel($alpha);
        $dropItems = $this->itemsThatLowerAlpha($matrix, $rows, $alpha);
        $k = count($rows);
        $alphaText = $alpha !== null ? $this->formatCoef($alpha, 2) : '-';
        $rText = $rTable !== null ? $this->formatCoef($rTable, 3) : '-';

        if ($nUji < 3) {
            $kalimat = $label.': belum cukup peserta yang menjawab seluruh butir (N uji = '.$nUji.'). Reliabilitas dan keputusan butir belum dihitung.';
        } else {
            $kalimat = $label.': reliabilitas '.$reliability['sentence'].' (α = '.$alphaText.'; N uji = '.$nUji.'; r tabel = '.$rText.'). Dari '.$k.' butir, '.$counts['layak'].' layak, '.$counts['ditinjau'].' ditinjau, dan '.$counts['lemah'].' lemah.';
            if ($counts['insufficient'] > 0) {
                $kalimat .= ' '.$counts['insufficient'].' butir belum cukup variasi jawaban untuk dihitung.';
            }
            if ($dropItems !== []) {
                $kalimat .= ' Butir '.$this->joinNomor($dropItems).' paling menurunkan reliabilitas.';
            }
        }

        return [
            'n_uji' => $nUji,
            'n_butir' => $k,
            'r_tabel' => $rTable,
            'alpha' => $alpha,
            'reliabilitas_key' => $reliability['key'],
            'reliabilitas_label' => $reliability['label'],
            'indikatif' => $nUji < self::N_UJI_STABIL,
            'counts' => $counts,
            'butir_menurunkan' => $dropItems,
            'kalimat' => $kalimat,
        ];
    }

    /**
     * @return array{key: string, label: string, sentence: string}
     */
    private function reliabilityLabel(?float $alpha): array
    {
        if ($alpha === null) {
            return ['key' => 'unknown', 'label' => 'Belum cukup data', 'sentence' => 'belum dapat dihitung'];
        }

        if ($alpha >= self::ALPHA_RELIABEL) {
            return ['key' => 'reliabel', 'label' => 'Reliabel', 'sentence' => 'reliabel'];
        }

        if ($alpha >= self::ALPHA_CUKUP) {
            return ['key' => 'cukup', 'label' => 'Cukup', 'sentence' => 'cukup'];
        }

        return ['key' => 'rendah', 'label' => 'Belum memadai', 'sentence' => 'belum memadai'];
    }

    /**
     * @param  array<int, array<int, float>>  $matrix
     * @param  array<int, array<string, mixed>>  $rows
     * @return array<int, int>
     */
    private function itemsThatLowerAlpha(array $matrix, array $rows, ?float $alpha): array
    {
        if ($alpha === null || count($matrix) < 3 || count($rows) < 3) {
            return [];
        }

        $k = count($matrix[0] ?? []);
        $drops = [];

        for ($j = 0; $j < $k; $j++) {
            $reduced = [];
            foreach ($matrix as $row) {
                $copy = $row;
                array_splice($copy, $j, 1);
                $reduced[] = $copy;
            }

            $without = $this->cronbachAlpha($reduced);
            if ($without !== null && ($without - $alpha) >= 0.01) {
                $drops[] = ['nomor' => (int) ($rows[$j]['nomor'] ?? ($j + 1)), 'delta' => $without - $alpha];
            }
        }

        usort($drops, fn (array $left, array $right) => $right['delta'] <=> $left['delta']);

        return array_map(fn (array $item) => $item['nomor'], array_slice($drops, 0, 3));
    }

    /**
     * @param  array<int, int>  $nomor
     */
    private function joinNomor(array $nomor): string
    {
        $nomor = array_values($nomor);
        $count = count($nomor);
        if ($count === 0) {
            return '';
        }
        if ($count === 1) {
            return (string) $nomor[0];
        }
        if ($count === 2) {
            return $nomor[0].' dan '.$nomor[1];
        }

        $last = array_pop($nomor);

        return implode(', ', $nomor).', dan '.$last;
    }

    private function formatCoef(float $value, int $decimals): string
    {
        return number_format($value, $decimals, '.', '');
    }

    /**
     * @param  array<int, float>  $values
     */
    private function variance(array $values): float
    {
        $n = count($values);
        if ($n < 2) {
            return 0.0;
        }

        $mean = array_sum($values) / $n;
        $sum = 0.0;
        foreach ($values as $value) {
            $delta = $value - $mean;
            $sum += $delta * $delta;
        }

        return $sum / ($n - 1);
    }

    private function tCritical(int $df): float
    {
        $target = 0.975;
        $lo = 0.0;
        $hi = 1.0;

        while ($this->studentTCdf($hi, $df) < $target && $hi < 1_000_000) {
            $hi *= 2;
        }

        for ($i = 0; $i < 60; $i++) {
            $mid = ($lo + $hi) / 2;
            if ($this->studentTCdf($mid, $df) < $target) {
                $lo = $mid;
            } else {
                $hi = $mid;
            }
        }

        return ($lo + $hi) / 2;
    }

    private function studentTCdf(float $t, int $df): float
    {
        if ($t === 0.0) {
            return 0.5;
        }

        $x = $df / ($df + ($t * $t));
        $tail = 0.5 * $this->regularizedIncompleteBeta($x, $df / 2, 0.5);

        return $t > 0 ? 1 - $tail : $tail;
    }

    private function regularizedIncompleteBeta(float $x, float $a, float $b): float
    {
        if ($x <= 0.0) {
            return 0.0;
        }
        if ($x >= 1.0) {
            return 1.0;
        }

        $logBeta = $this->logGamma($a) + $this->logGamma($b) - $this->logGamma($a + $b);
        $front = exp(($a * log($x)) + ($b * log(1 - $x)) - $logBeta);

        if ($x < ($a + 1) / ($a + $b + 2)) {
            return $front * $this->betaContinuedFraction($a, $b, $x) / $a;
        }

        return 1 - ($front * $this->betaContinuedFraction($b, $a, 1 - $x) / $b);
    }

    private function betaContinuedFraction(float $a, float $b, float $x): float
    {
        $maxIterations = 200;
        $epsilon = 1e-12;
        $min = 1e-30;
        $qab = $a + $b;
        $qap = $a + 1;
        $qam = $a - 1;
        $c = 1.0;
        $d = 1.0 - ($qab * $x / $qap);
        if (abs($d) < $min) {
            $d = $min;
        }
        $d = 1.0 / $d;
        $h = $d;

        for ($m = 1; $m <= $maxIterations; $m++) {
            $m2 = 2 * $m;
            $aa = $m * ($b - $m) * $x / (($qam + $m2) * ($a + $m2));
            $d = 1.0 + ($aa * $d);
            if (abs($d) < $min) {
                $d = $min;
            }
            $c = 1.0 + ($aa / $c);
            if (abs($c) < $min) {
                $c = $min;
            }
            $d = 1.0 / $d;
            $h *= $d * $c;

            $aa = -($a + $m) * ($qab + $m) * $x / (($a + $m2) * ($qap + $m2));
            $d = 1.0 + ($aa * $d);
            if (abs($d) < $min) {
                $d = $min;
            }
            $c = 1.0 + ($aa / $c);
            if (abs($c) < $min) {
                $c = $min;
            }
            $d = 1.0 / $d;
            $delta = $d * $c;
            $h *= $delta;

            if (abs($delta - 1.0) < $epsilon) {
                break;
            }
        }

        return $h;
    }

    private function logGamma(float $x): float
    {
        if ($x < 0.5) {
            return log(M_PI / sin(M_PI * $x)) - $this->logGamma(1 - $x);
        }

        $p = [
            0.99999999999980993,
            676.5203681218851,
            -1259.1392167224028,
            771.32342877765313,
            -176.61502916214059,
            12.507343278686905,
            -0.13857109526572012,
            9.9843695780789e-6,
            1.5056327351493116e-7,
        ];

        $x -= 1;
        $a = $p[0];
        $t = $x + 7.5;
        for ($i = 1; $i < 9; $i++) {
            $a += $p[$i] / ($x + $i);
        }

        return (0.5 * log(2 * M_PI)) + (($x + 0.5) * log($t)) - $t + log($a);
    }

    /**
     * @param  array<int, float>  $xs
     * @param  array<int, float>  $ys
     */
    private function pearson(array $xs, array $ys): ?float
    {
        $n = min(count($xs), count($ys));
        if ($n < 3) {
            return null;
        }

        $xs = array_slice($xs, 0, $n);
        $ys = array_slice($ys, 0, $n);

        $meanX = array_sum($xs) / $n;
        $meanY = array_sum($ys) / $n;
        $num = 0.0;
        $denX = 0.0;
        $denY = 0.0;

        for ($i = 0; $i < $n; $i++) {
            $dx = $xs[$i] - $meanX;
            $dy = $ys[$i] - $meanY;
            $num += $dx * $dy;
            $denX += $dx * $dx;
            $denY += $dy * $dy;
        }

        if ($denX == 0.0 || $denY == 0.0) {
            return null;
        }

        return $num / sqrt($denX * $denY);
    }

    private function text(mixed $value): string
    {
        $value = trim((string) ($value ?? ''));

        return $value === '' ? '-' : $value;
    }

    private function columnLetter(int $index): string
    {
        $letter = '';
        $index++;

        while ($index > 0) {
            $index--;
            $letter = chr(65 + ($index % 26)).$letter;
            $index = intdiv($index, 26);
        }

        return $letter;
    }
}
