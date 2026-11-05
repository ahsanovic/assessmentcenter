<?php

namespace App\Livewire\Admin\LiterasiDigital\RefPilar;

use App\Models\LiterasiDigital\RefPilarLiterasiDigital;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

#[Layout('components.layouts.admin.app', ['title' => 'Referensi Pilar Literasi Digital'])]
class Index extends Component
{
    public $selected_id;

    public $showModal = false;

    public $editId;

    public $kode;

    public $nama;

    public $urutan;

    public $is_active = 'true';

    public function render()
    {
        $data = RefPilarLiterasiDigital::withCount([
            'soal as soal_aktif_count' => fn ($query) => $query->where('is_active', 'true'),
            'soal as soal_count',
        ])->orderBy('urutan')->get();

        return view('livewire.admin.literasi-digital.ref-pilar.index', compact('data'));
    }

    public function edit($id)
    {
        $data = RefPilarLiterasiDigital::findOrFail($id);
        $this->editId = $data->id;
        $this->kode = $data->kode;
        $this->nama = $data->nama;
        $this->urutan = $data->urutan;
        $this->is_active = $data->is_active;
        $this->showModal = true;
        $this->resetValidation();
        $this->dispatch('modalOpened');
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetValidation();
        $this->reset(['editId', 'kode', 'nama', 'urutan', 'is_active']);
    }

    public function save()
    {
        $this->validate([
            'nama' => 'required|string|max:255',
            'urutan' => 'required|integer|min:1|max:9',
            'is_active' => 'required|in:true,false',
        ], [
            'nama.required' => 'harus diisi',
            'urutan.required' => 'harus diisi',
            'is_active.required' => 'harus dipilih',
        ]);

        $bentrok = RefPilarLiterasiDigital::where('urutan', $this->urutan)
            ->where('id', '!=', $this->editId)
            ->exists();

        if ($bentrok) {
            $this->addError('urutan', 'urutan sudah dipakai pilar lain');

            return;
        }

        try {
            $data = RefPilarLiterasiDigital::findOrFail($this->editId);
            $old = $data->getOriginal();
            $data->nama = $this->nama;
            $data->urutan = $this->urutan;
            $data->is_active = $this->is_active;
            $data->save();

            activity_log($data, 'update', 'ref-pilar-literasi-digital', $old);
            $this->dispatch('toast', ['type' => 'success', 'message' => 'berhasil ubah data']);
            $this->closeModal();
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
            $data = RefPilarLiterasiDigital::withCount('soal')->findOrFail($this->selected_id);

            if ($data->soal_count > 0) {
                $this->dispatch('toast', ['type' => 'error', 'message' => 'pilar masih memiliki soal']);

                return;
            }

            $old = $data->getOriginal();
            activity_log($data, 'delete', 'ref-pilar-literasi-digital', $old);
            $data->delete();
            $this->dispatch('toast', ['type' => 'success', 'message' => 'berhasil menghapus data']);
        } catch (\Throwable $th) {
            $this->dispatch('toast', ['type' => 'error', 'message' => 'gagal menghapus data']);
        }
    }
}
