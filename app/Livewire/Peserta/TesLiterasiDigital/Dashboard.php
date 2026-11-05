<?php

namespace App\Livewire\Peserta\TesLiterasiDigital;

use App\Models\LiterasiDigital\UjianLiterasiDigital;
use App\Models\Peserta;
use App\Services\LiterasiDigital\LiterasiDigitalUjianService;
use Livewire\Attributes\Layout;
use Livewire\Component;
use RuntimeException;

#[Layout('components.layouts.peserta.app', ['title' => 'Tes Literasi Digital'])]
class Dashboard extends Component
{
    public function start(LiterasiDigitalUjianService $ujianService)
    {
        $peserta = Peserta::where('id', auth()->guard('peserta')->user()->id)->first(['id']);
        if ($peserta) {
            $peserta->test_started_at = now();
            $peserta->save();
        }

        $selesai = UjianLiterasiDigital::where('event_id', activeEventId())
            ->where('peserta_id', activePesertaId())
            ->where('is_finished', 'true')
            ->exists();

        if ($selesai) {
            session()->flash('toast', [
                'type' => 'error',
                'message' => 'Anda sudah melakukan Tes Literasi Digital.',
            ]);

            return $this->redirect(route('peserta.tes-literasi-digital.home'), navigate: true);
        }

        $berlangsung = UjianLiterasiDigital::where('event_id', activeEventId())
            ->where('peserta_id', activePesertaId())
            ->where('is_finished', 'false')
            ->exists();

        if ($berlangsung) {
            return $this->redirect(route('peserta.tes-literasi-digital.ujian', ['id' => 1]));
        }

        try {
            $ujianService->mulai(activeEventId(), activePesertaId());
        } catch (RuntimeException $e) {
            session()->flash('toast', [
                'type' => 'error',
                'message' => $e->getMessage(),
            ]);

            return $this->redirect(route('peserta.tes-literasi-digital.home'), navigate: true);
        }

        return $this->redirect(route('peserta.tes-literasi-digital.ujian', ['id' => 1]));
    }

    public function render()
    {
        return view('livewire.peserta.tes-literasi-digital.dashboard');
    }
}
