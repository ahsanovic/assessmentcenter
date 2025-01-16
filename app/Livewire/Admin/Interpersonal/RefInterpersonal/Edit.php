<?php

namespace App\Livewire\Admin\Interpersonal\RefInterpersonal;

use App\Http\Requests\RefInterpersonalRequest;
use App\Models\Interpersonal\RefInterpersonal;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Component;

#[Layout('components.layouts.admin.app', ['title' => 'Referensi Interpersonal'])]
class Edit extends Component
{   
    public $indikator_nama;
    public $indikator_nomor;
    public $kualifikasi = [];

    #[Locked]
    public $id;
    
    public function mount($id)
    {
        try {
            $data = RefInterpersonal::findOrFail($id);
            $this->id = $data->id;
            $this->indikator_nama = $data->indikator_nama;
            $this->indikator_nomor = $data->indikator_nomor;
            $this->kualifikasi = $data->kualifikasi;
        } catch (\Throwable $th) {
            //throw $th;
            $this->dispatch('toast', ['type' => 'error', 'message' => 'terjadi kesalahan']);
        }
    }

    public function render()
    {
        return view('livewire.admin.interpersonal.referensi.edit');
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
            $data = RefInterpersonal::find($this->id);
            
            $check_duplicate = RefInterpersonal::where('indikator_nomor', '!=', $this->indikator_nomor)->get(['indikator_nomor']);
            foreach ($check_duplicate as $value) {
                if ($value->indikator_nomor == $data->indikator_nomor) {
                    $this->dispatch('toast', ['type' => 'error', 'message' => 'data dengan nomor indikator ' . $this->indikator_nomor . ' sudah ada!']);
                    return;
                }
            }

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
                'message' => 'berhasil ubah data'
            ]);

            $this->redirect(route('admin.ref-interpersonal'), true);
        } catch (\Throwable $th) {
            // throw $th;
            $this->dispatch('toast', ['type' => 'error', 'message' => 'terjadi kesalahan']);
        }
    }
}
