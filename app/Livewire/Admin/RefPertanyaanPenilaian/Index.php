<?php

namespace App\Livewire\Admin\RefPertanyaanPenilaian;

use App\Models\RefPertanyaanPenilaian;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.admin.app', ['title' => 'Referensi Pertanyaan Penilaian Pribadi'])]
class Index extends Component
{
    use WithPagination;

    public $selected_id;

    #[Url(as: 'q')]
    public ?string $search = '';

    public function updatedSearch()
    {
        $this->resetPage();
    }
    
    public function render()
    {
        $data = RefPertanyaanPenilaian::when($this->search, function($query) {
                    $query->where('pertanyaan', 'like', '%' . $this->search . '%');
                })
                ->orderByDesc('id')
                ->paginate(10);
        
        return view('livewire.admin.pertanyaan-penilaian.index', compact('data'));
    }

    public function resetFilters()
    {
        $this->reset();
        $this->resetPage();
        $this->render();
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
            RefPertanyaanPenilaian::find($this->selected_id)->delete();

            $this->dispatch('toast', ['type' => 'success', 'message' => 'berhasil menghapus data']);
        } catch (\Throwable $th) {
            // throw $th;
            $this->dispatch('toast', ['type' => 'error', 'message' => 'gagal menghapus data']);
        }
    }
}
