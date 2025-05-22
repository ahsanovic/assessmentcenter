<?php

namespace App\Livewire\Admin\Interpersonal\SoalInterpersonal;

use App\Livewire\Forms\SoalInterpersonalForm;
use App\Models\Interpersonal\RefInterpersonal;
use App\Models\Interpersonal\SoalInterpersonal;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.admin.app', ['title' => 'Soal Interpersonal'])]
class Create extends Component
{
    public SoalInterpersonalForm $form;

    public function render()
    {
        $indikator = RefInterpersonal::pluck('indikator_nama', 'id')->toArray();

        return view('livewire.admin.interpersonal.soal.create', compact('indikator'));
    }

    public function save()
    {
        $this->validate();

        try {
            $data = SoalInterpersonal::create([
                'jenis_indikator_id' => $this->form->jenis_indikator_id,
                'soal' => $this->form->soal,
                'opsi_a' => $this->form->opsi_a,
                'poin_opsi_a' => $this->form->poin_opsi_a,
                'opsi_b' => $this->form->opsi_b,
                'poin_opsi_b' => $this->form->poin_opsi_b,
                'opsi_c' => $this->form->opsi_c,
                'poin_opsi_c' => $this->form->poin_opsi_c,
            ]);

            activity_log($data, 'create', 'soal-interpersonal');

            session()->flash('toast', [
                'type' => 'success',
                'message' => 'berhasil tambah data'
            ]);

            $this->redirect(route('admin.soal-interpersonal'), true);
        } catch (\Throwable $th) {
            // throw $th;
            $this->dispatch('toast', ['type' => 'error', 'message' => 'terjadi kesalahan']);
        }
    }
}
