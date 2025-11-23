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
use App\Models\SettingWaktuTes;
use Illuminate\Support\Facades\Auth;

trait StartTestTrait
{
    public function startTest($nama_tes, $urutan_tes)
    {
        switch ($nama_tes) {
            case 'Kemampuan Interpersonal':
                $this->_startTesInterpersonal($urutan_tes);
                break;
            case 'Belajar Cepat dan Pengembangan Diri':
                $this->_startTesPengembanganDiri($urutan_tes);
                break;
            case 'Kecerdasan Emosi':
                $this->_startTesKecerdasanEmosi($urutan_tes);
                break;
            case 'Motivasi dan Komitmen':
                $this->_startTesMotivasiKomitmen($urutan_tes);
                break;
            case 'Berpikir Kritis dan Strategis':
                $this->_startTesBerpikirKritis($urutan_tes);
                break;
            case 'Problem Solving':
                $this->_startTesProblemSolving($urutan_tes);
                break;
            case 'Kesadaran Diri':
                $this->_startTesKesadaranDiri($urutan_tes);
                break;
        }
    }

    protected function _startTesInterpersonal($urutan_tes)
    {
        $ujian_selesai = UjianInterpersonal::where('event_id', Auth::guard('peserta')->user()->event_id)
            ->where('peserta_id', Auth::guard('peserta')->user()->id)
            ->where('is_finished', 'true')
            ->count();

        if ($ujian_selesai > 0) {
            return $this->redirect(route('peserta.tes-potensi.interpersonal', ['id' => 1]), navigate: true);
        }

        $ujian_berlangsung = UjianInterpersonal::where('event_id', Auth::guard('peserta')->user()->event_id)
            ->where('peserta_id', Auth::guard('peserta')->user()->id)
            ->where('is_finished', 'false')
            ->count();

        if ($ujian_berlangsung > 0) {
            return $this->redirect(route('peserta.tes-potensi.interpersonal', ['id' => 1]));
        } else {
            $soal = SoalInterpersonal::get(['id']);
            $jumlah_soal = $soal->count();
            $soal_id = $soal->implode('id', ',');
            $jawaban_kosong = implode(',', array_fill(0, $jumlah_soal, 0));

            if ($urutan_tes === 1) {
                $durasi_tes = SettingWaktuTes::where('is_active', 'true')->where('jenis_tes', 1)->first(['waktu']); // 1 = tes potensi
                $waktu_tes_berakhir = now()->addMinutes($durasi_tes->waktu);
            } else {
                $waktu_tes_berakhir = null;
            }

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
            $ujian->urutan_tes = $urutan_tes;
            $ujian->waktu_tes_berakhir = $waktu_tes_berakhir;
            $ujian->save();

            return $this->redirect(route('peserta.tes-potensi.interpersonal', ['id' => 1]));
        }
    }

    protected function _startTesPengembanganDiri($urutan_tes)
    {
        $ujian_selesai = UjianPengembanganDiri::where('event_id', Auth::guard('peserta')->user()->event_id)
            ->where('peserta_id', Auth::guard('peserta')->user()->id)
            ->where('is_finished', 'true')
            ->count();

        if ($ujian_selesai > 0) {
            return $this->redirect(route('peserta.tes-potensi.pengembangan-diri', ['id' => 1]), navigate: true);
        }

        $ujian_berlangsung = UjianPengembanganDiri::where('event_id', Auth::guard('peserta')->user()->event_id)
            ->where('peserta_id', Auth::guard('peserta')->user()->id)
            ->where('is_finished', 'false')
            ->count();

        if ($ujian_berlangsung > 0) {
            return $this->redirect(route('peserta.tes-potensi.pengembangan-diri', ['id' => 1]));
        } else {
            $soal = SoalPengembanganDiri::get(['id']);
            $jumlah_soal = $soal->count();
            $soal_id = $soal->implode('id', ',');
            $jawaban_kosong = implode(',', array_fill(0, $jumlah_soal, 0));

            if ($urutan_tes === 1) {
                $durasi_tes = SettingWaktuTes::where('is_active', 'true')->where('jenis_tes', 1)->first(['waktu']);
                $waktu_tes_berakhir = now()->addMinutes($durasi_tes->waktu);
            } else {
                $waktu_tes_berakhir = null;
            }

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
            $ujian->urutan_tes = $urutan_tes;
            $ujian->waktu_tes_berakhir = $waktu_tes_berakhir;
            $ujian->save();

            return $this->redirect(route('peserta.tes-potensi.pengembangan-diri', ['id' => 1]));
        }
    }

    protected function _startTesKecerdasanEmosi($urutan_tes)
    {
        $ujian_selesai = UjianKecerdasanEmosi::where('event_id', Auth::guard('peserta')->user()->event_id)
            ->where('peserta_id', Auth::guard('peserta')->user()->id)
            ->where('is_finished', 'true')
            ->count();

        if ($ujian_selesai > 0) {
            return $this->redirect(route('peserta.tes-potensi.kecerdasan-emosi', ['id' => 1]), navigate: true);
        }

        $ujian_berlangsung = UjianKecerdasanEmosi::where('event_id', Auth::guard('peserta')->user()->event_id)
            ->where('peserta_id', Auth::guard('peserta')->user()->id)
            ->where('is_finished', 'false')
            ->count();

        if ($ujian_berlangsung > 0) {
            return $this->redirect(route('peserta.tes-potensi.kecerdasan-emosi', ['id' => 1]));
        } else {
            $soal = SoalKecerdasanEmosi::get(['id']);
            $jumlah_soal = $soal->count();
            $soal_id = $soal->implode('id', ',');
            $jawaban_kosong = implode(',', array_fill(0, $jumlah_soal, 0));

            if ($urutan_tes === 1) {
                $durasi_tes = SettingWaktuTes::where('is_active', 'true')->where('jenis_tes', 1)->first(['waktu']);
                $waktu_tes_berakhir = now()->addMinutes($durasi_tes->waktu);
            } else {
                $waktu_tes_berakhir = null;
            }

            $ujian = new UjianKecerdasanEmosi();
            $ujian->peserta_id = Auth::guard('peserta')->user()->id;
            $ujian->event_id = Auth::guard('peserta')->user()->event_id;
            $ujian->soal_id = $soal_id;
            $ujian->jawaban = $jawaban_kosong;
            $ujian->nilai_indikator_kd = 0;
            $ujian->nilai_indikator_mpd = 0;
            $ujian->nilai_indikator_ke = 0;
            $ujian->nilai_indikator_ks = 0;
            $ujian->urutan_tes = $urutan_tes;
            $ujian->waktu_tes_berakhir = $waktu_tes_berakhir;
            $ujian->save();

            return $this->redirect(route('peserta.tes-potensi.kecerdasan-emosi', ['id' => 1]));
        }
    }

    protected function _startTesMotivasiKomitmen($urutan_tes)
    {
        $ujian_selesai = UjianMotivasiKomitmen::where('event_id', Auth::guard('peserta')->user()->event_id)
            ->where('peserta_id', Auth::guard('peserta')->user()->id)
            ->where('is_finished', 'true')
            ->count();

        if ($ujian_selesai > 0) {
            return $this->redirect(route('peserta.tes-potensi.motivasi-komitmen', ['id' => 1]), navigate: true);
        }

        $ujian_berlangsung = UjianMotivasiKomitmen::where('event_id', Auth::guard('peserta')->user()->event_id)
            ->where('peserta_id', Auth::guard('peserta')->user()->id)
            ->where('is_finished', 'false')
            ->count();

        if ($ujian_berlangsung > 0) {
            return $this->redirect(route('peserta.tes-potensi.motivasi-komitmen', ['id' => 1]));
        } else {
            $soal = SoalMotivasiKomitmen::get(['id']);
            $jumlah_soal = $soal->count();
            $soal_id = $soal->implode('id', ',');
            $jawaban_kosong = implode(',', array_fill(0, $jumlah_soal, 0));

            if ($urutan_tes === 1) {
                $durasi_tes = SettingWaktuTes::where('is_active', 'true')->where('jenis_tes', 1)->first(['waktu']);
                $waktu_tes_berakhir = now()->addMinutes($durasi_tes->waktu);
            } else {
                $waktu_tes_berakhir = null;
            }

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
            $ujian->urutan_tes = $urutan_tes;
            $ujian->waktu_tes_berakhir = $waktu_tes_berakhir;
            $ujian->save();

            return $this->redirect(route('peserta.tes-potensi.motivasi-komitmen', ['id' => 1]));
        }
    }

    protected function _startTesBerpikirKritis($urutan_tes)
    {
        $ujian_selesai = UjianBerpikirKritis::where('event_id', Auth::guard('peserta')->user()->event_id)
            ->where('peserta_id', Auth::guard('peserta')->user()->id)
            ->where('is_finished', 'true')
            ->count();

        if ($ujian_selesai > 0) {
            return $this->redirect(route('peserta.tes-potensi.berpikir-kritis', ['id' => 1]), navigate: true);
        }

        $ujian_berlangsung = UjianBerpikirKritis::where('event_id', Auth::guard('peserta')->user()->event_id)
            ->where('peserta_id', Auth::guard('peserta')->user()->id)
            ->where('is_finished', 'false')
            ->count();

        if ($ujian_berlangsung > 0) {
            return $this->redirect(route('peserta.tes-potensi.berpikir-kritis', ['id' => 1]));
        } else {
            $soal = SoalBerpikirKritis::get(['id']);
            $jumlah_soal = $soal->count();
            $soal_id = $soal->implode('id', ',');
            $jawaban_kosong = implode(',', array_fill(0, $jumlah_soal, 0));

            if ($urutan_tes === 1) {
                $durasi_tes = SettingWaktuTes::where('is_active', 'true')->where('jenis_tes', 1)->first(['waktu']);
                $waktu_tes_berakhir = now()->addMinutes($durasi_tes->waktu);
            } else {
                $waktu_tes_berakhir = null;
            }

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
            $ujian->urutan_tes = $urutan_tes;
            $ujian->waktu_tes_berakhir = $waktu_tes_berakhir;
            $ujian->save();

            return $this->redirect(route('peserta.tes-potensi.berpikir-kritis', ['id' => 1]));
        }
    }

    protected function _startTesProblemSolving($urutan_tes)
    {
        $ujian_selesai = UjianProblemSolving::where('event_id', Auth::guard('peserta')->user()->event_id)
            ->where('peserta_id', Auth::guard('peserta')->user()->id)
            ->where('is_finished', 'true')
            ->count();

        if ($ujian_selesai > 0) {
            return $this->redirect(route('peserta.tes-potensi.problem-solving', ['id' => 1]), navigate: true);
        }

        $ujian_berlangsung = UjianProblemSolving::where('event_id', Auth::guard('peserta')->user()->event_id)
            ->where('peserta_id', Auth::guard('peserta')->user()->id)
            ->where('is_finished', 'false')
            ->count();

        if ($ujian_berlangsung > 0) {
            return $this->redirect(route('peserta.tes-potensi.problem-solving', ['id' => 1]));
        } else {
            $soal = SoalProblemSolving::get(['id']);
            $jumlah_soal = $soal->count();
            $soal_id = $soal->implode('id', ',');
            $jawaban_kosong = implode(',', array_fill(0, $jumlah_soal, 0));

            if ($urutan_tes === 1) {
                $durasi_tes = SettingWaktuTes::where('is_active', 'true')->where('jenis_tes', 1)->first(['waktu']);
                $waktu_tes_berakhir = now()->addMinutes($durasi_tes->waktu);
            } else {
                $waktu_tes_berakhir = null;
            }

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
            $ujian->urutan_tes = $urutan_tes;
            $ujian->waktu_tes_berakhir = $waktu_tes_berakhir;
            $ujian->save();

            return $this->redirect(route('peserta.tes-potensi.problem-solving', ['id' => 1]));
        }
    }

    protected function _startTesKesadaranDiri($urutan_tes)
    {
        $ujian_selesai = UjianKesadaranDiri::where('event_id', Auth::guard('peserta')->user()->event_id)
            ->where('peserta_id', Auth::guard('peserta')->user()->id)
            ->where('is_finished', 'true')
            ->count();

        if ($ujian_selesai) {
            return $this->redirect(route('peserta.tes-potensi.kesadaran-diri', ['id' => 1]), navigate: true);
        }

        $ujian_berlangsung = UjianKesadaranDiri::where('event_id', Auth::guard('peserta')->user()->event_id)
            ->where('peserta_id', Auth::guard('peserta')->user()->id)
            ->where('is_finished', 'true')
            ->count();

        if ($ujian_berlangsung) {
            return $this->redirect(route('peserta.tes-potensi.kesadaran-diri', ['id' => 1]));
        } else {
            $soal = SoalKesadaranDiri::get(['id']);
            $jumlah_soal = $soal->count();
            $soal_id = $soal->implode('id', ',');
            $jawaban_kosong = implode(',', array_fill(0, $jumlah_soal, 0));

            if ($urutan_tes === 1) {
                $durasi_tes = SettingWaktuTes::where('is_active', 'true')->where('jenis_tes', 1)->first(['waktu']);
                $waktu_tes_berakhir = now()->addMinutes($durasi_tes->waktu);
            } else {
                $waktu_tes_berakhir = null;
            }

            $ujian = new UjianKesadaranDiri();
            $ujian->peserta_id = Auth::guard('peserta')->user()->id;
            $ujian->event_id = Auth::guard('peserta')->user()->event_id;
            $ujian->soal_id = $soal_id;
            $ujian->jawaban = $jawaban_kosong;
            $ujian->nilai_indikator_1 = 0;
            $ujian->nilai_indikator_2 = 0;
            $ujian->nilai_indikator_3 = 0;
            $ujian->urutan_tes = $urutan_tes;
            $ujian->waktu_tes_berakhir = $waktu_tes_berakhir;
            $ujian->save();

            return $this->redirect(route('peserta.tes-potensi.kesadaran-diri', ['id' => 1]));
        }
    }
}
