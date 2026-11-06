<?php

namespace Tests\Unit;

use App\Services\Pspk\PspkButirService;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Tests\TestCase;

class PspkButirServiceTest extends TestCase
{
    public function test_level_dua_memakai_benar_salah_dan_mengabaikan_butir_kosong(): void
    {
        $service = new PspkButirService;
        $bundle = $service->analyze([
            'int' => [
                'id' => 1,
                'label' => 'Integritas',
                'participants' => [
                    $this->person(1, [[3, 'C', 'A'], [1, 'Satu', 'A'], [2, 'Dua', 'A']], 2),
                    $this->person(2, [[1, 'Satu', 'A'], [2, 'Dua', 'A'], [3, 'C', 'B']], 2),
                    $this->person(3, [[1, 'Satu', 'A'], [2, 'Dua', 'B'], [3, 'C', 'B']], 2),
                    $this->person(4, [[1, 'Satu', 'B'], [2, 'Dua', 'B'], [3, 'C', 'B']], 2),
                    $this->person(5, [[1, 'Satu', 'A'], [2, 'Dua', 'A'], [3, 'C', null]], 2),
                ],
            ],
        ], 2);

        $aspek = $bundle['aspek']['int'];
        $this->assertSame('kunci_jawaban', $bundle['skor_model']);
        $this->assertFalse($bundle['show_jenis']);
        $this->assertEqualsWithDelta(0.75, $aspek['conclusion']['alpha'], 0.00001);
        $this->assertSame(4, $aspek['conclusion']['n_uji']);
        $this->assertSame(3, $aspek['conclusion']['n_butir']);
        $this->assertTrue($aspek['conclusion']['indikatif']);

        $first = $aspek['items'][0];
        $this->assertSame(1, $first['soal_id']);
        $this->assertSame(5, $first['n_peserta']);
        $this->assertSame(5, $first['n_terjawab']);
        $this->assertSame(4, $first['n_uji_butir']);
        $this->assertEqualsWithDelta(0.522233, $first['r_item_total'], 0.0001);
        $this->assertSame('ditinjau', $first['keputusan_key']);
        $this->assertSame('mudah', $first['kesukaran_key']);
        $this->assertSame('0.80', $first['p_label']);
        $this->assertSame(['A'], $first['kunci_letters']);
        $this->assertSame('sukar', $aspek['items'][2]['kesukaran_key']);
        $this->assertSame('0.25', $aspek['items'][2]['p_label']);
        $this->assertSame(4, $aspek['items'][2]['n_terjawab']);
    }

    public function test_level_politomus_memakai_poin_bukan_benar_salah(): void
    {
        $service = new PspkButirService;
        $bundle = $service->analyze([
            'ks' => [
                'id' => 2,
                'label' => 'Kerjasama',
                'participants' => [
                    $this->politomus(1, [3, 2]),
                    $this->politomus(2, [2, 3]),
                    $this->politomus(3, [1, 0]),
                    $this->politomus(4, [0, 1]),
                ],
            ],
        ], 1);

        $aspek = $bundle['aspek']['ks'];
        $this->assertSame('skor_opsi', $bundle['skor_model']);
        $this->assertEqualsWithDelta(0.75, $aspek['conclusion']['alpha'], 0.00001);
        $this->assertEqualsWithDelta(0.6, $aspek['items'][0]['r_item_total'], 0.00001);
        $this->assertSame('ditinjau', $aspek['items'][0]['keputusan_key']);
        $this->assertSame('0.50', $aspek['items'][0]['p_label']);
        $this->assertSame('sedang', $aspek['items'][0]['kesukaran_key']);
        $this->assertSame(['A'], $aspek['items'][0]['kunci_letters']);
        $this->assertSame(25.0, $aspek['items'][0]['percentages']['A']);
        $this->assertStringContainsString('(3)', $aspek['items'][0]['kunci']);
    }

    public function test_korelasi_negatif_berstatus_lemah_dan_butir_tanpa_variasi_belum_cukup(): void
    {
        $service = new PspkButirService;
        $bundle = $service->analyze([
            'kom' => [
                'id' => 3,
                'label' => 'Komunikasi',
                'participants' => [
                    $this->politomus(1, [3, 0]),
                    $this->politomus(2, [2, 1]),
                    $this->politomus(3, [1, 2]),
                    $this->politomus(4, [0, 3]),
                ],
            ],
        ], 3);

        $items = $bundle['aspek']['kom']['items'];
        $this->assertTrue($bundle['show_jenis']);
        $this->assertEqualsWithDelta(-1.0, $items[0]['r_item_total'], 0.00001);
        $this->assertSame('lemah', $items[0]['keputusan_key']);
        $this->assertSame('Ankas', $items[0]['jenis_label']);
        $this->assertSame('SJT', $items[1]['jenis_label']);

        $flat = $service->analyze([
            'oph' => [
                'id' => 4,
                'label' => 'Orientasi Pada Hasil',
                'participants' => [
                    $this->flatPerson(1, 0),
                    $this->flatPerson(2, 1),
                    $this->flatPerson(3, 2),
                    $this->flatPerson(4, 3),
                ],
            ],
        ], 4);

        $constant = $flat['aspek']['oph']['items'][0];
        $this->assertNull($constant['r_item_total']);
        $this->assertSame('insufficient', $constant['keputusan_key']);
        $this->assertSame('SJT', $flat['aspek']['oph']['items'][1]['jenis_label']);
        $this->assertSame('Ankas', $flat['aspek']['oph']['items'][0]['jenis_label']);
    }

    public function test_alpha_berlapis_memakai_irisan_peserta_semua_aspek(): void
    {
        $service = new PspkButirService;
        $pattern = [
            $this->person(1, [[1, 'Satu', 'A'], [2, 'Dua', 'A'], [3, 'Tiga', 'A']], 2),
            $this->person(2, [[1, 'Satu', 'A'], [2, 'Dua', 'B'], [3, 'Tiga', 'B']], 2),
            $this->person(3, [[1, 'Satu', 'B'], [2, 'Dua', 'A'], [3, 'Tiga', 'B']], 2),
            $this->person(4, [[1, 'Satu', 'B'], [2, 'Dua', 'B']], 2),
        ];

        $bundle = $service->analyze([
            'ks' => ['id' => 2, 'label' => 'Kerjasama', 'participants' => $pattern],
            'int' => ['id' => 1, 'label' => 'Integritas', 'participants' => $pattern],
        ], 2);

        $this->assertSame(['int', 'ks'], array_keys($bundle['aspek']));
        $integritas = $bundle['aspek']['int'];
        $this->assertSame(2, $integritas['conclusion']['n_butir_alpha']);
        $this->assertSame(3, $integritas['conclusion']['n_butir']);
        $this->assertSame(4, $integritas['items'][0]['n_uji_butir']);
        $this->assertSame(3, $integritas['items'][2]['n_uji_butir']);
        $this->assertEqualsWithDelta(0.0, $integritas['conclusion']['alpha'], 0.00001);
        $this->assertStringContainsString('2 butir yang dikerjakan seluruh peserta uji', $integritas['conclusion']['kalimat']);
        $this->assertEqualsWithDelta(0.5, $bundle['stratified']['alpha'], 0.00001);
        $this->assertSame(4, $bundle['stratified']['n_uji']);
        $this->assertSame('rendah', $bundle['stratified']['key']);
    }

    public function test_lembar_analisa_menulis_jenis_untuk_level_tiga(): void
    {
        $service = new PspkButirService;
        $bundle = $service->analyze([
            'int' => [
                'id' => 1,
                'label' => 'Integritas',
                'participants' => [
                    $this->politomus(1, [3, 1]),
                ],
            ],
        ], 3);

        $book = new Spreadsheet;
        $book->getActiveSheet()->setTitle('Jawaban PSPK');
        $service->addAnalysisSheets($book, 'Event Uji', $bundle);

        $this->assertSame('Analisa Integritas', $book->getSheet(0)->getTitle());
        $this->assertSame('Jawaban PSPK', $book->getSheet(1)->getTitle());
        $this->assertSame('Jenis', $book->getSheet(0)->getCell('D3')->getValue());
        $this->assertSame('Ankas', $book->getSheet(0)->getCell('D4')->getValue());
        $this->assertSame('Event Uji', $book->getSheet(0)->getCell('A1')->getValue());
    }

    /**
     * @param  array<int, array{0: int, 1: string, 2: ?string}>  $items
     * @return array<string, mixed>
     */
    private function person(int $id, array $items, int $level): array
    {
        return [
            'ujian_id' => $id,
            'items' => array_map(function (array $item) use ($level) {
                $letter = $item[2];
                $score = null;
                if ($letter !== null) {
                    $score = strtoupper($letter) === 'A' ? 1.0 : 0.0;
                }

                return [
                    'soal_id' => $item[0],
                    'soal' => $item[1],
                    'letter' => $letter === null ? null : strtoupper($letter),
                    'score' => $score,
                    'scoreable' => true,
                    'max_score' => 1.0,
                    'kunci' => 'A — kunci',
                    'kunci_letters' => ['A'],
                    'jenis_soal' => $level >= 3 ? 1 : 0,
                    'jenis_label' => $level >= 3 ? 'Ankas' : '-',
                ];
            }, $items),
        ];
    }

    /**
     * @param  array{0: int, 1: int}  $scores
     * @return array<string, mixed>
     */
    private function politomus(int $id, array $scores): array
    {
        $letters = ['A', 'B', 'C', 'D'];
        $items = [];

        foreach ($scores as $index => $score) {
            $items[] = [
                'soal_id' => $index + 1,
                'soal' => 'Butir '.($index + 1),
                'letter' => $letters[$score],
                'score' => (float) $score,
                'scoreable' => true,
                'max_score' => 3.0,
                'kunci' => 'A — terbaik (3)',
                'kunci_letters' => ['A'],
                'jenis_soal' => $index === 0 ? 1 : 2,
                'jenis_label' => $index === 0 ? 'Ankas' : 'SJT',
            ];
        }

        return ['ujian_id' => $id, 'items' => $items];
    }

    /**
     * @return array<string, mixed>
     */
    private function flatPerson(int $id, int $second): array
    {
        return [
            'ujian_id' => $id,
            'items' => [
                [
                    'soal_id' => 11,
                    'soal' => 'Tetap',
                    'letter' => 'C',
                    'score' => 2.0,
                    'scoreable' => true,
                    'max_score' => 3.0,
                    'kunci' => 'A',
                    'kunci_letters' => ['A'],
                    'jenis_soal' => 2,
                    'jenis_label' => 'SJT',
                ],
                [
                    'soal_id' => 2,
                    'soal' => 'Berubah',
                    'letter' => ['A', 'B', 'C', 'D'][$second],
                    'score' => (float) $second,
                    'scoreable' => true,
                    'max_score' => 3.0,
                    'kunci' => 'A',
                    'kunci_letters' => ['A'],
                    'jenis_soal' => 1,
                    'jenis_label' => 'Ankas',
                ],
            ],
        ];
    }
}
