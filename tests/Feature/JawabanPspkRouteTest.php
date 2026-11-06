<?php

namespace Tests\Feature;

use App\Livewire\Admin\JawabanPeserta\Pspk\Analisa;
use App\Livewire\Admin\JawabanPeserta\Pspk\Show;
use App\Services\Pspk\PspkButirService;
use App\Services\PspkJawabanService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Tests\TestCase;

class JawabanPspkRouteTest extends TestCase
{
    public function test_tamu_diarahkan_dan_id_event_non_angka_ditolak(): void
    {
        $this->get('/bkdac/jawaban-peserta/pspk')
            ->assertRedirect();

        $this->get('/bkdac/jawaban-peserta/pspk/bukan-angka')
            ->assertNotFound();
    }

    public function test_halaman_event_menunda_analisa_sampai_komponen_dimuat(): void
    {
        $event = $this->eventPspk();
        if ($event === null) {
            $this->markTestSkipped('Tidak ada event PSPK.');
        }

        DB::flushQueryLog();
        DB::enableQueryLog();

        $show = Livewire::test(Show::class, ['idEvent' => $event['id']]);
        $show->assertOk();
        $show->assertSee('Menghitung daya beda');
        $show->assertDontSee('Reliabilitas aspek');

        $queries = collect(DB::getQueryLog())->pluck('query');
        foreach ($queries as $sql) {
            $this->assertStringNotContainsString('`jawaban`', strtolower($sql));
        }
    }

    public function test_analisa_butir_cepat_saat_tab_dan_filter_diganti(): void
    {
        $event = $this->eventPspk();
        if ($event === null) {
            $this->markTestSkipped('Tidak ada event PSPK.');
        }

        Cache::flush();
        Livewire::withoutLazyLoading();
        DB::flushQueryLog();
        DB::enableQueryLog();

        $analisa = Livewire::test(Analisa::class, [
            'eventId' => $event['id'],
            'levelPspk' => $event['level'],
        ]);
        $analisa->assertOk();
        $analisa->assertSee('Reliabilitas tes');
        $analisa->assertSee('Reliabilitas aspek');

        $first = collect(DB::getQueryLog())->pluck('query');
        $this->assertNotEmpty($first);
        $this->assertTrue($first->contains(fn (string $sql) => str_contains(strtolower($sql), '`jawaban`')));
        foreach ($first as $sql) {
            $normalized = strtolower(preg_replace('/\s+/', ' ', $sql) ?? '');
            $this->assertStringNotContainsString('select *', $normalized);
            $this->assertStringNotContainsString(' exists ', $normalized);
        }

        DB::flushQueryLog();
        $analisa->call('setFilterKeputusan', 'lemah')->assertSet('filterKeputusan', 'lemah');
        $analisa->call('setFilterKeputusan', 'selain-daftar')->assertSet('filterKeputusan', 'lemah');

        $second = collect(DB::getQueryLog())->pluck('query');
        $this->assertNotEmpty($second);
        $this->assertLessThanOrEqual(8, $second->count());
        foreach ($second as $sql) {
            $normalized = strtolower($sql);
            $this->assertStringNotContainsString('`jawaban`', $normalized);
            $this->assertStringNotContainsString('opsi_a', $normalized);
        }

        $bundle = app(PspkButirService::class)->analyzeEvent($event['id'], $event['level']);
        $kode = (string) array_key_first($bundle['aspek']);
        if ($kode !== '') {
            $analisa->call('setAspek', $kode)->assertSet('aspek', $kode)->assertSet('filterKeputusan', 'semua');
            $analisa->assertSee('analisa-pspk-'.$kode.'-', false);
        }
    }

    public function test_excel_memuat_lembar_analisa(): void
    {
        $event = $this->eventPspk();
        if ($event === null) {
            $this->markTestSkipped('Tidak ada event PSPK.');
        }

        $show = Livewire::test(Show::class, ['idEvent' => $event['id']]);
        $show->call('downloadExcel')->assertFileDownloaded();
        $content = base64_decode((string) data_get($show->effects, 'download.content'));
        $path = storage_path('app/testing-butir-pspk-event.xlsx');
        file_put_contents($path, $content);

        try {
            $book = IOFactory::load($path);
            $titles = [];
            for ($i = 0; $i < $book->getSheetCount(); $i++) {
                $titles[] = $book->getSheet($i)->getTitle();
            }

            $this->assertTrue(collect($titles)->contains(fn (string $title) => str_starts_with($title, 'Analisa ')));
            $this->assertContains('Jawaban PSPK', $titles);
            $this->headerNamed($book->getSheet(0), 'r Item-Total');
        } finally {
            @unlink($path);
        }
    }

    /**
     * @return array{id: int, level: int}|null
     */
    private function eventPspk(): ?array
    {
        $row = DB::table('ujian_pspk')
            ->join('event', 'event.id', '=', 'ujian_pspk.event_id')
            ->whereIn('event.metode_tes_id', PspkJawabanService::PSPK_METODE_IDS)
            ->first(['ujian_pspk.event_id', 'event.metode_tes_id']);

        if ($row === null) {
            return null;
        }

        $level = app(PspkJawabanService::class)->levelFromMetode((int) $row->metode_tes_id);

        return $level === null ? null : [
            'id' => (int) $row->event_id,
            'level' => $level,
        ];
    }

    private function headerNamed(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet, string $header): string
    {
        foreach (range('A', 'Z') as $column) {
            if ($sheet->getCell($column.'3')->getValue() === $header) {
                return $header;
            }
        }

        $this->fail('Kolom '.$header.' tidak ada pada lembar analisa.');
    }
}
