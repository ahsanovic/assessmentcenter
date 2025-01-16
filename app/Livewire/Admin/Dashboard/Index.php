<?php

namespace App\Livewire\Admin\Dashboard;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.admin.app', ['title' => 'Dashboard'])]
class Index extends Component
{
    public function render()
    {
        return view('livewire..admin.dashboard.dashboard');
    }
}
