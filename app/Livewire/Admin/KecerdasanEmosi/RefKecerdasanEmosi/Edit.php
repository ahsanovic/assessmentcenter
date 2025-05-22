<?php

namespace App\Livewire\Admin\KecerdasanEmosi\RefKecerdasanEmosi;

use App\Http\Requests\RefKecerdasanEmosiRequest;
use App\Models\KecerdasanEmosi\RefKecerdasanEmosi;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Component;

#[Layout('components.layouts.admin.app', ['title' => 'Referensi Kecerdasan Emosi'])]
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
            $data = RefKecerdasanEmosi::findOrFail($id);
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
        return view('livewire.admin.kecerdasan-emosi.referensi.edit');
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
            $data = RefKecerdasanEmosi::find($this->id);
            $old_data = $data->getOriginal();

            $check_duplicate = RefKecerdasanEmosi::where('indikator_nomor', '!=', $this->indikator_nomor)->get(['indikator_nomor']);
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

            activity_log($data, 'update', 'ref-kecerdasan-emosi', $old_data);

            session()->flash('toast', [
                'type' => 'success',
                'message' => 'berhasil ubah data'
            ]);

            $this->redirect(route('admin.ref-kecerdasan-emosi'), true);
        } catch (\Throwable $th) {
            // throw $th;
            $this->dispatch('toast', ['type' => 'error', 'message' => 'terjadi kesalahan']);
        }
    }
}
