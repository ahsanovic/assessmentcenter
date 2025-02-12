<?php

namespace App\Livewire\Admin\KesadaranDiri\SoalKesadaranDiri;

use App\Livewire\Forms\SoalKesadaranDiriForm;
use App\Models\KesadaranDiri\RefKesadaranDiri;
use App\Models\KesadaranDiri\SoalKesadaranDiri;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Component;

#[Layout('components.layouts.admin.app', ['title' => 'Soal Kesadaran Diri'])]
class Edit extends Component
{   
    public SoalKesadaranDiriForm $form;

    public $previous_url;

    #[Locked]
    public $id;
    
    public function mount($id)
    {
        try {
            $this->previous_url = url()->previous();
    
            $data = SoalKesadaranDiri::findOrFail($id);
            $this->id = $data->id;
            $this->form->jenis_indikator_id = $data->jenis_indikator_id;
            $this->form->soal = $data->soal;
            $this->form->opsi_a = $data->opsi_a;
            $this->form->opsi_b = $data->opsi_b;
            $this->form->opsi_c = $data->opsi_c;
            $this->form->poin_opsi_a = $data->poin_opsi_a;
            $this->form->poin_opsi_b = $data->poin_opsi_b;
            $this->form->poin_opsi_c = $data->poin_opsi_c;
        } catch (\Throwable $th) {
            //throw $th;
            $this->dispatch('toast', ['type' => 'error', 'message' => 'terjadi kesalahan']);
        }
    }

    public function render()
    {
        $indikator = RefKesadaranDiri::pluck('indikator_nama', 'id')->toArray();
        
        return view('livewire.admin.kesadaran-diri.soal.edit', compact('indikator'));
    }

    public function save()
    {
        try {
            SoalKesadaranDiri::whereId($this->id)->update($this->validate());

            session()->flash('toast', [
                'type' => 'success',
                'message' => 'berhasil ubah data'
            ]);

            $this->redirect($this->previous_url, true);
        } catch (\Throwable $th) {
            // throw $th;
            $this->dispatch('toast', ['type' => 'error', 'message' => 'terjadi kesalahan']);
        }
    }
}
