<?php

namespace Tests\Feature;

use App\Livewire\Admin\HasilResponden\Show;
use App\Livewire\Peserta\Kuesioner\Index as KuesionerIndex;
use App\Models\Event;
use App\Models\EventGroup;
use App\Models\JawabanResponden;
use App\Models\Kuesioner;
use App\Models\Peserta;
use App\Services\PesertaParticipationService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Auth;
use Livewire\Livewire;
use Tests\TestCase;

class HasilRespondenShowTest extends TestCase
{
    use DatabaseTransactions;

    public function test_detail_menampilkan_jawaban_meski_peserta_tercatat_di_event_lain(): void
    {
        $group = EventGroup::create([
            'nama' => 'Grup hasil responden '.uniqid(),
            'is_active' => 'true',
        ]);

        $kuesionerEvent = $this->makeEvent($group->id, Event::METODE_KUESIONER_RESPONDEN, 'Kuesioner');
        $eventLain = $this->makeEvent($group->id, 7, 'PSPK');
        $nip = $this->nip();

        $pesertaEvent = $this->makePeserta($kuesionerEvent->id, $nip, 'Siti Event');
        $pesertaLain = $this->makePeserta($eventLain->id, $nip, 'Patrick Lintas');
        $belumMengisi = $this->makePeserta($kuesionerEvent->id, $this->nip(), 'Tidak Mengisi');

        JawabanResponden::create([
            'event_id' => $kuesionerEvent->id,
            'peserta_id' => $pesertaLain->id,
            'kuesioner_id' => '2,3',
            'skor' => '5,4',
            'jawaban_esai' => 'Perlu perbaikan jadwal',
        ]);
        JawabanResponden::create([
            'event_id' => $kuesionerEvent->id,
            'peserta_id' => $pesertaEvent->id,
            'kuesioner_id' => '2',
            'skor' => '3',
            'jawaban_esai' => null,
        ]);
        JawabanResponden::create([
            'event_id' => $eventLain->id,
            'peserta_id' => $pesertaLain->id,
            'kuesioner_id' => '2',
            'skor' => '1',
            'jawaban_esai' => 'Bukan event ini',
        ]);

        Livewire::test(Show::class, ['idEvent' => $kuesionerEvent->id])
            ->assertSee('PATRICK LINTAS')
            ->assertSee('SITI EVENT')
            ->assertSee('Perlu perbaikan jadwal')
            ->assertDontSee('TIDAK MENGISI')
            ->assertDontSee('Bukan event ini')
            ->assertDontSee('Tidak ada data peserta')
            ->set('search', 'patrick')
            ->assertSee('PATRICK LINTAS')
            ->assertDontSee('SITI EVENT');

        $this->assertNotNull($belumMengisi->id);
    }

    public function test_pengiriman_kuesioner_melekat_pada_peserta_event_yang_dikerjakan(): void
    {
        $group = EventGroup::create([
            'nama' => 'Grup kirim kuesioner '.uniqid(),
            'is_active' => 'true',
        ]);

        $kuesionerEvent = $this->makeEvent($group->id, Event::METODE_KUESIONER_RESPONDEN, 'Kuesioner');
        $eventLogin = $this->makeEvent($group->id, 7, 'PSPK');
        $nip = $this->nip();
        $pesertaKuesioner = $this->makePeserta($kuesionerEvent->id, $nip, 'Siti Aktif');
        $pesertaLogin = $this->makePeserta($eventLogin->id, $nip, 'Siti Login');

        JawabanResponden::create([
            'event_id' => $kuesionerEvent->id,
            'peserta_id' => $pesertaLogin->id,
            'kuesioner_id' => '2',
            'skor' => '1',
            'jawaban_esai' => 'Jawaban lama',
        ]);

        Auth::guard('peserta')->login($pesertaLogin);
        session([
            PesertaParticipationService::SESSION_GROUP_KEY => 'group_'.$group->id,
            PesertaParticipationService::SESSION_ACTIVE_PESERTA_ID => $pesertaKuesioner->id,
        ]);

        $component = Livewire::test(KuesionerIndex::class);
        foreach (Kuesioner::query()->where('is_active', 't')->get() as $item) {
            if ($item->is_esai === 't') {
                $component->set("jawaban_responden.{$item->id}.jawaban_esai", 'Saran dari peserta aktif');
            } else {
                $component->set("jawaban_responden.{$item->id}.skor", 4);
            }
        }

        $component->set('konfirmasi', true)->call('submit');

        $tersimpan = JawabanResponden::query()->where('event_id', $kuesionerEvent->id)->get();

        $this->assertCount(1, $tersimpan);
        $this->assertSame($pesertaKuesioner->id, (int) $tersimpan->first()->peserta_id);
        $this->assertSame('Saran dari peserta aktif', $tersimpan->first()->jawaban_esai);
    }

    private function makeEvent(int $groupId, int $metodeTesId, string $nama): Event
    {
        return Event::create([
            'nama_event' => $nama.' '.uniqid(),
            'event_group_id' => $groupId,
            'metode_tes_id' => $metodeTesId,
            'tgl_mulai' => '12-11-2026',
            'tgl_selesai' => '12-11-2026',
            'jumlah_peserta' => 10,
            'pin_ujian' => '1234',
            'is_open' => 'false',
            'is_finished' => 'false',
        ]);
    }

    private function makePeserta(int $eventId, string $nip, string $nama): Peserta
    {
        return Peserta::create([
            'event_id' => $eventId,
            'jenis_peserta_id' => 1,
            'nama' => $nama,
            'nip' => $nip,
            'jabatan' => 'Analis',
            'unit_kerja' => 'BKD',
            'instansi' => 'Jatim',
            'password' => bcrypt('password123'),
            'is_active' => 'true',
        ]);
    }

    private function nip(): string
    {
        return '9'.str_pad((string) random_int(1, 99999999999999999), 17, '0', STR_PAD_LEFT);
    }
}
