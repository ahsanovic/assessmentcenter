<?php

namespace App\Livewire\Admin\KecerdasanEmosi\SoalKecerdasanEmosi;

use App\Livewire\Forms\SoalKecerdasanEmosiForm;
use App\Models\KecerdasanEmosi\RefKecerdasanEmosi;
use App\Models\KecerdasanEmosi\SoalKecerdasanEmosi;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.admin.app', ['title' => 'Soal Kecerdasan Emosi'])]
class Create extends Component
{
    public SoalKecerdasanEmosiForm $form;

    public function render()
    {
        $indikator = RefKecerdasanEmosi::pluck('indikator_nama', 'id')->toArray();

        return view('livewire.admin.kecerdasan-emosi.soal.create', compact('indikator'));
    }

    public function save()
    {
        $this->validate();

        try {
            $data = SoalKecerdasanEmosi::create([
                'jenis_indikator_id' => $this->form->jenis_indikator_id,
                'soal' => $this->form->soal,
                'opsi_a' => $this->form->opsi_a,
                'poin_opsi_a' => $this->form->poin_opsi_a,
                'opsi_b' => $this->form->opsi_b,
                'poin_opsi_b' => $this->form->poin_opsi_b,
                'opsi_c' => $this->form->opsi_c,
                'poin_opsi_c' => $this->form->poin_opsi_c,
            ]);

            activity_log($data, 'create', 'soal-kecerdasan-emosi');

            session()->flash('toast', [
                'type' => 'success',
                'message' => 'berhasil tambah data'
            ]);

            $this->redirect(route('admin.soal-kecerdasan-emosi'), true);
        } catch (\Throwable $th) {
            // throw $th;
            $this->dispatch('toast', ['type' => 'error', 'message' => 'terjadi kesalahan']);
        }
    }
}
