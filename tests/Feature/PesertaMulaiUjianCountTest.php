<?php

namespace Tests\Feature;

use App\Livewire\Admin\DataTes\TesIntelektual\TesBerlangsung\Index as IntelektualBerlangsung;
use App\Livewire\Admin\DataTes\TesIntelektual\TesSelesai\Index as IntelektualSelesai;
use App\Livewire\Admin\DataTes\TesLiterasiDigital\TesBerlangsung\Index as LiterasiBerlangsung;
use App\Livewire\Admin\DataTes\TesLiterasiDigital\TesSelesai\Index as LiterasiSelesai;
use App\Livewire\Admin\DataTes\TesPotensi\TesBerlangsung\Index as PotensiBerlangsung;
use App\Livewire\Admin\DataTes\TesPotensi\TesSelesai\Index as PotensiSelesai;
use App\Livewire\Admin\DataTes\TesPspk\TesBerlangsung\Index as PspkBerlangsung;
use App\Livewire\Admin\DataTes\TesPspk\TesSelesai\Index as PspkSelesai;
use App\Models\Event;
use App\Models\Intelektual\UjianIntelektualSubTes2;
use App\Models\Intelektual\UjianIntelektualSubTes3;
use App\Models\Interpersonal\UjianInterpersonal;
use App\Models\KesadaranDiri\UjianKesadaranDiri;
use App\Models\LiterasiDigital\UjianLiterasiDigital;
use App\Models\Peserta;
use App\Models\Pspk\UjianPspk;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use InvalidArgumentException;
use Livewire\Livewire;
use Tests\TestCase;

class PesertaMulaiUjianCountTest extends TestCase
{
    use DatabaseTransactions;

    public function test_hitungan_hanya_peserta_aktif_yang_punya_ujian_di_event_yang_sama(): void
    {
        $potensi = $this->makeEvent(1, 'Potensi mulai');
        $eventLain = $this->makeEvent(1, 'Event lain');
        $mulai = $this->makePeserta($potensi->id);
        $this->makePeserta($potensi->id, ['test_started_at' => now()]);
        $nonaktif = $this->makePeserta($potensi->id, ['is_active' => 'false']);
        $ujianSalahEvent = $this->makePeserta($potensi->id);

        UjianInterpersonal::create([
            'event_id' => $potensi->id,
            'peserta_id' => $mulai->id,
            'is_finished' => 'true',
        ]);
        UjianKesadaranDiri::create([
            'event_id' => $potensi->id,
            'peserta_id' => $mulai->id,
        ]);
        UjianInterpersonal::create([
            'event_id' => $potensi->id,
            'peserta_id' => $nonaktif->id,
        ]);
        UjianInterpersonal::create([
            'event_id' => $eventLain->id,
            'peserta_id' => $ujianSalahEvent->id,
        ]);

        $this->assertRasio($potensi, 'potensi', 3, 1);
        $this->assertTampil(PotensiBerlangsung::class, $potensi->nama_event, '3/1');
        $this->assertTampil(PotensiSelesai::class, $potensi->nama_event, '3/1', $potensi->nama_event);

        $intelektual = $this->makeEvent(2, 'Intelektual mulai');
        $intelektualMulai = $this->makePeserta($intelektual->id);
        $this->makePeserta($intelektual->id);
        UjianIntelektualSubTes2::create([
            'event_id' => $intelektual->id,
            'peserta_id' => $intelektualMulai->id,
            'nilai' => 0,
        ]);
        UjianIntelektualSubTes3::create([
            'event_id' => $intelektual->id,
            'peserta_id' => $intelektualMulai->id,
            'nilai' => 0,
            'is_finished' => 'true',
        ]);

        $this->assertRasio($intelektual, 'intelektual', 2, 1);
        $this->assertTampil(IntelektualBerlangsung::class, $intelektual->nama_event, '2/1');
        $this->assertTampil(IntelektualSelesai::class, $intelektual->nama_event, '2/1', $intelektual->nama_event);

        $literasi = $this->makeEvent(3, 'Literasi mulai');
        $literasiMulai = $this->makePeserta($literasi->id);
        $this->makePeserta($literasi->id);
        UjianLiterasiDigital::create([
            'event_id' => $literasi->id,
            'peserta_id' => $literasiMulai->id,
        ]);

        $this->assertRasio($literasi, 'literasi-digital', 2, 1);
        $this->assertTampil(LiterasiBerlangsung::class, $literasi->nama_event, '2/1');
        $this->assertTampil(LiterasiSelesai::class, $literasi->nama_event, '2/1', $literasi->nama_event);

        $pspk = $this->makeEvent(5, 'PSPK mulai');
        $pspkMulai = $this->makePeserta($pspk->id);
        $this->makePeserta($pspk->id);
        UjianPspk::create([
            'event_id' => $pspk->id,
            'peserta_id' => $pspkMulai->id,
            'is_finished' => 'true',
        ]);

        $this->assertRasio($pspk, 'pspk', 2, 1);
        $this->assertTampil(PspkBerlangsung::class, $pspk->nama_event, '2/1');
        $this->assertTampil(PspkSelesai::class, $pspk->nama_event, '2/1', $pspk->nama_event);
    }

    public function test_jenis_tes_yang_tidak_dikenal_ditolak(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Event::query()->withPesertaMulaiUjian('cakap')->get();
    }

    private function assertRasio(Event $event, string $jenis, int $terdaftar, int $mulai): void
    {
        $baris = Event::query()
            ->withCount('peserta')
            ->withPesertaMulaiUjian($jenis)
            ->findOrFail($event->id);

        $this->assertSame($terdaftar, (int) $baris->peserta_count);
        $this->assertSame($mulai, (int) $baris->peserta_mulai_ujian_count);
    }

    private function assertTampil(string $component, string $nama, string $rasio, ?string $cari = null): void
    {
        $uji = Livewire::test($component);
        if ($cari !== null) {
            $uji->set('search', $cari);
        }

        $html = $uji->html();
        $this->assertStringContainsString('Peserta Terdaftar/Mulai Ujian', $html);

        $posisi = strrpos($html, $nama);
        $this->assertNotFalse($posisi, "Nama event {$nama} tidak muncul.");

        $cuplikan = substr($html, $posisi, 800);
        [$terdaftar, $mulai] = array_map('intval', explode('/', $rasio));
        $this->assertStringContainsString($rasio, $cuplikan);
        $this->assertStringContainsString("{$terdaftar} peserta aktif, {$mulai} sudah mulai ujian", $cuplikan);
    }

    private function makeEvent(int $metodeTesId, string $nama): Event
    {
        return Event::create([
            'nama_event' => $nama.' '.uniqid(),
            'metode_tes_id' => $metodeTesId,
            'tgl_mulai' => '12-11-2026',
            'tgl_selesai' => '12-11-2026',
            'jumlah_peserta' => 50,
            'pin_ujian' => '1234',
            'is_open' => 'false',
            'is_finished' => 'false',
        ]);
    }

    private function makePeserta(int $eventId, array $extra = []): Peserta
    {
        return Peserta::create(array_merge([
            'event_id' => $eventId,
            'jenis_peserta_id' => 1,
            'nama' => 'Peserta Uji',
            'nip' => '9'.str_pad((string) random_int(1, 99999999999999999), 17, '0', STR_PAD_LEFT),
            'jabatan' => 'Analis',
            'unit_kerja' => 'BKD',
            'instansi' => 'Jatim',
            'password' => bcrypt('password123'),
            'is_active' => 'true',
        ], $extra));
    }
}
