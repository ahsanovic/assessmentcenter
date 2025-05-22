<?php

namespace App\Livewire\Admin\RefPertanyaanPengalaman;

use App\Models\RefPertanyaanPengalaman;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Component;

#[Layout('components.layouts.admin.app', ['title' => 'Referensi Pertanyaan'])]
class Form extends Component
{
    public $previous_url;
    public $isUpdate = false;
    public $pertanyaan;
    public $kode = [];
    public $urutan;

    #[Locked]
    public $id;

    public function mount($id = null)
    {
        try {
            if ($id) {
                $this->isUpdate = true;
                $this->previous_url = url()->previous();

                $data = RefPertanyaanPengalaman::findOrFail($id);
                $this->id = $data->id;
                $this->pertanyaan = $data->pertanyaan;
                $this->kode = collect(json_decode($data->kode, true))->mapWithKeys(fn($item) => [$item => true])->toArray();
                $this->urutan = $data->urutan;
            }
        } catch (\Throwable $th) {
            // throw $th;
            $this->dispatch('toast', ['type' => 'error', 'message' => 'terjadi kesalahan']);
        }
    }

    public function render()
    {
        $option_kode = [
            1 => 'OPH',
            2 => 'MP',
            3 => 'INT',
            4 => 'KS',
            5 => 'PP',
            6 => 'KOM',
            7 => 'PB',
            8 => 'PDOL',
            9 => 'PK',
        ];

        return view('livewire.admin.pertanyaan-pengalaman.form', compact('option_kode'));
    }

    protected function rules()
    {
        return [
            'pertanyaan' => 'required',
            'kode' => 'array',
            'urutan' => 'required|numeric'
        ];
    }

    protected function messages()
    {
        return [
            'pertanyaan.required' => 'harus diisi',
            'kode.array' => 'kode harus berupa array',
            'urutan.required' => 'harus diisi',
            'urutan.numeric' => 'harus angka',
        ];
    }

    public function save()
    {
        $this->validate();
        try {
            if ($this->isUpdate) {
                $cek_urutan = RefPertanyaanPengalaman::where('urutan', $this->urutan)->first(['urutan']);
                if ($cek_urutan) {
                    $this->dispatch('toast', ['type' => 'error', 'message' => 'pertanyaan dengan urutan ke- ' . $cek_urutan->urutan . ' sudah ada']);
                    return;
                }

                $data = RefPertanyaanPengalaman::find($this->id);
                $old_data = $data->getOriginal();
                $data->update([
                    'pertanyaan' => $this->pertanyaan,
                    'kode' => json_encode(array_keys(array_filter($this->kode))),
                    'urutan' => $this->urutan
                ]);

                activity_log($data, 'update', 'pertanyaan', $old_data);

                session()->flash('toast', [
                    'type' => 'success',
                    'message' => 'berhasil ubah data'
                ]);

                $this->redirect($this->previous_url, true);
            } else {
                $cek_urutan = RefPertanyaanPengalaman::where('urutan', $this->urutan)->first(['urutan']);
                if ($cek_urutan) {
                    $this->dispatch('toast', ['type' => 'error', 'message' => 'pertanyaan dengan urutan ke- ' . $cek_urutan->urutan . ' sudah ada']);
                    return;
                }

                $data = RefPertanyaanPengalaman::create([
                    'pertanyaan' => $this->pertanyaan,
                    'kode' => json_encode(array_keys(array_filter($this->kode))),
                    'urutan' => $this->urutan
                ]);

                activity_log($data, 'create', 'pertanyaan');

                session()->flash('toast', [
                    'type' => 'success',
                    'message' => 'berhasil tambah data'
                ]);

                $this->redirect(route('admin.pertanyaan-pengalaman'), true);
            }
        } catch (\Throwable $th) {
            // throw $th;
            $this->dispatch('toast', ['type' => 'error', 'message' => 'terjadi kesalahan']);
        }
    }
}
