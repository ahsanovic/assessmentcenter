<?php

namespace App\Livewire\Admin\ProblemSolving\RefProblemSolving\RefIndikatorProblemSolving;

use App\Http\Requests\RefIndikatorProblemSolvingRequest;
use App\Models\ProblemSolving\RefIndikatorProblemSolving;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.admin.app', ['title' => 'Referensi Indikator Problem Solving'])]
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
        return view('livewire.admin.problem-solving.referensi.indikator.create');
    }

    protected function rules()
    {
        $request = new RefIndikatorProblemSolvingRequest();
        return $request->rules();
    }

    protected function messages()
    {
        $request = new RefIndikatorProblemSolvingRequest();
        return $request->messages();
    }

    public function save()
    {
        $this->validate();

        try {
            $check_duplicate = RefIndikatorProblemSolving::where('indikator_nomor', $this->indikator_nomor)->exists();
            if ($check_duplicate) {
                $this->dispatch('toast', ['type' => 'error', 'message' => 'data dengan nomor indikator ' . $this->indikator_nomor . ' sudah ada!']);
                return;
            }

            $data = new RefIndikatorProblemSolving();
            $data->indikator_nama = $this->indikator_nama;
            $data->indikator_nomor = $this->indikator_nomor;

            $kualifikasiLevels = ['Kurang', 'Cukup', 'Baik'];
            $array_kualifikasi = [];
            foreach ($this->kualifikasi_deskripsi as $index => $item) {
                $array_kualifikasi[] = [
                    'kualifikasi' => $kualifikasiLevels[$index] ?? 'Tidak Diketahui',
                    'deskripsi' => $item['deskripsi'] ?? '',
                ];
            }

            $data->kualifikasi_deskripsi = $array_kualifikasi;
            $data->save();

            activity_log($data, 'create', 'ref-indikator-problem-solving');

            session()->flash('toast', [
                'type' => 'success',
                'message' => 'berhasil tambah data'
            ]);

            $this->redirect(route('admin.ref-indikator-problem-solving'), true);
        } catch (\Throwable $th) {
            // throw $th;
            $this->dispatch('toast', ['type' => 'error', 'message' => 'terjadi kesalahan']);
        }
    }
}
