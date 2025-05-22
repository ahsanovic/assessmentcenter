<?php

namespace App\Livewire\Admin\ProblemSolving\SoalProblemSolving;

use App\Livewire\Forms\SoalProblemSolvingForm;
use App\Models\ProblemSolving\RefAspekProblemSolving;
use App\Models\ProblemSolving\RefIndikatorProblemSolving;
use App\Models\ProblemSolving\SoalProblemSolving;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Component;

#[Layout('components.layouts.admin.app', ['title' => 'Soal Problem Solving'])]
class Edit extends Component
{
    public SoalProblemSolvingForm $form;

    public $previous_url;

    #[Locked]
    public $id;

    public function mount($id)
    {
        try {
            $data = SoalProblemSolving::findOrFail($id);
            $this->id = $data->id;
            $this->form->aspek_id = $data->aspek_id;
            $this->form->indikator_nomor = $data->indikator_nomor;
            $this->form->soal = $data->soal;
            $this->form->opsi_a = $data->opsi_a;
            $this->form->opsi_b = $data->opsi_b;
            $this->form->opsi_c = $data->opsi_c;
            $this->form->opsi_d = $data->opsi_d;
            $this->form->opsi_e = $data->opsi_e;
            $this->form->poin_opsi_a = $data->poin_opsi_a;
            $this->form->poin_opsi_b = $data->poin_opsi_b;
            $this->form->poin_opsi_c = $data->poin_opsi_c;
            $this->form->poin_opsi_d = $data->poin_opsi_d;
            $this->form->poin_opsi_e = $data->poin_opsi_e;
        } catch (\Throwable $th) {
            //throw $th;
            $this->dispatch('toast', ['type' => 'error', 'message' => 'terjadi kesalahan']);
        }
    }

    public function render()
    {
        $indikator_option = RefIndikatorProblemSolving::pluck('indikator_nama', 'indikator_nomor')->toArray();
        $aspek_option = RefAspekProblemSolving::pluck('aspek', 'id')->toArray();

        return view('livewire.admin.problem-solving.soal.edit', compact('indikator_option', 'aspek_option'));
    }

    public function save()
    {
        try {
            $data = SoalProblemSolving::find($this->id);
            $old_data = $data->getOriginal();
            $data->update($this->validate());

            activity_log($data, 'update', 'soal-problem-solving', $old_data);

            session()->flash('toast', [
                'type' => 'success',
                'message' => 'berhasil ubah data'
            ]);

            $this->redirect(route('admin.soal-problem-solving'), navigate: true);
        } catch (\Throwable $th) {
            // throw $th;
            $this->dispatch('toast', ['type' => 'error', 'message' => 'terjadi kesalahan']);
        }
    }
}
