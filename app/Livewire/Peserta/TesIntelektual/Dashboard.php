<?php

namespace App\Livewire\Peserta\TesIntelektual;

use App\Models\Intelektual\SoalIntelektual;
use App\Models\Intelektual\UjianIntelektualSubTes1;
use App\Models\Intelektual\UjianIntelektualSubTes2;
use App\Models\Intelektual\UjianIntelektualSubTes3;
use App\Models\Peserta;
use App\Models\SettingWaktuTes;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.peserta.app', ['title' => 'Dashboard'])]
class Dashboard extends Component
{
    public function startSubTes1()
    {
        $test_started = Peserta::where('id', auth()->guard('peserta')->user()->id)->first(['id']);
        if ($test_started) {
            $test_started->test_started_at = now();
            $test_started->save();
        }

        $ujian_selesai = UjianIntelektualSubTes1::where('event_id', Auth::guard('peserta')->user()->event_id)
            ->where('peserta_id', Auth::guard('peserta')->user()->id)
            ->where('is_finished', 'true')
            ->count();

        if ($ujian_selesai > 0) {
            session()->flash('toast', [
                'type' => 'error',
                'message' => 'Anda sudah melakukan Tes Intelektual.'
            ]);
            return $this->redirect(route('peserta.tes-intelektual.home'), navigate: true);
        }

        $ujian_berlangsung = UjianIntelektualSubTes1::where('event_id', Auth::guard('peserta')->user()->event_id)
            ->where('peserta_id', Auth::guard('peserta')->user()->id)
            ->where('is_finished', 'false')
            ->count();

        if ($ujian_berlangsung > 0) {
            return $this->redirect(route('peserta.tes-intelektual.subtes1', ['id' => 1]), navigate: true);
        } else {
            $soal = SoalIntelektual::where('sub_tes', 1)->get(['id', 'kunci_jawaban']);

            $jumlah_soal = $soal->count();
            $soal_id = $soal->implode('id', ',');
            $jawaban_kosong = implode(',', array_fill(0, $jumlah_soal, 0));

            $durasi_tes = SettingWaktuTes::where('is_active', 'true')->where('jenis_tes', 3)->first(['waktu']);
            $waktu_tes_berakhir = now()->addMinutes($durasi_tes->waktu);

            $ujian = new UjianIntelektualSubTes1();
            $ujian->event_id = Auth::guard('peserta')->user()->event_id;
            $ujian->peserta_id = Auth::guard('peserta')->user()->id;
            $ujian->soal_id = $soal_id;
            $ujian->jawaban = $jawaban_kosong;
            $ujian->kunci_jawaban = $soal->implode('kunci_jawaban', ',');
            $ujian->nilai = 0;
            $ujian->waktu_tes_berakhir = $waktu_tes_berakhir;
            $ujian->save();

            return $this->redirect(route('peserta.tes-intelektual.subtes1', ['id' => 1]), navigate: true);
        }
    }

    public function startSubTes2()
    {
        $test_started = Peserta::where('id', auth()->guard('peserta')->user()->id)->first(['id']);
        if ($test_started) {
            $test_started->test_started_at = now();
            $test_started->save();
        }

        $ujian_selesai = UjianIntelektualSubTes2::where('event_id', Auth::guard('peserta')->user()->event_id)
            ->where('peserta_id', Auth::guard('peserta')->user()->id)
            ->where('is_finished', 'true')
            ->count();

        if ($ujian_selesai > 0) {
            return $this->redirect(route('peserta.tes-intelektual.home'), navigate: true);
        }

        $ujian_berlangsung = UjianIntelektualSubTes2::where('event_id', Auth::guard('peserta')->user()->event_id)
            ->where('peserta_id', Auth::guard('peserta')->user()->id)
            ->where('is_finished', 'false')
            ->count();

        if ($ujian_berlangsung > 0) {
            return $this->redirect(route('peserta.tes-intelektual.subtes2', ['id' => 1]), navigate: true);
        } else {
            $soal = SoalIntelektual::where('sub_tes', 2)->get(['id', 'kunci_jawaban']);

            $jumlah_soal = $soal->count();
            $soal_id = $soal->implode('id', ',');
            $jawaban_kosong = implode(',', array_fill(0, $jumlah_soal, 0));

            $durasi_tes = SettingWaktuTes::where('is_active', 'true')->where('jenis_tes', 4)->first(['waktu']);
            $waktu_tes_berakhir = now()->addMinutes($durasi_tes->waktu);

            $ujian = new UjianIntelektualSubTes2();
            $ujian->event_id = Auth::guard('peserta')->user()->event_id;
            $ujian->peserta_id = Auth::guard('peserta')->user()->id;
            $ujian->soal_id = $soal_id;
            $ujian->jawaban = $jawaban_kosong;
            $ujian->kunci_jawaban = $soal->implode('kunci_jawaban', ',');
            $ujian->nilai = 0;
            $ujian->waktu_tes_berakhir = $waktu_tes_berakhir;
            $ujian->save();

            return $this->redirect(route('peserta.tes-intelektual.subtes2', ['id' => 1]), navigate: true);
        }
    }

    public function startSubTes3()
    {
        $test_started = Peserta::where('id', auth()->guard('peserta')->user()->id)->first(['id']);
        if ($test_started) {
            $test_started->test_started_at = now();
            $test_started->save();
        }

        $ujian_selesai = UjianIntelektualSubTes3::where('event_id', Auth::guard('peserta')->user()->event_id)
            ->where('peserta_id', Auth::guard('peserta')->user()->id)
            ->where('is_finished', 'true')
            ->count();

        if ($ujian_selesai > 0) {
            return $this->redirect(route('peserta.tes-intelektual.home'), navigate: true);
        }

        $ujian_berlangsung = UjianIntelektualSubTes3::where('event_id', Auth::guard('peserta')->user()->event_id)
            ->where('peserta_id', Auth::guard('peserta')->user()->id)
            ->where('is_finished', 'false')
            ->count();

        if ($ujian_berlangsung > 0) {
            return $this->redirect(route('peserta.tes-intelektual.subtes3', ['id' => 1]), navigate: true);
        } else {
            $soal = SoalIntelektual::where('sub_tes', 3)->get(['id', 'kunci_jawaban']);

            $jumlah_soal = $soal->count();
            $soal_id = $soal->implode('id', ',');
            $jawaban_kosong = implode(',', array_fill(0, $jumlah_soal, 0));

            $durasi_tes = SettingWaktuTes::where('is_active', 'true')->where('jenis_tes', 5)->first(['waktu']);
            $waktu_tes_berakhir = now()->addMinutes($durasi_tes->waktu);

            $ujian = new UjianIntelektualSubTes3();
            $ujian->event_id = Auth::guard('peserta')->user()->event_id;
            $ujian->peserta_id = Auth::guard('peserta')->user()->id;
            $ujian->soal_id = $soal_id;
            $ujian->jawaban = $jawaban_kosong;
            $ujian->kunci_jawaban = $soal->implode('kunci_jawaban', ',');
            $ujian->nilai = 0;
            $ujian->waktu_tes_berakhir = $waktu_tes_berakhir;
            $ujian->save();

            return $this->redirect(route('peserta.tes-intelektual.subtes3', ['id' => 1]), navigate: true);
        }
    }

    public function render()
    {
        return view('livewire.peserta.tes-intelektual.dashboard');
    }
}
