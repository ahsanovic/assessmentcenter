<?php

namespace App\Livewire\Admin\Pspk\Soal;

use App\Livewire\Forms\SoalPspkForm;
use App\Models\Pspk\RefLevelPspk;
use App\Models\Pspk\SoalPspk;
use App\Models\RefAspekPspk;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Component;

#[Layout('components.layouts.admin.app', ['title' => 'Soal PSPK'])]
class Form extends Component
{
    public SoalPspkForm $form;
    public $isUpdate = false;

    #[Locked]
    public $id;

    public function mount($id = null)
    {
        try {
            if ($id) {
                $this->isUpdate = true;

                $data = SoalPspk::findOrFail($id);
                $this->id = $data->id;
                $this->form->level_pspk_id = $data->level_pspk_id;
                $this->form->aspek = $data->aspek_id;
                $this->form->soal = $data->soal;
                $this->form->opsi_a = $data->opsi_a;
                $this->form->poin_opsi_a = $data->poin_opsi_a;
                $this->form->opsi_b = $data->opsi_b;
                $this->form->poin_opsi_b = $data->poin_opsi_b;
                $this->form->opsi_c = $data->opsi_c;
                $this->form->poin_opsi_c = $data->poin_opsi_c;
                $this->form->opsi_d = $data->opsi_d;
                $this->form->poin_opsi_d = $data->poin_opsi_d;
                $this->form->opsi_e = $data->opsi_e;
                $this->form->poin_opsi_e = $data->poin_opsi_e;
                $this->form->kunci_jawaban = $data->kunci_jawaban;
            }
        } catch (\Throwable $th) {
            // throw $th;
            $this->dispatch('toast', ['type' => 'error', 'message' => 'terjadi kesalahan']);
        }
    }

    public function render()
    {
        $aspek_options = RefAspekPspk::pluck('nama_aspek', 'id');
        $level_pspk_options = RefLevelPspk::pluck('level_pspk', 'id');

        return view('livewire.admin.pspk.soal.form', compact('aspek_options', 'level_pspk_options'));
    }

    public function save()
    {
        $this->validate();
        try {
            if ($this->isUpdate) {
                $data = SoalPspk::findOrFail($this->id);
                $old_data = $data->getOriginal();

                $data->level_pspk_id = $this->form->level_pspk_id;
                $data->aspek_id = $this->form->aspek;
                $data->soal = $this->form->soal;
                $data->opsi_a = $this->form->opsi_a;
                $data->poin_opsi_a = $this->form->poin_opsi_a;
                $data->opsi_b = $this->form->opsi_b;
                $data->poin_opsi_b = $this->form->poin_opsi_b;
                $data->opsi_c = $this->form->opsi_c;
                $data->poin_opsi_c = $this->form->poin_opsi_c;
                $data->opsi_d = $this->form->opsi_d;
                $data->poin_opsi_d = $this->form->poin_opsi_d;
                $data->opsi_e = $this->form->opsi_e;
                $data->poin_opsi_e = $this->form->poin_opsi_e;
                $data->kunci_jawaban = $this->form->kunci_jawaban;
                $data->save();

                activity_log($data, 'update', 'soal-pspk', $old_data);

                session()->flash('toast', [
                    'type' => 'success',
                    'message' => 'berhasil ubah data'
                ]);

                $this->redirect(route('admin.soal-pspk'), true);
            } else {
                $model = SoalPspk::create([
                    'level_pspk_id' => $this->form->level_pspk_id,
                    'aspek_id' => $this->form->aspek,
                    'soal' => $this->form->soal,
                    'opsi_a' => $this->form->opsi_a,
                    'poin_opsi_a' => $this->form->poin_opsi_a,
                    'opsi_b' => $this->form->opsi_b,
                    'poin_opsi_b' => $this->form->poin_opsi_b,
                    'opsi_c' => $this->form->opsi_c,
                    'poin_opsi_c' => $this->form->poin_opsi_c,
                    'opsi_d' => $this->form->opsi_d,
                    'poin_opsi_d' => $this->form->poin_opsi_d,
                    'opsi_e' => $this->form->opsi_e,
                    'poin_opsi_e' => $this->form->poin_opsi_e,
                    'kunci_jawaban' => $this->form->kunci_jawaban,
                ]);

                activity_log($model, 'create', 'soal-pspk');

                session()->flash('toast', [
                    'type' => 'success',
                    'message' => 'berhasil tambah data'
                ]);

                $this->redirect(route('admin.soal-pspk'), true);
            }
        } catch (\Throwable $th) {
            // throw $th;
            $this->dispatch('toast', ['type' => 'error', 'message' => 'terjadi kesalahan']);
        }
    }
}
