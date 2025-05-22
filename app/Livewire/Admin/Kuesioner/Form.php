<?php

namespace App\Livewire\Admin\Kuesioner;

use App\Livewire\Forms\KuesionerForm;
use App\Models\Kuesioner;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Component;

#[Layout('components.layouts.admin.app', ['title' => 'Kuesioner'])]
class Form extends Component
{
    public KuesionerForm $form;
    public $isUpdate = false;
    public $is_active;
    public $is_esai;

    #[Locked]
    public $id;

    public function mount($id = null)
    {
        try {
            if ($id) {
                $this->isUpdate = true;

                $data = Kuesioner::findOrFail($id);
                $this->id = $data->id;
                $this->form->deskripsi = $data->deskripsi;
                $this->form->is_esai = $data->is_esai;
                $this->form->is_active = $data->is_active;
            }
        } catch (\Throwable $th) {
            // throw $th;
            $this->dispatch('toast', ['type' => 'error', 'message' => 'terjadi kesalahan']);
        }
    }

    public function render()
    {
        return view('livewire.admin.kuesioner.form');
    }

    public function save()
    {
        $this->validate();
        try {
            if ($this->isUpdate) {
                $data = Kuesioner::findOrFail($this->id);
                $old_data = $data->getOriginal();

                $data->deskripsi = $this->form->deskripsi;
                $data->is_esai = $this->form->is_esai;
                $data->is_active = $this->form->is_active;
                $data->save();

                activity_log($data, 'update', 'kuesioner', $old_data);

                session()->flash('toast', [
                    'type' => 'success',
                    'message' => 'berhasil ubah data'
                ]);

                $this->redirect(route('admin.kuesioner'), true);
            } else {
                $data = Kuesioner::create([
                    'deskripsi' => $this->form->deskripsi,
                    'is_esai' => $this->form->is_esai,
                    'is_active' => $this->form->is_active,
                ]);

                activity_log($data, 'create', 'kuesioner');

                session()->flash('toast', [
                    'type' => 'success',
                    'message' => 'berhasil tambah data'
                ]);

                $this->redirect(route('admin.kuesioner'), true);
            }
        } catch (\Throwable $th) {
            // throw $th;
            $this->dispatch('toast', ['type' => 'error', 'message' => 'terjadi kesalahan']);
        }
    }
}
