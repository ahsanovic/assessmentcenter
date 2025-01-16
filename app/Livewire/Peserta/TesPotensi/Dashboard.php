<?php

namespace App\Livewire\Peserta\TesPotensi;

use App\Models\Interpersonal\SoalInterpersonal;
use App\Models\Interpersonal\UjianInterpersonal;
use App\Models\KecerdasanEmosi\SoalKecerdasanEmosi;
use App\Models\KecerdasanEmosi\UjianKecerdasanEmosi;
use App\Models\PengembanganDiri\SoalPengembanganDiri;
use App\Models\PengembanganDiri\UjianPengembanganDiri;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.peserta.app', ['title' => 'Dashboard'])]
class Dashboard extends Component
{
    public function render()
    {
        $test_interpersonal = UjianInterpersonal::where('event_id', Auth::guard('peserta')->user()->event_id)
            ->where('peserta_id', Auth::guard('peserta')->user()->id)
            ->where('is_finished', 'true')
            ->first(['is_finished']);
        
        $test_pengembangan_diri = UjianPengembanganDiri::where('event_id', Auth::guard('peserta')->user()->event_id)
            ->where('peserta_id', Auth::guard('peserta')->user()->id)
            ->where('is_finished', 'true')
            ->first(['is_finished']);

        $test_kecerdasan_emosi = UjianKecerdasanEmosi::where('event_id', Auth::guard('peserta')->user()->event_id)
            ->where('peserta_id', Auth::guard('peserta')->user()->id)
            ->where('is_finished', 'true')
            ->first(['is_finished']);

        return view('livewire..peserta.tes-potensi.dashboard', [
            'test_interpersonal' => $test_interpersonal,
            'test_pengembangan_diri' => $test_pengembangan_diri,
            'test_kecerdasan_emosi' => $test_kecerdasan_emosi
        ]);
    }

    public function startTesInterpersonal()
    {
        // cek peserta sudah selesai tes atau belum
        $checking_test = UjianInterpersonal::where('event_id', Auth::guard('peserta')->user()->event_id)
            ->where('peserta_id', Auth::guard('peserta')->user()->id)
            ->where('is_finished', 'true')
            ->first(['id']);

        if ($checking_test) {
            $this->dispatch('toast', ['type' => 'error', 'message' => 'Anda sudah melakukan tes ini']);
            return;
        }

        // cek peserta sudah mulai tes / belum
        $count_ujian = UjianInterpersonal::where('event_id', Auth::guard('peserta')->user()->event_id)
            ->where('peserta_id', Auth::guard('peserta')->user()->id)
            ->where('is_finished', 'false')
            ->count();

        if ($count_ujian > 0) {
            return $this->redirect(route('peserta.tes-potensi.interpersonal', ['id' => 1]), navigate: true);
        }

        $soal = SoalInterpersonal::get(['id']);
        $jumlah_soal = $soal->count();
        $soal_id = $soal->implode('id', ',');

        for ($i = 0; $i < $jumlah_soal; $i++) {
            $jawaban_kosong[$i] = 0;
        }

        $jawaban_kosong = implode(',', $jawaban_kosong);

        $ujian = new UjianInterpersonal();
        $ujian->event_id = Auth::guard('peserta')->user()->event_id;
        $ujian->peserta_id = Auth::guard('peserta')->user()->id;
        $ujian->soal_id = $soal_id;
        $ujian->jawaban = $jawaban_kosong;
        $ujian->nilai_indikator_ke = 0;
        $ujian->nilai_indikator_bt = 0;
        $ujian->nilai_indikator_as = 0;
        $ujian->nilai_indikator_de = 0;
        $ujian->nilai_indikator_smk = 0;
        $ujian->save();

        return $this->redirect(route('peserta.tes-potensi.interpersonal', ['id' => 1]), navigate: true);
    }

    public function startTesPengembanganDiri()
    {
        // cek peserta sudah selesai tes atau belum
        $checking_test = UjianPengembanganDiri::where('event_id', Auth::guard('peserta')->user()->event_id)
            ->where('peserta_id', Auth::guard('peserta')->user()->id)
            ->where('is_finished', 'true')
            ->first(['id']);

        if ($checking_test) {
            $this->dispatch('toast', ['type' => 'error', 'message' => 'Anda sudah melakukan tes ini']);
            return;
        }

        // cek peserta sudah mulai tes / belum
        $count_ujian = UjianPengembanganDiri::where('event_id', Auth::guard('peserta')->user()->event_id)
            ->where('peserta_id', Auth::guard('peserta')->user()->id)
            ->where('is_finished', 'false')
            ->count();

        if ($count_ujian > 0) {
            return $this->redirect(route('peserta.tes-potensi.pengembangan-diri', ['id' => 1]), navigate: true);
        }

        $soal = SoalPengembanganDiri::get(['id']);
        $jumlah_soal = $soal->count();
        $soal_id = $soal->implode('id', ',');

        for ($i = 0; $i < $jumlah_soal; $i++) {
            $jawaban_kosong[$i] = 0;
        }

        $jawaban_kosong = implode(',', $jawaban_kosong);

        $ujian = new UjianPengembanganDiri();
        $ujian->peserta_id = Auth::guard('peserta')->user()->id;
        $ujian->event_id = Auth::guard('peserta')->user()->event_id;
        $ujian->soal_id = $soal_id;
        $ujian->jawaban = $jawaban_kosong;
        $ujian->nilai_indikator_mb = 0;
        $ujian->nilai_indikator_mit = 0;
        $ujian->nilai_indikator_pde = 0;
        $ujian->nilai_indikator_spd = 0;
        $ujian->nilai_indikator_ed = 0;
        $ujian->save();

        return $this->redirect(route('peserta.tes-potensi.pengembangan-diri', ['id' => 1]), navigate: true);
    }

    public function startTesKecerdasanEmosi()
    {
        // cek peserta sudah selesai tes atau belum
        $checking_test = UjianKecerdasanEmosi::where('event_id', Auth::guard('peserta')->user()->event_id)
            ->where('peserta_id', Auth::guard('peserta')->user()->id)
            ->where('is_finished', 'true')
            ->first(['id']);

        if ($checking_test) {
            $this->dispatch('toast', ['type' => 'error', 'message' => 'Anda sudah melakukan tes ini']);
            return;
        }

        // cek peserta sudah mulai tes / belum
        $count_ujian = UjianKecerdasanEmosi::where('event_id', Auth::guard('peserta')->user()->event_id)
            ->where('peserta_id', Auth::guard('peserta')->user()->id)
            ->where('is_finished', 'false')
            ->count();

        if ($count_ujian > 0) {
            return $this->redirect(route('peserta.tes-potensi.kecerdasan-emosi', ['id' => 1]), navigate: true);
        }

        $soal = SoalKecerdasanEmosi::get(['id']);
        $jumlah_soal = $soal->count();
        $soal_id = $soal->implode('id', ',');

        for ($i = 0; $i < $jumlah_soal; $i++) {
            $jawaban_kosong[$i] = 0;
        }

        $jawaban_kosong = implode(',', $jawaban_kosong);

        $ujian = new UjianKecerdasanEmosi();
        $ujian->peserta_id = Auth::guard('peserta')->user()->id;
        $ujian->event_id = Auth::guard('peserta')->user()->event_id;
        $ujian->soal_id = $soal_id;
        $ujian->jawaban = $jawaban_kosong;
        $ujian->nilai_indikator_kd = 0;
        $ujian->nilai_indikator_mpd = 0;
        $ujian->nilai_indikator_ke = 0;
        $ujian->nilai_indikator_ks = 0;
        $ujian->save();

        return $this->redirect(route('peserta.tes-potensi.kecerdasan-emosi', ['id' => 1]), navigate: true);
    }
}
