<?php

namespace Tests\Feature;

use App\Livewire\Admin\Event\ShowPeserta;
use App\Models\Event;
use App\Models\EventGroup;
use App\Models\Peserta;
use App\Models\Pspk\UjianPspk;
use App\Models\User;
use App\Services\EventGroupPesertaSyncService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Auth;
use Livewire\Livewire;
use Tests\TestCase;

class EventGroupPesertaSyncTest extends TestCase
{
    use DatabaseTransactions;

    public function test_level_pspk_tidak_disalin_silang_dan_dashboard_mengikuti_baris_peserta(): void
    {
        Auth::login(User::query()->firstOrFail());

        $group = EventGroup::create([
            'nama' => 'Grup sync PSPK '.uniqid(),
            'is_active' => 'true',
        ]);

        $potensi = $this->makeEvent($group->id, 2, 'Potensi');
        $literasi = $this->makeEvent($group->id, 3, 'Literasi');
        $level1 = $this->makeEvent($group->id, 5, 'PSPK 1');
        $level2 = $this->makeEvent($group->id, 6, 'PSPK 2');

        $service = app(EventGroupPesertaSyncService::class);

        $pesertaLevel1 = $this->makePeserta($level1->id, $this->nip());
        $copied = $service->replicateFromPeserta($pesertaLevel1);

        $this->assertSame(2, $copied);
        $this->assertTrue($this->existsOn($potensi->id, $pesertaLevel1->nip));
        $this->assertTrue($this->existsOn($literasi->id, $pesertaLevel1->nip));
        $this->assertFalse($this->existsOn($level2->id, $pesertaLevel1->nip));

        $this->assertNotNull($service->conflictingPspkMessage($level2, $pesertaLevel1->nip, null));
        $this->assertNull($service->conflictingPspkMessage($potensi, $pesertaLevel1->nip, null));

        Livewire::test(ShowPeserta::class, ['idEvent' => $level2->id])
            ->set('jenis_peserta_id', 1)
            ->set('nama', 'Budi Santoso')
            ->set('nip', $pesertaLevel1->nip)
            ->set('jabatan', 'Analis')
            ->set('instansi', 'Jatim')
            ->set('unit_kerja', 'BKD')
            ->set('password', 'password123')
            ->call('save')
            ->assertHasErrors(['nip']);

        $this->assertFalse($this->existsOn($level2->id, $pesertaLevel1->nip));

        $tanpaLevel = $this->makePeserta($potensi->id, $this->nip());
        $service->replicateFromPeserta($tanpaLevel);

        $this->assertTrue($this->existsOn($literasi->id, $tanpaLevel->nip));
        $this->assertFalse($this->existsOn($level1->id, $tanpaLevel->nip));
        $this->assertFalse($this->existsOn($level2->id, $tanpaLevel->nip));

        $missing = $service->missingPspkLevelKeys($potensi, collect([$pesertaLevel1, $tanpaLevel]));
        $this->assertArrayNotHasKey('nip:'.$pesertaLevel1->nip, $missing);
        $this->assertArrayHasKey('nip:'.$tanpaLevel->nip, $missing);

        $this->assertNull($service->deletionBlockedReason($pesertaLevel1));
        $removed = $service->syncDeleteFromPeserta($pesertaLevel1, $pesertaLevel1->nip, null);
        $pesertaLevel1->delete();

        $this->assertSame(0, $removed);
        $this->assertFalse(Peserta::whereKey($pesertaLevel1->id)->exists());
        $this->assertTrue($this->existsOn($potensi->id, $pesertaLevel1->nip));
        $this->assertTrue($this->existsOn($literasi->id, $pesertaLevel1->nip));

        $pesertaLevel2 = $this->makePeserta($level2->id, $this->nip());
        $service->replicateFromPeserta($pesertaLevel2);
        $level2Row = Peserta::query()->where('event_id', $level2->id)->where('nip', $pesertaLevel2->nip)->firstOrFail();

        UjianPspk::create([
            'event_id' => $level2->id,
            'peserta_id' => $level2Row->id,
        ]);

        $this->assertNotNull($service->deletionBlockedReason($level2Row));

        $sharedRow = Peserta::query()->where('event_id', $potensi->id)->where('nip', $pesertaLevel2->nip)->firstOrFail();
        $this->assertNotNull($service->deletionBlockedReason($sharedRow));

        Livewire::test(ShowPeserta::class, ['idEvent' => $level2->id])
            ->set('selected_id', $level2Row->id)
            ->call('destroy');

        $this->assertTrue(Peserta::whereKey($level2Row->id)->exists());
        $this->assertTrue($this->existsOn($potensi->id, $pesertaLevel2->nip));
    }

    public function test_grup_tanpa_pspk_tetap_menyalin_dan_menghapus_ke_semua_event(): void
    {
        Auth::login(User::query()->firstOrFail());

        $group = EventGroup::create([
            'nama' => 'Grup tanpa PSPK '.uniqid(),
            'is_active' => 'true',
        ]);

        $potensi = $this->makeEvent($group->id, 2, 'Potensi');
        $literasi = $this->makeEvent($group->id, 3, 'Literasi');
        $service = app(EventGroupPesertaSyncService::class);

        $peserta = $this->makePeserta($potensi->id, $this->nip());
        $this->assertSame(1, $service->replicateFromPeserta($peserta));
        $this->assertTrue($this->existsOn($literasi->id, $peserta->nip));
        $this->assertSame([], $service->missingPspkLevelKeys($potensi, collect([$peserta])));

        $peserta->nama = 'Nama Baru';
        $peserta->save();
        $this->assertSame(1, $service->syncUpdateFromPeserta($peserta, $peserta->nip, null));
        $this->assertSame('NAMA BARU', Peserta::query()->where('event_id', $literasi->id)->where('nip', $peserta->nip)->firstOrFail()->getAttributes()['nama']);

        $this->assertSame(1, $service->syncDeleteFromPeserta($peserta, $peserta->nip, null));
        $peserta->delete();
        $this->assertFalse($this->existsOn($literasi->id, $peserta->nip));
        $this->assertFalse(Peserta::whereKey($peserta->id)->exists());
    }

    public function test_hapus_dari_tes_bersama_ikut_menghapus_level_yang_belum_dikerjakan(): void
    {
        Auth::login(User::query()->firstOrFail());

        $group = EventGroup::create([
            'nama' => 'Grup hapus bersama '.uniqid(),
            'is_active' => 'true',
        ]);

        $potensi = $this->makeEvent($group->id, 2, 'Potensi');
        $level1 = $this->makeEvent($group->id, 5, 'PSPK 1');
        $service = app(EventGroupPesertaSyncService::class);

        $peserta = $this->makePeserta($level1->id, $this->nip());
        $service->replicateFromPeserta($peserta);

        $shared = Peserta::query()->where('event_id', $potensi->id)->where('nip', $peserta->nip)->firstOrFail();
        $this->assertNull($service->deletionBlockedReason($shared));
        $this->assertSame(1, $service->syncDeleteFromPeserta($shared, $shared->nip, null));
        $shared->delete();

        $this->assertFalse($this->existsOn($level1->id, $peserta->nip));
        $this->assertFalse($this->existsOn($potensi->id, $peserta->nip));
    }

    public function test_nik_tidak_boleh_masuk_dua_level_pspk(): void
    {
        Auth::login(User::query()->firstOrFail());

        $group = EventGroup::create([
            'nama' => 'Grup NIK PSPK '.uniqid(),
            'is_active' => 'true',
        ]);

        $potensi = $this->makeEvent($group->id, 2, 'Potensi');
        $level1 = $this->makeEvent($group->id, 5, 'PSPK 1');
        $level2 = $this->makeEvent($group->id, 6, 'PSPK 2');
        $service = app(EventGroupPesertaSyncService::class);
        $nik = '9'.str_pad((string) random_int(1, 999999999999999), 15, '0', STR_PAD_LEFT);

        $peserta = Peserta::create([
            'event_id' => $level1->id,
            'jenis_peserta_id' => 2,
            'nama' => 'Peserta Non Asn',
            'nik' => $nik,
            'unit_kerja' => 'SDM',
            'instansi' => 'Jatim',
            'password' => bcrypt('password123'),
            'is_active' => 'true',
        ]);

        $this->assertSame(1, $service->replicateFromPeserta($peserta));
        $this->assertTrue(Peserta::query()->where('event_id', $potensi->id)->where('nik', $nik)->exists());
        $this->assertFalse(Peserta::query()->where('event_id', $level2->id)->where('nik', $nik)->exists());
        $this->assertNotNull($service->conflictingPspkMessage($level2, null, $nik));
        $this->assertArrayNotHasKey('nik:'.$nik, $service->missingPspkLevelKeys($potensi, collect([
            Peserta::query()->where('event_id', $potensi->id)->where('nik', $nik)->firstOrFail(),
        ])));
    }

    private function makeEvent(int $groupId, int $metodeTesId, string $nama): Event
    {
        return Event::create([
            'nama_event' => $nama.' '.uniqid(),
            'event_group_id' => $groupId,
            'metode_tes_id' => $metodeTesId,
            'tgl_mulai' => '08-11-2026',
            'tgl_selesai' => '08-11-2026',
            'jumlah_peserta' => 10,
            'pin_ujian' => '1234',
            'is_open' => 'false',
            'is_finished' => 'false',
        ]);
    }

    private function makePeserta(int $eventId, string $nip): Peserta
    {
        return Peserta::create([
            'event_id' => $eventId,
            'jenis_peserta_id' => 1,
            'nama' => 'Peserta Uji',
            'nip' => $nip,
            'jabatan' => 'Analis',
            'unit_kerja' => 'BKD',
            'instansi' => 'Jatim',
            'password' => bcrypt('password123'),
            'is_active' => 'true',
        ]);
    }

    private function existsOn(int $eventId, string $nip): bool
    {
        return Peserta::query()->where('event_id', $eventId)->where('nip', $nip)->exists();
    }

    private function nip(): string
    {
        return '9'.str_pad((string) random_int(1, 99999999999999999), 17, '0', STR_PAD_LEFT);
    }
}
