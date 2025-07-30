<?php

namespace App\Livewire\Admin\CakapDigital\SoalCakapDigital;

use App\Livewire\Forms\SoalCakapDigitalForm;
use App\Models\CakapDigital\SoalCakapDigital;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Component;

#[Layout('components.layouts.admin.app', ['title' => 'Cakap Digital'])]
class Form extends Component
{
    public SoalCakapDigitalForm $form;
    public $isUpdate = false;

    #[Locked]
    public $id;

    public function mount($id = null)
    {
        try {
            if ($id) {
                $this->isUpdate = true;

                $data = SoalCakapDigital::findOrFail($id);
                $this->id = $data->id;
                $this->form->jenis_soal = $data->jenis_soal;
                $this->form->soal = $data->soal;
                $this->form->opsi_a = $data->opsi_a;
                $this->form->opsi_b = $data->opsi_b;
                $this->form->opsi_c = $data->opsi_c;
                $this->form->opsi_d = $data->opsi_d;
                $this->form->opsi_e = $data->opsi_e;
                $this->form->kunci_jawaban = $data->kunci_jawaban;
            }
        } catch (\Throwable $th) {
            // throw $th;
            $this->dispatch('toast', ['type' => 'error', 'message' => 'terjadi kesalahan']);
        }
    }

    public function render()
    {
        return view('livewire.admin.cakap-digital.soal.form');
    }

    public function save()
    {
        $this->validate();
        try {
            if ($this->isUpdate) {
                $data = SoalCakapDigital::findOrFail($this->id);
                $old_data = $data->getOriginal();

                $data->jenis_soal = $this->form->jenis_soal;
                $data->soal = $this->form->soal;
                $data->opsi_a = $this->form->opsi_a;
                $data->opsi_b = $this->form->opsi_b;
                $data->opsi_c = $this->form->opsi_c;
                $data->opsi_d = $this->form->opsi_d;
                $data->opsi_e = $this->form->opsi_e;
                $data->kunci_jawaban = $this->form->kunci_jawaban;
                $data->save();

                activity_log($data, 'update', 'soal-cakap-digital', $old_data);

                session()->flash('toast', [
                    'type' => 'success',
                    'message' => 'berhasil ubah data'
                ]);

                $this->redirect(route('admin.soal-cakap-digital'), true);
            } else {
                $model = SoalCakapDigital::create([
                    'jenis_soal' => $this->form->jenis_soal,
                    'soal' => $this->form->soal,
                    'opsi_a' => $this->form->opsi_a,
                    'opsi_b' => $this->form->opsi_b,
                    'opsi_c' => $this->form->opsi_c,
                    'opsi_d' => $this->form->opsi_d,
                    'opsi_e' => $this->form->opsi_e,
                    'kunci_jawaban' => $this->form->kunci_jawaban,
                ]);

                activity_log($model, 'create', 'soal-cakap-digital');

                session()->flash('toast', [
                    'type' => 'success',
                    'message' => 'berhasil tambah data'
                ]);

                $this->redirect(route('admin.soal-cakap-digital'), true);
            }
        } catch (\Throwable $th) {
            // throw $th;
            $this->dispatch('toast', ['type' => 'error', 'message' => 'terjadi kesalahan']);
        }
    }
}
