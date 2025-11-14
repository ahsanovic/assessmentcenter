<?php

namespace App\Livewire\Peserta\TesPspk;

use App\Models\Peserta;
use App\Models\Pspk\SoalPspk;
use App\Models\Pspk\UjianPspk;
use App\Models\RefAspekPspk;
use App\Models\SettingWaktuTes;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.peserta.app', ['title' => 'Dashboard'])]
class Dashboard extends Component
{
    public function start()
    {
        $test_started = Peserta::where('id', auth()->guard('peserta')->user()->id)->first(['id']);
        if ($test_started) {
            $test_started->test_started_at = now();
            $test_started->save();
        }

        $ujian_selesai = UjianPspk::where('event_id', Auth::guard('peserta')->user()->event_id)
            ->where('peserta_id', Auth::guard('peserta')->user()->id)
            ->where('is_finished', 'true')
            ->count();

        if ($ujian_selesai > 0) {
            return $this->redirect(route('peserta.tes-pspk.hasil'), navigate: true);
        }

        $ujian_berlangsung = UjianPspk::where('event_id', Auth::guard('peserta')->user()->event_id)
            ->where('peserta_id', Auth::guard('peserta')->user()->id)
            ->where('is_finished', 'false')
            ->count();

        if ($ujian_berlangsung > 0) {
            return $this->redirect(route('peserta.tes-pspk.ujian', ['id' => 1]), navigate: true);
        } else {
            $metode_tes_id = Auth::guard('peserta')->user()->event->metode_tes_id;
            switch ($metode_tes_id) {
                case '5': // PSPK level 1
                    $level_pspk = 1;
                    break;
            }

            $soal = SoalPspk::where('level_pspk_id', $level_pspk)
                ->inRandomOrder()
                ->get(['id', 'kunci_jawaban']);

            $jumlah_soal = $soal->count();
            $soal_id = $soal->implode('id', ',');
            $jawaban_kosong = implode(',', array_fill(0, $jumlah_soal, 0));

            $durasi_tes = SettingWaktuTes::where('is_active', 'true')->where('jenis_tes', 7)->first(['waktu']); // 7 = PSPK Level 1
            $waktu_tes_berakhir = now()->addMinutes($durasi_tes->waktu);

            $aspek_list = RefAspekPspk::pluck('kode_aspek')->toArray();
            $skor_awal = array_fill_keys($aspek_list, 0);

            $ujian = new UjianPspk();
            $ujian->event_id = Auth::guard('peserta')->user()->event_id;
            $ujian->peserta_id = Auth::guard('peserta')->user()->id;
            $ujian->soal_id = $soal_id;
            $ujian->jawaban = $jawaban_kosong;
            $ujian->kunci_jawaban = $soal->implode('kunci_jawaban', ',');
            $ujian->skor_aspek = $skor_awal;
            $ujian->waktu_tes_berakhir = $waktu_tes_berakhir;
            $ujian->save();

            return $this->redirect(route('peserta.tes-pspk.ujian', ['id' => 1]), navigate: true);
        }
    }

    public function render()
    {
        return view('livewire.peserta.tes-pspk.dashboard');
    }
}
