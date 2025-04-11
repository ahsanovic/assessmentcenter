<?php

namespace App\Traits;

use App\Models\BerpikirKritis\UjianBerpikirKritis;
use App\Models\Interpersonal\UjianInterpersonal;
use App\Models\KecerdasanEmosi\UjianKecerdasanEmosi;
use App\Models\KesadaranDiri\UjianKesadaranDiri;
use App\Models\MotivasiKomitmen\UjianMotivasiKomitmen;
use App\Models\PengembanganDiri\UjianPengembanganDiri;
use App\Models\ProblemSolving\UjianProblemSolving;
use Illuminate\Support\Facades\Auth;

trait TimerTrait
{
    public function timerTest($nama_tes)
    {
        switch ($nama_tes) {
            case 'Kemampuan Interpersonal':
                $this->_timerInterpersonal();
                break;
            case 'Belajar Cepat dan Pengembangan Diri':
                $this->_timerPengembanganDiri();
                break;
            case 'Kecerdasan Emosi':
                $this->_timerKecerdasanEmosi();
                break;
            case 'Motivasi dan Komitmen':
                $this->_timerMotivasiKomitmen();
                break;
            case 'Berpikir Kritis dan Strategis':
                $this->_timerBerpikirKritis();
                break;
            case 'Problem Solving':
                $this->_timerProblemSolving();
                break;
            case 'Kesadaran Diri':
                $this->_timerKesadaranDiri();
                break;
        }
    }

    protected function _timerInterpersonal()
    {
        $first_test = UjianInterpersonal::where('peserta_id', Auth::guard('peserta')->user()->id)
            ->where('event_id', Auth::guard('peserta')->user()->event_id)
            ->first();
        if ($first_test) {
            $this->waktu_tes_berakhir = $first_test->waktu_tes_berakhir;
        }
        $this->timer = $this->waktu_tes_berakhir->timestamp;
    }

    protected function _timerPengembanganDiri()
    {
        $first_test = UjianPengembanganDiri::where('peserta_id', Auth::guard('peserta')->user()->id)
            ->where('event_id', Auth::guard('peserta')->user()->event_id)
            ->first();
        if ($first_test) {
            $this->waktu_tes_berakhir = $first_test->waktu_tes_berakhir;
        }
        $this->timer = $this->waktu_tes_berakhir->timestamp;
    }
    
    protected function _timerKecerdasanEmosi()
    {
        $first_test = UjianKecerdasanEmosi::where('peserta_id', Auth::guard('peserta')->user()->id)
            ->where('event_id', Auth::guard('peserta')->user()->event_id)
            ->first();
        if ($first_test) {
            $this->waktu_tes_berakhir = $first_test->waktu_tes_berakhir;
        }
        $this->timer = $this->waktu_tes_berakhir->timestamp;
    }

    protected function _timerMotivasiKomitmen()
    {
        $first_test = UjianMotivasiKomitmen::where('peserta_id', Auth::guard('peserta')->user()->id)
            ->where('event_id', Auth::guard('peserta')->user()->event_id)
            ->first();
        if ($first_test) {
            $this->waktu_tes_berakhir = $first_test->waktu_tes_berakhir;
        }
        $this->timer = $this->waktu_tes_berakhir->timestamp;
    }
    
    protected function _timerBerpikirKritis()
    {
        $first_test = UjianBerpikirKritis::where('peserta_id', Auth::guard('peserta')->user()->id)
            ->where('event_id', Auth::guard('peserta')->user()->event_id)
            ->first();
        if ($first_test) {
            $this->waktu_tes_berakhir = $first_test->waktu_tes_berakhir;
        }
        $this->timer = $this->waktu_tes_berakhir->timestamp;
    }

    protected function _timerProblemSolving()
    {
        $first_test = UjianProblemSolving::where('peserta_id', Auth::guard('peserta')->user()->id)
            ->where('event_id', Auth::guard('peserta')->user()->event_id)
            ->first();
        if ($first_test) {
            $this->waktu_tes_berakhir = $first_test->waktu_tes_berakhir;
        }
        $this->timer = $this->waktu_tes_berakhir->timestamp;
    }

    protected function _timerKesadaranDiri()
    {
        $first_test = UjianKesadaranDiri::where('peserta_id', Auth::guard('peserta')->user()->id)
            ->where('event_id', Auth::guard('peserta')->user()->event_id)
            ->first();
        if ($first_test) {
            $this->waktu_tes_berakhir = $first_test->waktu_tes_berakhir;
        }
        $this->timer = $this->waktu_tes_berakhir->timestamp;
    }
}
