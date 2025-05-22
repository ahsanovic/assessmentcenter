<?php

namespace App\Livewire\Admin\AlatTes;

use App\Livewire\Forms\AlatTesForm;
use App\Models\RefAlatTes;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Component;

#[Layout('components.layouts.admin.app', ['title' => 'Alat Tes'])]
class Form extends Component
{
    public AlatTesForm $form;
    public $previous_url;
    public $isUpdate = false;

    #[Locked]
    public $id;

    public function mount($id = null)
    {
        try {
            if ($id) {
                $this->isUpdate = true;
                $this->previous_url = url()->previous();

                $data = RefAlatTes::findOrFail($id);
                $this->id = $data->id;
                $this->form->alat_tes = $data->alat_tes;
                $this->form->definisi_aspek_potensi = $data->definisi_aspek_potensi;
            }
        } catch (\Throwable $th) {
            // throw $th;
            $this->dispatch('toast', ['type' => 'error', 'message' => 'terjadi kesalahan']);
        }
    }

    public function render()
    {
        return view('livewire.admin.alat-tes.form');
    }

    public function save()
    {
        $this->validate();
        try {
            if ($this->isUpdate) {
                $data = RefAlatTes::findOrFail($this->id);
                $old_data = $data->getOriginal();

                $data->alat_tes = $this->form->alat_tes;
                $data->definisi_aspek_potensi = $this->form->definisi_aspek_potensi;
                $data->save();

                activity_log($data, 'update', 'alat-tes', $old_data);

                session()->flash('toast', [
                    'type' => 'success',
                    'message' => 'berhasil ubah data'
                ]);

                $this->redirect($this->previous_url, true);
            } else {
                $check_duplicate = RefAlatTes::where('alat_tes', $this->form->alat_tes)->exists();
                if ($check_duplicate) {
                    $this->dispatch('toast', ['type' => 'error', 'message' => 'data dengan nama alat tes ' . $this->form->alat_tes . ' sudah ada!']);
                    return;
                }

                $model = RefAlatTes::create([
                    'alat_tes' => $this->form->alat_tes,
                    'definisi_aspek_potensi' => $this->form->definisi_aspek_potensi,
                ]);

                activity_log($model, 'create', 'alat-tes');

                session()->flash('toast', [
                    'type' => 'success',
                    'message' => 'berhasil tambah data'
                ]);

                $this->redirect(route('admin.alat-tes'), true);
            }
        } catch (\Throwable $th) {
            // throw $th;
            $this->dispatch('toast', ['type' => 'error', 'message' => 'terjadi kesalahan']);
        }
    }
}
