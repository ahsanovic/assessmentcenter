<?php

namespace App\Livewire\Admin\DataTes\TesBerlangsung;

use App\Models\Event;
use App\Models\KecerdasanEmosi\UjianKecerdasanEmosi;
use App\Models\RefJabatanDiuji;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
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
        $data = Event::withCount(['peserta', 'ujianInterpersonal', 'ujianPengembanganDiri', 'ujianKecerdasanEmosi'])
            ->with(['peserta', 'ujianInterpersonal', 'ujianPengembanganDiri', 'ujianKecerdasanEmosi'])
            ->whereIsFinished('false')
            ->orderByDesc('id')
            ->paginate(10);

        $option_jabatan_diuji = RefJabatanDiuji::pluck('jenis', 'id');

        return view('livewire.admin.data-tes.tes-berlangsung.index', compact('data', 'option_jabatan_diuji'));
    }

    public function deleteConfirmation($id)
    {
        $this->selected_id = $id;
        $this->dispatch('show-delete-confirmation');
    }

    #[On('delete')]
    public function destroy()
    {
        try {
            Event::find($this->selected_id)->delete();

            $this->dispatch('toast', ['type' => 'success', 'message' => 'berhasil menghapus data']);
        } catch (\Throwable $th) {
            // throw $th;
            $this->dispatch('toast', ['type' => 'error', 'message' => 'gagal menghapus data']);
        }
    }
}
