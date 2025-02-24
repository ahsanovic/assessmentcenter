<?php

namespace App\Livewire\Admin\ProblemSolving\RefProblemSolving\RefIndikatorProblemSolving;

use App\Http\Requests\RefIndikatorProblemSolvingRequest;
use App\Models\ProblemSolving\RefIndikatorProblemSolving;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Component;

#[Layout('components.layouts.admin.app', ['title' => 'Referensi Indikator Problem Solving'])]
class Edit extends Component
{   
    public $indikator_nama;
    public $indikator_nomor;
    public $kualifikasi_deskripsi = [];

    #[Locked]
    public $id;
    
    public function mount($id)
    {
        try {
            $data = RefIndikatorProblemSolving::findOrFail($id);
            $this->id = $data->id;
            $this->indikator_nama = $data->indikator_nama;
            $this->indikator_nomor = $data->indikator_nomor;
            $this->kualifikasi_deskripsi = $data->kualifikasi_deskripsi;
        } catch (\Throwable $th) {
            //throw $th;
            $this->dispatch('toast', ['type' => 'error', 'message' => 'terjadi kesalahan']);
        }
    }

    public function render()
    {
        return view('livewire.admin.problem-solving.referensi.indikator.edit');
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
            $data = RefIndikatorProblemSolving::find($this->id);
            
            $check_duplicate = RefIndikatorProblemSolving::where('indikator_nomor', '!=', $this->indikator_nomor)->get(['indikator_nomor']);
            foreach ($check_duplicate as $value) {
                if ($value->indikator_nomor == $data->indikator_nomor) {
                    $this->dispatch('toast', ['type' => 'error', 'message' => 'data dengan nomor indikator ' . $this->indikator_nomor . ' sudah ada!']);
                    return;
                }
            }

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

            session()->flash('toast', [
                'type' => 'success',
                'message' => 'berhasil ubah data'
            ]);

            $this->redirect(route('admin.ref-indikator-problem-solving'), true);
        } catch (\Throwable $th) {
            // throw $th;
            $this->dispatch('toast', ['type' => 'error', 'message' => 'terjadi kesalahan']);
        }
    }
}
