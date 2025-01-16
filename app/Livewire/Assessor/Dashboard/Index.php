<?php

namespace App\Livewire\Assessor\Dashboard;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.assessor.app', ['title' => 'Dashboard'])]
class Index extends Component
{
    public function render()
    {
        return view('livewire..assessor.dashboard.index');
    }
}
