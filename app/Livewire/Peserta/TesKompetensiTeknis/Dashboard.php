<?php

namespace App\Livewire\Peserta\TesKompetensiTeknis;

use App\Models\KompetensiTeknis\SoalKompetensiTeknis;
use App\Models\KompetensiTeknis\UjianKompetensiTeknis;
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

        $ujian_selesai = UjianKompetensiTeknis::where('event_id', Auth::guard('peserta')->user()->event_id)
            ->where('peserta_id', Auth::guard('peserta')->user()->id)
            ->where('is_finished', 'true')
            ->count();

        if ($ujian_selesai > 0) {
            return $this->redirect(route('peserta.tes-kompetensi-teknis.hasil'), navigate: true);
        }

        $ujian_berlangsung = UjianKompetensiTeknis::where('event_id', Auth::guard('peserta')->user()->event_id)
            ->where('peserta_id', Auth::guard('peserta')->user()->id)
            ->where('is_finished', 'false')
            ->count();

        if ($ujian_berlangsung > 0) {
            return $this->redirect(route('peserta.tes-kompetensi-teknis.ujian', ['id' => 1]), navigate: true);
        } else {
            $soal = SoalKompetensiTeknis::where('jenis_jabatan_id', auth()->guard('peserta')->user()->event->jabatan_diuji_id)
                ->inRandomOrder()
                ->limit(60)
                ->get(['id', 'kunci_jawaban']);

            $jumlah_soal = $soal->count();
            $soal_id = $soal->implode('id', ',');
            $jawaban_kosong = implode(',', array_fill(0, $jumlah_soal, 0));

            $durasi_tes = SettingWaktuTes::where('is_active', 'true')->where('jenis_tes', 6)->first(['waktu']); // 6 = Kompetensi Teknis
            $waktu_tes_berakhir = now()->addMinutes($durasi_tes->waktu);

            $ujian = new UjianKompetensiTeknis();
            $ujian->event_id = Auth::guard('peserta')->user()->event_id;
            $ujian->peserta_id = Auth::guard('peserta')->user()->id;
            $ujian->soal_id = $soal_id;
            $ujian->jawaban = $jawaban_kosong;
            $ujian->kunci_jawaban = $soal->implode('kunci_jawaban', ',');
            $ujian->nilai_total = 0;
            $ujian->waktu_tes_berakhir = $waktu_tes_berakhir;
            $ujian->save();

            return $this->redirect(route('peserta.tes-kompetensi-teknis.ujian', ['id' => 1]), navigate: true);
        }
    }

    public function render()
    {
        return view('livewire.peserta.tes-kompetensi-teknis.dashboard');
    }
}
