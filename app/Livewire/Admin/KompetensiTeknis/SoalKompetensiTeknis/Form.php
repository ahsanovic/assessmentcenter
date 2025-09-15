<?php

namespace App\Livewire\Admin\KompetensiTeknis\SoalKompetensiTeknis;

use App\Livewire\Forms\SoalKompetensiTeknisForm;
use App\Models\KompetensiTeknis\SoalKompetensiTeknis;
use App\Models\RefJabatanDiuji;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Component;

#[Layout('components.layouts.admin.app', ['title' => 'Kompetensi Teknis'])]
class Form extends Component
{
    public SoalKompetensiTeknisForm $form;
    public $isUpdate = false;

    #[Locked]
    public $id;

    public function mount($id = null)
    {
        try {
            if ($id) {
                $this->isUpdate = true;

                $data = SoalKompetensiTeknis::findOrFail($id);
                $this->id = $data->id;
                $this->form->jenis_jabatan = $data->jenis_jabatan_id;
                $this->form->soal = $data->soal;
                $this->form->opsi_a = $data->opsi_a;
                $this->form->opsi_b = $data->opsi_b;
                $this->form->opsi_c = $data->opsi_c;
                $this->form->opsi_d = $data->opsi_d;
                $this->form->kunci_jawaban = $data->kunci_jawaban;
            }
        } catch (\Throwable $th) {
            // throw $th;
            $this->dispatch('toast', ['type' => 'error', 'message' => 'terjadi kesalahan']);
        }
    }

    public function render()
    {
        $jenis_jabatan_options = RefJabatanDiuji::pluck('jenis', 'id');

        return view('livewire.admin.kompetensi-teknis.soal.form', compact('jenis_jabatan_options'));
    }

    public function save()
    {
        $this->validate();
        try {
            if ($this->isUpdate) {
                $data = SoalKompetensiTeknis::findOrFail($this->id);
                $old_data = $data->getOriginal();

                $data->jenis_jabatan_id = $this->form->jenis_jabatan;
                $data->soal = $this->form->soal;
                $data->opsi_a = $this->form->opsi_a;
                $data->opsi_b = $this->form->opsi_b;
                $data->opsi_c = $this->form->opsi_c;
                $data->opsi_d = $this->form->opsi_d;
                $data->kunci_jawaban = $this->form->kunci_jawaban;
                $data->save();

                activity_log($data, 'update', 'soal-kompetensi-teknis', $old_data);

                session()->flash('toast', [
                    'type' => 'success',
                    'message' => 'berhasil ubah data'
                ]);

                $this->redirect(route('admin.soal-kompetensi-teknis'), true);
            } else {
                $model = SoalKompetensiTeknis::create([
                    'jenis_jabatan_id' => $this->form->jenis_jabatan,
                    'soal' => $this->form->soal,
                    'opsi_a' => $this->form->opsi_a,
                    'opsi_b' => $this->form->opsi_b,
                    'opsi_c' => $this->form->opsi_c,
                    'opsi_d' => $this->form->opsi_d,
                    'kunci_jawaban' => $this->form->kunci_jawaban,
                ]);

                activity_log($model, 'create', 'soal-kompetensi-teknis');

                session()->flash('toast', [
                    'type' => 'success',
                    'message' => 'berhasil tambah data'
                ]);

                $this->redirect(route('admin.soal-kompetensi-teknis'), true);
            }
        } catch (\Throwable $th) {
            // throw $th;
            $this->dispatch('toast', ['type' => 'error', 'message' => 'terjadi kesalahan']);
        }
    }
}
