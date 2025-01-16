<?php

namespace App\Livewire\Admin\PengembanganDiri\SoalPengembanganDiri;

use App\Livewire\Forms\SoalPengembanganDiriForm;
use App\Models\PengembanganDiri\RefPengembanganDiri;
use App\Models\PengembanganDiri\SoalPengembanganDiri;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Component;

#[Layout('components.layouts.admin.app', ['title' => 'Soal Pengembangan Diri'])]
class Edit extends Component
{   
    public SoalPengembanganDiriForm $form;

    public $previous_url;

    #[Locked]
    public $id;
    
    public function mount($id)
    {
        try {
            $this->previous_url = url()->previous();
    
            $data = SoalPengembanganDiri::findOrFail($id);
            $this->id = $data->id;
            $this->form->jenis_indikator_id = $data->jenis_indikator_id;
            $this->form->soal = $data->soal;
            $this->form->opsi_a = $data->opsi_a;
            $this->form->opsi_b = $data->opsi_b;
            $this->form->poin_opsi_a = $data->poin_opsi_a;
            $this->form->poin_opsi_b = $data->poin_opsi_b;
        } catch (\Throwable $th) {
            //throw $th;
            $this->dispatch('toast', ['type' => 'error', 'message' => 'terjadi kesalahan']);
        }
    }

    public function render()
    {
        $indikator = RefPengembanganDiri::pluck('indikator_nama', 'id')->toArray();
        
        return view('livewire.admin.pengembangan-diri.soal.edit', compact('indikator'));
    }

    public function save()
    {
        try {
            SoalPengembanganDiri::whereId($this->id)->update($this->validate());

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
