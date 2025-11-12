<?php

namespace App\Livewire\Admin\Pspk\Ref;

use App\Models\Pspk\RefDescPspk;
use App\Models\Pspk\RefLevelPspk;
use App\Models\RefAspekPspk;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.admin.app', ['title' => 'Referensi Deskripsi PSPK'])]
class Index extends Component
{
    use WithPagination;

    public $selected_id;
    public $aspek_id;
    public $level_pspk;

    #[Url(as: 'q')]
    public ?string $search = '';

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $aspek_options = RefAspekPspk::pluck('nama_aspek', 'id');
        $level_pspk_options = RefLevelPspk::pluck('level_pspk', 'id');

        $data = RefDescPspk::when($this->search, function ($query) {
            $query->where('deskripsi', 'like', '%' . $this->search . '%')
                ->orWhere('deskripsi_min', 'like', '%' . $this->search . '%')
                ->orWhere('deskripsi_plus', 'like', '%' . $this->search . '%');
        })
            ->when($this->level_pspk, function ($query) {
                $query->where('level_pspk', $this->level_pspk);
            })
            ->when($this->aspek_id, function ($query) {
                $query->where('aspek_id', $this->aspek_id);
            })
            ->orderByDesc('id')
            ->paginate(10);

        return view('livewire.admin.pspk.ref-deskripsi.index', compact('data', 'aspek_options', 'level_pspk_options'));
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
            $data = RefDescPspk::find($this->selected_id);
            $old_data = $data->getOriginal();

            activity_log($data, 'delete', 'ref-pspk', $old_data);

            $data->delete();

            $this->dispatch('toast', ['type' => 'success', 'message' => 'berhasil menghapus data']);
        } catch (\Throwable $th) {
            // throw $th;
            $this->dispatch('toast', ['type' => 'error', 'message' => 'gagal menghapus data']);
        }
    }
}
