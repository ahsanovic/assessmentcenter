<?php

namespace App\Livewire\Admin\TtdLaporan;

use App\Models\Event;
use App\Models\TtdLaporan;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

#[Layout('components.layouts.admin.app', ['title' => 'Ttd Laporan Penilaian'])]
class Index extends Component
{
    use WithPagination, WithFileUploads;

    public $selected_id;
    public $is_active;
    public $showModal = false;
    public $isUpdate = false;
    
    public $ttd_url;
    public $ttd;
    public $nama;
    public $nip;
    public $modal_is_active;

    #[Locked]
    public $editId;

    public function render()
    {
        $data = TtdLaporan::when($this->is_active, function ($query) {
            $query->where('is_active', $this->is_active);
        })
            ->orderByDesc('id')
            ->paginate(10);

        return view('livewire.admin.ttd-laporan.index', compact('data'));
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
        $this->nama = '';
        $this->nip = '';
        $this->ttd = null;
        $this->ttd_url = null;
        $this->modal_is_active = 't';
        $this->isUpdate = false;
        $this->editId = null;
        $this->showModal = true;
        $this->dispatch('modalOpened');
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetValidation();
        $this->nama = '';
        $this->nip = '';
        $this->ttd = null;
        $this->ttd_url = null;
        $this->modal_is_active = 't';
        $this->isUpdate = false;
        $this->editId = null;
    }

    public function edit($id)
    {
        try {
            $data = TtdLaporan::findOrFail($id);
            $this->editId = $data->id;
            $this->nama = $data->nama;
            $this->nip = $data->nip;
            $this->modal_is_active = $data->is_active;
            $this->ttd_url = $data->ttd ? Storage::url($data->ttd) : null;
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
            'nama' => 'required',
            'nip' => 'required|numeric|digits:18',
            'ttd' => $this->isUpdate
                ? 'nullable|image|mimes:jpeg,png,jpg|max:200'
                : 'required|image|mimes:jpeg,png,jpg|max:200',
        ];
    }

    protected function messages()
    {
        return [
            'nama.required' => 'harus diisi',
            'nip.required' => 'harus diisi',
            'nip.numeric' => 'harus berupa angka',
            'nip.digits' => 'panjang karakter harus 18',
            'ttd.required' => 'harus diisi',
            'ttd.image' => 'file harus berupa gambar',
            'ttd.mimes' => 'file harus berupa gambar dengan format jpeg, png, jpg',
            'ttd.max' => 'file maksimal 200 KB'
        ];
    }

    public function updatedTtd()
    {
        if ($this->ttd) {
            $this->ttd_url = $this->ttd->temporaryUrl();
        }
    }

    public function save()
    {
        $this->validate();
        try {
            if ($this->isUpdate) {
                $data = TtdLaporan::findOrFail($this->editId);
                $old_data = $data->getOriginal();

                // Hapus file lama jika ada
                if ($this->ttd) {
                    if ($data->ttd && Storage::disk('public')->exists($data->ttd)) {
                        Storage::disk('public')->delete($data->ttd);
                    }

                    $path = $this->ttd->storeAs('tte', uniqid() . '.' . $this->ttd->extension(), 'public');
                }

                $data->nama = $this->nama;
                $data->nip = $this->nip;
                $data->is_active = $this->modal_is_active;
                $data->ttd = $this->ttd ? 'tte/' . basename($path) : $data->ttd;
                $data->save();

                activity_log($data, 'update', 'ttd-laporan', $old_data);

                $this->dispatch('toast', ['type' => 'success', 'message' => 'berhasil ubah data']);
            } else {
                $path = $this->ttd->storeAs('tte', uniqid() . '.' . $this->ttd->extension(), 'public');

                $data = TtdLaporan::create([
                    'nama' => $this->nama,
                    'nip' => $this->nip,
                    'ttd' => 'tte/' . basename($path),
                ]);

                activity_log($data, 'create', 'ttd-laporan');

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
            $data = TtdLaporan::find($this->selected_id);
            $old_data = $data->getOriginal();

            if ($data) {
                // Hapus file ttd jika ada
                if ($data->ttd && Storage::disk('public')->exists($data->ttd)) {
                    Storage::disk('public')->delete($data->ttd);
                }

                activity_log($data, 'delete', 'ttd-laporan', $old_data);

                $data->delete();
            }

            $this->dispatch('toast', ['type' => 'success', 'message' => 'berhasil menghapus data']);
        } catch (\Throwable $th) {
            // throw $th;
            $this->dispatch('toast', ['type' => 'error', 'message' => 'gagal menghapus data']);
        }
    }
}
