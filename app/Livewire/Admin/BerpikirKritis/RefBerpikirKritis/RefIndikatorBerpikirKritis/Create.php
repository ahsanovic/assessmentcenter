<?php

namespace App\Livewire\Admin\BerpikirKritis\RefBerpikirKritis\RefIndikatorBerpikirKritis;

use App\Http\Requests\RefIndikatorBerpikirKritisRequest;
use App\Models\BerpikirKritis\RefIndikatorBerpikirKritis;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.admin.app', ['title' => 'Referensi Indikator Berpikir Kritis dan Strategis'])]
class Create extends Component
{
    public $indikator_nama;
    public $indikator_nomor;
    public $kualifikasi_deskripsi = [];

    public function mount()
    {
        $this->kualifikasi_deskripsi = [
            ['deskripsi' => ''], // Tinggi
            ['deskripsi' => ''], // Sedang
            ['deskripsi' => ''], // Rendah
        ];
    }

    public function render()
    {
        return view('livewire.admin.berpikir-kritis.referensi.indikator.create');
    }

    protected function rules()
    {
        $request = new RefIndikatorBerpikirKritisRequest();
        return $request->rules();
    }

    protected function messages()
    {
        $request = new RefIndikatorBerpikirKritisRequest();
        return $request->messages();
    }

    public function save()
    {
        $this->validate();

        try {
            $check_duplicate = RefIndikatorBerpikirKritis::where('indikator_nomor', $this->indikator_nomor)->exists();
            if ($check_duplicate) {
                $this->dispatch('toast', ['type' => 'error', 'message' => 'data dengan nomor indikator ' . $this->indikator_nomor . ' sudah ada!']);
                return;
            }

            $data = new RefIndikatorBerpikirKritis();
            $data->indikator_nama = $this->indikator_nama;
            $data->indikator_nomor = $this->indikator_nomor;

            $kualifikasiLevels = ['Rendah', 'Sedang', 'Tinggi'];
            $array_kualifikasi = [];
            foreach ($this->kualifikasi_deskripsi as $index => $item) {
                $array_kualifikasi[] = [
                    'kualifikasi' => $kualifikasiLevels[$index] ?? 'Tidak Diketahui',
                    'deskripsi' => $item['deskripsi'] ?? '',
                ];
            }

            $data->kualifikasi_deskripsi = $array_kualifikasi;
            $data->save();

            activity_log($data, 'create', 'ref-indikator-berpikir-kritis');

            session()->flash('toast', [
                'type' => 'success',
                'message' => 'berhasil tambah data'
            ]);

            $this->redirect(route('admin.ref-indikator-berpikir-kritis'), true);
        } catch (\Throwable $th) {
            throw $th;
            $this->dispatch('toast', ['type' => 'error', 'message' => 'terjadi kesalahan']);
        }
    }
}
