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
        $first_tes = UjianInterpersonal::where('peserta_id', Auth::guard('peserta')->user()->id)
            ->where('event_id', Auth::guard('peserta')->user()->event_id)
            ->first();
        if ($first_tes) {
            $this->waktu_tes_berakhir = $first_tes->waktu_tes_berakhir;
        }
        $this->timer = $this->waktu_tes_berakhir->timestamp;
    }

    protected function _timerPengembanganDiri()
    {
        $first_tes = UjianPengembanganDiri::where('peserta_id', Auth::guard('peserta')->user()->id)
            ->where('event_id', Auth::guard('peserta')->user()->event_id)
            ->first();
        if ($first_tes) {
            $this->waktu_tes_berakhir = $first_tes->waktu_tes_berakhir;
        }
        $this->timer = $this->waktu_tes_berakhir->timestamp;
    }
    
    protected function _timerKecerdasanEmosi()
    {
        $first_tes = UjianKecerdasanEmosi::where('peserta_id', Auth::guard('peserta')->user()->id)
            ->where('event_id', Auth::guard('peserta')->user()->event_id)
            ->first();
        if ($first_tes) {
            $this->waktu_tes_berakhir = $first_tes->waktu_tes_berakhir;
        }
        $this->timer = $this->waktu_tes_berakhir->timestamp;
    }

    protected function _timerMotivasiKomitmen()
    {
        $first_tes = UjianMotivasiKomitmen::where('peserta_id', Auth::guard('peserta')->user()->id)
            ->where('event_id', Auth::guard('peserta')->user()->event_id)
            ->first();
        if ($first_tes) {
            $this->waktu_tes_berakhir = $first_tes->waktu_tes_berakhir;
        }
        $this->timer = $this->waktu_tes_berakhir->timestamp;
    }
    
    protected function _timerBerpikirKritis()
    {
        $first_tes = UjianBerpikirKritis::where('peserta_id', Auth::guard('peserta')->user()->id)
            ->where('event_id', Auth::guard('peserta')->user()->event_id)
            ->first();
        if ($first_tes) {
            $this->waktu_tes_berakhir = $first_tes->waktu_tes_berakhir;
        }
        $this->timer = $this->waktu_tes_berakhir->timestamp;
    }

    protected function _timerProblemSolving()
    {
        $first_tes = UjianProblemSolving::where('peserta_id', Auth::guard('peserta')->user()->id)
            ->where('event_id', Auth::guard('peserta')->user()->event_id)
            ->first();
        if ($first_tes) {
            $this->waktu_tes_berakhir = $first_tes->waktu_tes_berakhir;
        }
        $this->timer = $this->waktu_tes_berakhir->timestamp;
    }

    protected function _timerKesadaranDiri()
    {
        $first_tes = UjianKesadaranDiri::where('peserta_id', Auth::guard('peserta')->user()->id)
            ->where('event_id', Auth::guard('peserta')->user()->event_id)
            ->first();
        if ($first_tes) {
            $this->waktu_tes_berakhir = $first_tes->waktu_tes_berakhir;
        }
        $this->timer = $this->waktu_tes_berakhir->timestamp;
    }
}
