<?php

namespace App\Livewire\Admin\KesadaranDiri\RefKesadaranDiri;

use App\Http\Requests\RefKesadaranDiriRequest;
use App\Models\KesadaranDiri\RefKesadaranDiri;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.admin.app', ['title' => 'Referensi Kesadaran Diri'])]
class Index extends Component
{
    use WithPagination;

    public $selected_id;
    public $showModal = false;
    public $isUpdate = false;
    public $indikator_nama;
    public $indikator_nomor;
    public $kualifikasi = [];
    public $editId;

    public function render()
    {
        $data = RefKesadaranDiri::paginate(10);

        return view('livewire.admin.kesadaran-diri.referensi.index', compact('data'));
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
            $data = RefKesadaranDiri::find($this->selected_id);
            $old_data = $data->getOriginal();

            activity_log($data, 'delete', 'ref-kesadaran-diri', $old_data);

            $data->delete();

            $this->dispatch('toast', ['type' => 'success', 'message' => 'berhasil menghapus data']);
        } catch (\Throwable $th) {
            // throw $th;
            $this->dispatch('toast', ['type' => 'error', 'message' => 'gagal menghapus data']);
        }
    }

    public function openModal()
    {
        $this->resetValidation();
        $this->reset(['indikator_nama', 'indikator_nomor', 'kualifikasi']);
        $this->kualifikasi = [
            ['uraian_potensi' => ''],
            ['uraian_potensi' => ''],
            ['uraian_potensi' => ''],
            ['uraian_potensi' => ''],
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
        $this->reset(['indikator_nama', 'indikator_nomor', 'kualifikasi']);
        $this->isUpdate = false;
        $this->editId = null;
    }

    public function edit($id)
    {
        try {
            $data = RefKesadaranDiri::findOrFail($id);
            $this->editId = $data->id;
            $this->indikator_nama = $data->indikator_nama;
            $this->indikator_nomor = $data->indikator_nomor;
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
        $request = new RefKesadaranDiriRequest();
        return $request->rules();
    }

    protected function messages()
    {
        $request = new RefKesadaranDiriRequest();
        return $request->messages();
    }

    public function save()
    {
        $this->validate();
        try {
            if ($this->isUpdate) {
                $data = RefKesadaranDiri::findOrFail($this->editId);
                $old_data = $data->getOriginal();

                $kualifikasiLevels = ['Sangat Baik', 'Baik', 'Cukup', 'Kurang/Sangat Kurang'];
                $array_kualifikasi = [];
                foreach ($this->kualifikasi as $index => $item) {
                    $array_kualifikasi[] = [
                        'kualifikasi' => $kualifikasiLevels[$index] ?? 'Tidak Diketahui',
                        'uraian_potensi' => $item['uraian_potensi'] ?? '',
                    ];
                }

                $data->kualifikasi = $array_kualifikasi;
                $data->indikator_nama = $this->indikator_nama;
                $data->indikator_nomor = $this->indikator_nomor;
                $data->save();

                activity_log($data, 'update', 'ref-kesadaran-diri', $old_data);
                $this->dispatch('toast', ['type' => 'success', 'message' => 'berhasil ubah data']);
            } else {
                $check_duplicate = RefKesadaranDiri::where('indikator_nomor', $this->indikator_nomor)->exists();
                if ($check_duplicate) {
                    $this->dispatch('toast', ['type' => 'error', 'message' => 'data dengan nomor indikator ' . $this->indikator_nomor . ' sudah ada!']);
                    return;
                }

                $model = new RefKesadaranDiri();
                $model->indikator_nama = $this->indikator_nama;
                $model->indikator_nomor = $this->indikator_nomor;

                $kualifikasiLevels = ['Sangat Baik', 'Baik', 'Cukup', 'Kurang/Sangat Kurang'];
                $array_kualifikasi = [];
                foreach ($this->kualifikasi as $index => $item) {
                    $array_kualifikasi[] = [
                        'kualifikasi' => $kualifikasiLevels[$index] ?? 'Tidak Diketahui',
                        'uraian_potensi' => $item['uraian_potensi'] ?? '',
                    ];
                }

                $model->kualifikasi = $array_kualifikasi;
                $model->save();

                activity_log($model, 'create', 'ref-kesadaran-diri');
                $this->dispatch('toast', ['type' => 'success', 'message' => 'berhasil tambah data']);
            }
            $this->closeModal();
            $this->resetPage();
        } catch (\Throwable $th) {
            $this->dispatch('toast', ['type' => 'error', 'message' => 'terjadi kesalahan']);
        }
    }
}
