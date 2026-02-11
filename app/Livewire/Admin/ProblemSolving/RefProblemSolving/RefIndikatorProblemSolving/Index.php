<?php

namespace App\Livewire\Admin\ProblemSolving\RefProblemSolving\RefIndikatorProblemSolving;

use App\Http\Requests\RefIndikatorProblemSolvingRequest;
use App\Models\ProblemSolving\RefIndikatorProblemSolving;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.admin.app', ['title' => 'Referensi Indikator Problem Solving'])]
class Index extends Component
{
    use WithPagination;

    public $selected_id;
    public $showModal = false;
    public $isUpdate = false;
    public $indikator_nama;
    public $indikator_nomor;
    public $kualifikasi_deskripsi = [];
    public $editId;

    public function render()
    {
        $data = RefIndikatorProblemSolving::paginate(10);

        return view('livewire.admin.problem-solving.referensi.indikator.index', compact('data'));
    }

    public function openModal()
    {
        $this->resetValidation();
        $this->reset(['indikator_nama', 'indikator_nomor', 'kualifikasi_deskripsi']);
        $this->kualifikasi_deskripsi = [
            ['deskripsi' => ''],
            ['deskripsi' => ''],
            ['deskripsi' => ''],
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
        $this->reset(['indikator_nama', 'indikator_nomor', 'kualifikasi_deskripsi']);
        $this->isUpdate = false;
        $this->editId = null;
    }

    public function edit($id)
    {
        try {
            $data = RefIndikatorProblemSolving::findOrFail($id);
            $this->editId = $data->id;
            $this->indikator_nama = $data->indikator_nama;
            $this->indikator_nomor = $data->indikator_nomor;
            $this->kualifikasi_deskripsi = $data->kualifikasi_deskripsi;
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
            if ($this->isUpdate) {
                $data = RefIndikatorProblemSolving::findOrFail($this->editId);
                $old_data = $data->getOriginal();

                $kualifikasiLevels = ['Kurang', 'Cukup', 'Baik'];
                $array_kualifikasi = [];
                foreach ($this->kualifikasi_deskripsi as $index => $item) {
                    $array_kualifikasi[] = [
                        'kualifikasi' => $kualifikasiLevels[$index] ?? 'Tidak Diketahui',
                        'deskripsi' => $item['deskripsi'] ?? '',
                    ];
                }

                $data->kualifikasi_deskripsi = $array_kualifikasi;
                $data->indikator_nama = $this->indikator_nama;
                $data->indikator_nomor = $this->indikator_nomor;
                $data->save();

                activity_log($data, 'update', 'ref-indikator-problem-solving', $old_data);
                $this->dispatch('toast', ['type' => 'success', 'message' => 'berhasil ubah data']);
            } else {
                $check_duplicate = RefIndikatorProblemSolving::where('indikator_nomor', $this->indikator_nomor)->exists();
                if ($check_duplicate) {
                    $this->dispatch('toast', ['type' => 'error', 'message' => 'data dengan nomor indikator ' . $this->indikator_nomor . ' sudah ada!']);
                    return;
                }

                $model = new RefIndikatorProblemSolving();
                $model->indikator_nama = $this->indikator_nama;
                $model->indikator_nomor = $this->indikator_nomor;

                $kualifikasiLevels = ['Kurang', 'Cukup', 'Baik'];
                $array_kualifikasi = [];
                foreach ($this->kualifikasi_deskripsi as $index => $item) {
                    $array_kualifikasi[] = [
                        'kualifikasi' => $kualifikasiLevels[$index] ?? 'Tidak Diketahui',
                        'deskripsi' => $item['deskripsi'] ?? '',
                    ];
                }

                $model->kualifikasi_deskripsi = $array_kualifikasi;
                $model->save();

                activity_log($model, 'create', 'ref-indikator-problem-solving');
                $this->dispatch('toast', ['type' => 'success', 'message' => 'berhasil tambah data']);
            }
            $this->closeModal();
            $this->resetPage();
        } catch (\Throwable $th) {
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
            $data = RefIndikatorProblemSolving::find($this->selected_id);
            $old_data = $data->getOriginal();

            activity_log($data, 'delete', 'ref-indikator-problem-solving', $old_data);

            $data->delete();

            $this->dispatch('toast', ['type' => 'success', 'message' => 'berhasil menghapus data']);
        } catch (\Throwable $th) {
            // throw $th;
            $this->dispatch('toast', ['type' => 'error', 'message' => 'gagal menghapus data']);
        }
    }
}
