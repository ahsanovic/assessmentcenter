<?php

namespace App\Livewire\Admin\LiterasiDigital\RefDeskripsiLevel;

use App\Models\LiterasiDigital\RefDeskripsiLevelLiterasiDigital;
use App\Models\LiterasiDigital\RefPilarLiterasiDigital;
use App\Services\LiterasiDigital\DeskripsiLevelLiterasiDigitalService;
use App\Services\LiterasiDigital\LiterasiDigitalUjianService;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.admin.app', ['title' => 'Referensi Deskripsi Level'])]
class Index extends Component
{
    public bool $showModal = false;

    public ?int $editId = null;

    public string $namaPilar = '';

    public ?int $level = null;

    public string $rentang = '';

    public ?string $deskripsi = null;

    public function render()
    {
        $data = RefPilarLiterasiDigital::query()
            ->with(['deskripsiLevel' => fn ($query) => $query->orderBy('level')])
            ->orderBy('urutan')
            ->orderBy('id')
            ->get();

        return view('livewire.admin.literasi-digital.ref-deskripsi-level.index', [
            'data' => $data,
        ]);
    }

    public function edit($id): void
    {
        $data = RefDeskripsiLevelLiterasiDigital::with('pilar')->findOrFail($id);
        $this->editId = $data->id;
        $this->namaPilar = $data->pilar?->nama ?? '-';
        $this->level = (int) $data->level;
        $this->rentang = LiterasiDigitalUjianService::labelRentang($this->level);
        $this->deskripsi = $data->deskripsi;
        $this->showModal = true;
        $this->resetValidation();
        $this->dispatch('modalOpened');
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetValidation();
        $this->reset(['editId', 'namaPilar', 'level', 'rentang', 'deskripsi']);
    }

    public function save(DeskripsiLevelLiterasiDigitalService $deskripsi): void
    {
        $this->validate([
            'deskripsi' => 'nullable|string|max:5000',
        ], [
            'deskripsi.max' => 'maksimal 5000 karakter',
        ]);

        try {
            $data = RefDeskripsiLevelLiterasiDigital::findOrFail($this->editId);
            $old = $data->getOriginal();
            $berubah = $deskripsi->simpan($data, $this->deskripsi);

            if ($berubah) {
                $data->refresh();
                activity_log($data, 'update', 'ref-deskripsi-literasi-digital', $old);
            }

            $this->dispatch('toast', ['type' => 'success', 'message' => 'berhasil ubah data']);
            $this->closeModal();
        } catch (\Throwable $th) {
            $this->dispatch('toast', ['type' => 'error', 'message' => 'terjadi kesalahan']);
        }
    }
}
