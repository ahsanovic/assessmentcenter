<?php

namespace App\Livewire\Peserta\TesLiterasiDigital;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.peserta.app', ['title' => 'Hasil Tes Literasi Digital'])]
class Hasil extends Component
{
    public function render()
    {
        return view('livewire.peserta.tes-literasi-digital.hasil');
    }
}
