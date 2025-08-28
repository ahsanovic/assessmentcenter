<?php

namespace App\Livewire\Peserta\TesPotensi;

use App\Models\Event;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('components.layouts.peserta.app', ['title' => 'Dashboard'])]
class Index extends Component
{
    public $pin_ujian;

    // #[Validate(['pin_ujian' => 'required'], message: [
    //     'pin_ujian.required' => 'harus diisi',
    // ])]

    public function render()
    {
        return view('livewire..peserta.tes-potensi.index');
    }

    public function submit()
    {
        // $this->validate();
        try {
            $pin = Event::whereId(Auth::user()->event_id)
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

            return $this->redirect(route('peserta.tes-potensi.home'), navigate: true);
        } catch (\Throwable $th) {
            // throw $th;
            $this->dispatch('toast', ['type' => 'error', 'message' => 'terjadi kesalahan']);
        }
    }
}
