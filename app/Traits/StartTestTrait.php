<?php

namespace App\Traits;

use App\Models\BerpikirKritis\SoalBerpikirKritis;
use App\Models\BerpikirKritis\UjianBerpikirKritis;
use App\Models\Interpersonal\SoalInterpersonal;
use App\Models\Interpersonal\UjianInterpersonal;
use App\Models\KecerdasanEmosi\SoalKecerdasanEmosi;
use App\Models\KecerdasanEmosi\UjianKecerdasanEmosi;
use App\Models\KesadaranDiri\SoalKesadaranDiri;
use App\Models\KesadaranDiri\UjianKesadaranDiri;
use App\Models\MotivasiKomitmen\SoalMotivasiKomitmen;
use App\Models\MotivasiKomitmen\UjianMotivasiKomitmen;
use App\Models\PengembanganDiri\SoalPengembanganDiri;
use App\Models\PengembanganDiri\UjianPengembanganDiri;
use App\Models\ProblemSolving\SoalProblemSolving;
use App\Models\ProblemSolving\UjianProblemSolving;
use Illuminate\Support\Facades\Auth;

trait StartTestTrait
{
    public function startTest($nama_tes)
    {
        switch ($nama_tes) {
            case 'Kemampuan Interpersonal':
                $this->_startTesInterpersonal();
                break;
            case 'Belajar Cepat dan Pengembangan Diri':
                $this->_startTesPengembanganDiri();
                break;
            case 'Kecerdasan Emosi':
                $this->_startTesKecerdasanEmosi();
                break;
            case 'Motivasi dan Komitmen':
                $this->_startTesMotivasiKomitmen();
                break;
            case 'Berpikir Kritis dan Strategis':
                $this->_startTesBerpikirKritis();
                break;
            case 'Problem Solving':
                $this->_startTesProblemSolving();
                break;
            case 'Kesadaran Diri':
                $this->_startTesKesadaranDiri();
                break;
        }
    }

    protected function _startTesInterpersonal()
    {   
        $ujian = UjianInterpersonal::where('event_id', Auth::guard('peserta')->user()->event_id)
            ->where('peserta_id', Auth::guard('peserta')->user()->id)
            ->where('is_finished', 'true')
            ->first();

        if ($ujian) {
            return $this->redirect(route('peserta.tes-potensi.interpersonal', ['id' => 1]), navigate: true);
        }

        $soal = SoalInterpersonal::get(['id']);
        $jumlah_soal = $soal->count();
        $soal_id = $soal->implode('id', ',');
        $jawaban_kosong = implode(',', array_fill(0, $jumlah_soal, 0));

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

    protected function _startTesPengembanganDiri()
    {
        // cek peserta sudah mulai tes / belum
        $ujian = UjianPengembanganDiri::where('event_id', Auth::guard('peserta')->user()->event_id)
            ->where('peserta_id', Auth::guard('peserta')->user()->id)
            ->where('is_finished', 'true')
            ->first();

        if ($ujian) {
            return $this->redirect(route('peserta.tes-potensi.pengembangan-diri', ['id' => 1]), navigate: true);
        }

        $soal = SoalPengembanganDiri::get(['id']);
        $jumlah_soal = $soal->count();
        $soal_id = $soal->implode('id', ',');
        $jawaban_kosong = implode(',', array_fill(0, $jumlah_soal, 0));

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

    protected function _startTesKecerdasanEmosi()
    {
        // cek peserta sudah mulai tes / belum
        $ujian = UjianKecerdasanEmosi::where('event_id', Auth::guard('peserta')->user()->event_id)
            ->where('peserta_id', Auth::guard('peserta')->user()->id)
            ->where('is_finished', 'true')
            ->first();

        if ($ujian) {
            return $this->redirect(route('peserta.tes-potensi.kecerdasan-emosi', ['id' => 1]), navigate: true);
        }

        $soal = SoalKecerdasanEmosi::get(['id']);
        $jumlah_soal = $soal->count();
        $soal_id = $soal->implode('id', ',');
        $jawaban_kosong = implode(',', array_fill(0, $jumlah_soal, 0));

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

    protected function _startTesMotivasiKomitmen()
    {
        // cek peserta sudah mulai tes / belum
        $ujian = UjianMotivasiKomitmen::where('event_id', Auth::guard('peserta')->user()->event_id)
            ->where('peserta_id', Auth::guard('peserta')->user()->id)
            ->where('is_finished', 'true')
            ->first();

        if ($ujian) {
            return $this->redirect(route('peserta.tes-potensi.motivasi-komitmen', ['id' => 1]), navigate: true);
        }

        $soal = SoalMotivasiKomitmen::get(['id']);
        $jumlah_soal = $soal->count();
        $soal_id = $soal->implode('id', ',');
        $jawaban_kosong = implode(',', array_fill(0, $jumlah_soal, 0));

        $ujian = new UjianMotivasiKomitmen();
        $ujian->peserta_id = Auth::guard('peserta')->user()->id;
        $ujian->event_id = Auth::guard('peserta')->user()->event_id;
        $ujian->soal_id = $soal_id;
        $ujian->jawaban = $jawaban_kosong;
        $ujian->nilai_indikator_1 = 0;
        $ujian->nilai_indikator_2 = 0;
        $ujian->nilai_indikator_3 = 0;
        $ujian->nilai_indikator_4 = 0;
        $ujian->nilai_indikator_5 = 0;
        $ujian->save();

        return $this->redirect(route('peserta.tes-potensi.motivasi-komitmen', ['id' => 1]), navigate: true);
    }

    protected function _startTesBerpikirKritis()
    {
        // cek peserta sudah mulai tes / belum
        $ujian = UjianBerpikirKritis::where('event_id', Auth::guard('peserta')->user()->event_id)
            ->where('peserta_id', Auth::guard('peserta')->user()->id)
            ->where('is_finished', 'true')
            ->first();

        if ($ujian) {
            return $this->redirect(route('peserta.tes-potensi.berpikir-kritis', ['id' => 1]), navigate: true);
        }

        $soal = SoalBerpikirKritis::get(['id']);
        $jumlah_soal = $soal->count();
        $soal_id = $soal->implode('id', ',');
        $jawaban_kosong = implode(',', array_fill(0, $jumlah_soal, 0));

        $ujian = new UjianBerpikirKritis();
        $ujian->peserta_id = Auth::guard('peserta')->user()->id;
        $ujian->event_id = Auth::guard('peserta')->user()->event_id;
        $ujian->soal_id = $soal_id;
        $ujian->jawaban = $jawaban_kosong;
        $ujian->nilai_indikator_1 = 0;
        $ujian->nilai_indikator_2 = 0;
        $ujian->nilai_indikator_3 = 0;
        $ujian->nilai_indikator_4 = 0;
        $ujian->nilai_indikator_5 = 0;
        $ujian->nilai_indikator_6 = 0;
        $ujian->nilai_indikator_7 = 0;
        $ujian->nilai_indikator_8 = 0;
        $ujian->save();

        return $this->redirect(route('peserta.tes-potensi.berpikir-kritis', ['id' => 1]), navigate: true);
    }

    protected function _startTesProblemSolving()
    {
        // cek peserta sudah mulai tes / belum
        $ujian = UjianProblemSolving::where('event_id', Auth::guard('peserta')->user()->event_id)
            ->where('peserta_id', Auth::guard('peserta')->user()->id)
            ->where('is_finished', 'true')
            ->first();

        if ($ujian) {
            return $this->redirect(route('peserta.tes-potensi.problem-solving', ['id' => 1]), navigate: true);
        }

        $soal = SoalProblemSolving::get(['id']);
        $jumlah_soal = $soal->count();
        $soal_id = $soal->implode('id', ',');
        $jawaban_kosong = implode(',', array_fill(0, $jumlah_soal, 0));

        $ujian = new UjianProblemSolving();
        $ujian->peserta_id = Auth::guard('peserta')->user()->id;
        $ujian->event_id = Auth::guard('peserta')->user()->event_id;
        $ujian->soal_id = $soal_id;
        $ujian->jawaban = $jawaban_kosong;
        $ujian->nilai_indikator_1 = 0;
        $ujian->nilai_indikator_2 = 0;
        $ujian->nilai_indikator_3 = 0;
        $ujian->nilai_indikator_4 = 0;
        $ujian->nilai_indikator_5 = 0;
        $ujian->nilai_indikator_6 = 0;
        $ujian->nilai_indikator_7 = 0;
        $ujian->nilai_indikator_8 = 0;
        $ujian->save();

        return $this->redirect(route('peserta.tes-potensi.problem-solving', ['id' => 1]), navigate: true);
    }

    protected function _startTesKesadaranDiri()
    {
        // cek peserta sudah mulai tes / belum
        $ujian = UjianKesadaranDiri::where('event_id', Auth::guard('peserta')->user()->event_id)
            ->where('peserta_id', Auth::guard('peserta')->user()->id)
            ->where('is_finished', 'true')
            ->first();

        if ($ujian) {
            return $this->redirect(route('peserta.tes-potensi.kesadaran-diri', ['id' => 1]), navigate: true);
        }

        $soal = SoalKesadaranDiri::get(['id']);
        $jumlah_soal = $soal->count();
        $soal_id = $soal->implode('id', ',');
        $jawaban_kosong = implode(',', array_fill(0, $jumlah_soal, 0));

        $ujian = new UjianKesadaranDiri();
        $ujian->peserta_id = Auth::guard('peserta')->user()->id;
        $ujian->event_id = Auth::guard('peserta')->user()->event_id;
        $ujian->soal_id = $soal_id;
        $ujian->jawaban = $jawaban_kosong;
        $ujian->nilai_indikator_1 = 0;
        $ujian->nilai_indikator_2 = 0;
        $ujian->nilai_indikator_3 = 0;
        $ujian->save();

        return $this->redirect(route('peserta.tes-potensi.kesadaran-diri', ['id' => 1]), navigate: true);
    }
}
