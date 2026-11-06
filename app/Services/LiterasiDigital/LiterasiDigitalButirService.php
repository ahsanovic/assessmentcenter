<?php

namespace App\Services\LiterasiDigital;

use App\Models\LiterasiDigital\SoalLiterasiDigital;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LiterasiDigitalButirService
{
    public const R_LAYAK = 0.30;

    public const R_LEMAH = 0.20;

    public const ALPHA_RELIABEL = 0.80;

    public const ALPHA_CUKUP = 0.70;

    public const N_UJI_STABIL = 30;

    public const LETTERS = ['A', 'B', 'C', 'D', 'E'];

    public const PILAR = [
        'budaya' => 'Budaya Digital',
        'cakap' => 'Cakap Digital',
        'keamanan' => 'Keamanan dan Keselamatan Digital',
        'etika' => 'Etika Digital',
    ];

    private const SHEET = [
        'budaya' => 'Analisa Budaya',
        'cakap' => 'Analisa Cakap',
        'keamanan' => 'Analisa Keamanan',
        'etika' => 'Analisa Etika',
    ];

    /** @var array<int, float|null> */
    private array $criticalRByN = [];

    /**
     * @param  array<string, array{label?: string, participants?: array<int, array{ujian_id?: int, items?: array<int, array<string, mixed>>}>}>  $pillars
     * @return array{pillars: array<string, array<string, mixed>>, stratified: array<string, mixed>}
     */
    public function analyze(array $pillars): array
    {
        $computed = [];

        foreach (self::PILAR as $kode => $defaultLabel) {
            $input = $pillars[$kode] ?? [];
            $label = filled($input['label'] ?? null) ? (string) $input['label'] : $defaultLabel;
            $computed[$kode] = $this->analyzePillar($kode, $label, $input['participants'] ?? []);
        }

        $stratified = $this->stratifiedAlpha($computed);

        foreach ($computed as $kode => $pillar) {
            unset($computed[$kode]['complete']);
        }

        return [
            'pillars' => $computed,
            'stratified' => $stratified,
        ];
    }

    /**
     * @return array{pillars: array<string, array<string, mixed>>, stratified: array<string, mixed>}
     */
    public function analyzeEvent(int $eventId): array
    {
        $ujianIds = DB::table('ujian_literasi_digital as ujian')
            ->join('peserta', 'peserta.id', '=', 'ujian.peserta_id')
            ->where('ujian.event_id', $eventId)
            ->pluck('ujian.id');

        if ($ujianIds->isEmpty()) {
            return $this->analyze([]);
        }

        $answers = DB::table('jawaban_literasi_digital as jawaban')
            ->join('ref_pilar_literasi_digital as pilar', 'pilar.id', '=', 'jawaban.pilar_id')
            ->whereIn('jawaban.ujian_id', $ujianIds->all())
            ->whereIn('pilar.kode', array_keys(self::PILAR))
            ->get([
                'jawaban.ujian_id',
                'jawaban.soal_id',
                'jawaban.jawaban',
                'pilar.kode',
                'pilar.nama',
            ]);

        $soalIds = $answers->pluck('soal_id')->unique()->filter()->values();
        $soalById = $soalIds->isEmpty()
            ? collect()
            : DB::table('soal_literasi_digital')
                ->whereIn('id', $soalIds->all())
                ->get(['id', 'soal', 'kunci_jawaban', 'opsi_a', 'opsi_b', 'opsi_c', 'opsi_d', 'opsi_e'])
                ->keyBy('id');

        $formatter = new LiterasiDigitalJawabanService;
        $soalCache = [];
        $grouped = [];
        $names = [];

        foreach ($answers as $baris) {
            $kode = (string) $baris->kode;
            if (! array_key_exists($kode, self::PILAR)) {
                continue;
            }

            $soalId = (int) $baris->soal_id;
            $soal = $soalById->get($soalId);
            if ($soal === null) {
                continue;
            }

            if (! isset($soalCache[$soalId])) {
                $model = new SoalLiterasiDigital;
                $model->forceFill([
                    'soal' => $soal->soal,
                    'kunci_jawaban' => $soal->kunci_jawaban,
                    'opsi_a' => $soal->opsi_a,
                    'opsi_b' => $soal->opsi_b,
                    'opsi_c' => $soal->opsi_c,
                    'opsi_d' => $soal->opsi_d,
                    'opsi_e' => $soal->opsi_e,
                ]);
                $kunci = $this->normalizeLetter($soal->kunci_jawaban);
                $soalCache[$soalId] = [
                    'soal' => (string) $soal->soal,
                    'kunci' => $kunci,
                    'kunci_teks' => $kunci ? $formatter->formatJawaban($model, $kunci) : null,
                ];
            }

            $names[$kode] = (string) $baris->nama;
            $cached = $soalCache[$soalId];
            $grouped[$kode][(int) $baris->ujian_id][] = [
                'soal_id' => $soalId,
                'soal' => $cached['soal'],
                'kunci' => $cached['kunci'],
                'kunci_teks' => $cached['kunci_teks'],
                'letter' => $this->normalizeLetter($baris->jawaban),
            ];
        }

        $input = [];
        foreach (self::PILAR as $kode => $label) {
            $participants = [];
            foreach ($grouped[$kode] ?? [] as $ujianId => $items) {
                $participants[] = [
                    'ujian_id' => $ujianId,
                    'items' => $items,
                ];
            }

            $input[$kode] = [
                'label' => filled($names[$kode] ?? null) ? $names[$kode] : $label,
                'participants' => $participants,
            ];
        }

        return $this->analyze($input);
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
        if (array_key_exists($n, $this->criticalRByN)) {
            return $this->criticalRByN[$n];
        }

        $df = $n - 2;
        if ($df < 1) {
            return $this->criticalRByN[$n] = null;
        }

        $t = $this->tCritical($df);

        return $this->criticalRByN[$n] = $t / sqrt(($t * $t) + $df);
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
                $column[] = (float) ($row[$j] ?? 0);
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
     * @param  array<int, array<string, mixed>>  $items
     * @return array<int, array<string, string>>
     */
    public function exportItemRows(array $items): array
    {
        $rows = [];

        foreach ($items as $item) {
            $row = [
                'nomor' => (string) $item['nomor'],
                'soal' => 'Soal '.$item['nomor'].'. '.$item['pertanyaan'],
                'kunci' => (string) ($item['kunci'] ?? '-'),
                'n_peserta' => (string) $item['n_peserta'],
                'n_terjawab' => (string) $item['n_terjawab'],
                'n_uji' => (string) $item['n_uji_butir'],
                'p' => (string) ($item['p_label'] ?? '-'),
                'kesukaran' => (string) ($item['kesukaran_label'] ?? '-'),
                'r_item_total' => $item['r_item_total'] !== null ? $this->formatCoef((float) $item['r_item_total'], 3) : '-',
                'r_tabel' => $item['r_tabel'] !== null ? $this->formatCoef((float) $item['r_tabel'], 3) : '-',
                'keputusan' => (string) ($item['keputusan_label'] ?? '-'),
            ];

            foreach (self::LETTERS as $letter) {
                $row['n_'.$letter] = (string) ($item['counts'][$letter] ?? 0);
                $row['pct_'.$letter] = (string) ($item['percentages'][$letter] ?? 0);
            }

            $rows[] = $row;
        }

        return $rows;
    }

    /**
     * @param  array{pillars: array<string, array<string, mixed>>, stratified: array<string, mixed>}  $bundle
     */
    public function addAnalysisSheets(Spreadsheet $spreadsheet, string $eventName, array $bundle): void
    {
        $index = 0;

        foreach (self::PILAR as $kode => $label) {
            $pillar = $bundle['pillars'][$kode] ?? null;
            if (! is_array($pillar)) {
                continue;
            }

            $sheet = $spreadsheet->createSheet($index);
            $index++;
            $subtitle = trim((string) ($pillar['conclusion']['kalimat'] ?? ''));
            $stratified = trim((string) ($bundle['stratified']['kalimat'] ?? ''));
            if ($stratified !== '') {
                $subtitle = trim($subtitle.' '.$stratified);
            }

            $this->fillAnalysisSheet(
                $sheet,
                self::SHEET[$kode] ?? $label,
                $eventName,
                $this->exportItemRows($pillar['items'] ?? []),
                $subtitle
            );
        }

        if ($spreadsheet->getSheetCount() > 0) {
            $spreadsheet->setActiveSheetIndex(0);
        }
    }

    /**
     * @param  array<int, array<string, mixed>>  $participants
     * @return array<string, mixed>
     */
    private function analyzePillar(string $kode, string $label, array $participants): array
    {
        $people = $this->collectPeople($participants);
        $stats = [];
        $meta = [];
        $complete = [];

        foreach ($people as $ujianId => $items) {
            $scores = [];
            $blocked = false;

            foreach ($items as $soalId => $item) {
                if (! isset($stats[$soalId])) {
                    $stats[$soalId] = [
                        'n' => 0,
                        'answered' => 0,
                        'correct' => 0,
                        'counts' => array_fill_keys(self::LETTERS, 0),
                    ];
                    $meta[$soalId] = $item;
                }

                $stats[$soalId]['n']++;
                if ($item['letter'] !== null) {
                    $stats[$soalId]['answered']++;
                    if (isset($stats[$soalId]['counts'][$item['letter']])) {
                        $stats[$soalId]['counts'][$item['letter']]++;
                    }
                    if ($item['scoreable'] && $item['correct']) {
                        $stats[$soalId]['correct']++;
                    }
                }

                if (! $item['scoreable']) {
                    continue;
                }

                if ($item['letter'] === null) {
                    $blocked = true;

                    continue;
                }

                $scores[$soalId] = $item['correct'] ? 1.0 : 0.0;
            }

            if (! $blocked && $scores !== []) {
                $complete[$ujianId] = $scores;
            }
        }

        $soalIds = array_map('intval', array_keys($stats));
        sort($soalIds, SORT_NUMERIC);

        $rows = [];
        $itemById = [];

        foreach ($soalIds as $index => $soalId) {
            $info = $meta[$soalId];
            $stat = $stats[$soalId];
            $xs = [];
            $ys = [];

            if ($info['scoreable']) {
                foreach ($complete as $scores) {
                    if (! array_key_exists($soalId, $scores)) {
                        continue;
                    }

                    $xs[] = (float) $scores[$soalId];
                    $ys[] = array_sum($scores) - (float) $scores[$soalId];
                }
            }

            $nUjiButir = count($xs);
            $r = $this->pearson($xs, $ys);
            $rTable = $this->criticalR($nUjiButir);
            $decision = $this->decideItem($r, $rTable);
            $difficulty = $this->difficulty($info['scoreable'], (int) $stat['correct'], (int) $stat['answered']);
            $percentages = [];

            foreach (self::LETTERS as $letter) {
                $percentages[$letter] = $stat['answered'] > 0
                    ? round(($stat['counts'][$letter] / $stat['answered']) * 100, 1)
                    : 0.0;
            }

            $row = [
                'nomor' => $index + 1,
                'soal_id' => $soalId,
                'pertanyaan' => $info['soal'],
                'kunci' => $info['kunci_teks'] ?? ($info['kunci'] ?? '-'),
                'kunci_letter' => $info['kunci'],
                'n_peserta' => (int) $stat['n'],
                'n_terjawab' => (int) $stat['answered'],
                'n_uji_butir' => $nUjiButir,
                'counts' => $stat['counts'],
                'percentages' => $percentages,
                'p' => $difficulty['p'],
                'p_label' => $difficulty['label_angka'],
                'kesukaran_key' => $difficulty['key'],
                'kesukaran_label' => $difficulty['label'],
                'r_item_total' => $r,
                'r_tabel' => $rTable,
                'keputusan_key' => $decision['key'],
                'keputusan_label' => $decision['label'],
            ];

            $rows[] = $row;
            $itemById[$soalId] = $row;
        }

        $matrix = $this->matrixFromPeople($complete);
        $alpha = $this->cronbachAlpha($matrix['matrix']);
        $aligned = [];
        foreach ($matrix['columns'] as $soalId) {
            if (isset($itemById[$soalId])) {
                $aligned[] = $itemById[$soalId];
            }
        }

        $drops = count($aligned) === count($matrix['columns'])
            ? $this->itemsThatLowerAlpha($matrix['matrix'], $aligned, $alpha)
            : [];
        $nUji = count($complete);

        return [
            'kode' => $kode,
            'label' => $label,
            'items' => $rows,
            'complete' => $complete,
            'conclusion' => $this->buildConclusion(
                $label,
                $rows,
                $alpha,
                $this->criticalR($nUji),
                $nUji,
                count($matrix['columns']),
                $drops
            ),
        ];
    }

    /**
     * @param  array<int, array<string, mixed>>  $participants
     * @return array<int, array<int, array<string, mixed>>>
     */
    private function collectPeople(array $participants): array
    {
        $people = [];
        $fallback = 0;

        foreach ($participants as $participant) {
            $fallback--;
            $ujianId = (int) ($participant['ujian_id'] ?? 0);
            if ($ujianId === 0) {
                $ujianId = $fallback;
            }

            foreach ($participant['items'] ?? [] as $item) {
                $soalId = (int) ($item['soal_id'] ?? 0);
                if ($soalId === 0 || isset($people[$ujianId][$soalId])) {
                    continue;
                }

                $letter = $this->normalizeLetter($item['letter'] ?? null);
                $kunci = $this->normalizeLetter($item['kunci'] ?? null);
                $people[$ujianId][$soalId] = [
                    'soal' => trim((string) ($item['soal'] ?? $item['pertanyaan'] ?? '')),
                    'kunci' => $kunci,
                    'kunci_teks' => filled($item['kunci_teks'] ?? null) ? (string) $item['kunci_teks'] : ($kunci ?? '-'),
                    'letter' => $letter,
                    'scoreable' => $kunci !== null,
                    'correct' => $kunci !== null && $letter !== null && $letter === $kunci,
                ];
            }
        }

        return $people;
    }

    /**
     * @return array{p: ?float, label_angka: string, key: string, label: string}
     */
    private function difficulty(bool $scoreable, int $correct, int $answered): array
    {
        if (! $scoreable || $answered < 1) {
            return ['p' => null, 'label_angka' => '-', 'key' => 'none', 'label' => '-'];
        }

        $p = $correct / $answered;
        $rounded = round($p, 2);
        $key = match (true) {
            $rounded > 0.70 => 'mudah',
            $rounded < 0.30 => 'sukar',
            default => 'sedang',
        };

        return [
            'p' => $p,
            'label_angka' => $this->formatCoef($rounded, 2),
            'key' => $key,
            'label' => match ($key) {
                'mudah' => 'Mudah',
                'sukar' => 'Sukar',
                default => 'Sedang',
            },
        ];
    }

    /**
     * @param  array<int, array<int, float>>  $people
     * @return array{matrix: array<int, array<int, float>>, columns: array<int, int>}
     */
    private function matrixFromPeople(array $people): array
    {
        $columns = $this->commonColumns($people);
        $matrix = [];

        foreach ($people as $scores) {
            $row = [];
            foreach ($columns as $soalId) {
                if (! array_key_exists($soalId, $scores)) {
                    $row = [];
                    break;
                }
                $row[] = (float) $scores[$soalId];
            }
            if ($row !== []) {
                $matrix[] = $row;
            }
        }

        return ['matrix' => $matrix, 'columns' => $columns];
    }

    /**
     * @param  array<int, array<int, float>>  $people
     * @return array<int, int>
     */
    private function commonColumns(array $people): array
    {
        $columns = null;

        foreach ($people as $scores) {
            $ids = array_map('intval', array_keys($scores));
            $columns = $columns === null ? $ids : array_values(array_map('intval', array_intersect($columns, $ids)));
        }

        if ($columns === null) {
            return [];
        }

        sort($columns, SORT_NUMERIC);

        return $columns;
    }

    /**
     * @param  array<string, array<string, mixed>>  $pillars
     * @return array<string, mixed>
     */
    private function stratifiedAlpha(array $pillars): array
    {
        $empty = $this->emptyStratified(0);
        $active = [];

        foreach ($pillars as $kode => $pillar) {
            if (($pillar['complete'] ?? []) !== []) {
                $active[$kode] = $pillar;
            }
        }

        if (count($active) < 2) {
            return $empty;
        }

        $common = null;
        foreach ($active as $pillar) {
            $ids = array_map('intval', array_keys($pillar['complete']));
            $common = $common === null ? $ids : array_values(array_map('intval', array_intersect($common, $ids)));
        }

        $nUji = count($common ?? []);
        if ($nUji < 3) {
            return $this->emptyStratified($nUji);
        }

        $alphas = [];
        $variances = [];
        $totals = array_fill_keys($common, 0.0);

        foreach ($active as $kode => $pillar) {
            $subset = [];
            foreach ($common as $ujianId) {
                $subset[$ujianId] = $pillar['complete'][$ujianId];
            }

            $built = $this->matrixFromPeople($subset);
            $alpha = $this->cronbachAlpha($built['matrix']);
            if ($alpha === null || $built['columns'] === []) {
                return $this->emptyStratified($nUji);
            }

            $scores = [];
            foreach ($common as $ujianId) {
                $score = 0.0;
                foreach ($built['columns'] as $soalId) {
                    $score += (float) $subset[$ujianId][$soalId];
                }
                $scores[] = $score;
                $totals[$ujianId] += $score;
            }

            $alphas[$kode] = $alpha;
            $variances[$kode] = $this->variance($scores);
        }

        $totalVariance = $this->variance(array_map(fn (int $ujianId) => $totals[$ujianId], $common));
        if ($totalVariance <= 0.0) {
            return $this->emptyStratified($nUji);
        }

        $residual = 0.0;
        foreach ($alphas as $kode => $alpha) {
            $residual += $variances[$kode] * (1 - $alpha);
        }

        $alpha = 1 - ($residual / $totalVariance);
        $reliability = $this->reliabilityLabel($alpha);

        return [
            'alpha' => $alpha,
            'n_uji' => $nUji,
            'key' => $reliability['key'],
            'label' => $reliability['label'],
            'kalimat' => 'α berlapis tes = '.$this->formatCoef($alpha, 2).' (N uji = '.$nUji.'), dari peserta yang menyelesaikan semua pilar.',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function emptyStratified(int $nUji): array
    {
        $reliability = $this->reliabilityLabel(null);

        return [
            'alpha' => null,
            'n_uji' => $nUji,
            'key' => $reliability['key'],
            'label' => $reliability['label'],
            'kalimat' => 'α berlapis belum dapat dihitung. Angka ini memakai peserta yang menyelesaikan setiap pilar.',
        ];
    }

    /**
     * @param  array<int, array<string, mixed>>  $rows
     * @param  array<int, int>  $drops
     * @return array<string, mixed>
     */
    private function buildConclusion(string $label, array $rows, ?float $alpha, ?float $rTable, int $nUji, int $nButirAlpha, array $drops): array
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
        $k = count($rows);
        $alphaText = $alpha !== null ? $this->formatCoef($alpha, 2) : '-';
        $rText = $rTable !== null ? $this->formatCoef($rTable, 3) : '-';

        if ($rows === []) {
            $kalimat = $label.': belum ada jawaban pada pilar ini.';
        } elseif ($nUji < 3) {
            $kalimat = $label.': belum cukup peserta yang menjawab seluruh butir (N uji = '.$nUji.'). Reliabilitas dan keputusan butir belum dihitung.';
        } else {
            $kalimat = $label.': reliabilitas '.$reliability['sentence'].' (α = '.$alphaText.'; N uji = '.$nUji.'; r tabel = '.$rText.'). Dari '.$k.' butir, '.$counts['layak'].' layak, '.$counts['ditinjau'].' ditinjau, dan '.$counts['lemah'].' lemah.';
            if ($counts['insufficient'] > 0) {
                $kalimat .= ' '.$counts['insufficient'].' butir belum cukup data untuk dinilai.';
            }
            if ($nButirAlpha !== $k) {
                $kalimat .= ' Reliabilitas dihitung dari '.$nButirAlpha.' butir yang dikerjakan seluruh peserta uji.';
            }
            if ($drops !== []) {
                $kalimat .= ' Butir '.$this->joinNomor($drops).' paling menurunkan reliabilitas.';
            }
        }

        return [
            'n_uji' => $nUji,
            'n_butir' => $k,
            'n_butir_alpha' => $nButirAlpha,
            'r_tabel' => $rTable,
            'alpha' => $alpha,
            'reliabilitas_key' => $reliability['key'],
            'reliabilitas_label' => $reliability['label'],
            'indikatif' => $nUji < self::N_UJI_STABIL,
            'counts' => $counts,
            'butir_menurunkan' => $drops,
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

    private function normalizeLetter(mixed $value): ?string
    {
        $value = strtoupper(trim((string) $value));

        if (! in_array($value, self::LETTERS, true)) {
            return null;
        }

        return $value;
    }

    /**
     * @param  array<int, array<string, string>>  $rows
     */
    private function fillAnalysisSheet(Worksheet $sheet, string $title, string $eventName, array $rows, string $subtitle): void
    {
        $headers = ['Nomor', 'Soal', 'Kunci', 'N Peserta', 'N Terjawab', 'N Uji'];
        $keys = ['nomor', 'soal', 'kunci', 'n_peserta', 'n_terjawab', 'n_uji'];

        foreach (self::LETTERS as $letter) {
            $headers[] = 'N '.$letter;
            $headers[] = '% '.$letter;
            $keys[] = 'n_'.$letter;
            $keys[] = 'pct_'.$letter;
        }

        foreach ([
            'Tingkat Kesukaran (P)' => 'p',
            'Kategori Kesukaran' => 'kesukaran',
            'r Item-Total' => 'r_item_total',
            'r Tabel' => 'r_tabel',
            'Keputusan' => 'keputusan',
        ] as $header => $key) {
            $headers[] = $header;
            $keys[] = $key;
        }

        $sheet->setTitle(Str::limit($title, 31, ''));
        $lastCol = $this->columnLetter(count($headers) - 1);
        $sheet->mergeCells("A1:{$lastCol}1");
        $sheet->setCellValueExplicit('A1', $eventName !== '' ? $eventName : 'Event', DataType::TYPE_STRING);
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->mergeCells("A2:{$lastCol}2");
        $sheet->setCellValueExplicit('A2', $subtitle, DataType::TYPE_STRING);
        $sheet->getStyle('A2')->getAlignment()->setWrapText(true)->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getRowDimension(2)->setRowHeight(48);

        foreach ($headers as $colIndex => $header) {
            $sheet->setCellValueExplicit($this->columnLetter($colIndex).'3', $header, DataType::TYPE_STRING);
        }

        $sheet->getStyle("A3:{$lastCol}3")->getFont()->setBold(true);
        $sheet->getStyle("A3:{$lastCol}3")->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFE2E8F0');

        $rowNumber = 4;
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
            $lastRow = $rowNumber - 1;
            $sheet->getStyle("A4:{$lastCol}{$lastRow}")->getAlignment()
                ->setWrapText(true)
                ->setVertical(Alignment::VERTICAL_TOP);
        }

        for ($i = 0; $i < count($headers); $i++) {
            $width = match ($i) {
                0 => 10,
                1 => 55,
                2 => 32,
                default => 14,
            };
            $sheet->getColumnDimension($this->columnLetter($i))->setWidth($width);
        }

        $sheet->freezePane('A4');
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
