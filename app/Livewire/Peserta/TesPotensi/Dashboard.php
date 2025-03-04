<?php

namespace App\Livewire\Peserta\TesPotensi;

use App\Models\Settings;
use App\Traits\StartTestTrait;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.peserta.app', ['title' => 'Dashboard'])]
class Dashboard extends Component
{
    use StartTestTrait;

    public $first_test;

    public function mount()
    {
        $this->first_test = Settings::where('urutan', 1)->first();
    }

    public function start()
    {
        if ($this->first_test) {
            session(['current_test' => $this->first_test->alat_tes_id]);
            $this->first_test->load('alatTes');

            $this->startTest($this->first_test->alatTes->alat_tes);
        }
    }

    public function render()
    {
        return view('livewire..peserta.tes-potensi.dashboard');
    }
}
