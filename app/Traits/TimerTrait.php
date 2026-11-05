<?php

namespace App\Traits;

use App\Models\BerpikirKritis\UjianBerpikirKritis;
use App\Models\CakapDigital\UjianCakapDigital;
use App\Models\LiterasiDigital\UjianLiterasiDigital;
use App\Models\Intelektual\UjianIntelektualSubTes1;
use App\Models\Intelektual\UjianIntelektualSubTes2;
use App\Models\Intelektual\UjianIntelektualSubTes3;
use App\Models\Interpersonal\UjianInterpersonal;
use App\Models\KecerdasanEmosi\UjianKecerdasanEmosi;
use App\Models\KesadaranDiri\UjianKesadaranDiri;
use App\Models\KompetensiTeknis\UjianKompetensiTeknis;
use App\Models\MotivasiKomitmen\UjianMotivasiKomitmen;
use App\Models\PengembanganDiri\UjianPengembanganDiri;
use App\Models\ProblemSolving\UjianProblemSolving;
use App\Models\Pspk\UjianPspk;

trait TimerTrait
{
    public $waktu_tes_berakhir;

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
            case 'Cakap Digital':
                $this->_timerCakapDigital();
                break;
            case 'Literasi Digital':
                $this->_timerLiterasiDigital();
                break;
            case 'Intelektual Sub Tes 1':
                $this->_timerIntelektualSubTes1();
                break;
            case 'Intelektual Sub Tes 2':
                $this->_timerIntelektualSubTes2();
                break;
            case 'Intelektual Sub Tes 3':
                $this->_timerIntelektualSubTes3();
                break;
            case 'Kompetensi Teknis':
                $this->_timerKompetensiTeknis();
                break;
            case 'Pspk':
                $this->_timerPspk();
                break;
        }
    }

    protected function applyTimerFromTest(?object $test): void
    {
        if (! $test?->waktu_tes_berakhir) {
            return;
        }

        $this->waktu_tes_berakhir = $test->waktu_tes_berakhir;
        $this->timer = $this->waktu_tes_berakhir->timestamp;
    }

    protected function _timerIntelektualSubTes1()
    {
        $test = UjianIntelektualSubTes1::where('peserta_id', activePesertaId())
            ->where('event_id', activeEventId())
            ->first();

        $this->applyTimerFromTest($test);
    }

    protected function _timerIntelektualSubTes2()
    {
        $test = UjianIntelektualSubTes2::where('peserta_id', activePesertaId())
            ->where('event_id', activeEventId())
            ->first();

        $this->applyTimerFromTest($test);
    }

    protected function _timerIntelektualSubTes3()
    {
        $test = UjianIntelektualSubTes3::where('peserta_id', activePesertaId())
            ->where('event_id', activeEventId())
            ->first();

        $this->applyTimerFromTest($test);
    }

    protected function _timerCakapDigital()
    {
        $test = UjianCakapDigital::where('peserta_id', activePesertaId())
            ->where('event_id', activeEventId())
            ->first();

        $this->applyTimerFromTest($test);
    }

    protected function _timerLiterasiDigital()
    {
        $test = UjianLiterasiDigital::where('peserta_id', activePesertaId())
            ->where('event_id', activeEventId())
            ->first();

        $this->applyTimerFromTest($test);
    }

    protected function _timerKompetensiTeknis()
    {
        $test = UjianKompetensiTeknis::where('peserta_id', activePesertaId())
            ->where('event_id', activeEventId())
            ->first();

        $this->applyTimerFromTest($test);
    }

    protected function _timerPspk()
    {
        $test = UjianPspk::where('peserta_id', activePesertaId())
            ->where('event_id', activeEventId())
            ->first();

        $this->applyTimerFromTest($test);
    }

    protected function _timerInterpersonal()
    {
        $test = UjianInterpersonal::where('peserta_id', activePesertaId())
            ->where('event_id', activeEventId())
            ->first();

        $this->applyTimerFromTest($test);
    }

    protected function _timerPengembanganDiri()
    {
        $test = UjianPengembanganDiri::where('peserta_id', activePesertaId())
            ->where('event_id', activeEventId())
            ->first();

        $this->applyTimerFromTest($test);
    }

    protected function _timerKecerdasanEmosi()
    {
        $test = UjianKecerdasanEmosi::where('peserta_id', activePesertaId())
            ->where('event_id', activeEventId())
            ->first();

        $this->applyTimerFromTest($test);
    }

    protected function _timerMotivasiKomitmen()
    {
        $test = UjianMotivasiKomitmen::where('peserta_id', activePesertaId())
            ->where('event_id', activeEventId())
            ->first();

        $this->applyTimerFromTest($test);
    }

    protected function _timerBerpikirKritis()
    {
        $test = UjianBerpikirKritis::where('peserta_id', activePesertaId())
            ->where('event_id', activeEventId())
            ->first();

        $this->applyTimerFromTest($test);
    }

    protected function _timerProblemSolving()
    {
        $test = UjianProblemSolving::where('peserta_id', activePesertaId())
            ->where('event_id', activeEventId())
            ->first();

        $this->applyTimerFromTest($test);
    }

    protected function _timerKesadaranDiri()
    {
        $test = UjianKesadaranDiri::where('peserta_id', activePesertaId())
            ->where('event_id', activeEventId())
            ->first();

        $this->applyTimerFromTest($test);
    }
}
