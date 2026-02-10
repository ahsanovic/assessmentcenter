<?php

namespace App\Livewire\Admin\RefPertanyaanPenilaian;

use App\Models\RefPertanyaanPenilaian;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.admin.app', ['title' => 'Referensi Pertanyaan Penilaian Pribadi'])]
class Index extends Component
{
    use WithPagination;

    public $selected_id;
    public $showModal = false;
    public $isUpdate = false;
    
    public $pertanyaan;
    public $urutan;

    #[Locked]
    public $editId;

    #[Url(as: 'q')]
    public ?string $search = '';

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $data = RefPertanyaanPenilaian::when($this->search, function ($query) {
            $query->where('pertanyaan', 'like', '%' . $this->search . '%');
        })
            ->orderByDesc('id')
            ->paginate(10);

        return view('livewire.admin.pertanyaan-penilaian.index', compact('data'));
    }

    public function resetFilters()
    {
        $this->reset();
        $this->resetPage();
        $this->render();
    }

    public function openModal()
    {
        $this->resetValidation();
        $this->pertanyaan = '';
        $this->urutan = '';
        $this->isUpdate = false;
        $this->editId = null;
        $this->showModal = true;
        $this->dispatch('modalOpened');
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetValidation();
        $this->pertanyaan = '';
        $this->urutan = '';
        $this->isUpdate = false;
        $this->editId = null;
    }

    public function edit($id)
    {
        try {
            $data = RefPertanyaanPenilaian::findOrFail($id);
            $this->editId = $data->id;
            $this->pertanyaan = $data->pertanyaan;
            $this->urutan = $data->urutan;
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
        return [
            'pertanyaan' => 'required',
            'urutan' => 'required|numeric'
        ];
    }

    protected function messages()
    {
        return [
            'pertanyaan.required' => 'harus diisi',
            'urutan.required' => 'harus diisi',
            'urutan.numeric' => 'harus angka',
        ];
    }

    public function save()
    {
        $this->validate();
        try {
            if ($this->isUpdate) {
                $cek_urutan = RefPertanyaanPenilaian::where('urutan', $this->urutan)
                    ->where('id', '!=', $this->editId)
                    ->first(['urutan']);
                if ($cek_urutan) {
                    $this->dispatch('toast', ['type' => 'error', 'message' => 'pertanyaan dengan urutan ke- ' . $cek_urutan->urutan . ' sudah ada']);
                    return;
                }

                $data = RefPertanyaanPenilaian::find($this->editId);
                $old_data = $data->getOriginal();
                $data->update([
                    'pertanyaan' => $this->pertanyaan,
                    'urutan' => $this->urutan
                ]);

                activity_log($data, 'update', 'penilaian', $old_data);

                $this->dispatch('toast', ['type' => 'success', 'message' => 'berhasil ubah data']);
            } else {
                $cek_urutan = RefPertanyaanPenilaian::where('urutan', $this->urutan)->first(['urutan']);
                if ($cek_urutan) {
                    $this->dispatch('toast', ['type' => 'error', 'message' => 'pertanyaan dengan urutan ke- ' . $cek_urutan->urutan . ' sudah ada']);
                    return;
                }

                $data = RefPertanyaanPenilaian::create([
                    'pertanyaan' => $this->pertanyaan,
                    'urutan' => $this->urutan
                ]);

                activity_log($data, 'create', 'penilaian');

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
            $data = RefPertanyaanPenilaian::find($this->selected_id);
            $old_data = $data->getOriginal();

            activity_log($data, 'delete', 'penilaian', $old_data);

            $data->delete();

            $this->dispatch('toast', ['type' => 'success', 'message' => 'berhasil menghapus data']);
        } catch (\Throwable $th) {
            // throw $th;
            $this->dispatch('toast', ['type' => 'error', 'message' => 'gagal menghapus data']);
        }
    }
}
