<?php

namespace App\Livewire\Admin\Pspk\Ref;

use App\Livewire\Forms\DescPspkForm;
use App\Models\Pspk\RefDescPspk;
use App\Models\Pspk\RefLevelPspk;
use App\Models\RefAspekPspk;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Component;

#[Layout('components.layouts.admin.app', ['title' => 'Referensi Deskripsi PSPK'])]
class Form extends Component
{
    public DescPspkForm $form;
    public $isUpdate = false;

    #[Locked]
    public $id;

    public function mount($id = null)
    {
        try {
            if ($id) {
                $this->isUpdate = true;

                $data = RefDescPspk::findOrFail($id);
                $this->id = $data->id;
                $this->form->level_pspk = $data->level_pspk;
                $this->form->aspek = $data->aspek_id;
                $this->form->deskripsi_min = $data->deskripsi_min;
                $this->form->deskripsi = $data->deskripsi;
                $this->form->deskripsi_plus = $data->deskripsi_plus;
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

        return view('livewire.admin.pspk.ref-deskripsi.form', compact('aspek_options', 'level_pspk_options'));
    }

    public function save()
    {
        $this->validate();
        try {
            if ($this->isUpdate) {
                $data = RefDescPspk::findOrFail($this->id);
                $old_data = $data->getOriginal();

                $data->level_pspk = $this->form->level_pspk;
                $data->aspek_id = $this->form->aspek;
                $data->deskripsi_min = $this->form->deskripsi_min;
                $data->deskripsi = $this->form->deskripsi;
                $data->deskripsi_plus = $this->form->deskripsi_plus;
                $data->save();

                activity_log($data, 'update', 'ref-pspk', $old_data);

                session()->flash('toast', [
                    'type' => 'success',
                    'message' => 'berhasil ubah data'
                ]);

                $this->redirect(route('admin.ref-pspk'), true);
            } else {
                $model = RefDescPspk::create([
                    'level_pspk' => $this->form->level_pspk,
                    'aspek_id' => $this->form->aspek,
                    'deskripsi_min' => $this->form->deskripsi_min,
                    'deskripsi' => $this->form->deskripsi,
                    'deskripsi_plus' => $this->form->deskripsi_plus,
                ]);

                activity_log($model, 'create', 'ref-pspk');

                session()->flash('toast', [
                    'type' => 'success',
                    'message' => 'berhasil tambah data'
                ]);

                $this->redirect(route('admin.ref-pspk'), true);
            }
        } catch (\Throwable $th) {
            // throw $th;
            $this->dispatch('toast', ['type' => 'error', 'message' => 'terjadi kesalahan']);
        }
    }
}
