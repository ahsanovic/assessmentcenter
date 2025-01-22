<?php

namespace App\Livewire\Admin\Settings;

use App\Models\RefAlatTes;
use App\Models\Settings;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.admin.app', ['title' => 'Setting Tes'])]
class Index extends Component
{
    use WithPagination;

    public $selected_id;
    public $alat_tes;

    #[Url(as: 'q')]
    public ?string $search = '';

    public function updatedSearch()
    {
        $this->resetPage();
    }
    
    public function render()
    {
        $data = Settings::with('alatTes')->when($this->alat_tes, function($query) {
                    $query->where('alat_tes_id', $this->alat_tes);
                })
                ->orderByDesc('id')
                ->paginate(10);

        $option_alat_tes = RefAlatTes::pluck('alat_tes', 'id');
        
        return view('livewire.admin.settings.index', compact('data', 'option_alat_tes'));
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
            Settings::find($this->selected_id)->delete();

            $this->dispatch('toast', ['type' => 'success', 'message' => 'berhasil menghapus data']);
        } catch (\Throwable $th) {
            // throw $th;
            $this->dispatch('toast', ['type' => 'error', 'message' => 'gagal menghapus data']);
        }
    }
}
