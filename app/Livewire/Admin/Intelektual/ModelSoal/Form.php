<?php

namespace App\Livewire\Admin\Intelektual\ModelSoal;

use App\Livewire\Forms\ModelSoalForm;
use App\Models\Intelektual\ModelSoal;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Component;

#[Layout('components.layouts.admin.app', ['title' => 'Intelektual'])]
class Form extends Component
{
    public ModelSoalForm $form;
    public $isUpdate = false;
    public $jenis;
    public $is_active;

    #[Locked]
    public $id;

    public function mount($id = null)
    {
        try {
            if ($id) {
                $this->isUpdate = true;

                $data = ModelSoal::findOrFail($id);
                $this->id = $data->id;
                $this->form->jenis = $data->jenis;
                $this->form->is_active = $data->is_active;
            }
        } catch (\Throwable $th) {
            // throw $th;
            $this->dispatch('toast', ['type' => 'error', 'message' => 'terjadi kesalahan']);
        }
    }

    public function render()
    {
        return view('livewire.admin.intelektual.model-soal.form');
    }

    public function save()
    {
        $this->validate();
        try {
            if ($this->isUpdate) {
                $data = ModelSoal::findOrFail($this->id);
                $old_data = $data->getOriginal();

                if ($data->is_active == 'true') {
                    ModelSoal::where('id', $this->id)->update(['is_active' => 'false']);
                    ModelSoal::where('id', '!=', $this->id)->update(['is_active' => 'true']);
                } else {
                    ModelSoal::where('id', $this->id)->update(['is_active' => 'true']);
                    ModelSoal::where('id', '!=', $this->id)->update(['is_active' => 'false']);
                }

                $data->jenis = $this->form->jenis;
                $data->is_active = $this->form->is_active;
                $data->save();


                activity_log($data, 'update', 'model-soal-intelektual', $old_data);

                session()->flash('toast', [
                    'type' => 'success',
                    'message' => 'berhasil ubah data'
                ]);

                $this->redirect(route('admin.model-soal-intelektual'), true);
            } else {
                $check_duplicate = ModelSoal::where('jenis', $this->form->jenis)->exists();
                if ($check_duplicate) {
                    $this->dispatch('toast', [
                        'type' => 'error',
                        'message' => 'data dengan model soal ' . $this->form->jenis . ' sudah ada!'
                    ]);
                    return;
                }

                $model = ModelSoal::create([
                    'jenis' => $this->form->jenis,
                ]);

                activity_log($model, 'create', 'model-soal-intelektual');

                session()->flash('toast', [
                    'type' => 'success',
                    'message' => 'berhasil tambah data'
                ]);

                $this->redirect(route('admin.model-soal-intelektual'), true);
            }
        } catch (\Throwable $th) {
            // throw $th;
            $this->dispatch('toast', ['type' => 'error', 'message' => 'terjadi kesalahan']);
        }
    }
}
