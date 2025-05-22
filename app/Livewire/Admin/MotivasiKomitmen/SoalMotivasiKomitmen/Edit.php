<?php

namespace App\Livewire\Admin\MotivasiKomitmen\SoalMotivasiKomitmen;

use App\Livewire\Forms\SoalMotivasiKomitmenForm;
use App\Models\MotivasiKomitmen\RefMotivasiKomitmen;
use App\Models\MotivasiKomitmen\SoalMotivasiKomitmen;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Component;

#[Layout('components.layouts.admin.app', ['title' => 'Soal Motivasi dan Komitmen'])]
class Edit extends Component
{
    public SoalMotivasiKomitmenForm $form;

    public $previous_url;

    #[Locked]
    public $id;

    public function mount($id)
    {
        try {
            $this->previous_url = url()->previous();

            $data = SoalMotivasiKomitmen::findOrFail($id);
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
        $indikator = RefMotivasiKomitmen::pluck('indikator_nama', 'id')->toArray();

        return view('livewire.admin.motivasi-komitmen.soal.edit', compact('indikator'));
    }

    public function save()
    {
        try {
            $data = SoalMotivasiKomitmen::find($this->id);
            $old_data = $data->getOriginal();
            $data->update($this->validate());

            activity_log($data, 'update', 'soal-motivasi-komitmen', $old_data);

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
