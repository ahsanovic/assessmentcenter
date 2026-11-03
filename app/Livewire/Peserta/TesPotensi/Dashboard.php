<?php

namespace App\Livewire\Peserta\TesPotensi;

use App\Models\Peserta;
use App\Models\Settings;
use App\Traits\StartTestTrait;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.peserta.app', ['title' => 'Dashboard'])]
class Dashboard extends Component
{
    use StartTestTrait;

    public $first_test;

    public function mount(): void
    {
        if (activePeserta()?->event->metode_tes_id === 2
            && ! pesertaParticipationService()->intelektualSelesai()) {
            session()->flash('toast', [
                'type' => 'warning',
                'message' => 'Selesaikan Tes Intelektual terlebih dahulu.',
            ]);

            $this->redirect(route('peserta.tes-intelektual.home'), navigate: true);

            return;
        }

        $this->first_test = Settings::where('urutan', 1)->first();
    }

    public function start()
    {
        $test_started = Peserta::where('id', activePesertaId())->first(['id', 'test_started_at']);

        if ($test_started && ! $test_started->test_started_at) {
            $test_started->test_started_at = now();
            $test_started->save();
        }

        $ringkasan = collect($this->ringkasanSubtesPotensi());
        $target = $ringkasan->firstWhere('status', 'berlangsung')
            ?? $ringkasan->firstWhere('status', 'belum');

        if ($target) {
            $this->bukaSubtes((int) $target['urutan']);
        }
    }

    public function render()
    {
        $subtes = $this->ringkasanSubtesPotensi();

        return view('livewire..peserta.tes-potensi.dashboard', [
            'ada_progres' => collect($subtes)->contains(fn ($item) => $item['status'] !== 'belum'),
        ]);
    }
}
