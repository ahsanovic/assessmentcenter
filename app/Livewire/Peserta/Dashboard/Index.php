<?php

namespace App\Livewire\Peserta\Dashboard;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.peserta.app', ['title' => 'Dashboard'])]
class Index extends Component
{
    public function render()
    {
        return view('livewire..peserta.dashboard.index');
    }
}
