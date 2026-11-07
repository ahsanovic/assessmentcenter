<?php

namespace App\Livewire\Admin\LiterasiDigital\RefSaranPengembangan;

use App\Models\LiterasiDigital\RefPilarLiterasiDigital;
use App\Models\LiterasiDigital\RefSaranPengembanganLiterasiDigital;
use App\Services\LiterasiDigital\LiterasiDigitalUjianService;
use App\Services\LiterasiDigital\SaranPengembanganLiterasiDigitalService;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.admin.app', ['title' => 'Referensi Saran Pengembangan'])]
class Index extends Component
{
    public bool $showModal = false;

    public ?int $editId = null;

    public string $namaPilar = '';

    public ?int $level = null;

    public string $rentang = '';

    public ?string $saran = null;

    public function render()
    {
        $data = RefPilarLiterasiDigital::query()
            ->with(['saranPengembangan' => fn ($query) => $query->orderBy('level')])
            ->orderBy('urutan')
            ->orderBy('id')
            ->get();

        return view('livewire.admin.literasi-digital.ref-saran-pengembangan.index', [
            'data' => $data,
        ]);
    }

    public function edit($id): void
    {
        $data = RefSaranPengembanganLiterasiDigital::with('pilar')->findOrFail($id);
        $this->editId = $data->id;
        $this->namaPilar = $data->pilar?->nama ?? '-';
        $this->level = (int) $data->level;
        $this->rentang = LiterasiDigitalUjianService::labelRentang($this->level);
        $this->saran = $data->saran;
        $this->showModal = true;
        $this->resetValidation();
        $this->dispatch('modalOpened');
        $this->dispatch('set-markdown', [
            'model' => 'saran',
            'value' => (string) ($this->saran ?? ''),
        ]);
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetValidation();
        $this->reset(['editId', 'namaPilar', 'level', 'rentang', 'saran']);
    }

    public function save(SaranPengembanganLiterasiDigitalService $saran): void
    {
        $this->validate([
            'saran' => 'nullable|string|max:10000',
        ], [
            'saran.max' => 'maksimal 10000 karakter',
        ]);

        try {
            $data = RefSaranPengembanganLiterasiDigital::findOrFail($this->editId);
            $old = $data->getOriginal();
            $berubah = $saran->simpan($data, $this->saran);

            if ($berubah) {
                $data->refresh();
                activity_log($data, 'update', 'ref-saran-literasi-digital', $old);
            }

            $this->dispatch('toast', ['type' => 'success', 'message' => 'berhasil ubah data']);
            $this->closeModal();
        } catch (\Throwable $th) {
            $this->dispatch('toast', ['type' => 'error', 'message' => 'terjadi kesalahan']);
        }
    }
}
