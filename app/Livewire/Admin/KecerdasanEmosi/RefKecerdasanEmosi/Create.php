<?php

namespace App\Livewire\Admin\KecerdasanEmosi\RefKecerdasanEmosi;

use App\Http\Requests\RefKecerdasanEmosiRequest;
use App\Models\KecerdasanEmosi\RefKecerdasanEmosi;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.admin.app', ['title' => 'Referensi Kecerdasan Emosi'])]
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
        return view('livewire.admin.kecerdasan-emosi.referensi.create');
    }

    protected function rules()
    {
        $request = new RefKecerdasanEmosiRequest();
        return $request->rules();
    }

    protected function messages()
    {
        $request = new RefKecerdasanEmosiRequest();
        return $request->messages();
    }

    public function save()
    {
        $this->validate();

        try {
            $check_duplicate = RefKecerdasanEmosi::where('indikator_nomor', $this->indikator_nomor)->exists();
            if ($check_duplicate) {
                $this->dispatch('toast', ['type' => 'error', 'message' => 'data dengan nomor indikator ' . $this->indikator_nomor . ' sudah ada!']);
                return;
            }

            $data = new RefKecerdasanEmosi();
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

            session()->flash('toast', [
                'type' => 'success',
                'message' => 'berhasil tambah data'
            ]);

            $this->redirect(route('admin.ref-kecerdasan-emosi'), true);
        } catch (\Throwable $th) {
            // throw $th;
            $this->dispatch('toast', ['type' => 'error', 'message' => 'terjadi kesalahan']);
        }
    }
}
