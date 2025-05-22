<?php

namespace App\Livewire\Admin\Kuesioner;

use App\Models\Kuesioner;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.admin.app', ['title' => 'Kuesioner'])]
class Index extends Component
{
    use WithPagination;

    public $selected_id;
    public $is_active;
    public $is_esai;

    #[Url(as: 'q')]
    public ?string $search = '';

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $data = Kuesioner::when($this->search, function ($query) {
            $query->where('deskripsi', 'like', '%' . $this->search . '%');
        })
            ->when($this->is_esai, function ($query) {
                $query->where('is_esai', $this->is_esai);
            })
            ->when($this->is_active, function ($query) {
                $query->where('is_active', $this->is_active);
            })
            ->orderByDesc('id')
            ->paginate(10);

        return view('livewire.admin.kuesioner.index', compact('data'));
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
            $data = Kuesioner::find($this->selected_id);
            $old_data = $data->getOriginal();

            activity_log($data, 'delete', 'kuesioner', $old_data);

            $data->delete();

            $this->dispatch('toast', ['type' => 'success', 'message' => 'berhasil menghapus data']);
        } catch (\Throwable $th) {
            // throw $th;
            $this->dispatch('toast', ['type' => 'error', 'message' => 'gagal menghapus data']);
        }
    }
}
