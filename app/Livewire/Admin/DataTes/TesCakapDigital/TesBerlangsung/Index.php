<?php

namespace App\Livewire\Admin\DataTes\TesCakapDigital\TesBerlangsung;

use App\Models\Event;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.admin.app', ['title' => 'Event'])]
class Index extends Component
{
    use WithPagination;

    public $jabatan_diuji;
    public $tgl_mulai;
    public $selected_id;

    public function render()
    {
        $data = Event::withCount(['peserta', 'ujianCakapDigital'])
            ->with(['peserta', 'ujianCakapDigital'])
            ->whereIsFinished('false')
            ->where('metode_tes_id', 3)
            ->orderByDesc('id')
            ->paginate(10);

        return view('livewire.admin.data-tes.tes-cakap-digital.tes-berlangsung.index', compact('data'));
    }
}
