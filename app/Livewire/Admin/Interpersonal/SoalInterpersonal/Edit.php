<?php

namespace App\Livewire\Admin\Interpersonal\SoalInterpersonal;

use App\Livewire\Forms\SoalInterpersonalForm;
use App\Models\Interpersonal\RefInterpersonal;
use App\Models\Interpersonal\SoalInterpersonal;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Component;

#[Layout('components.layouts.admin.app', ['title' => 'Soal Interpersonal'])]
class Edit extends Component
{
    public SoalInterpersonalForm $form;

    public $previous_url;

    #[Locked]
    public $id;

    public function mount($id)
    {
        try {
            $this->previous_url = url()->previous();

            $data = SoalInterpersonal::findOrFail($id);
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
        $indikator = RefInterpersonal::pluck('indikator_nama', 'id')->toArray();

        return view('livewire.admin.interpersonal.soal.edit', compact('indikator'));
    }

    public function save()
    {
        try {
            $data = SoalInterpersonal::find($this->id);
            $old_data = $data->getOriginal();
            $data->update($this->validate());

            activity_log($data, 'update', 'soal-interpersonal', $old_data);

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
