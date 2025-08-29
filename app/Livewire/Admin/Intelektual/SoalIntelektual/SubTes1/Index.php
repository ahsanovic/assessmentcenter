<?php

namespace App\Livewire\Admin\Intelektual\SoalIntelektual\SubTes1;

use App\Models\Intelektual\SoalIntelektual;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.admin.app', ['title' => 'Soal Sub Tes 1'])]
class Index extends Component
{
    use WithPagination;

    public $selected_id;

    public function render()
    {
        $data = SoalIntelektual::where('sub_tes', 1)->paginate(10);

        return view('livewire.admin.intelektual.soal-subtes1.index', compact('data'));
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
            $data = SoalIntelektual::find($this->selected_id);
            $old_data = $data->getOriginal();

            activity_log($data, 'delete', 'soal-intelektual-subtes1', $old_data);

            $data->delete();

            $this->dispatch('toast', ['type' => 'success', 'message' => 'berhasil menghapus data']);
        } catch (\Throwable $th) {
            // throw $th;
            $this->dispatch('toast', ['type' => 'error', 'message' => 'gagal menghapus data']);
        }
    }
}
