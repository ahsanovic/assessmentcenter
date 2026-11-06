<?php

namespace Tests\Unit;

use App\Services\LiterasiDigital\LiterasiDigitalButirService;
use App\Services\LiterasiDigital\LiterasiDigitalJawabanService;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Tests\TestCase;

class LiterasiDigitalButirServiceTest extends TestCase
{
    public function test_alpha_dan_daya_beda_pada_paket_yang_sama(): void
    {
        $service = new LiterasiDigitalButirService;
        $bundle = $service->analyze([
            'budaya' => [
                'label' => 'Budaya Digital',
                'participants' => [
                    $this->person(1, [[3, 'C', 'a', 'A'], [1, '=CMD', 'a', 'A'], [2, 'B', 'a', 'A']]),
                    $this->person(2, [[1, '=CMD', 'a', 'A'], [2, 'B', 'a', 'A'], [3, 'C', 'a', 'B']]),
                    $this->person(3, [[1, '=CMD', 'a', 'A'], [2, 'B', 'a', 'B'], [3, 'C', 'a', 'B']]),
                    $this->person(4, [[1, '=CMD', 'a', 'B'], [2, 'B', 'a', 'B'], [3, 'C', 'a', 'B']]),
                    $this->person(5, [[1, '=CMD', 'a', 'A'], [2, 'B', 'a', 'A'], [3, 'C', 'a', null]]),
                ],
            ],
        ]);

        $pillar = $bundle['pillars']['budaya'];
        $this->assertEqualsWithDelta(0.75, $pillar['conclusion']['alpha'], 0.00001);
        $this->assertSame(4, $pillar['conclusion']['n_uji']);
        $this->assertSame(3, $pillar['conclusion']['n_butir']);
        $this->assertSame(3, $pillar['conclusion']['n_butir_alpha']);
        $this->assertSame('cukup', $pillar['conclusion']['reliabilitas_key']);
        $this->assertTrue($pillar['conclusion']['indikatif']);
        $this->assertEqualsWithDelta(0.950, $service->criticalR(4), 0.002);

        $this->assertSame([1, 2, 3], array_column($pillar['items'], 'soal_id'));
        $first = $pillar['items'][0];
        $this->assertSame(1, $first['nomor']);
        $this->assertSame(5, $first['n_peserta']);
        $this->assertSame(5, $first['n_terjawab']);
        $this->assertSame(4, $first['n_uji_butir']);
        $this->assertEqualsWithDelta(0.522233, $first['r_item_total'], 0.0001);
        $this->assertSame('ditinjau', $first['keputusan_key']);
        $this->assertSame('mudah', $first['kesukaran_key']);
        $this->assertSame('0.80', $first['p_label']);
        $this->assertSame(80.0, $first['percentages']['A']);
        $this->assertSame(20.0, $first['percentages']['B']);
        $this->assertSame('sukar', $pillar['items'][2]['kesukaran_key']);
        $this->assertSame('0.25', $pillar['items'][2]['p_label']);
    }

    public function test_irisan_butir_dan_alpha_berlapis(): void
    {
        $service = new LiterasiDigitalButirService;
        $pattern = [
            $this->person(1, [[1, 'Satu', 'A', 'A'], [2, 'Dua', 'A', 'A'], [3, 'Tiga', 'A', 'A']]),
            $this->person(2, [[1, 'Satu', 'A', 'A'], [2, 'Dua', 'A', 'B'], [3, 'Tiga', 'A', 'B']]),
            $this->person(3, [[1, 'Satu', 'A', 'B'], [2, 'Dua', 'A', 'A'], [3, 'Tiga', 'A', 'B']]),
            $this->person(4, [[1, 'Satu', 'A', 'B'], [2, 'Dua', 'A', 'B']]),
        ];

        $bundle = $service->analyze([
            'budaya' => ['participants' => $pattern],
            'cakap' => ['participants' => $pattern],
        ]);

        $budaya = $bundle['pillars']['budaya'];
        $this->assertSame(2, $budaya['conclusion']['n_butir_alpha']);
        $this->assertSame(3, $budaya['conclusion']['n_butir']);
        $this->assertSame(4, $budaya['items'][0]['n_uji_butir']);
        $this->assertSame(3, $budaya['items'][2]['n_uji_butir']);
        $this->assertEqualsWithDelta(0.0, $budaya['conclusion']['alpha'], 0.00001);
        $this->assertStringContainsString('2 butir yang dikerjakan seluruh peserta uji', $budaya['conclusion']['kalimat']);

        $this->assertEqualsWithDelta(0.5, $bundle['stratified']['alpha'], 0.00001);
        $this->assertSame(4, $bundle['stratified']['n_uji']);
        $this->assertSame('rendah', $bundle['stratified']['key']);
    }

    public function test_butir_tanpa_variasi_atau_tanpa_kunci_belum_cukup_data(): void
    {
        $service = new LiterasiDigitalButirService;
        $bundle = $service->analyze([
            'etika' => [
                'participants' => [
                    $this->person(1, [[1, 'Sama', 'A', 'A'], [2, 'Kosong', null, 'B']]),
                    $this->person(2, [[1, 'Sama', 'A', 'A'], [2, 'Kosong', null, 'A']]),
                    $this->person(3, [[1, 'Sama', 'A', 'A'], [2, 'Kosong', null, 'C']]),
                ],
            ],
        ]);

        $items = $bundle['pillars']['etika']['items'];
        $this->assertNull($bundle['pillars']['etika']['conclusion']['alpha']);
        $this->assertSame('insufficient', $items[0]['keputusan_key']);
        $this->assertNull($items[0]['r_item_total']);
        $this->assertSame('mudah', $items[0]['kesukaran_key']);
        $this->assertSame('insufficient', $items[1]['keputusan_key']);
        $this->assertSame('-', $items[1]['p_label']);
        $this->assertSame('none', $items[1]['kesukaran_key']);
        $this->assertSame(3, $items[1]['n_terjawab']);
    }

    public function test_keputusan_butir_mengikuti_ambang(): void
    {
        $service = new LiterasiDigitalButirService;

        $this->assertSame('layak', $service->decideItem(0.40, 0.20)['key']);
        $this->assertSame('ditinjau', $service->decideItem(0.30, 0.30)['key']);
        $this->assertSame('ditinjau', $service->decideItem(0.25, 0.20)['key']);
        $this->assertSame('lemah', $service->decideItem(0.19, 0.10)['key']);
        $this->assertSame('lemah', $service->decideItem(-0.40, 0.20)['key']);
        $this->assertSame('insufficient', $service->decideItem(null, 0.20)['key']);
        $this->assertNull($service->criticalR(2));
    }

    public function test_lembar_analisa_menulis_teks(): void
    {
        $service = new LiterasiDigitalButirService;
        $bundle = $service->analyze([
            'budaya' => [
                'participants' => [
                    $this->person(1, [[1, '=1+1', 'A', 'A'], [2, 'Dua', 'A', 'B']]),
                ],
            ],
        ]);

        $book = (new LiterasiDigitalJawabanService)->makeSpreadsheet('Event Uji', [[
            'nip' => '001',
        ]]);
        $service->addAnalysisSheets($book, 'Event Uji', $bundle);

        $this->assertSame(5, $book->getSheetCount());
        $this->assertSame('Analisa Budaya', $book->getSheet(0)->getTitle());
        $this->assertSame('Analisa Etika', $book->getSheet(3)->getTitle());
        $this->assertSame('Jawaban Literasi Digital', $book->getSheet(4)->getTitle());
        $this->assertSame('001', $book->getSheet(4)->getCell('A4')->getValue());

        $sheet = $book->getSheet(0);
        $this->assertSame('Event Uji', $sheet->getCell('A1')->getValue());
        $this->assertSame('Soal 1. =1+1', $sheet->getCell('B4')->getValue());
        $this->assertSame(DataType::TYPE_STRING, $sheet->getCell('B4')->getDataType());

        $path = storage_path('app/testing-butir-literasi.xlsx');
        $writer = IOFactory::createWriter($book, 'Xlsx');
        $writer->setPreCalculateFormulas(false);
        $writer->save($path);

        try {
            $loaded = IOFactory::load($path);
            $this->assertSame('Soal 1. =1+1', $loaded->getSheet(0)->getCell('B4')->getValue());
        } finally {
            @unlink($path);
        }
    }

    /**
     * @param  array<int, array{0: int, 1: string, 2: ?string, 3: ?string}>  $items
     * @return array{ujian_id: int, items: array<int, array<string, mixed>>}
     */
    private function person(int $id, array $items): array
    {
        return [
            'ujian_id' => $id,
            'items' => array_map(fn (array $item) => [
                'soal_id' => $item[0],
                'soal' => $item[1],
                'kunci' => $item[2],
                'letter' => $item[3],
                'kunci_teks' => $item[2] ? strtoupper((string) $item[2]).' — '.$item[1] : null,
            ], $items),
        ];
    }
}
