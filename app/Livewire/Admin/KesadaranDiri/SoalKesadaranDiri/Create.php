<?php

namespace App\Livewire\Admin\KesadaranDiri\SoalKesadaranDiri;

use App\Livewire\Forms\SoalKesadaranDiriForm;
use App\Models\KesadaranDiri\RefKesadaranDiri;
use App\Models\KesadaranDiri\SoalKesadaranDiri;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.admin.app', ['title' => 'Soal Kesadaran Diri'])]
class Create extends Component
{   
    public SoalKesadaranDiriForm $form;
    
    public function render()
    {
        $indikator = RefKesadaranDiri::pluck('indikator_nama', 'id')->toArray();
        
        return view('livewire.admin.kesadaran-diri.soal.create', compact('indikator'));
    }

    public function save()
    {
        $this->validate();

        try {
            SoalKesadaranDiri::create([
                'jenis_indikator_id' => $this->form->jenis_indikator_id,
                'soal' => $this->form->soal,
                'opsi_a' => $this->form->opsi_a,
                'poin_opsi_a' => $this->form->poin_opsi_a,
                'opsi_b' => $this->form->opsi_b,
                'poin_opsi_b' => $this->form->poin_opsi_b,
                'opsi_c' => $this->form->opsi_c,
                'poin_opsi_c' => $this->form->poin_opsi_c,
            ]);

            session()->flash('toast', [
                'type' => 'success',
                'message' => 'berhasil tambah data'
            ]);

            $this->redirect(route('admin.soal-kesadaran-diri'), true);
        } catch (\Throwable $th) {
            // throw $th;
            $this->dispatch('toast', ['type' => 'error', 'message' => 'terjadi kesalahan']);
        }
    }
}
