<?php

namespace App\Livewire\Admin\TtdLaporan;

use App\Models\TtdLaporan;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
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
        $ttdRules = [
            $this->isUpdate ? 'nullable' : 'required',
            'image',
            'mimes:jpeg,jpg,png',
            'mimetypes:image/jpeg,image/png',
            'max:200',
            'dimensions:max_width=8192,max_height=8192',
        ];

        $rules = [
            'nama' => 'required|string|max:255',
            'nip' => 'required|numeric|digits:18',
            'ttd' => $ttdRules,
        ];

        if ($this->isUpdate) {
            $rules['modal_is_active'] = ['required', Rule::in(['t', 'f'])];
        }

        return $rules;
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
            'ttd.max' => 'file maksimal 200 KB',
            'ttd.dimensions' => 'dimensi gambar terlalu besar (maks. 8192×8192 px)',
            'ttd.mimetypes' => 'file harus berupa gambar JPEG atau PNG',
            'modal_is_active.required' => 'status harus dipilih',
            'modal_is_active.in' => 'status tidak valid',
        ];
    }

    /**
     * Simpan file gambar dengan nama aman (ekstensi dari MIME, bukan nama asli klien).
     */
    private function storeTtdFile($file): string
    {
        $extension = strtolower((string) $file->guessExtension());
        if (! in_array($extension, ['jpg', 'jpeg', 'png'], true)) {
            $extension = 'jpg';
        }

        return $file->storeAs('tte', uniqid('tte_', true).'.'.$extension, 'public');
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

                    $path = $this->storeTtdFile($this->ttd);
                }

                $data->nama = $this->nama;
                $data->nip = $this->nip;
                $data->is_active = $this->modal_is_active;
                $data->ttd = $this->ttd ? 'tte/' . basename($path) : $data->ttd;
                $data->save();

                activity_log($data, 'update', 'ttd-laporan', $old_data);

                $this->dispatch('toast', ['type' => 'success', 'message' => 'berhasil ubah data']);
            } else {
                $path = $this->storeTtdFile($this->ttd);

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

            if (! $data) {
                $this->dispatch('toast', ['type' => 'error', 'message' => 'data tidak ditemukan']);
                return;
            }

            $old_data = $data->getOriginal();

            if ($data->ttd && Storage::disk('public')->exists($data->ttd)) {
                Storage::disk('public')->delete($data->ttd);
            }

            activity_log($data, 'delete', 'ttd-laporan', $old_data);

            $data->delete();

            $this->dispatch('toast', ['type' => 'success', 'message' => 'berhasil menghapus data']);
        } catch (\Throwable $th) {
            // throw $th;
            $this->dispatch('toast', ['type' => 'error', 'message' => 'gagal menghapus data']);
        }
    }
}
