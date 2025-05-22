<?php

namespace App\Livewire\Admin\BerpikirKritis\RefBerpikirKritis\RefIndikatorBerpikirKritis;

use App\Http\Requests\RefIndikatorBerpikirKritisRequest;
use App\Models\BerpikirKritis\RefIndikatorBerpikirKritis;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Component;

#[Layout('components.layouts.admin.app', ['title' => 'Referensi Indikator Berpikir Kritis dan Strategis'])]
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
            $data = RefIndikatorBerpikirKritis::findOrFail($id);
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
        return view('livewire.admin.berpikir-kritis.referensi.indikator.edit');
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
            $data = RefIndikatorBerpikirKritis::find($this->id);
            $old_data = $data->getOriginal();

            $check_duplicate = RefIndikatorBerpikirKritis::where('indikator_nomor', '!=', $this->indikator_nomor)->get(['indikator_nomor']);
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

            activity_log($data, 'update', 'ref-indikator-berpikir-kritis', $old_data);

            session()->flash('toast', [
                'type' => 'success',
                'message' => 'berhasil ubah data'
            ]);

            $this->redirect(route('admin.ref-indikator-berpikir-kritis'), true);
        } catch (\Throwable $th) {
            throw $th;
            $this->dispatch('toast', ['type' => 'error', 'message' => 'terjadi kesalahan']);
        }
    }
}
