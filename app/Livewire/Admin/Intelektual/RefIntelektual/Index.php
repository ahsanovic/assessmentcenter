<?php

namespace App\Livewire\Admin\Intelektual\RefIntelektual;

use App\Http\Requests\RefIntelektualRequest;
use App\Models\Intelektual\RefIntelektual;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.admin.app', ['title' => 'Referensi Intelektual'])]
class Index extends Component
{
    use WithPagination;

    public $selected_id;
    public $showModal = false;
    public $isUpdate = false;
    public $indikator;
    public $sub_tes;
    public $kualifikasi = [];

    #[Locked]
    public $editId;

    public function render()
    {
        $data = RefIntelektual::paginate(10);

        return view('livewire.admin.intelektual.referensi.index', compact('data'));
    }

    public function openModal()
    {
        $this->resetValidation();
        $this->reset(['indikator', 'sub_tes']);
        $this->kualifikasi = [
            ['uraian_potensi' => ''], // Sangat Baik
            ['uraian_potensi' => ''], // Baik
            ['uraian_potensi' => ''], // Cukup
            ['uraian_potensi' => ''], // Kurang
            ['uraian_potensi' => ''], // Sangat Kurang
        ];
        $this->isUpdate = false;
        $this->editId = null;
        $this->showModal = true;
        $this->dispatch('modalOpened');
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetValidation();
        $this->reset(['indikator', 'sub_tes', 'kualifikasi']);
        $this->isUpdate = false;
        $this->editId = null;
    }

    public function edit($id)
    {
        try {
            $data = RefIntelektual::findOrFail($id);
            $this->editId = $data->id;
            $this->indikator = $data->indikator;
            $this->sub_tes = $data->sub_tes;
            $this->kualifikasi = $data->kualifikasi;
            $this->isUpdate = true;
            $this->showModal = true;
            $this->resetValidation();
            $this->dispatch('modalOpened');
        } catch (\Throwable $th) {
            $this->dispatch('toast', ['type' => 'error', 'message' => 'terjadi kesalahan']);
        }
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
                $data = RefIntelektual::findOrFail($this->editId);
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

                $this->dispatch('toast', ['type' => 'success', 'message' => 'berhasil ubah data']);
            } else {
                $check_duplicate = RefIntelektual::where('indikator', $this->indikator)->orWhere('sub_tes', $this->sub_tes)->exists();
                if ($check_duplicate) {
                    $this->dispatch('toast', ['type' => 'error', 'message' => 'data dengan nama indikator ' . $this->indikator . ' atau data dengan sub tes ke-' . $this->sub_tes . ' sudah ada!']);
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

                $this->dispatch('toast', ['type' => 'success', 'message' => 'berhasil tambah data']);
            }

            $this->closeModal();
            $this->resetPage();
        } catch (\Throwable $th) {
            // throw $th;
            $this->dispatch('toast', ['type' => 'error', 'message' => 'terjadi kesalahan']);
        }
    }

    public function deleteConfirmation($id)
    {
        $this->selected_id = $id;
        $this->dispatch('show-delete-confirmation');
    }

    #[On('delete')]
    public function destroy()
    {
        try {
            $data = RefIntelektual::find($this->selected_id);
            $old_data = $data->getOriginal();

            activity_log($data, 'delete', 'ref-intelektual', $old_data);

            $data->delete();

            $this->dispatch('toast', ['type' => 'success', 'message' => 'berhasil menghapus data']);
        } catch (\Throwable $th) {
            // throw $th;
            $this->dispatch('toast', ['type' => 'error', 'message' => 'gagal menghapus data']);
        }
    }
}
