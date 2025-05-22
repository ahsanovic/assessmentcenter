<?php

namespace App\Livewire\Admin\Interpersonal\RefInterpersonal;

use App\Http\Requests\RefInterpersonalRequest;
use App\Models\Interpersonal\RefInterpersonal;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.admin.app', ['title' => 'Referensi Interpersonal'])]
class Create extends Component
{
    public $indikator_nama;
    public $indikator_nomor;
    public $kualifikasi = [];

    public function mount()
    {
        $this->kualifikasi = [
            ['uraian_potensi' => ''], // Sangat Baik
            ['uraian_potensi' => ''], // Baik
            ['uraian_potensi' => ''], // Cukup
            ['uraian_potensi' => ''], // Kurang/Sangat Kurang
        ];
    }

    public function render()
    {
        return view('livewire.admin.interpersonal.referensi.create');
    }

    protected function rules()
    {
        $request = new RefInterpersonalRequest();
        return $request->rules();
    }

    protected function messages()
    {
        $request = new RefInterpersonalRequest();
        return $request->messages();
    }

    public function save()
    {
        $this->validate();

        try {
            $check_duplicate = RefInterpersonal::where('indikator_nomor', $this->indikator_nomor)->exists();
            if ($check_duplicate) {
                $this->dispatch('toast', ['type' => 'error', 'message' => 'data dengan nomor indikator ' . $this->indikator_nomor . ' sudah ada!']);
                return;
            }

            $data = new RefInterpersonal();
            $data->indikator_nama = $this->indikator_nama;
            $data->indikator_nomor = $this->indikator_nomor;

            $kualifikasiLevels = ['Sangat Baik', 'Baik', 'Cukup', 'Kurang/Sangat Kurang'];
            $array_kualifikasi = [];
            foreach ($this->kualifikasi as $index => $item) {
                $array_kualifikasi[] = [
                    'kualifikasi' => $kualifikasiLevels[$index] ?? 'Tidak Diketahui',
                    'uraian_potensi' => $item['uraian_potensi'] ?? '',
                ];
            }

            $data->kualifikasi = $array_kualifikasi;
            $data->save();

            activity_log($data, 'create', 'ref-interpersonal');

            session()->flash('toast', [
                'type' => 'success',
                'message' => 'berhasil tambah data'
            ]);

            $this->redirect(route('admin.ref-interpersonal'), true);
        } catch (\Throwable $th) {
            // throw $th;
            $this->dispatch('toast', ['type' => 'error', 'message' => 'terjadi kesalahan']);
        }
    }
}
