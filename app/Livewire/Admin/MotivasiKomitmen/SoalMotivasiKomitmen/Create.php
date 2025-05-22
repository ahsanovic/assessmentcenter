<?php

namespace App\Livewire\Admin\MotivasiKomitmen\SoalMotivasiKomitmen;

use App\Livewire\Forms\SoalMotivasiKomitmenForm;
use App\Models\MotivasiKomitmen\RefMotivasiKomitmen;
use App\Models\MotivasiKomitmen\SoalMotivasiKomitmen;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.admin.app', ['title' => 'Soal Motivasi dan Komitmen'])]
class Create extends Component
{
    public SoalMotivasiKomitmenForm $form;

    public function render()
    {
        $indikator = RefMotivasiKomitmen::pluck('indikator_nama', 'id')->toArray();

        return view('livewire.admin.motivasi-komitmen.soal.create', compact('indikator'));
    }

    public function save()
    {
        $this->validate();

        try {
            $data = SoalMotivasiKomitmen::create([
                'jenis_indikator_id' => $this->form->jenis_indikator_id,
                'soal' => $this->form->soal,
                'opsi_a' => $this->form->opsi_a,
                'poin_opsi_a' => $this->form->poin_opsi_a,
                'opsi_b' => $this->form->opsi_b,
                'poin_opsi_b' => $this->form->poin_opsi_b,
            ]);

            activity_log($data, 'create', 'soal-motivasi-komitmen');

            session()->flash('toast', [
                'type' => 'success',
                'message' => 'berhasil tambah data'
            ]);

            $this->redirect(route('admin.soal-motivasi-komitmen'), true);
        } catch (\Throwable $th) {
            // throw $th;
            $this->dispatch('toast', ['type' => 'error', 'message' => 'terjadi kesalahan']);
        }
    }
}
