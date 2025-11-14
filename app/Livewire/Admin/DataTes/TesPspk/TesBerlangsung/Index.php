<?php

namespace App\Livewire\Admin\DataTes\TesPspk\TesBerlangsung;

use App\Models\Event;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.admin.app', ['title' => 'Tes PSPK'])]
class Index extends Component
{
    use WithPagination;

    public $tgl_mulai;
    public $selected_id;

    public function render()
    {
        $data = Event::withCount(['peserta', 'ujianPspk'])
            ->with(['peserta', 'ujianPspk'])
            ->whereIsFinished('false')
            ->whereIn('metode_tes_id', [5,6])
            ->orderByDesc('id')
            ->paginate(10);

        return view('livewire.admin.data-tes.tes-pspk.tes-berlangsung.index', compact('data'));
    }
}
