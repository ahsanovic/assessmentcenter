<?php

namespace App\Livewire\Admin\PengembanganDiri\SoalPengembanganDiri;

use App\Livewire\Forms\SoalPengembanganDiriForm;
use App\Models\PengembanganDiri\RefPengembanganDiri;
use App\Models\PengembanganDiri\SoalPengembanganDiri;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.admin.app', ['title' => 'Soal Pengembangan Diri'])]
class Create extends Component
{
    public SoalPengembanganDiriForm $form;

    public function render()
    {
        $indikator = RefPengembanganDiri::pluck('indikator_nama', 'id')->toArray();

        return view('livewire.admin.pengembangan-diri.soal.create', compact('indikator'));
    }

    public function save()
    {
        $this->validate();

        try {
            $data = SoalPengembanganDiri::create([
                'jenis_indikator_id' => $this->form->jenis_indikator_id,
                'soal' => $this->form->soal,
                'opsi_a' => $this->form->opsi_a,
                'poin_opsi_a' => $this->form->poin_opsi_a,
                'opsi_b' => $this->form->opsi_b,
                'poin_opsi_b' => $this->form->poin_opsi_b,
            ]);

            activity_log($data, 'create', 'soal-pengembangan-diri');

            session()->flash('toast', [
                'type' => 'success',
                'message' => 'berhasil tambah data'
            ]);

            $this->redirect(route('admin.soal-pengembangan-diri'), true);
        } catch (\Throwable $th) {
            // throw $th;
            $this->dispatch('toast', ['type' => 'error', 'message' => 'terjadi kesalahan']);
        }
    }
}
