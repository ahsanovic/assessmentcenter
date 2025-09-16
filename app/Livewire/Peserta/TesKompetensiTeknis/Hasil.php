<?php

namespace App\Livewire\Peserta\TesKompetensiTeknis;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.peserta.app', ['title' => 'Hasil Nilai Tes'])]
class Hasil extends Component
{
    public function render()
    {
        return view('livewire.peserta.tes-kompetensi-teknis.hasil');
    }
}
