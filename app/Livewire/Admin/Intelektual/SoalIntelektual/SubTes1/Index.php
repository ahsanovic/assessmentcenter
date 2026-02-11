<?php

namespace App\Livewire\Admin\Intelektual\SoalIntelektual\SubTes1;

use App\Livewire\Forms\SoalIntelektualFormSubTes1;
use App\Models\Intelektual\RefModelIntelektual;
use App\Models\Intelektual\SoalIntelektual;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.admin.app', ['title' => 'Soal Sub Tes 1'])]
class Index extends Component
{
    use WithPagination;

    public $selected_id;
    public $showModal = false;
    public $isUpdate = false;
    public SoalIntelektualFormSubTes1 $form;

    #[Locked]
    public $editId;

    public function render()
    {
        $data = SoalIntelektual::where('sub_tes', 1)->paginate(10);
        $model_soal_options = RefModelIntelektual::pluck('jenis', 'id');

        return view('livewire.admin.intelektual.soal-subtes1.index', compact('data', 'model_soal_options'));
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
            $data = SoalIntelektual::findOrFail($id);
            $this->editId = $data->id;
            $this->form->model_id = $data->model_id;
            $this->form->soal = $data->soal;
            $this->form->opsi_a = $data->opsi_a;
            $this->form->opsi_b = $data->opsi_b;
            $this->form->opsi_c = $data->opsi_c;
            $this->form->opsi_d = $data->opsi_d;
            $this->form->opsi_e = $data->opsi_e;
            $this->form->kunci_jawaban = $data->kunci_jawaban;
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
                $data = SoalIntelektual::findOrFail($this->editId);
                $old_data = $data->getOriginal();

                $data->model_id = $this->form->model_id;
                $data->soal = $this->form->soal;
                $data->opsi_a = $this->form->opsi_a;
                $data->opsi_b = $this->form->opsi_b;
                $data->opsi_c = $this->form->opsi_c;
                $data->opsi_d = $this->form->opsi_d;
                $data->opsi_e = $this->form->opsi_e;
                $data->kunci_jawaban = $this->form->kunci_jawaban;
                $data->save();

                activity_log($data, 'update', 'soal-intelektual-subtes1', $old_data);

                $this->dispatch('toast', ['type' => 'success', 'message' => 'berhasil ubah data']);
            } else {
                $model = SoalIntelektual::create([
                    'model_id' => $this->form->model_id,
                    'sub_tes' => 1,
                    'soal' => $this->form->soal,
                    'opsi_a' => $this->form->opsi_a,
                    'opsi_b' => $this->form->opsi_b,
                    'opsi_c' => $this->form->opsi_c,
                    'opsi_d' => $this->form->opsi_d,
                    'opsi_e' => $this->form->opsi_e,
                    'kunci_jawaban' => $this->form->kunci_jawaban,
                ]);

                activity_log($model, 'create', 'soal-intelektual-subtes1');

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
