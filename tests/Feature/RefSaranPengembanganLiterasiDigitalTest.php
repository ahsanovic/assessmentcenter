<?php

namespace Tests\Feature;

use App\Livewire\Admin\LiterasiDigital\RefSaranPengembangan\Index;
use App\Models\LiterasiDigital\RefPilarLiterasiDigital;
use App\Models\LiterasiDigital\RefSaranLiterasiDigitalTerbitan;
use App\Models\LiterasiDigital\RefSaranLiterasiDigitalTerbitanItem;
use App\Models\LiterasiDigital\RefSaranLiterasiDigitalVersi;
use App\Models\LiterasiDigital\RefSaranPengembanganLiterasiDigital;
use App\Models\LiterasiDigital\UjianLiterasiDigital;
use App\Models\User;
use App\Services\LiterasiDigital\LiterasiDigitalUjianService;
use App\Services\LiterasiDigital\SaranPengembanganLiterasiDigitalService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Tests\TestCase;

class RefSaranPengembanganLiterasiDigitalTest extends TestCase
{
    use DatabaseTransactions;

    public function test_tamu_tidak_bisa_membuka_halaman(): void
    {
        $this->get(route('admin.ref-saran-literasi-digital'))
            ->assertRedirect();
    }

    public function test_halaman_menampilkan_dua_puluh_baris_dan_hanya_teks_yang_berubah(): void
    {
        $this->masukSebagaiAdmin();

        $this->get(route('admin.ref-saran-literasi-digital'))
            ->assertOk()
            ->assertSee('Referensi Saran Pengembangan')
            ->assertSee('Mengisi sel kosong belum mengunci event yang sudah punya hasil');

        $pilar = RefPilarLiterasiDigital::query()->orderBy('urutan')->orderBy('id')->get();
        $this->assertCount(4, $pilar);

        $semua = RefSaranPengembanganLiterasiDigital::query()->orderBy('pilar_id')->orderBy('level')->get();
        $this->assertCount(20, $semua);
        $awal = $semua->mapWithKeys(fn (RefSaranPengembanganLiterasiDigital $baris) => [$baris->id => $baris->saran]);

        foreach ($pilar as $item) {
            $level = $item->saranPengembangan()->orderBy('level')->pluck('level')->map(fn ($nilai) => (int) $nilai)->all();
            $this->assertSame([1, 2, 3, 4, 5], $level);
        }

        $baris = $this->baris('etika', 5);
        $teks = "  ## Etika\n\n- jaga data pribadi  ";

        Livewire::test(Index::class)
            ->assertSee('Budaya Digital')
            ->assertSee('Cakap Digital')
            ->assertSee('Keamanan dan Keselamatan Digital')
            ->assertSee('Etika Digital')
            ->assertSee('13–15')
            ->call('edit', $baris->id)
            ->assertSet('namaPilar', 'Etika Digital')
            ->assertSet('level', 5)
            ->assertSet('rentang', '13–15')
            ->assertSee('data-markdown-editor', false)
            ->assertDontSee('wire:model="saran"', false)
            ->set('level', 1)
            ->set('namaPilar', 'Bukan pilar')
            ->set('saran', $teks)
            ->call('save')
            ->assertHasNoErrors()
            ->assertSet('showModal', false)
            ->assertSee('jaga data pribadi');

        $baris->refresh();
        $this->assertSame(5, (int) $baris->level);
        $this->assertSame("## Etika\n\n- jaga data pribadi", $baris->saran);

        $lain = RefSaranPengembanganLiterasiDigital::query()->whereKeyNot($baris->id)->get();
        foreach ($lain as $item) {
            $this->assertSame($awal[$item->id], $item->saran);
        }

        Livewire::test(Index::class)
            ->call('edit', $baris->id)
            ->set('saran', '   ')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertNull($baris->refresh()->saran);

        Livewire::test(Index::class)
            ->call('edit', $baris->id)
            ->set('saran', str_repeat('a', 10001))
            ->call('save')
            ->assertHasErrors(['saran' => 'max']);

        $this->assertNull($baris->refresh()->saran);
    }

    public function test_terbitan_hanya_menambah_teks_yang_berubah_dan_event_lama_tetap(): void
    {
        $service = new SaranPengembanganLiterasiDigitalService;
        $etika = $this->baris('etika', 5);
        $budaya = $this->baris('budaya', 1);
        $penanda = uniqid();
        $teksEtika = "Jaga privasi {$penanda}\n- tutup sesi";
        $teksBudaya = "Biasakan unggah {$penanda}";

        $versiAwal = RefSaranLiterasiDigitalVersi::query()->count();
        $terbitanAwal = RefSaranLiterasiDigitalTerbitan::query()->count();

        $eventLama = $this->buatEvent();
        $this->buatHasil($this->buatUjian($eventLama, $this->buatPeserta($eventLama)));
        $eventBelumHasil = $this->buatEvent();

        $this->assertNull($service->teksUntuk($eventLama, (int) $etika->pilar_id, 5));

        $this->assertTrue($service->simpan($etika, $teksEtika));
        $this->assertFalse($service->simpan($etika->refresh(), "  {$teksEtika}  "));

        $this->assertSame($versiAwal + 1, RefSaranLiterasiDigitalVersi::query()->count());
        $this->assertSame($terbitanAwal + 1, RefSaranLiterasiDigitalTerbitan::query()->count());

        $terbitanPertama = (int) RefSaranLiterasiDigitalTerbitan::query()->orderByDesc('id')->value('id');
        $this->assertSame(20, RefSaranLiterasiDigitalTerbitanItem::query()->where('terbitan_id', $terbitanPertama)->count());
        $this->assertSame(1, RefSaranLiterasiDigitalTerbitanItem::query()->where('terbitan_id', $terbitanPertama)->whereNotNull('versi_id')->count());
        $this->assertNull($this->pin($eventLama));
        $this->assertNull($this->pin($eventBelumHasil));
        $this->assertSame($teksEtika, $service->teksUntuk($eventLama, (int) $etika->pilar_id, 5));
        $this->assertNull($this->pin($eventLama));
        $this->assertSame($teksEtika, $service->teksUntuk($eventBelumHasil, (int) $etika->pilar_id, 5));
        $this->assertNull($this->pin($eventBelumHasil));

        $this->assertTrue($service->simpan($budaya, $teksBudaya));

        $this->assertSame($versiAwal + 2, RefSaranLiterasiDigitalVersi::query()->count());
        $terbitanKedua = (int) RefSaranLiterasiDigitalTerbitan::query()->orderByDesc('id')->value('id');
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
        $this->assertNull($this->pin($eventBelumHasil));
        $this->assertSame($teksEtika, $service->teksUntuk($eventLama, (int) $etika->pilar_id, 5));
        $this->assertSame($teksBudaya, $service->teksUntuk($eventLama, (int) $budaya->pilar_id, 1));
        $this->assertSame($sementara, $service->teksUntuk($eventBelumHasil, (int) $etika->pilar_id, 5));
        $this->assertSame($versiAwal + 3, RefSaranLiterasiDigitalVersi::query()->count());
        $this->assertTrue($service->simpan($etika->refresh(), $teksEtika));
        $this->assertSame($terbitanKedua, $this->pin($eventLama));
        $this->assertSame($versiAwal + 3, RefSaranLiterasiDigitalVersi::query()->count());
        $this->assertSame(
            $this->versiId($terbitanPertama, (int) $etika->pilar_id, 5),
            $this->versiId((int) RefSaranLiterasiDigitalTerbitan::query()->orderByDesc('id')->value('id'), (int) $etika->pilar_id, 5)
        );

        $this->assertTrue($service->simpan($etika->refresh(), null));
        $this->assertTrue($service->simpan($budaya->refresh(), '   '));
        $this->assertSame($versiAwal + 3, RefSaranLiterasiDigitalVersi::query()->count());
        $terbitanKosong = (int) RefSaranLiterasiDigitalTerbitan::query()->orderByDesc('id')->value('id');
        $this->assertSame(0, RefSaranLiterasiDigitalTerbitanItem::query()->where('terbitan_id', $terbitanKosong)->whereNotNull('versi_id')->count());
        $this->assertSame($terbitanKedua, $this->pin($eventLama));
        $this->assertSame($teksEtika, $service->teksUntuk($eventLama, (int) $etika->pilar_id, 5));

        $eventMenunggu = $this->buatEvent();
        $this->buatHasil($this->buatUjian($eventMenunggu, $this->buatPeserta($eventMenunggu)));
        $this->assertNull($this->pin($eventMenunggu));

        $teksBaru = "Rumusan baru {$penanda}";
        $this->assertTrue($service->simpan($etika->refresh(), $teksBaru));
        $terbitanBaru = (int) RefSaranLiterasiDigitalTerbitan::query()->orderByDesc('id')->value('id');
        $this->assertSame($terbitanKedua, $this->pin($eventLama));
        $this->assertNull($this->pin($eventMenunggu));
        $this->assertSame($teksEtika, $service->teksUntuk($eventLama, (int) $etika->pilar_id, 5));
        $this->assertSame($teksBaru, $service->teksUntuk($eventMenunggu, (int) $etika->pilar_id, 5));
        $this->assertNull($this->pin($eventMenunggu));
        $service->kunciUntukEvent($eventMenunggu);
        $this->assertSame($terbitanBaru, $this->pin($eventMenunggu));
        $this->assertSame($terbitanKedua, $this->pin($eventLama));
        $this->assertNull($this->pin($eventBelumHasil));
        $this->assertSame($versiAwal + 4, RefSaranLiterasiDigitalVersi::query()->count());
    }

    public function test_selesai_ujian_mengunci_terbitan_yang_berlaku(): void
    {
        $service = new SaranPengembanganLiterasiDigitalService;
        $ujianService = new LiterasiDigitalUjianService;
        $etika = $this->baris('etika', 5);
        $penanda = uniqid();
        $teksA = "Saran selesai {$penanda}";
        $teksB = "Saran selesai berikutnya {$penanda}";

        $event = $this->buatEvent();
        $ujian = $this->buatUjian($event, $this->buatPeserta($event));
        $ujianService->selesai($ujian);
        $this->assertNull($this->pin($event));

        $this->assertTrue($service->simpan($etika, $teksA));
        $this->assertNull($this->pin($event));
        $terbitanA = (int) RefSaranLiterasiDigitalTerbitan::query()->orderByDesc('id')->value('id');

        $this->assertTrue($service->simpan($etika->refresh(), $teksB));
        $this->assertSame($terbitanA, $this->pin($event));
        $this->assertSame($teksA, $service->teksUntuk($event, (int) $etika->pilar_id, 5));

        $eventMenunggu = $this->buatEvent();
        $this->buatHasil($this->buatUjian($eventMenunggu, $this->buatPeserta($eventMenunggu)));

        $eventBaru = $this->buatEvent();
        $ujianBaru = $this->buatUjian($eventBaru, $this->buatPeserta($eventBaru));
        $ujianService->selesai($ujianBaru);

        $this->assertNull($this->pin($eventMenunggu));
        $this->assertSame(
            (int) RefSaranLiterasiDigitalTerbitan::query()->orderByDesc('id')->value('id'),
            $this->pin($eventBaru)
        );
        $this->assertSame($teksB, $service->teksUntuk($eventBaru, (int) $etika->pilar_id, 5));
        $this->assertSame($teksA, $service->teksUntuk($event, (int) $etika->pilar_id, 5));
    }

    private function masukSebagaiAdmin(): void
    {
        $admin = User::query()->where('role', 'admin')->where('is_active', 't')->first();
        $this->assertNotNull($admin);
        $this->actingAs($admin, 'admin');
    }

    private function baris(string $kode, int $level): RefSaranPengembanganLiterasiDigital
    {
        return RefSaranPengembanganLiterasiDigital::query()
            ->whereHas('pilar', fn ($query) => $query->where('kode', $kode))
            ->where('level', $level)
            ->firstOrFail();
    }

    private function buatEvent(): int
    {
        return (int) DB::table('event')->insertGetId([
            'nama_event' => 'Uji saran '.uniqid(),
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
            'nama' => 'Peserta Saran',
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
        $id = DB::table('event')->where('id', $eventId)->value('saran_literasi_digital_terbitan_id');

        return $id === null ? null : (int) $id;
    }

    private function versiId(int $terbitanId, int $pilarId, int $level): ?int
    {
        $id = RefSaranLiterasiDigitalTerbitanItem::query()
            ->where('terbitan_id', $terbitanId)
            ->where('pilar_id', $pilarId)
            ->where('level', $level)
            ->value('versi_id');

        return $id === null ? null : (int) $id;
    }
}
