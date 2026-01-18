<?php

namespace App\Livewire\Admin\Settings\Urutan;

use App\Livewire\Forms\SettingUrutanForm;
use App\Models\RefAlatTes;
use App\Models\Settings;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Component;

#[Layout('components.layouts.admin.app', ['title' => 'Urutan Tes'])]
class Form extends Component
{
    public SettingUrutanForm $form;
    public $isUpdate = false;

    #[Locked]
    public $id;

    public function mount($id = null)
    {
        try {
            if ($id) {
                $this->isUpdate = true;

                $data = Settings::findOrFail($id);
                $this->id = $data->id;
                $this->form->alat_tes_id = $data->alat_tes_id;
                $this->form->urutan = $data->urutan;
            }
        } catch (\Throwable $th) {
            // throw $th;
            $this->dispatch('toast', ['type' => 'error', 'message' => 'terjadi kesalahan']);
        }
    }

    public function render()
    {
        $option_alat_tes = RefAlatTes::pluck('alat_tes', 'id');
        return view('livewire.admin.settings.urutan.form', compact('option_alat_tes'));
    }

    public function save()
    {
        $this->validate();
        try {
            if ($this->isUpdate) {
                $check_duplicate = Settings::where('urutan', $this->form->urutan)->where('alat_tes_id', '=', $this->form->alat_tes_id)->exists();
                if ($check_duplicate) {
                    $this->dispatch('toast', ['type' => 'error', 'message' => 'data dengan urutan tes ' . $this->form->urutan . ' dan alat tes ' . RefAlatTes::find($this->form->alat_tes_id)->alat_tes . ' sudah ada!']);
                    return;
                }

                $data = Settings::find($this->id);
                $old_data = $data->getOriginal();

                $data->update($this->validate());

                activity_log($data, 'update', 'urutan-tes', $old_data);

                session()->flash('toast', [
                    'type' => 'success',
                    'message' => 'berhasil ubah data'
                ]);

                $this->redirect(route('admin.settings.urutan'), true);
            } else {
                $data = Settings::create([
                    'alat_tes_id' => $this->form->alat_tes_id,
                    'urutan' => $this->form->urutan,
                ]);

                activity_log($data, 'create', 'urutan-tes');

                session()->flash('toast', [
                    'type' => 'success',
                    'message' => 'berhasil tambah data'
                ]);

                $this->redirect(route('admin.settings.urutan'), true);
            }
        } catch (\Throwable $th) {
            throw $th;
            $this->dispatch('toast', ['type' => 'error', 'message' => 'terjadi kesalahan']);
        }
    }
}
