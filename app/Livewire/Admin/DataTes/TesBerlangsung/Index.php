<?php

namespace App\Livewire\Admin\DataTes\TesBerlangsung;

use App\Models\Event;
use App\Models\RefJabatanDiuji;
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
        $data = Event::withCount(['peserta', 'ujianInterpersonal', 'ujianPengembanganDiri', 'ujianKecerdasanEmosi', 'ujianMotivasiKomitmen'])
            ->with(['peserta', 'ujianInterpersonal', 'ujianPengembanganDiri', 'ujianKecerdasanEmosi', 'ujianMotivasiKomitmen'])
            ->whereIsFinished('false')
            ->orderByDesc('id')
            ->paginate(10);

        $option_jabatan_diuji = RefJabatanDiuji::pluck('jenis', 'id');

        return view('livewire.admin.data-tes.tes-berlangsung.index', compact('data', 'option_jabatan_diuji'));
    }
}
