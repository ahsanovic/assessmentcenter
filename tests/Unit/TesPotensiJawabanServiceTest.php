<?php

namespace Tests\Unit;

use App\Models\ProblemSolving\UjianProblemSolving;
use App\Services\TesPotensi\TesPotensiJawabanService;
use Tests\TestCase;

class TesPotensiJawabanServiceTest extends TestCase
{
    public function test_problem_solving_bank_memiliki_sebelas_butir(): void
    {
        $service = new TesPotensiJawabanService;
        $map = $service->loadSoalMap('problem-solving');

        $this->assertCount(11, $map);
    }

    public function test_jawaban_ujian_lama_mengabaikan_soal_yang_sudah_dihapus(): void
    {
        $service = new TesPotensiJawabanService;
        $map = $service->loadSoalMap('problem-solving');

        $ujian = new UjianProblemSolving;
        $ujian->soal_id = '2,1,8,3,12,4,5,6,7,9,10,11,13,14';
        $ujian->jawaban = 'A,B,C,D,E,A,B,C,D,E,A,B,C,D';

        $pairs = $service->answerPairsFromUjian($ujian, $map);

        $this->assertArrayNotHasKey(2, $pairs);
        $this->assertArrayNotHasKey(8, $pairs);
        $this->assertArrayNotHasKey(12, $pairs);
        $this->assertCount(11, $pairs);
        $this->assertSame('B', $pairs[1]);
    }

    public function test_build_detail_rows_mengikuti_urutan_bank_soal(): void
    {
        $service = new TesPotensiJawabanService;
        $map = $service->loadSoalMap('problem-solving');

        $ujian = new UjianProblemSolving;
        $ujian->soal_id = implode(',', $map->keys()->all());
        $ujian->jawaban = implode(',', array_fill(0, $map->count(), 'A'));

        $rows = $service->buildDetailRowsForUjian($ujian, 'problem-solving', $map);

        $this->assertCount(11, $rows);
        $this->assertSame(1, $rows[0]['nomor']);
        $this->assertSame(11, $rows[10]['nomor']);
    }

    public function test_item_analysis_menghitung_persentase_opsi(): void
    {
        $service = new TesPotensiJawabanService;
        $eventId = (int) \App\Models\Event::query()
            ->whereIn('metode_tes_id', TesPotensiJawabanService::METODE_TES_IDS)
            ->whereHas('ujianInterpersonal')
            ->value('id');

        if ($eventId === 0) {
            $this->markTestSkipped('Tidak ada event dengan ujian interpersonal.');
        }

        $rows = $service->buildItemAnalysisRows($eventId, 'interpersonal');

        $this->assertNotEmpty($rows);
        $first = $rows[0];
        $this->assertArrayHasKey('percentages', $first);
        $this->assertArrayHasKey('A', $first['percentages']);
        $this->assertArrayHasKey('keputusan_key', $first);

        $bundle = $service->analyzeSubtest($eventId, 'interpersonal');
        $this->assertArrayHasKey('kalimat', $bundle['conclusion']);
        $this->assertArrayHasKey('alpha', $bundle['conclusion']);
    }

    public function test_r_tabel_dan_keputusan_butir(): void
    {
        $service = new TesPotensiJawabanService;

        $this->assertEqualsWithDelta(0.9969, $service->criticalR(3), 0.002);
        $this->assertEqualsWithDelta(0.3494, $service->criticalR(32), 0.002);

        $this->assertSame('layak', $service->decideItem(0.40, 0.30)['key']);
        $this->assertSame('ditinjau', $service->decideItem(0.40, 0.50)['key']);
        $this->assertSame('ditinjau', $service->decideItem(0.25, 0.20)['key']);
        $this->assertSame('lemah', $service->decideItem(0.10, 0.05)['key']);
        $this->assertSame('lemah', $service->decideItem(-0.40, 0.20)['key']);
        $this->assertSame('insufficient', $service->decideItem(null, 0.30)['key']);
    }

    public function test_cronbach_alpha_satu_untuk_skor_yang_identik(): void
    {
        $service = new TesPotensiJawabanService;

        $alpha = $service->cronbachAlpha([
            [1, 1],
            [2, 2],
            [3, 3],
        ]);

        $this->assertEqualsWithDelta(1.0, $alpha, 0.0001);
        $this->assertNull($service->cronbachAlpha([[1, 1], [2, 2]]));
    }
}
