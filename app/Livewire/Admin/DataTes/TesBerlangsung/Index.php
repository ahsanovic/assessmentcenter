<?php

namespace App\Livewire\Admin\DataTes\TesBerlangsung;

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
        $data = Event::withCount(['peserta', 'ujianInterpersonal', 'ujianKesadaranDiri', 'ujianBerpikirKritis', 'ujianProblemSolving', 'ujianPengembanganDiri', 'ujianKecerdasanEmosi', 'ujianMotivasiKomitmen'])
            ->with(['peserta', 'ujianInterpersonal', 'ujianKesadaranDiri', 'ujianBerpikirKritis', 'ujianProblemSolving', 'ujianPengembanganDiri', 'ujianKecerdasanEmosi', 'ujianMotivasiKomitmen'])
            ->whereIsFinished('false')
            ->orderByDesc('id')
            ->paginate(10);

        return view('livewire.admin.data-tes.tes-berlangsung.index', compact('data'));
    }
}
