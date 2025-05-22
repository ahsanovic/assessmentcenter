<?php

namespace App\Livewire\Admin\RefPertanyaanPenilaian;

use App\Models\RefPertanyaanPenilaian;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Component;

#[Layout('components.layouts.admin.app', ['title' => 'Referensi Pertanyaan Penilaian Pribadi'])]
class Form extends Component
{
    public $previous_url;
    public $isUpdate = false;
    public $pertanyaan;
    public $urutan;

    #[Locked]
    public $id;

    public function mount($id = null)
    {
        try {
            if ($id) {
                $this->isUpdate = true;
                $this->previous_url = url()->previous();

                $data = RefPertanyaanPenilaian::findOrFail($id);
                $this->id = $data->id;
                $this->pertanyaan = $data->pertanyaan;
                $this->urutan = $data->urutan;
            }
        } catch (\Throwable $th) {
            // throw $th;
            $this->dispatch('toast', ['type' => 'error', 'message' => 'terjadi kesalahan']);
        }
    }

    public function render()
    {
        return view('livewire.admin.pertanyaan-penilaian.form');
    }

    protected function rules()
    {
        return [
            'pertanyaan' => 'required',
            'urutan' => 'required|numeric'
        ];
    }

    protected function messages()
    {
        return [
            'pertanyaan.required' => 'harus diisi',
            'urutan.required' => 'harus diisi',
            'urutan.numeric' => 'harus angka',
        ];
    }

    public function save()
    {
        $this->validate();
        try {
            if ($this->isUpdate) {
                $cek_urutan = RefPertanyaanPenilaian::where('urutan', $this->urutan)->first(['urutan']);
                if ($cek_urutan) {
                    $this->dispatch('toast', ['type' => 'error', 'message' => 'pertanyaan dengan urutan ke- ' . $cek_urutan->urutan . ' sudah ada']);
                    return;
                }

                $data = RefPertanyaanPenilaian::find($this->id);
                $old_data = $data->getOriginal();
                $data->update([
                    'pertanyaan' => $this->pertanyaan,
                    'urutan' => $this->urutan
                ]);

                activity_log($data, 'update', 'penilaian', $old_data);

                session()->flash('toast', [
                    'type' => 'success',
                    'message' => 'berhasil ubah data'
                ]);

                $this->redirect($this->previous_url, true);
            } else {
                $cek_urutan = RefPertanyaanPenilaian::where('urutan', $this->urutan)->first(['urutan']);
                if ($cek_urutan) {
                    $this->dispatch('toast', ['type' => 'error', 'message' => 'pertanyaan dengan urutan ke- ' . $cek_urutan->urutan . ' sudah ada']);
                    return;
                }

                $data = RefPertanyaanPenilaian::create([
                    'pertanyaan' => $this->pertanyaan,
                    'urutan' => $this->urutan
                ]);

                activity_log($data, 'create', 'penilaian');

                session()->flash('toast', [
                    'type' => 'success',
                    'message' => 'berhasil tambah data'
                ]);

                $this->redirect(route('admin.pertanyaan-penilaian'), true);
            }
        } catch (\Throwable $th) {
            // throw $th;
            $this->dispatch('toast', ['type' => 'error', 'message' => 'terjadi kesalahan']);
        }
    }
}
