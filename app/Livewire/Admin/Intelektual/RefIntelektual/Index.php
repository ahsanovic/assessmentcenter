<?php

namespace App\Livewire\Admin\Intelektual\RefIntelektual;

use App\Models\Intelektual\RefIntelektual;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.admin.app', ['title' => 'Referensi Intelektual'])]
class Index extends Component
{
    use WithPagination;

    public $selected_id;

    public function render()
    {
        $data = RefIntelektual::paginate(10);

        return view('livewire.admin.intelektual.referensi.index', compact('data'));
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
            $data = RefIntelektual::find($this->selected_id);
            $old_data = $data->getOriginal();

            activity_log($data, 'delete', 'ref-intelektual', $old_data);

            $data->delete();

            $this->dispatch('toast', ['type' => 'success', 'message' => 'berhasil menghapus data']);
        } catch (\Throwable $th) {
            // throw $th;
            $this->dispatch('toast', ['type' => 'error', 'message' => 'gagal menghapus data']);
        }
    }
}
