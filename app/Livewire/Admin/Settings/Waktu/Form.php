<?php

namespace App\Livewire\Admin\Settings\Waktu;

use App\Livewire\Forms\SettingWaktuTesForm;
use App\Models\SettingWaktuTes;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Component;

#[Layout('components.layouts.admin.app', ['title' => 'Waktu Tes'])]
class Form extends Component
{
    public SettingWaktuTesForm $form;
    public $isUpdate = false;
    public $is_active;

    #[Locked]
    public $id;

    public function mount($id = null)
    {
        try {
            if ($id) {
                $this->isUpdate = true;

                $data = SettingWaktuTes::findOrFail($id);
                $this->id = $data->id;
                $this->form->waktu = $data->waktu;
                $this->form->is_active = $data->is_active;
            }
        } catch (\Throwable $th) {
            // throw $th;
            $this->dispatch('toast', ['type' => 'error', 'message' => 'terjadi kesalahan']);
        }
    }

    public function render()
    {
        return view('livewire.admin.settings.waktu.form');
    }

    public function save()
    {
        $this->validate();
        try {
            if ($this->isUpdate) {
                $data = SettingWaktuTes::find($this->id);
                $old_data = $data->getOriginal();

                $data->update($this->validate());

                activity_log($data, 'update', 'waktu-tes', $old_data);

                session()->flash('toast', [
                    'type' => 'success',
                    'message' => 'berhasil ubah data'
                ]);

                $this->redirect(route('admin.settings.waktu'), true);
            } else {
                $data = SettingWaktuTes::create([
                    'waktu' => $this->form->waktu,
                    'is_active' => $this->form->is_active,
                ]);

                activity_log($data, 'create', 'waktu-tes');

                session()->flash('toast', [
                    'type' => 'success',
                    'message' => 'berhasil tambah data'
                ]);

                $this->redirect(route('admin.settings.waktu'), true);
            }
        } catch (\Throwable $th) {
            // throw $th;
            $this->dispatch('toast', ['type' => 'error', 'message' => 'terjadi kesalahan']);
        }
    }
}
