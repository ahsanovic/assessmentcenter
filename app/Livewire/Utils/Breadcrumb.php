<?php

namespace App\Livewire\Admin\DistribusiPeserta;

use App\Models\Event;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class Breadcrumb extends Component
{
    public $breadcrumbs = [];

    public function mount($breadcrumbs = [])
    {
        $this->breadcrumbs = $breadcrumbs;
    }

    public function render()
    {
        return view('components.breadcrumb');
    }
}
