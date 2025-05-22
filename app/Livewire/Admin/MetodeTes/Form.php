<?php

namespace App\Livewire\Admin\MetodeTes;

use App\Livewire\Forms\MetodeTesForm;
use App\Models\RefMetodeTes;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Component;

#[Layout('components.layouts.admin.app', ['title' => 'Metode Tes'])]
class Form extends Component
{
    public MetodeTesForm $form;
    public $isUpdate = false;

    #[Locked]
    public $id;

    public function mount($id = null)
    {
        try {
            if ($id) {
                $this->isUpdate = true;

                $data = RefMetodeTes::findOrFail($id);
                $this->id = $data->id;
                $this->form->metode_tes = $data->metode_tes;
            }
        } catch (\Throwable $th) {
            // throw $th;
            $this->dispatch('toast', ['type' => 'error', 'message' => 'terjadi kesalahan']);
        }
    }

    public function render()
    {
        return view('livewire.admin.metode-tes.form');
    }

    public function save()
    {
        $this->validate();
        try {
            if ($this->isUpdate) {
                $data = RefMetodeTes::findOrFail($this->id);
                $old_data = $data->getOriginal();
                $data->metode_tes = $this->form->metode_tes;
                $data->save();

                activity_log($data, 'update', 'metode-tes', $old_data);

                session()->flash('toast', [
                    'type' => 'success',
                    'message' => 'berhasil ubah data'
                ]);

                $this->redirect(route('admin.metode-tes'), true);
            } else {
                $check_duplicate = RefMetodeTes::where('metode_tes', $this->form->metode_tes)->exists();
                if ($check_duplicate) {
                    $this->dispatch('toast', ['type' => 'error', 'message' => 'metode tes ' . $this->form->metode_tes . ' sudah ada!']);
                    return;
                }

                $data = RefMetodeTes::create([
                    'metode_tes' => $this->form->metode_tes,
                ]);

                activity_log($data, 'create', 'metode-tes');

                session()->flash('toast', [
                    'type' => 'success',
                    'message' => 'berhasil tambah data'
                ]);

                $this->redirect(route('admin.metode-tes'), true);
            }
        } catch (\Throwable $th) {
            // throw $th;
            $this->dispatch('toast', ['type' => 'error', 'message' => 'terjadi kesalahan']);
        }
    }
}
