<?php

namespace App\Livewire\Admin\RefPegawai;

use App\Livewire\Forms\PegawaiForm;
use App\Models\RefPegawai;
use App\Services\Pegawai\PegawaiQrCodeService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.admin.app', ['title' => 'Data Pegawai'])]
class Index extends Component
{
    use WithPagination;

    public $selected_id;

    public $showModal = false;

    public $isUpdate = false;

    public PegawaiForm $form;

    #[Locked]
    public $editId;

    #[Url(as: 'q')]
    public ?string $search = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function openModal(): void
    {
        $this->resetValidation();
        $this->form->reset();
        $this->isUpdate = false;
        $this->editId = null;
        $this->showModal = true;
        $this->dispatch('modalOpened');
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetValidation();
        $this->form->reset();
        $this->isUpdate = false;
        $this->editId = null;
    }

    public function edit(int $id): void
    {
        try {
            $data = RefPegawai::findOrFail($id);
            $this->editId = $data->id;
            $this->form->nama = $data->nama;
            $this->form->nip = $data->nip;
            $this->isUpdate = true;
            $this->showModal = true;
            $this->resetValidation();
            $this->dispatch('modalOpened');
        } catch (\Throwable $th) {
            $this->dispatch('toast', ['type' => 'error', 'message' => 'terjadi kesalahan']);
        }
    }

    public function save(): void
    {
        $this->validate();

        try {
            if ($this->isUpdate) {
                $data = RefPegawai::findOrFail($this->editId);
                $old_data = $data->getOriginal();

                $namaChanged = $data->nama !== $this->form->nama;
                $nipChanged = $data->nip !== $this->form->nip;

                $duplicate = RefPegawai::where('nip', $this->form->nip)
                    ->where('id', '!=', $data->id)
                    ->exists();

                if ($duplicate) {
                    $this->dispatch('toast', ['type' => 'error', 'message' => 'NIP sudah digunakan pegawai lain']);

                    return;
                }

                if ($namaChanged || $nipChanged) {
                    PegawaiQrCodeService::deleteIfExists($data->qrcode_path);
                    $data->qrcode_path = null;
                }

                $data->nama = $this->form->nama;
                $data->nip = $this->form->nip;
                $data->save();

                activity_log($data, 'update', 'ref-pegawai', $old_data);

                $this->dispatch('toast', ['type' => 'success', 'message' => 'berhasil ubah data']);
            } else {
                $duplicate = RefPegawai::where('nip', $this->form->nip)->exists();
                if ($duplicate) {
                    $this->dispatch('toast', ['type' => 'error', 'message' => 'NIP sudah terdaftar']);

                    return;
                }

                $model = RefPegawai::create([
                    'nama' => $this->form->nama,
                    'nip' => $this->form->nip,
                ]);

                activity_log($model, 'create', 'ref-pegawai');

                $this->dispatch('toast', ['type' => 'success', 'message' => 'berhasil tambah data']);
            }

            $this->closeModal();
            $this->resetPage();
        } catch (\Throwable $th) {
            $this->dispatch('toast', ['type' => 'error', 'message' => 'terjadi kesalahan']);
        }
    }

    public function generateQrCode(int $id): void
    {
        try {
            $pegawai = RefPegawai::findOrFail($id);
            PegawaiQrCodeService::generate($pegawai);
            activity_log($pegawai, 'update', 'ref-pegawai', ['action' => 'generate-qrcode']);
            $this->dispatch('toast', ['type' => 'success', 'message' => 'QR Code berhasil dibuat']);
        } catch (\Throwable $th) {
            $this->dispatch('toast', ['type' => 'error', 'message' => 'gagal membuat QR Code']);
        }
    }

    public function resetFilters(): void
    {
        $this->reset(['search']);
        $this->resetPage();
    }

    public function deleteConfirmation(int $id): void
    {
        $this->selected_id = $id;
        $this->dispatch('show-delete-confirmation');
    }

    #[On('delete')]
    public function destroy(): void
    {
        try {
            $data = RefPegawai::find($this->selected_id);
            if (! $data) {
                return;
            }

            $old_data = $data->getOriginal();
            PegawaiQrCodeService::deleteIfExists($data->qrcode_path);
            activity_log($data, 'delete', 'ref-pegawai', $old_data);
            $data->delete();

            $this->dispatch('toast', ['type' => 'success', 'message' => 'berhasil menghapus data']);
        } catch (\Throwable $th) {
            $this->dispatch('toast', ['type' => 'error', 'message' => 'gagal menghapus data']);
        }
    }

    public function render()
    {
        $data = RefPegawai::when($this->search, function ($query) {
            $query->where(function ($q) {
                $q->where('nama', 'like', '%'.$this->search.'%')
                    ->orWhere('nip', 'like', '%'.$this->search.'%');
            });
        })
            ->orderByDesc('id')
            ->paginate(10);

        return view('livewire.admin.ref-pegawai.index', compact('data'));
    }
}
