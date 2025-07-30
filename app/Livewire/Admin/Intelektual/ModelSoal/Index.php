<?php

namespace App\Livewire\Admin\Intelektual\ModelSoal;

use App\Models\Intelektual\ModelSoal;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.admin.app', ['title' => 'Model Soal Intelektual'])]
class Index extends Component
{
    use WithPagination;

    public $selected_id;
    public $is_active;

    public function render()
    {
        $data = ModelSoal::paginate(10);

        return view('livewire.admin.intelektual.model-soal.index', compact('data'));
    }

    public function deleteConfirmation($id)
    {
        $this->selected_id = $id;
        $this->dispatch('show-delete-confirmation');
    }

    public function changeStatusConfirmation($id)
    {
        $this->selected_id = $id;
        $this->dispatch('change-status-confirmation');
    }

    #[On('changeStatus')]
    public function changeStatus()
    {
        try {
            $data = ModelSoal::find($this->selected_id);
            $old_data = $data->getOriginal();

            if ($data->is_active == 'true') {
                ModelSoal::where('id', $this->selected_id)->update(['is_active' => 'false']);
                ModelSoal::where('id', '!=', $this->selected_id)->update(['is_active' => 'true']);
            } else {
                ModelSoal::where('id', $this->selected_id)->update(['is_active' => 'true']);
                ModelSoal::where('id', '!=', $this->selected_id)->update(['is_active' => 'false']);
            }

            activity_log($data, 'update', 'model-soal-intelektual', $old_data);

            $this->dispatch('toast', ['type' => 'success', 'message' => 'berhasil merubah status']);
        } catch (\Throwable $th) {
            // throw $th;
            $this->dispatch('toast', ['type' => 'error', 'message' => 'gagal merubah status']);
        }
    }

    #[On('delete')]
    public function destroy()
    {
        try {
            $data = ModelSoal::find($this->selected_id);
            $old_data = $data->getOriginal();

            activity_log($data, 'delete', 'model-soal-intelektual', $old_data);

            $data->delete();

            $this->dispatch('toast', ['type' => 'success', 'message' => 'berhasil menghapus data']);
        } catch (\Throwable $th) {
            // throw $th;
            $this->dispatch('toast', ['type' => 'error', 'message' => 'gagal menghapus data']);
        }
    }
}
