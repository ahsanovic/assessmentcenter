<?php

namespace App\Livewire\Peserta\TesCakapDigital;

use App\Models\CakapDigital\SoalCakapDigital;
use App\Models\CakapDigital\UjianCakapDigital;
use App\Models\Peserta;
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

        $ujian_selesai = UjianCakapDigital::where('event_id', Auth::guard('peserta')->user()->event_id)
            ->where('peserta_id', Auth::guard('peserta')->user()->id)
            ->where('is_finished', 'true')
            ->count();

        if ($ujian_selesai > 0) {
            return $this->redirect(route('peserta.tes-cakap-digital.hasil'), navigate: true);
        }

        $ujian_berlangsung = UjianCakapDigital::where('event_id', Auth::guard('peserta')->user()->event_id)
            ->where('peserta_id', Auth::guard('peserta')->user()->id)
            ->where('is_finished', 'false')
            ->count();

        if ($ujian_berlangsung > 0) {
            return $this->redirect(route('peserta.tes-cakap-digital.ujian', ['id' => 1]), navigate: true);
        } else {
            $soal_literasi = SoalCakapDigital::where('jenis_soal', 1)
                ->inRandomOrder()
                ->limit(60)
                ->get(['id', 'kunci_jawaban']);

            $soal_emerging = SoalCakapDigital::where('jenis_soal', 2)
                ->inRandomOrder()
                ->limit(60)
                ->get(['id', 'kunci_jawaban']);

            $soal = $soal_literasi->merge($soal_emerging);

            $jumlah_soal = $soal->count();
            $soal_id = $soal->implode('id', ',');
            $jawaban_kosong = implode(',', array_fill(0, $jumlah_soal, 0));

            $durasi_tes = SettingWaktuTes::where('is_active', 'true')->where('jenis_tes', 2)->first(['waktu']); // 2 = Cakap Digital
            $waktu_tes_berakhir = now()->addMinutes($durasi_tes->waktu);

            $ujian = new UjianCakapDigital();
            $ujian->event_id = Auth::guard('peserta')->user()->event_id;
            $ujian->peserta_id = Auth::guard('peserta')->user()->id;
            $ujian->soal_id = $soal_id;
            $ujian->jawaban = $jawaban_kosong;
            $ujian->kunci_jawaban = $soal->implode('kunci_jawaban', ',');
            $ujian->nilai_literasi = 0;
            $ujian->nilai_emerging = 0;
            $ujian->waktu_tes_berakhir = $waktu_tes_berakhir;
            $ujian->save();

            return $this->redirect(route('peserta.tes-cakap-digital.ujian', ['id' => 1]), navigate: true);
        }
    }

    public function render()
    {
        return view('livewire.peserta.tes-cakap-digital.dashboard');
    }
}
