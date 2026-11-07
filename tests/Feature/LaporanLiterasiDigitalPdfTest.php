<?php

namespace Tests\Feature;

use App\Models\LiterasiDigital\HasilLiterasiDigital;
use App\Models\LiterasiDigital\RefDeskripsiLevelLiterasiDigital;
use App\Models\LiterasiDigital\RefPilarLiterasiDigital;
use App\Models\LiterasiDigital\RefSaranPengembanganLiterasiDigital;
use App\Models\LiterasiDigital\UjianLiterasiDigital;
use App\Models\NomorLaporan;
use App\Models\Peserta;
use App\Models\User;
use App\Services\LiterasiDigital\DeskripsiLevelLiterasiDigitalService;
use App\Services\LiterasiDigital\LaporanLiterasiDigitalService;
use App\Services\LiterasiDigital\LiterasiDigitalUjianService;
use App\Services\LiterasiDigital\SaranPengembanganLiterasiDigitalService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class LaporanLiterasiDigitalPdfTest extends TestCase
{
    use DatabaseTransactions;

    public function test_tamu_tidak_bisa_mengunduh_laporan(): void
    {
        $this->get(route('admin.tes-selesai.literasi-digital.download', [
            'idEvent' => 1,
            'identifier' => '1',
        ]))->assertRedirect();

        $this->get(route('admin.tes-selesai.literasi-digital.download-all-laporan', [
            'idEvent' => 1,
        ]))->assertRedirect();
    }

    public function test_laporan_memakai_standar_level_tiga_dan_teks_terbitan_event(): void
    {
        $this->masukSebagaiAdmin();

        $penanda = uniqid();
        $deskripsiA = 'Narasi etika terkunci '.$penanda;
        $deskripsiB = 'Narasi etika baru '.$penanda.'x';
        $saranA = "## Jaga {$penanda}\n\n- rahasiakan sandi {$penanda}";
        $saranB = 'Teks saran baru '.$penanda.'x';
        $narasiPilar = 'Kebiasaan memakai perangkat digital '.$penanda;

        $deskripsi = new DeskripsiLevelLiterasiDigitalService;
        $saran = new SaranPengembanganLiterasiDigitalService;
        $deskripsi->simpan($this->barisDeskripsi('etika', 5), $deskripsiA);
        $saran->simpan($this->barisSaran('etika', 5), $saranA);

        $budaya = RefPilarLiterasiDigital::query()->where('kode', 'budaya')->firstOrFail();
        $budaya->deskripsi = $narasiPilar;
        $budaya->save();

        $eventId = $this->buatEvent();
        $nip = '999'.random_int(100000, 999999);
        $pesertaId = $this->buatPeserta($eventId, $nip);
        $ujian = $this->buatUjian($eventId, $pesertaId, true);
        $this->buatHasil($ujian, [
            'level_budaya' => 2,
            'level_cakap' => 3,
            'level_keamanan' => 4,
            'level_etika' => 5,
            'jpm' => 55.25,
            'kategori' => 'Kurang Optimal',
        ]);

        NomorLaporan::query()->create([
            'event_id' => $eventId,
            'nomor' => 'BKD/800/LD/'.$penanda,
            'tanggal' => '2026-11-07',
        ]);

        $deskripsi->kunciUntukEvent($eventId);
        $saran->kunciUntukEvent($eventId);
        $deskripsi->simpan($this->barisDeskripsi('etika', 5)->refresh(), $deskripsiB);
        $saran->simpan($this->barisSaran('etika', 5)->refresh(), $saranB);

        $hasil = HasilLiterasiDigital::query()->where('ujian_id', $ujian->id)->firstOrFail();
        $susun = app(LaporanLiterasiDigitalService::class)->susun($eventId, $hasil);

        $this->assertSame(
            ['Budaya Digital', 'Cakap Digital', 'Keamanan dan Keselamatan Digital', 'Etika Digital'],
            array_column($susun['baris'], 'nama')
        );
        $this->assertSame([3, 3, 3, 3], array_column($susun['baris'], 'standar'));
        $this->assertSame([2, 3, 4, 5], array_column($susun['baris'], 'capaian'));
        $this->assertSame(14, $susun['total_capaian']);
        $this->assertSame(LiterasiDigitalUjianService::JPM_PENYEBUT, $susun['total_standar']);
        $this->assertSame(55.25, $susun['jpm']);
        $this->assertSame('Kurang Optimal', $susun['kategori']);
        $this->assertSame($deskripsiA, $susun['baris'][3]['deskripsi']);
        $this->assertStringContainsString('rahasiakan sandi '.$penanda, $susun['baris'][3]['saran_html']);
        $this->assertStringNotContainsString($saranB, $susun['baris'][3]['saran_html']);

        $peserta = Peserta::query()->findOrFail($pesertaId);
        $html = view('livewire.admin.data-tes.tes-literasi-digital.tes-selesai.download-pdf', array_merge($susun, [
            'peserta' => $peserta,
            'nomor_laporan' => 'BKD/800/LD/'.$penanda,
            'tte' => null,
        ]))->render();

        $this->assertStringContainsString('LAPORAN PENILAIAN LITERASI DIGITAL', $html);
        $this->assertStringContainsString('Pemetaan Literasi Digital', $html);
        $this->assertStringContainsString('BKD/800/LD/'.$penanda, $html);
        $this->assertStringContainsString('>ASPEK<', $html);
        $this->assertStringNotContainsString('ASPEK KOMPETENSI', $html);
        $this->assertStringNotContainsString('UNIT KOMPETENSI', $html);
        $this->assertStringContainsString($narasiPilar, $html);
        $this->assertStringContainsString($deskripsiA, $html);
        $this->assertStringNotContainsString($deskripsiB, $html);
        $this->assertStringContainsString('rahasiakan sandi '.$penanda, $html);
        $this->assertStringNotContainsString($saranB, $html);
        $this->assertStringContainsString('total nilai capaian adalah 14', $html);
        $this->assertStringContainsString('total nilai standar yaitu 12', $html);
        $this->assertStringContainsString('55.25%', $html);
        $this->assertStringContainsString('Kurang Optimal', $html);
        $this->assertStringContainsString('Siti Rahma', $html);
        $this->assertStringNotContainsString('<td width="20">Level</td>', $html);

        $halaman = $this->get(route('admin.tes-selesai.literasi-digital.show-peserta', $eventId));
        $halaman->assertOk();
        $halaman->assertSee('Laporan PDF (.zip)');
        $halaman->assertSee(route('admin.tes-selesai.literasi-digital.download', [
            'idEvent' => $eventId,
            'identifier' => $nip,
        ]), false);

        $pdf = $this->get(route('admin.tes-selesai.literasi-digital.download', [
            'idEvent' => $eventId,
            'identifier' => $nip,
        ]));
        $pdf->assertOk();
        $this->assertStringContainsString('application/pdf', (string) $pdf->headers->get('content-type'));
        $this->assertStringStartsWith('%PDF', $pdf->getContent());

        $zip = $this->get(route('admin.tes-selesai.literasi-digital.download-all-laporan', [
            'idEvent' => $eventId,
            'tanggalTes' => '2026-11-07',
        ]));
        $zip->assertOk();
        $zip->assertHeader('content-type', 'application/zip');
        $this->assertStringStartsWith('PK', $zip->streamedContent());

        $this->get(route('admin.tes-selesai.literasi-digital.download', [
            'idEvent' => $eventId,
            'identifier' => '0000000000000000',
        ]))->assertNotFound();
    }

    public function test_ujian_yang_belum_selesai_tidak_punya_laporan(): void
    {
        $this->masukSebagaiAdmin();

        $eventId = $this->buatEvent();
        $nip = '998'.random_int(100000, 999999);
        $pesertaId = $this->buatPeserta($eventId, $nip);
        $ujian = $this->buatUjian($eventId, $pesertaId, false);
        $this->buatHasil($ujian, [
            'level_budaya' => 1,
            'level_cakap' => 1,
            'level_keamanan' => 1,
            'level_etika' => 1,
            'jpm' => 33.33,
            'kategori' => 'Kurang Optimal',
        ]);

        $this->get(route('admin.tes-selesai.literasi-digital.download', [
            'idEvent' => $eventId,
            'identifier' => $nip,
        ]))->assertNotFound();
    }

    private function masukSebagaiAdmin(): void
    {
        $admin = User::query()->where('role', 'admin')->where('is_active', 't')->first();
        $this->assertNotNull($admin);
        $this->actingAs($admin, 'admin');
    }

    private function barisDeskripsi(string $kode, int $level): RefDeskripsiLevelLiterasiDigital
    {
        return RefDeskripsiLevelLiterasiDigital::query()
            ->whereHas('pilar', fn ($query) => $query->where('kode', $kode))
            ->where('level', $level)
            ->firstOrFail();
    }

    private function barisSaran(string $kode, int $level): RefSaranPengembanganLiterasiDigital
    {
        return RefSaranPengembanganLiterasiDigital::query()
            ->whereHas('pilar', fn ($query) => $query->where('kode', $kode))
            ->where('level', $level)
            ->firstOrFail();
    }

    private function buatEvent(): int
    {
        return (int) DB::table('event')->insertGetId([
            'nama_event' => 'Uji laporan literasi '.uniqid(),
            'jumlah_peserta' => 1,
            'pin_ujian' => 'LD'.random_int(10, 99),
            'is_open' => 'false',
            'is_finished' => 'false',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function buatPeserta(int $eventId, string $nip): int
    {
        return (int) DB::table('peserta')->insertGetId([
            'event_id' => $eventId,
            'nama' => 'Siti Rahma',
            'nip' => $nip,
            'jabatan' => 'Analis SDM',
            'unit_kerja' => 'BKD',
            'instansi' => 'Pemerintah Provinsi Jawa Timur',
            'test_started_at' => '2026-11-07 08:00:00',
            'is_active' => 'true',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function buatUjian(int $eventId, int $pesertaId, bool $selesai): UjianLiterasiDigital
    {
        return UjianLiterasiDigital::query()->create([
            'event_id' => $eventId,
            'peserta_id' => $pesertaId,
            'jumlah_soal' => 60,
            'skor_budaya' => 5,
            'level_budaya' => 2,
            'skor_cakap' => 8,
            'level_cakap' => 3,
            'skor_keamanan' => 11,
            'level_keamanan' => 4,
            'skor_etika' => 14,
            'level_etika' => 5,
            'skor_total' => 38,
            'jpm' => 55.25,
            'kategori' => 'Kurang Optimal',
            'is_finished' => $selesai ? 'true' : 'false',
        ]);
    }

    /**
     * @param  array<string, mixed>  $nilai
     */
    private function buatHasil(UjianLiterasiDigital $ujian, array $nilai): void
    {
        DB::table('hasil_literasi_digital')->insert(array_merge([
            'event_id' => $ujian->event_id,
            'peserta_id' => $ujian->peserta_id,
            'ujian_id' => $ujian->id,
            'jumlah_soal' => 60,
            'skor_budaya' => 5,
            'skor_cakap' => 8,
            'skor_keamanan' => 11,
            'skor_etika' => 14,
            'skor_total' => 38,
            'created_at' => now(),
            'updated_at' => now(),
        ], $nilai));
    }
}
