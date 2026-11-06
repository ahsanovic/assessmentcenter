<?php

namespace Tests\Feature;

use App\Livewire\Admin\JawabanPeserta\LiterasiDigital\Analisa;
use App\Livewire\Admin\JawabanPeserta\LiterasiDigital\Show;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Tests\TestCase;

class JawabanLiterasiDigitalRouteTest extends TestCase
{
    public function test_tamu_diarahkan_dan_id_event_non_angka_ditolak(): void
    {
        $this->get('/bkdac/jawaban-peserta/literasi-digital')
            ->assertRedirect();

        $this->get('/bkdac/jawaban-peserta/literasi-digital/bukan-angka')
            ->assertNotFound();
    }

    public function test_halaman_event_menampilkan_analisa_dan_excel(): void
    {
        $eventId = DB::table('ujian_literasi_digital')
            ->join('event', 'event.id', '=', 'ujian_literasi_digital.event_id')
            ->where('event.metode_tes_id', 3)
            ->value('ujian_literasi_digital.event_id');

        if (! $eventId) {
            $this->markTestSkipped('Tidak ada event literasi digital.');
        }

        $show = Livewire::test(Show::class, ['idEvent' => (int) $eventId]);
        $show->assertOk();
        $show->assertSee('Reliabilitas tes');
        $show->assertSee('Reliabilitas pilar');
        $show->assertSee('analisa-budaya-', false);

        DB::flushQueryLog();
        DB::enableQueryLog();
        $analisa = Livewire::test(Analisa::class, ['eventId' => (int) $eventId]);
        $analisa->assertOk();
        $analisa->call('setFilterKeputusan', 'lemah')->assertSet('filterKeputusan', 'lemah');
        $analisa->call('setPilar', 'etika')->assertSet('pilar', 'etika')->assertSet('filterKeputusan', 'semua');
        $analisa->assertSee('analisa-etika-', false);
        $analisa->call('setPilar', 'bukan-pilar')->assertSet('pilar', 'budaya');
        $analisa->call('setFilterKeputusan', 'selain-daftar')->assertSet('filterKeputusan', 'semua');

        $queries = collect(DB::getQueryLog())->pluck('query');
        $this->assertNotEmpty($queries);
        foreach ($queries as $sql) {
            $normalized = strtolower(preg_replace('/\s+/', ' ', $sql) ?? '');
            $this->assertStringNotContainsString('select *', $normalized);
            $this->assertStringNotContainsString(' exists ', $normalized);
        }

        $show->call('downloadExcel')->assertFileDownloaded();
        $content = base64_decode((string) data_get($show->effects, 'download.content'));
        $path = storage_path('app/testing-butir-literasi-event.xlsx');
        file_put_contents($path, $content);

        try {
            $book = IOFactory::load($path);
            $this->assertSame(5, $book->getSheetCount());
            $this->assertSame('Analisa Budaya', $book->getSheet(0)->getTitle());
            $this->assertSame('Analisa Cakap', $book->getSheet(1)->getTitle());
            $this->assertSame('Analisa Keamanan', $book->getSheet(2)->getTitle());
            $this->assertSame('Analisa Etika', $book->getSheet(3)->getTitle());
            $this->assertSame('Jawaban Literasi Digital', $book->getSheet(4)->getTitle());
            $this->assertGreaterThan(3, $book->getSheet(0)->getHighestRow());
        } finally {
            @unlink($path);
        }
    }
}
