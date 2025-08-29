<?php

namespace App\Livewire\Admin\Intelektual\SoalIntelektual\SubTes1;

use App\Livewire\Forms\SoalIntelektualFormSubTes1;
use App\Models\Intelektual\RefModelIntelektual;
use App\Models\Intelektual\SoalIntelektual;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Component;

#[Layout('components.layouts.admin.app', ['title' => 'Soal Sub Tes 1'])]
class Form extends Component
{
    public SoalIntelektualFormSubTes1 $form;
    public $isUpdate = false;

    #[Locked]
    public $id;

    public function mount($id = null)
    {
        try {
            if ($id) {
                $this->isUpdate = true;
                $data = SoalIntelektual::findOrFail($id);
                $this->id = $data->id;
                $this->form->model_id = $data->model_id;
                $this->form->soal = $data->soal;
                $this->form->opsi_a = $data->opsi_a;
                $this->form->opsi_b = $data->opsi_b;
                $this->form->opsi_c = $data->opsi_c;
                $this->form->opsi_d = $data->opsi_d;
                $this->form->opsi_e = $data->opsi_e;
                $this->form->kunci_jawaban = $data->kunci_jawaban;
            }
        } catch (\Throwable $th) {
            // throw $th;
            $this->dispatch('toast', ['type' => 'error', 'message' => 'terjadi kesalahan']);
        }
    }

    public function render()
    {
        $model_soal_options = RefModelIntelektual::pluck('jenis', 'id');
        return view('livewire.admin.intelektual.soal-subtes1.form', compact('model_soal_options'));
    }

    public function save()
    {
        $this->validate();
        try {
            if ($this->isUpdate) {
                $data = SoalIntelektual::findOrFail($this->id);
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

                session()->flash('toast', [
                    'type' => 'success',
                    'message' => 'berhasil ubah data'
                ]);

                $this->redirect(route('admin.soal-intelektual-subtes1'), true);
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

                session()->flash('toast', [
                    'type' => 'success',
                    'message' => 'berhasil tambah data'
                ]);

                $this->redirect(route('admin.soal-intelektual-subtes1'), true);
            }
        } catch (\Throwable $th) {
            // throw $th;
            $this->dispatch('toast', ['type' => 'error', 'message' => 'terjadi kesalahan']);
        }
    }
}
