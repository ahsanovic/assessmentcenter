<?php

namespace App\Livewire\Admin\Intelektual\SoalIntelektual\SubTes2;

use App\Models\Intelektual\SoalIntelektual;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.admin.app', ['title' => 'Soal Sub Tes 2'])]
class Index extends Component
{
    use WithPagination;

    public $selected_id;
    public $selectedSoal; // untuk detail modal
    public $showDetailModal = false;

    public function render()
    {
        $data = SoalIntelektual::where('sub_tes', 2)->paginate(10);

        return view('livewire.admin.intelektual.soal-subtes2.index', compact('data'));
    }

    public function deleteConfirmation($id)
    {
        $this->selected_id = $id;
        $this->dispatch('show-delete-confirmation');
    }

    public function showDetail($id)
    {
        $this->selectedSoal = SoalIntelektual::with('modelSoal')->findOrFail($id);
        $this->showDetailModal = true;
    }

    #[On('delete')]
    public function destroy()
    {
        try {
            $data = SoalIntelektual::find($this->selected_id);
            $old_data = $data->getOriginal();

            $fields = [
                'image_soal',
                'image_opsi_a',
                'image_opsi_b',
                'image_opsi_c',
                'image_opsi_d',
                'image_opsi_e',
            ];

            foreach ($fields as $field) {
                if (!empty($data->$field) && Storage::disk('public')->exists($data->$field)) {
                    Storage::disk('public')->delete($data->$field);
                }
            }

            activity_log($data, 'delete', 'soal-intelektual-subtes2', $old_data);

            $data->delete();

            $this->dispatch('toast', ['type' => 'success', 'message' => 'berhasil menghapus data']);
        } catch (\Throwable $th) {
            // throw $th;
            $this->dispatch('toast', ['type' => 'error', 'message' => 'gagal menghapus data']);
        }
    }
}
