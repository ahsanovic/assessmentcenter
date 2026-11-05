<?php

namespace App\Livewire\Peserta\TesLiterasiDigital;

use App\Models\Event;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.peserta.app', ['title' => 'Tes Literasi Digital'])]
class Index extends Component
{
    public $pin_ujian;

    public function render()
    {
        return view('livewire.peserta.tes-literasi-digital.index');
    }

    public function submit()
    {
        try {
            $pin = Event::whereId(activeEventId())
                ->whereIsFinished('false')
                ->value('pin_ujian');

            if ($this->pin_ujian == null) {
                $this->dispatch('toast', ['type' => 'error', 'message' => 'PIN ujian harus diisi']);

                return;
            }

            if ($this->pin_ujian !== $pin) {
                $this->dispatch('toast', ['type' => 'error', 'message' => 'PIN ujian salah']);

                return;
            }

            session(['exam_pin' => $this->pin_ujian]);

            return $this->redirect(route('peserta.tes-literasi-digital.home'), navigate: true);
        } catch (\Throwable $th) {
            $this->dispatch('toast', ['type' => 'error', 'message' => 'terjadi kesalahan']);
        }
    }
}
