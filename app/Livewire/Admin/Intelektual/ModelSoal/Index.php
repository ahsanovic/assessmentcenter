<?php

namespace App\Livewire\Admin\Intelektual\ModelSoal;

use App\Livewire\Forms\ModelSoalForm;
use App\Models\Intelektual\ModelSoal;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.admin.app', ['title' => 'Model Soal Intelektual'])]
class Index extends Component
{
    use WithPagination;

    public $selected_id;
    public $is_active;
    public $showModal = false;
    public $isUpdate = false;
    public ModelSoalForm $form;

    #[Locked]
    public $editId;

    public function render()
    {
        $data = ModelSoal::paginate(10);

        return view('livewire.admin.intelektual.model-soal.index', compact('data'));
    }

    public function openModal()
    {
        $this->resetValidation();
        $this->form->reset();
        $this->isUpdate = false;
        $this->editId = null;
        $this->showModal = true;
        $this->dispatch('modalOpened');
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetValidation();
        $this->form->reset();
        $this->isUpdate = false;
        $this->editId = null;
    }

    public function edit($id)
    {
        try {
            $data = ModelSoal::findOrFail($id);
            $this->editId = $data->id;
            $this->form->jenis = $data->jenis;
            $this->form->is_active = $data->is_active;
            $this->isUpdate = true;
            $this->showModal = true;
            $this->resetValidation();
            $this->dispatch('modalOpened');
        } catch (\Throwable $th) {
            $this->dispatch('toast', ['type' => 'error', 'message' => 'terjadi kesalahan']);
        }
    }

    public function save()
    {
        $this->validate();
        try {
            if ($this->isUpdate) {
                $data = ModelSoal::findOrFail($this->editId);
                $old_data = $data->getOriginal();

                if ($this->form->is_active == 'true') {
                    ModelSoal::where('id', $this->editId)->update(['is_active' => 'true']);
                    ModelSoal::where('id', '!=', $this->editId)->update(['is_active' => 'false']);
                } else {
                    ModelSoal::where('id', $this->editId)->update(['is_active' => 'false']);
                }

                $data->jenis = $this->form->jenis;
                $data->is_active = $this->form->is_active;
                $data->save();

                activity_log($data, 'update', 'model-soal-intelektual', $old_data);

                $this->dispatch('toast', ['type' => 'success', 'message' => 'berhasil ubah data']);
            } else {
                $check_duplicate = ModelSoal::where('jenis', $this->form->jenis)->exists();
                if ($check_duplicate) {
                    $this->dispatch('toast', ['type' => 'error', 'message' => 'data dengan model soal ' . $this->form->jenis . ' sudah ada!']);
                    return;
                }

                $model = ModelSoal::create([
                    'jenis' => $this->form->jenis,
                ]);

                activity_log($model, 'create', 'model-soal-intelektual');

                $this->dispatch('toast', ['type' => 'success', 'message' => 'berhasil tambah data']);
            }

            $this->closeModal();
            $this->resetPage();
        } catch (\Throwable $th) {
            // throw $th;
            $this->dispatch('toast', ['type' => 'error', 'message' => 'terjadi kesalahan']);
        }
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
