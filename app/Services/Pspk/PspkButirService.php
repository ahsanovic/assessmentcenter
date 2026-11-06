<?php

namespace App\Services\Pspk;

use App\Services\LiterasiDigital\LiterasiDigitalButirService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PspkButirService
{
    public const LETTERS = ['A', 'B', 'C', 'D', 'E'];

    public function __construct(private ?LiterasiDigitalButirService $stats = null)
    {
        $this->stats ??= new LiterasiDigitalButirService;
    }

    /**
     * Skor mengikuti perhitungan laporan.
     * Level 2: benar/salah. Level 1, 3, dan 4: poin opsi, dijumlah per aspek.
     *
     * @return array{aspek: array<string, array<string, mixed>>, stratified: array<string, mixed>, skor_model: string, show_jenis: bool, level: int}
     */
    public function analyzeEvent(int $eventId, int $levelPspk): array
    {
        $levelPspk = in_array($levelPspk, [1, 2, 3, 4], true) ? $levelPspk : 1;
        $stamp = DB::table('ujian_pspk')
            ->where('event_id', $eventId)
            ->selectRaw('COUNT(*) as jumlah, MAX(updated_at) as cap')
            ->first();
        $soalCap = DB::table('soal_pspk')
            ->where('level_pspk_id', $levelPspk)
            ->max('updated_at');

        $key = sprintf(
            'pspk-butir:v1:%d:%d:%s:%s:%s',
            $eventId,
            $levelPspk,
            $stamp->jumlah ?? 0,
            $stamp->cap ?? '0',
            $soalCap ?? '0'
        );

        return Cache::remember($key, now()->addHours(6), function () use ($eventId, $levelPspk) {
            return $this->analyze($this->participantsForEvent($eventId, $levelPspk), $levelPspk);
        });
    }

    /**
     * @param  array<string, array<string, mixed>>  $aspekInput
     * @return array{aspek: array<string, array<string, mixed>>, stratified: array<string, mixed>, skor_model: string, show_jenis: bool, level: int}
     */
    public function analyze(array $aspekInput, int $levelPspk): array
    {
        $levelPspk = in_array($levelPspk, [1, 2, 3, 4], true) ? $levelPspk : 1;
        $mode = $levelPspk === 2 ? 'dikotomi' : 'politomus';

        uasort($aspekInput, function (array $left, array $right) {
            return ((int) ($left['id'] ?? 0)) <=> ((int) ($right['id'] ?? 0));
        });

        $computed = [];
        foreach ($aspekInput as $kode => $input) {
            $label = filled($input['label'] ?? null) ? (string) $input['label'] : (string) $kode;
            $computed[(string) $kode] = $this->analyzeAspek((string) $kode, $label, $input['participants'] ?? [], $mode, $levelPspk);
        }

        $stratified = $this->stratifiedAlpha($computed);

        foreach ($computed as $kode => $aspek) {
            unset($computed[$kode]['complete']);
        }

        return [
            'level' => $levelPspk,
            'skor_model' => $mode === 'dikotomi' ? 'kunci_jawaban' : 'skor_opsi',
            'show_jenis' => in_array($levelPspk, [3, 4], true),
            'aspek' => $computed,
            'stratified' => $stratified,
        ];
    }

    /**
     * @param  array{aspek: array<string, array<string, mixed>>, stratified: array<string, mixed>, show_jenis?: bool}  $bundle
     */
    public function addAnalysisSheets(Spreadsheet $spreadsheet, string $eventName, array $bundle): void
    {
        $index = 0;
        $used = [];
        $showJenis = (bool) ($bundle['show_jenis'] ?? false);

        foreach ($bundle['aspek'] as $aspek) {
            if (! is_array($aspek)) {
                continue;
            }

            $sheet = $spreadsheet->createSheet($index);
            $index++;
            $title = $this->sheetTitle((string) ($aspek['label'] ?? 'Aspek'), $used);
            $subtitle = trim((string) ($aspek['conclusion']['kalimat'] ?? ''));
            $stratified = trim((string) ($bundle['stratified']['kalimat'] ?? ''));
            if ($stratified !== '') {
                $subtitle = trim($subtitle.' '.$stratified);
            }

            $this->fillAnalysisSheet(
                $sheet,
                $title,
                $eventName,
                $this->exportItemRows($aspek['items'] ?? [], $showJenis),
                $subtitle,
                $showJenis
            );
        }

        if ($index > 0 && $spreadsheet->getSheetCount() > 0) {
            $spreadsheet->setActiveSheetIndex(0);
        }
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    private function participantsForEvent(int $eventId, int $levelPspk): array
    {
        $rows = DB::table('ujian_pspk as ujian')
            ->join('peserta', 'peserta.id', '=', 'ujian.peserta_id')
            ->where('ujian.event_id', $eventId)
            ->get(['ujian.id', 'ujian.soal_id', 'ujian.jawaban']);

        $wanted = [];
        $parsed = [];

        foreach ($rows as $row) {
            $ids = explode(',', (string) $row->soal_id);
            $answers = explode(',', (string) $row->jawaban);
            $pairs = [];

            foreach ($ids as $index => $id) {
                $soalId = (int) trim($id);
                if ($soalId === 0 || isset($pairs[$soalId])) {
                    continue;
                }

                $pairs[$soalId] = $answers[$index] ?? '0';
                $wanted[$soalId] = $soalId;
            }

            $parsed[] = [
                'ujian_id' => (int) $row->id,
                'pairs' => $pairs,
            ];
        }

        if ($wanted === []) {
            return [];
        }

        $soalRows = DB::table('soal_pspk as soal')
            ->leftJoin('ref_aspek_pspk as aspek', 'aspek.id', '=', 'soal.aspek_id')
            ->whereIn('soal.id', array_values($wanted))
            ->get([
                'soal.id',
                'soal.jenis_soal',
                'soal.aspek_id',
                'soal.soal',
                'soal.opsi_a',
                'soal.poin_opsi_a',
                'soal.opsi_b',
                'soal.poin_opsi_b',
                'soal.opsi_c',
                'soal.poin_opsi_c',
                'soal.opsi_d',
                'soal.poin_opsi_d',
                'soal.opsi_e',
                'soal.poin_opsi_e',
                'soal.kunci_jawaban',
                'aspek.kode_aspek',
                'aspek.nama_aspek',
            ]);

        $described = [];
        foreach ($soalRows as $soal) {
            $described[(int) $soal->id] = $this->describeSoal($soal, $levelPspk);
        }

        $grouped = [];

        foreach ($parsed as $person) {
            $buckets = [];

            foreach ($person['pairs'] as $soalId => $raw) {
                $soal = $described[$soalId] ?? null;
                if ($soal === null) {
                    continue;
                }

                $kode = $soal['aspek_kode'];
                $buckets[$kode]['id'] = $soal['aspek_id'];
                $buckets[$kode]['label'] = $soal['aspek_label'];
                $buckets[$kode]['items'][] = $this->scoreItem($soal, (string) $raw, $levelPspk);
            }

            foreach ($buckets as $kode => $bucket) {
                $grouped[$kode]['id'] = $bucket['id'];
                $grouped[$kode]['kode'] = $kode;
                $grouped[$kode]['label'] = $bucket['label'];
                $grouped[$kode]['participants'][] = [
                    'ujian_id' => $person['ujian_id'],
                    'items' => $bucket['items'],
                ];
            }
        }

        return $grouped;
    }

    /**
     * @return array<string, mixed>
     */
    private function describeSoal(object $soal, int $levelPspk): array
    {
        $options = [];
        foreach (['a', 'b', 'c', 'd', 'e'] as $suffix) {
            $text = $soal->{'opsi_'.$suffix} ?? null;
            if (! filled($text)) {
                continue;
            }

            $options[strtoupper($suffix)] = [
                'text' => trim((string) $text),
                'poin' => (int) ($soal->{'poin_opsi_'.$suffix} ?? 0),
            ];
        }

        $kode = filled($soal->kode_aspek ?? null) ? (string) $soal->kode_aspek : 'tanpa-aspek';
        $jenis = (int) ($soal->jenis_soal ?? 0);

        return [
            'id' => (int) $soal->id,
            'soal' => trim((string) ($soal->soal ?? '')),
            'options' => $options,
            'kunci' => $this->normalizeLetter($soal->kunci_jawaban ?? null),
            'aspek_id' => (int) ($soal->aspek_id ?? 0),
            'aspek_kode' => $kode,
            'aspek_label' => filled($soal->nama_aspek ?? null) ? (string) $soal->nama_aspek : 'Tanpa aspek',
            'jenis_soal' => $jenis,
            'jenis_label' => match (true) {
                in_array($levelPspk, [3, 4], true) && $jenis === 1 => 'Ankas',
                in_array($levelPspk, [3, 4], true) && $jenis === 2 => 'SJT',
                default => '-',
            },
        ];
    }

    /**
     * @param  array<string, mixed>  $soal
     * @return array<string, mixed>
     */
    private function scoreItem(array $soal, string $raw, int $levelPspk): array
    {
        $letter = $this->normalizeLetter($raw);
        $options = $soal['options'];

        if ($letter !== null && $options !== [] && ! isset($options[$letter])) {
            $letter = null;
        }

        if ($levelPspk === 2) {
            $kunci = $soal['kunci'];
            $scoreable = $kunci !== null;
            $score = null;
            if ($scoreable && $letter !== null) {
                $score = $letter === $kunci ? 1.0 : 0.0;
            }

            $text = $kunci !== null ? (string) ($options[$kunci]['text'] ?? '') : '';

            return [
                'soal_id' => $soal['id'],
                'soal' => $soal['soal'],
                'letter' => $letter,
                'score' => $score,
                'scoreable' => $scoreable,
                'max_score' => 1.0,
                'kunci' => $kunci === null ? '-' : ($text !== '' ? $kunci.' — '.$text : $kunci),
                'kunci_letters' => $kunci !== null ? [$kunci] : [],
                'jenis_soal' => $soal['jenis_soal'],
                'jenis_label' => $soal['jenis_label'],
            ];
        }

        $max = $options === [] ? 0 : max(array_column($options, 'poin'));
        $scoreable = $max > 0;
        $best = [];
        if ($scoreable) {
            foreach ($options as $optionLetter => $option) {
                if ((int) $option['poin'] === $max) {
                    $best[] = $optionLetter;
                }
            }
        }

        $score = null;
        if ($scoreable && $letter !== null && isset($options[$letter])) {
            $score = (float) $options[$letter]['poin'];
        }

        $parts = [];
        foreach ($best as $optionLetter) {
            $text = (string) ($options[$optionLetter]['text'] ?? '');
            $poin = (int) $options[$optionLetter]['poin'];
            $parts[] = $text !== '' ? $optionLetter.' — '.$text.' ('.$poin.')' : $optionLetter.' ('.$poin.')';
        }

        return [
            'soal_id' => $soal['id'],
            'soal' => $soal['soal'],
            'letter' => $letter,
            'score' => $score,
            'scoreable' => $scoreable,
            'max_score' => (float) $max,
            'kunci' => $parts === [] ? '-' : implode('; ', $parts),
            'kunci_letters' => $best,
            'jenis_soal' => $soal['jenis_soal'],
            'jenis_label' => $soal['jenis_label'],
        ];
    }

    /**
     * @param  array<int, array<string, mixed>>  $participants
     * @return array<string, mixed>
     */
    private function analyzeAspek(string $kode, string $label, array $participants, string $mode, int $levelPspk): array
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
                        'score_sum' => 0.0,
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
                    if ($item['scoreable'] && $item['score'] !== null) {
                        $stats[$soalId]['score_sum'] += (float) $item['score'];
                        if ($mode === 'dikotomi' && (float) $item['score'] >= (float) $item['max_score']) {
                            $stats[$soalId]['correct']++;
                        }
                    }
                }

                if (! $item['scoreable']) {
                    continue;
                }

                if ($item['score'] === null) {
                    $blocked = true;

                    continue;
                }

                $scores[$soalId] = (float) $item['score'];
            }

            if (! $blocked && $scores !== []) {
                $complete[$ujianId] = $scores;
            }
        }

        $soalIds = array_map('intval', array_keys($stats));
        usort($soalIds, function (int $left, int $right) use ($meta, $levelPspk) {
            if (in_array($levelPspk, [3, 4], true)) {
                $jenis = ((int) ($meta[$left]['jenis_soal'] ?? 0)) <=> ((int) ($meta[$right]['jenis_soal'] ?? 0));
                if ($jenis !== 0) {
                    return $jenis;
                }
            }

            return $left <=> $right;
        });

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
            $rTable = $this->stats->criticalR($nUjiButir);
            $decision = $this->stats->decideItem($r, $rTable);
            $difficulty = $this->difficulty(
                $mode,
                (bool) $info['scoreable'],
                (float) $stat['score_sum'],
                (int) $stat['correct'],
                (int) $stat['answered'],
                (float) $info['max_score']
            );
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
                'kunci' => $info['kunci'] ?? '-',
                'kunci_letters' => array_values($info['kunci_letters'] ?? []),
                'jenis_label' => (string) ($info['jenis_label'] ?? '-'),
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
        $alpha = $this->stats->cronbachAlpha($matrix['matrix']);
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
                $this->stats->criticalR($nUji),
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

                $people[$ujianId][$soalId] = $item;
            }
        }

        return $people;
    }

    /**
     * @return array{p: ?float, label_angka: string, key: string, label: string}
     */
    private function difficulty(string $mode, bool $scoreable, float $scoreSum, int $correct, int $answered, float $max): array
    {
        if (! $scoreable || $answered < 1 || ($mode === 'politomus' && $max <= 0)) {
            return ['p' => null, 'label_angka' => '-', 'key' => 'none', 'label' => '-'];
        }

        $p = $mode === 'dikotomi' ? ($correct / $answered) : ($scoreSum / ($answered * $max));
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
     * @param  array<string, array<string, mixed>>  $aspekList
     * @return array<string, mixed>
     */
    private function stratifiedAlpha(array $aspekList): array
    {
        $empty = $this->emptyStratified(0);
        $active = [];

        foreach ($aspekList as $kode => $aspek) {
            if (($aspek['complete'] ?? []) !== []) {
                $active[$kode] = $aspek;
            }
        }

        if (count($active) < 2) {
            return $empty;
        }

        $common = null;
        foreach ($active as $aspek) {
            $ids = array_map('intval', array_keys($aspek['complete']));
            $common = $common === null ? $ids : array_values(array_map('intval', array_intersect($common, $ids)));
        }

        $nUji = count($common ?? []);
        if ($nUji < 3) {
            return $this->emptyStratified($nUji);
        }

        $alphas = [];
        $variances = [];
        $totals = array_fill_keys($common, 0.0);

        foreach ($active as $kode => $aspek) {
            $subset = [];
            foreach ($common as $ujianId) {
                $subset[$ujianId] = $aspek['complete'][$ujianId];
            }

            $built = $this->matrixFromPeople($subset);
            $alpha = $this->stats->cronbachAlpha($built['matrix']);
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
            'kalimat' => 'α berlapis tes = '.$this->formatCoef($alpha, 2).' (N uji = '.$nUji.'), dari peserta yang menyelesaikan semua aspek.',
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
            'kalimat' => 'α berlapis belum dapat dihitung. Angka ini memakai peserta yang menyelesaikan setiap aspek.',
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
            $kalimat = $label.': belum ada jawaban pada aspek ini.';
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
            'indikatif' => $nUji < LiterasiDigitalButirService::N_UJI_STABIL,
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

        if ($alpha >= LiterasiDigitalButirService::ALPHA_RELIABEL) {
            return ['key' => 'reliabel', 'label' => 'Reliabel', 'sentence' => 'reliabel'];
        }

        if ($alpha >= LiterasiDigitalButirService::ALPHA_CUKUP) {
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

            $without = $this->stats->cronbachAlpha($reduced);
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

    private function normalizeLetter(mixed $value): ?string
    {
        $value = strtoupper(trim((string) $value));

        if (! in_array($value, self::LETTERS, true)) {
            return null;
        }

        return $value;
    }

    /**
     * @param  array<int, array<string, mixed>>  $items
     * @return array<int, array<string, string>>
     */
    private function exportItemRows(array $items, bool $showJenis): array
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

            if ($showJenis) {
                $row['jenis'] = (string) ($item['jenis_label'] ?? '-');
            }

            foreach (self::LETTERS as $letter) {
                $row['n_'.$letter] = (string) ($item['counts'][$letter] ?? 0);
                $row['pct_'.$letter] = (string) ($item['percentages'][$letter] ?? 0);
            }

            $rows[] = $row;
        }

        return $rows;
    }

    /**
     * @param  array<int, array<string, string>>  $rows
     */
    private function fillAnalysisSheet(Worksheet $sheet, string $title, string $eventName, array $rows, string $subtitle, bool $showJenis): void
    {
        $headers = ['Nomor', 'Soal', 'Kunci'];
        $keys = ['nomor', 'soal', 'kunci'];

        if ($showJenis) {
            $headers[] = 'Jenis';
            $keys[] = 'jenis';
        }

        foreach (['N Peserta' => 'n_peserta', 'N Terjawab' => 'n_terjawab', 'N Uji' => 'n_uji'] as $header => $key) {
            $headers[] = $header;
            $keys[] = $key;
        }

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

        $sheet->setTitle($title);
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

    /**
     * @param  array<string, bool>  $used
     */
    private function sheetTitle(string $label, array &$used): string
    {
        $clean = trim((string) preg_replace('/[\\\\\\/\\?\\*\\[\\]:]+/', ' ', $label));
        $clean = trim((string) preg_replace('/\s+/', ' ', $clean));
        $base = 'Analisa '.($clean !== '' ? $clean : 'Aspek');
        $title = Str::limit($base, 31, '');
        $candidate = $title;
        $n = 2;

        while (isset($used[$candidate])) {
            $suffix = ' '.$n;
            $candidate = Str::limit($title, 31 - strlen($suffix), '').$suffix;
            $n++;
        }

        $used[$candidate] = true;

        return $candidate;
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
