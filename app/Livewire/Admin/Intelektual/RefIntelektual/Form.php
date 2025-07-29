<?php

namespace App\Livewire\Admin\Intelektual\RefIntelektual;

use App\Http\Requests\RefIntelektualRequest;
use App\Models\Intelektual\RefIntelektual;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Component;

#[Layout('components.layouts.admin.app', ['title' => 'Intelektual'])]
class Form extends Component
{
    public $isUpdate = false;
    public $indikator;
    public $sub_tes;
    public $kualifikasi = [];

    #[Locked]
    public $id;

    public function mount($id = null)
    {
        try {
            if ($id) {
                $this->isUpdate = true;

                $data = RefIntelektual::findOrFail($id);
                $this->id = $data->id;
                $this->indikator = $data->indikator;
                $this->sub_tes = $data->sub_tes;
                $this->kualifikasi = $data->kualifikasi;
            } else {
                $this->isUpdate = false;
                $this->kualifikasi = [
                    ['uraian_potensi' => ''], // Sangat Baik
                    ['uraian_potensi' => ''], // Baik
                    ['uraian_potensi' => ''], // Cukup
                    ['uraian_potensi' => ''], // Kurang
                    ['uraian_potensi' => ''], // Sangat Kurang
                ];
            }
        } catch (\Throwable $th) {
            // throw $th;
            $this->dispatch('toast', ['type' => 'error', 'message' => 'terjadi kesalahan']);
        }
    }

    public function render()
    {
        return view('livewire.admin.intelektual.referensi.form');
    }

    protected function rules()
    {
        $request = new RefIntelektualRequest();
        return $request->rules();
    }

    protected function messages()
    {
        $request = new RefIntelektualRequest();
        return $request->messages();
    }

    public function save()
    {
        $this->validate();
        try {
            if ($this->isUpdate) {
                $data = RefIntelektual::findOrFail($this->id);
                $old_data = $data->getOriginal();

                $kualifikasiLevels = ['Sangat Baik', 'Baik', 'Cukup', 'Kurang', 'Sangat Kurang'];
                $array_kualifikasi = [];
                foreach ($this->kualifikasi as $index => $item) {
                    $array_kualifikasi[] = [
                        'kualifikasi' => $kualifikasiLevels[$index] ?? 'Tidak Diketahui',
                        'uraian_potensi' => $item['uraian_potensi'] ?? '',
                    ];
                }

                $data->kualifikasi = $array_kualifikasi;
                $data->indikator = $this->indikator;
                $data->sub_tes = $this->sub_tes;
                $data->save();

                activity_log($data, 'update', 'intelektual', $old_data);

                session()->flash('toast', [
                    'type' => 'success',
                    'message' => 'berhasil ubah data'
                ]);

                $this->redirect(route('admin.ref-intelektual'), true);
            } else {
                $check_duplicate = RefIntelektual::where('indikator', $this->indikator)->orWhere('sub_tes', $this->sub_tes)->exists();
                if ($check_duplicate) {
                    $this->dispatch('toast', [
                        'type' => 'error',
                        'message' => 'data dengan nama indikator ' . $this->indikator . ' atau data dengan sub tes ke-' . $this->sub_tes . ' sudah ada!'
                    ]);
                    return;
                }

                $model = new RefIntelektual();
                $model->indikator = $this->indikator;
                $model->sub_tes = $this->sub_tes;

                $kualifikasiLevels = ['Sangat Baik', 'Baik', 'Cukup', 'Kurang', 'Sangat Kurang'];
                $array_kualifikasi = [];
                foreach ($this->kualifikasi as $index => $item) {
                    $array_kualifikasi[] = [
                        'kualifikasi' => $kualifikasiLevels[$index] ?? 'Tidak Diketahui',
                        'uraian_potensi' => $item['uraian_potensi'] ?? '',
                    ];
                }

                $model->kualifikasi = $array_kualifikasi;
                $model->save();


                activity_log($model, 'create', 'intelektual');

                session()->flash('toast', [
                    'type' => 'success',
                    'message' => 'berhasil tambah data'
                ]);

                $this->redirect(route('admin.ref-intelektual'), true);
            }
        } catch (\Throwable $th) {
            throw $th;
            $this->dispatch('toast', ['type' => 'error', 'message' => 'terjadi kesalahan']);
        }
    }
}
