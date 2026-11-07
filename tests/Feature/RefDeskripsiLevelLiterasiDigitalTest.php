<?php

namespace Tests\Feature;

use App\Livewire\Admin\LiterasiDigital\RefDeskripsiLevel\Index;
use App\Models\LiterasiDigital\RefDeskripsiLevelLiterasiDigital;
use App\Models\LiterasiDigital\RefDeskripsiLiterasiDigitalTerbitan;
use App\Models\LiterasiDigital\RefDeskripsiLiterasiDigitalTerbitanItem;
use App\Models\LiterasiDigital\RefDeskripsiLiterasiDigitalVersi;
use App\Models\LiterasiDigital\RefPilarLiterasiDigital;
use App\Models\LiterasiDigital\UjianLiterasiDigital;
use App\Models\User;
use App\Services\LiterasiDigital\DeskripsiLevelLiterasiDigitalService;
use App\Services\LiterasiDigital\LiterasiDigitalUjianService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Tests\TestCase;

class RefDeskripsiLevelLiterasiDigitalTest extends TestCase
{
    use DatabaseTransactions;

    public function test_tamu_tidak_bisa_membuka_halaman(): void
    {
        $this->get(route('admin.ref-deskripsi-literasi-digital'))
            ->assertRedirect();
    }

    public function test_dua_puluh_deskripsi_kosong_dan_hanya_teks_yang_berubah(): void
    {
        $admin = User::query()->where('role', 'admin')->where('is_active', 't')->first();
        $this->assertNotNull($admin);
        $this->actingAs($admin, 'admin');

        $pilar = RefPilarLiterasiDigital::query()->orderBy('urutan')->orderBy('id')->get();
        $this->assertCount(4, $pilar);

        $semua = RefDeskripsiLevelLiterasiDigital::query()->orderBy('pilar_id')->orderBy('level')->get();
        $this->assertCount(20, $semua);
        $awal = $semua->mapWithKeys(fn (RefDeskripsiLevelLiterasiDigital $baris) => [$baris->id => $baris->deskripsi]);

        foreach ($pilar as $item) {
            $level = $item->deskripsiLevel()->orderBy('level')->pluck('level')->map(fn ($nilai) => (int) $nilai)->all();
            $this->assertSame([1, 2, 3, 4, 5], $level);
        }

        $baris = RefDeskripsiLevelLiterasiDigital::query()
            ->whereHas('pilar', fn ($query) => $query->where('kode', 'etika'))
            ->where('level', 5)
            ->firstOrFail();

        Livewire::test(Index::class)
            ->assertSee('Budaya Digital')
            ->assertSee('Cakap Digital')
            ->assertSee('Keamanan dan Keselamatan Digital')
            ->assertSee('Etika Digital')
            ->assertSee('Mengisi sel kosong belum mengunci event yang sudah punya hasil')
            ->assertSee('13–15')
            ->call('edit', $baris->id)
            ->assertSet('namaPilar', 'Etika Digital')
            ->assertSet('level', 5)
            ->assertSet('rentang', '13–15')
            ->set('level', 1)
            ->set('namaPilar', 'Bukan pilar')
            ->set('deskripsi', '  Narasi etika level lima.  ')
            ->call('save')
            ->assertHasNoErrors()
            ->assertSet('showModal', false);

        $baris->refresh();
        $this->assertSame(5, (int) $baris->level);
        $this->assertSame('Narasi etika level lima.', $baris->deskripsi);

        $lain = RefDeskripsiLevelLiterasiDigital::query()->whereKeyNot($baris->id)->get();
        foreach ($lain as $item) {
            $this->assertSame($awal[$item->id], $item->deskripsi);
        }

        Livewire::test(Index::class)
            ->call('edit', $baris->id)
            ->set('deskripsi', '   ')
            ->call('save')
            ->assertHasNoErrors()
            ->assertSee('Belum diisi');

        $this->assertNull($baris->refresh()->deskripsi);

        Livewire::test(Index::class)
            ->call('edit', $baris->id)
            ->set('deskripsi', str_repeat('a', 5001))
            ->call('save')
            ->assertHasErrors(['deskripsi' => 'max']);

        $this->assertNull($baris->refresh()->deskripsi);
    }

    public function test_draft_awal_diterbitkan_sekali_tanpa_mengunci_event(): void
    {
        $this->isolasiDraftKosong();
        $service = new DeskripsiLevelLiterasiDigitalService;
        $etika = $this->baris('etika', 5);
        $etika->deskripsi = "  Sudah ada\r\n";
        $etika->save();

        $event = $this->buatEvent();
        $this->buatHasil($this->buatUjian($event, $this->buatPeserta($event)));

        $terbitanId = $service->publikasikanDraftAwal();
        $this->assertNotNull($terbitanId);
        $this->assertNull($service->publikasikanDraftAwal());
        $this->assertNull($this->pin($event));
        $this->assertSame('Sudah ada', $etika->refresh()->deskripsi);
        $this->assertSame('Sudah ada', $service->teksUntuk($event, (int) $etika->pilar_id, 5));
        $this->assertSame(1, RefDeskripsiLiterasiDigitalVersi::query()->count());
        $this->assertSame(20, RefDeskripsiLiterasiDigitalTerbitanItem::query()->where('terbitan_id', $terbitanId)->count());
    }

    public function test_perubahan_pertama_ikut_membawa_deskripsi_lain_yang_sudah_ada(): void
    {
        $this->isolasiDraftKosong();
        $service = new DeskripsiLevelLiterasiDigitalService;
        $etika = $this->baris('etika', 5);
        $budaya = $this->baris('budaya', 1);
        $budaya->deskripsi = 'Teks budaya tetap';
        $budaya->save();

        $event = $this->buatEvent();
        $this->buatHasil($this->buatUjian($event, $this->buatPeserta($event)));

        $this->assertTrue($service->simpan($etika, 'Teks etika baru'));
        $this->assertNull($this->pin($event));
        $this->assertSame('Teks etika baru', $service->teksUntuk($event, (int) $etika->pilar_id, 5));
        $this->assertSame('Teks budaya tetap', $service->teksUntuk($event, (int) $budaya->pilar_id, 1));
        $this->assertSame(2, RefDeskripsiLiterasiDigitalVersi::query()->count());
    }

    public function test_terbitan_hanya_menambah_teks_yang_berubah_dan_event_lama_tetap(): void
    {
        $this->isolasiDraftKosong();
        $service = new DeskripsiLevelLiterasiDigitalService;
        $etika = $this->baris('etika', 5);
        $budaya = $this->baris('budaya', 1);
        $penanda = uniqid();
        $teksEtika = "Jaga privasi {$penanda}\n- tutup sesi";
        $teksBudaya = "Biasakan unggah {$penanda}";

        $versiAwal = RefDeskripsiLiterasiDigitalVersi::query()->count();
        $terbitanAwal = RefDeskripsiLiterasiDigitalTerbitan::query()->count();

        $eventLama = $this->buatEvent();
        $this->buatHasil($this->buatUjian($eventLama, $this->buatPeserta($eventLama)));
        $eventBelumHasil = $this->buatEvent();

        $this->assertNull($service->teksUntuk($eventLama, (int) $etika->pilar_id, 5));

        $this->assertTrue($service->simpan($etika, "  {$teksEtika}\r\n"));
        $this->assertFalse($service->simpan($etika->refresh(), "  {$teksEtika}  "));

        $this->assertSame($versiAwal + 1, RefDeskripsiLiterasiDigitalVersi::query()->count());
        $this->assertSame($terbitanAwal + 1, RefDeskripsiLiterasiDigitalTerbitan::query()->count());

        $terbitanPertama = (int) RefDeskripsiLiterasiDigitalTerbitan::query()->orderByDesc('id')->value('id');
        $this->assertSame(20, RefDeskripsiLiterasiDigitalTerbitanItem::query()->where('terbitan_id', $terbitanPertama)->count());
        $this->assertSame(1, RefDeskripsiLiterasiDigitalTerbitanItem::query()->where('terbitan_id', $terbitanPertama)->whereNotNull('versi_id')->count());
        $this->assertNull($this->pin($eventLama));
        $this->assertNull($this->pinSaran($eventLama));
        $this->assertNull($this->pin($eventBelumHasil));
        $this->assertSame($teksEtika, $service->teksUntuk($eventLama, (int) $etika->pilar_id, 5));
        $this->assertNull($this->pin($eventLama));
        $this->assertSame($teksEtika, $service->teksUntuk($eventBelumHasil, (int) $etika->pilar_id, 5));

        $this->assertTrue($service->simpan($budaya, $teksBudaya));

        $this->assertSame($versiAwal + 2, RefDeskripsiLiterasiDigitalVersi::query()->count());
        $terbitanKedua = (int) RefDeskripsiLiterasiDigitalTerbitan::query()->orderByDesc('id')->value('id');
        $this->assertNotSame($terbitanPertama, $terbitanKedua);
        $this->assertSame(
            $this->versiId($terbitanPertama, (int) $etika->pilar_id, 5),
            $this->versiId($terbitanKedua, (int) $etika->pilar_id, 5)
        );
        $this->assertNull($this->pin($eventLama));
        $this->assertSame($teksEtika, $service->teksUntuk($eventLama, (int) $etika->pilar_id, 5));
        $this->assertSame($teksBudaya, $service->teksUntuk($eventLama, (int) $budaya->pilar_id, 1));
        $this->assertSame($teksBudaya, $service->teksUntuk($eventBelumHasil, (int) $budaya->pilar_id, 1));

        $sementara = "Sementara {$penanda}";
        $this->assertTrue($service->simpan($etika->refresh(), $sementara));
        $this->assertSame($terbitanKedua, $this->pin($eventLama));
        $this->assertNull($this->pinSaran($eventLama));
        $this->assertNull($this->pin($eventBelumHasil));
        $this->assertSame($teksEtika, $service->teksUntuk($eventLama, (int) $etika->pilar_id, 5));
        $this->assertSame($teksBudaya, $service->teksUntuk($eventLama, (int) $budaya->pilar_id, 1));
        $this->assertSame($sementara, $service->teksUntuk($eventBelumHasil, (int) $etika->pilar_id, 5));
        $this->assertSame($versiAwal + 3, RefDeskripsiLiterasiDigitalVersi::query()->count());
        $this->assertTrue($service->simpan($etika->refresh(), $teksEtika));
        $this->assertSame($terbitanKedua, $this->pin($eventLama));
        $this->assertSame($versiAwal + 3, RefDeskripsiLiterasiDigitalVersi::query()->count());
        $this->assertSame(
            $this->versiId($terbitanPertama, (int) $etika->pilar_id, 5),
            $this->versiId((int) RefDeskripsiLiterasiDigitalTerbitan::query()->orderByDesc('id')->value('id'), (int) $etika->pilar_id, 5)
        );

        $this->assertTrue($service->simpan($etika->refresh(), null));
        $this->assertTrue($service->simpan($budaya->refresh(), '   '));
        $this->assertSame($versiAwal + 3, RefDeskripsiLiterasiDigitalVersi::query()->count());
        $terbitanKosong = (int) RefDeskripsiLiterasiDigitalTerbitan::query()->orderByDesc('id')->value('id');
        $this->assertSame(0, RefDeskripsiLiterasiDigitalTerbitanItem::query()->where('terbitan_id', $terbitanKosong)->whereNotNull('versi_id')->count());
        $this->assertSame($terbitanKedua, $this->pin($eventLama));
        $this->assertSame($teksEtika, $service->teksUntuk($eventLama, (int) $etika->pilar_id, 5));

        $eventMenunggu = $this->buatEvent();
        $this->buatHasil($this->buatUjian($eventMenunggu, $this->buatPeserta($eventMenunggu)));
        $this->assertNull($this->pin($eventMenunggu));

        $teksBaru = "Rumusan baru {$penanda}";
        $this->assertTrue($service->simpan($etika->refresh(), $teksBaru));
        $terbitanBaru = (int) RefDeskripsiLiterasiDigitalTerbitan::query()->orderByDesc('id')->value('id');
        $this->assertSame($terbitanKedua, $this->pin($eventLama));
        $this->assertNull($this->pin($eventMenunggu));
        $this->assertSame($teksEtika, $service->teksUntuk($eventLama, (int) $etika->pilar_id, 5));
        $this->assertSame($teksBaru, $service->teksUntuk($eventMenunggu, (int) $etika->pilar_id, 5));
        $service->kunciUntukEvent($eventMenunggu);
        $this->assertSame($terbitanBaru, $this->pin($eventMenunggu));
        $this->assertSame($terbitanKedua, $this->pin($eventLama));
        $this->assertNull($this->pin($eventBelumHasil));
        $this->assertSame($versiAwal + 4, RefDeskripsiLiterasiDigitalVersi::query()->count());
    }

    public function test_selesai_ujian_mengunci_terbitan_deskripsi_yang_berlaku(): void
    {
        $this->isolasiDraftKosong();
        $service = new DeskripsiLevelLiterasiDigitalService;
        $ujianService = new LiterasiDigitalUjianService;
        $etika = $this->baris('etika', 5);
        $penanda = uniqid();
        $teksA = "Deskripsi selesai {$penanda}";
        $teksB = "Deskripsi selesai berikutnya {$penanda}";

        $event = $this->buatEvent();
        $ujian = $this->buatUjian($event, $this->buatPeserta($event));
        $ujianService->selesai($ujian);
        $this->assertNull($this->pin($event));
        $pinSaranAwal = $this->pinSaran($event);

        $this->assertTrue($service->simpan($etika, $teksA));
        $this->assertNull($this->pin($event));
        $this->assertSame($pinSaranAwal, $this->pinSaran($event));
        $terbitanA = (int) RefDeskripsiLiterasiDigitalTerbitan::query()->orderByDesc('id')->value('id');

        $this->assertTrue($service->simpan($etika->refresh(), $teksB));
        $this->assertSame($terbitanA, $this->pin($event));
        $this->assertSame($teksA, $service->teksUntuk($event, (int) $etika->pilar_id, 5));
        $this->assertSame($pinSaranAwal, $this->pinSaran($event));

        $eventMenunggu = $this->buatEvent();
        $this->buatHasil($this->buatUjian($eventMenunggu, $this->buatPeserta($eventMenunggu)));

        $eventBaru = $this->buatEvent();
        $ujianBaru = $this->buatUjian($eventBaru, $this->buatPeserta($eventBaru));
        $ujianService->selesai($ujianBaru);

        $this->assertNull($this->pin($eventMenunggu));
        $this->assertSame(
            (int) RefDeskripsiLiterasiDigitalTerbitan::query()->orderByDesc('id')->value('id'),
            $this->pin($eventBaru)
        );
        $this->assertSame($pinSaranAwal, $this->pinSaran($event));
        $this->assertSame($teksB, $service->teksUntuk($eventBaru, (int) $etika->pilar_id, 5));
        $this->assertSame($teksA, $service->teksUntuk($event, (int) $etika->pilar_id, 5));
    }

    private function isolasiDraftKosong(): void
    {
        DB::table('event')->whereNotNull('deskripsi_literasi_digital_terbitan_id')->update([
            'deskripsi_literasi_digital_terbitan_id' => null,
            'updated_at' => now(),
        ]);
        DB::table('ref_deskripsi_literasi_digital_terbitan_item')->delete();
        DB::table('ref_deskripsi_literasi_digital_terbitan')->delete();
        DB::table('ref_deskripsi_literasi_digital_versi')->delete();
        DB::table('ref_deskripsi_level_literasi_digital')->update([
            'deskripsi' => null,
            'updated_at' => now(),
        ]);
    }

    private function baris(string $kode, int $level): RefDeskripsiLevelLiterasiDigital
    {
        return RefDeskripsiLevelLiterasiDigital::query()
            ->whereHas('pilar', fn ($query) => $query->where('kode', $kode))
            ->where('level', $level)
            ->firstOrFail();
    }

    private function buatEvent(): int
    {
        return (int) DB::table('event')->insertGetId([
            'nama_event' => 'Uji deskripsi '.uniqid(),
            'jumlah_peserta' => 1,
            'pin_ujian' => 'LD01',
            'is_open' => 'false',
            'is_finished' => 'false',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function buatPeserta(int $eventId): int
    {
        return (int) DB::table('peserta')->insertGetId([
            'event_id' => $eventId,
            'nama' => 'Peserta Deskripsi',
            'is_active' => 'true',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function buatUjian(int $eventId, int $pesertaId): UjianLiterasiDigital
    {
        return UjianLiterasiDigital::query()->create([
            'event_id' => $eventId,
            'peserta_id' => $pesertaId,
            'jumlah_soal' => 0,
            'skor_budaya' => 0,
            'level_budaya' => 1,
            'skor_cakap' => 0,
            'level_cakap' => 1,
            'skor_keamanan' => 0,
            'level_keamanan' => 1,
            'skor_etika' => 0,
            'level_etika' => 1,
            'skor_total' => 0,
            'jpm' => 0,
            'is_finished' => 'false',
        ]);
    }

    private function buatHasil(UjianLiterasiDigital $ujian): void
    {
        DB::table('hasil_literasi_digital')->insert([
            'event_id' => $ujian->event_id,
            'peserta_id' => $ujian->peserta_id,
            'ujian_id' => $ujian->id,
            'jumlah_soal' => 0,
            'skor_budaya' => 0,
            'level_budaya' => 1,
            'skor_cakap' => 0,
            'level_cakap' => 1,
            'skor_keamanan' => 0,
            'level_keamanan' => 1,
            'skor_etika' => 0,
            'level_etika' => 1,
            'skor_total' => 0,
            'jpm' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function pin(int $eventId): ?int
    {
        $id = DB::table('event')->where('id', $eventId)->value('deskripsi_literasi_digital_terbitan_id');

        return $id === null ? null : (int) $id;
    }

    private function pinSaran(int $eventId): ?int
    {
        $id = DB::table('event')->where('id', $eventId)->value('saran_literasi_digital_terbitan_id');

        return $id === null ? null : (int) $id;
    }

    private function versiId(int $terbitanId, int $pilarId, int $level): ?int
    {
        $id = RefDeskripsiLiterasiDigitalTerbitanItem::query()
            ->where('terbitan_id', $terbitanId)
            ->where('pilar_id', $pilarId)
            ->where('level', $level)
            ->value('versi_id');

        return $id === null ? null : (int) $id;
    }
}
