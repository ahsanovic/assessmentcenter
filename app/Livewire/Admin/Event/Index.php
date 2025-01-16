<?php

namespace App\Livewire\Admin\Event;

use App\Models\Event;
use App\Models\RefJabatanDiuji;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.admin.app', ['title' => 'Event'])]
class Index extends Component
{
    use WithPagination;

    public $jabatan_diuji;
    public $tgl_mulai;
    public $selected_id;

    #[Url(as: 'q')]
    public ?string $search =  '';

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedJabatanDiuji()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->reset();
        $this->resetPage();
        $this->render();
    }

    public function render()
    {
        $data = Event::withCount('assessor', 'peserta')->when($this->search, function($query) {
            $query->where('nama_event', 'like', '%' . $this->search . '%');
        })
        ->when($this->jabatan_diuji, function($query,) {
            $query->where('jabatan_diuji_id', $this->jabatan_diuji);
        })
        ->when($this->tgl_mulai, function($query) {
            $tgl_mulai = date('Y-m-d', strtotime($this->tgl_mulai));
            $query->where('tgl_mulai', $tgl_mulai);
        })
        ->with(['peserta', 'alatTes'])
        ->orderByDesc('id')
        ->paginate(10);

        $option_jabatan_diuji = RefJabatanDiuji::pluck('jenis', 'id');

        return view('livewire.admin.event.index', compact('data', 'option_jabatan_diuji'));
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
