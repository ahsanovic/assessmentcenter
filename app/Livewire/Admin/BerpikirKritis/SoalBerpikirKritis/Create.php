<?php

namespace App\Livewire\Admin\BerpikirKritis\SoalBerpikirKritis;

use App\Livewire\Forms\SoalBerpikirKritisForm;
use App\Models\BerpikirKritis\RefAspekBerpikirKritis;
use App\Models\BerpikirKritis\RefIndikatorBerpikirKritis;
use App\Models\BerpikirKritis\SoalBerpikirKritis;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.admin.app', ['title' => 'Soal Berpikir Kritis dan Strategis'])]
class Create extends Component
{   
    public SoalBerpikirKritisForm $form;
    
    public function render()
    {
        $indikator_option = RefIndikatorBerpikirKritis::pluck('indikator_nama', 'indikator_nomor')->toArray();
        $aspek_option = RefAspekBerpikirKritis::pluck('aspek', 'id')->toArray();
        
        return view('livewire.admin.berpikir-kritis.soal.create', compact('indikator_option', 'aspek_option'));
    }

    public function save()
    {
        $this->validate();

        try {
            SoalBerpikirKritis::create([
                'aspek_id' => $this->form->aspek_id,
                'indikator_nomor' => $this->form->indikator_nomor,
                'soal' => $this->form->soal,
                'opsi_a' => $this->form->opsi_a,
                'poin_opsi_a' => $this->form->poin_opsi_a,
                'opsi_b' => $this->form->opsi_b,
                'poin_opsi_b' => $this->form->poin_opsi_b,
                'opsi_c' => $this->form->opsi_c,
                'poin_opsi_c' => $this->form->poin_opsi_c,
                'opsi_d' => $this->form->opsi_d,
                'poin_opsi_d' => $this->form->poin_opsi_d,
                'opsi_e' => $this->form->opsi_e,
                'poin_opsi_e' => $this->form->poin_opsi_e,
            ]);

            session()->flash('toast', [
                'type' => 'success',
                'message' => 'berhasil tambah data'
            ]);

            $this->redirect(route('admin.soal-berpikir-kritis'), true);
        } catch (\Throwable $th) {
            // throw $th;
            $this->dispatch('toast', ['type' => 'error', 'message' => 'terjadi kesalahan']);
        }
    }
}
