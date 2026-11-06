<?php

namespace App\Livewire\Admin\JawabanPeserta\LiterasiDigital;

use App\Models\Event;
use App\Services\LiterasiDigital\LiterasiDigitalJawabanService;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.admin.app', ['title' => 'Analisis Jawaban — Tes Literasi Digital'])]
class Index extends Component
{
    use WithPagination;

    public ?string $tgl_mulai = null;

    #[Url(as: 'q')]
    public ?string $search = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedTglMulai(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset(['tgl_mulai', 'search']);
        $this->resetPage();
        $this->dispatch('reset-select2');
    }

    public function render()
    {
        $data = Event::query()
            ->with(['metodeTes'])
            ->withCount(['ujianLiterasiDigital as peserta_ujian_count'])
            ->where('metode_tes_id', LiterasiDigitalJawabanService::METODE_TES_ID)
            ->whereHas('ujianLiterasiDigital')
            ->when(filled($this->search), function ($query) {
                $query->where('nama_event', 'like', '%'.trim((string) $this->search).'%');
            })
            ->when(filled($this->tgl_mulai), function ($query) {
                $tanggal = $this->tanggalFilter();
                if ($tanggal) {
                    $query->whereDate('tgl_mulai', $tanggal);
                }
            })
            ->orderByDesc('tgl_mulai')
            ->orderByDesc('id')
            ->paginate(10);

        return view('livewire.admin.jawaban-peserta.literasi-digital.index', compact('data'));
    }

    private function tanggalFilter(): ?string
    {
        $input = trim((string) $this->tgl_mulai);

        try {
            $tanggal = Carbon::createFromFormat('!d-m-Y', $input);
        } catch (\Throwable) {
            return null;
        }

        if (! $tanggal || $tanggal->format('d-m-Y') !== $input) {
            return null;
        }

        return $tanggal->format('Y-m-d');
    }
}
