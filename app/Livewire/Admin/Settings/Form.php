<?php

namespace App\Livewire\Admin\Settings;

use App\Livewire\Forms\SettingsForm;
use App\Models\RefAlatTes;
use App\Models\Settings;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Component;

#[Layout('components.layouts.admin.app', ['title' => 'Alat Tes'])]
class Form extends Component
{   
    public SettingsForm $form;
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
    
                $data = Settings::findOrFail($id);
                $this->id = $data->id;
                $this->form->alat_tes_id = $data->alat_tes_id;
                $this->form->waktu = $data->waktu;
            }
        } catch (\Throwable $th) {
            // throw $th;
            $this->dispatch('toast', ['type' => 'error', 'message' => 'terjadi kesalahan']);
        }
    }
    
    public function render()
    {
        $option_alat_tes = RefAlatTes::pluck('alat_tes', 'id');
        return view('livewire.admin.settings.form', compact('option_alat_tes'));
    }

    public function save()
    {
        $this->validate();
        try {
            if ($this->isUpdate) {
                Settings::whereId($this->id)->update($this->validate());
                
                session()->flash('toast', [
                    'type' => 'success',
                    'message' => 'berhasil ubah data'
                ]);
                
                $this->redirect($this->previous_url, true);
            } else {
                Settings::create([
                    'alat_tes_id' => $this->form->alat_tes_id,
                    'waktu' => $this->form->waktu,
                ]);
    
                session()->flash('toast', [
                    'type' => 'success',
                    'message' => 'berhasil tambah data'
                ]);
    
                $this->redirect(route('admin.settings'), true);
            }
        } catch (\Throwable $th) {
            // throw $th;
            $this->dispatch('toast', ['type' => 'error', 'message' => 'terjadi kesalahan']);
        }
    }
}
