<?php

namespace App\Livewire\Admin\ProblemSolving\SoalProblemSolving;

use App\Livewire\Forms\SoalProblemSolvingForm;
use App\Models\ProblemSolving\RefAspekProblemSolving;
use App\Models\ProblemSolving\RefIndikatorProblemSolving;
use App\Models\ProblemSolving\SoalProblemSolving;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.admin.app', ['title' => 'Soal Problem Solving'])]
class Create extends Component
{   
    public SoalProblemSolvingForm $form;
    
    public function render()
    {
        $indikator_option = RefIndikatorProblemSolving::pluck('indikator_nama', 'indikator_nomor')->toArray();
        $aspek_option = RefAspekProblemSolving::pluck('aspek', 'id')->toArray();
        
        return view('livewire.admin.problem-solving.soal.create', compact('indikator_option', 'aspek_option'));
    }

    public function save()
    {
        $this->validate();

        try {
            SoalProblemSolving::create([
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

            $this->redirect(route('admin.soal-problem-solving'), true);
        } catch (\Throwable $th) {
            // throw $th;
            $this->dispatch('toast', ['type' => 'error', 'message' => 'terjadi kesalahan']);
        }
    }
}
