<?php

namespace App\Livewire\Admin\JawabanPeserta\Pspk;

use App\Models\Event;
use App\Models\Pspk\RefLevelPspk;
use App\Services\PspkJawabanService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.admin.app', ['title' => 'Jawaban Peserta — Tes PSPK'])]
class Index extends Component
{
    use WithPagination;

    public ?int $level_pspk = null;

    public ?string $tgl_mulai = null;

    public ?string $event = null;

    #[Url(as: 'q')]
    public ?string $search = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedLevelPspk(): void
    {
        $this->resetPage();
        $this->event = null;
        $this->dispatch('reset-select2');
    }

    public function updatedTglMulai(): void
    {
        $this->resetPage();
    }

    public function updatedEvent(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset(['level_pspk', 'tgl_mulai', 'event', 'search']);
        $this->resetPage();
        $this->dispatch('reset-select2');
    }

    public function render()
    {
        $service = app(PspkJawabanService::class);

        $data = Event::query()
            ->with(['metodeTes'])
            ->withCount(['ujianPspk as peserta_ujian_count'])
            ->whereIn('metode_tes_id', PspkJawabanService::PSPK_METODE_IDS)
            ->whereHas('ujianPspk')
            ->when($this->level_pspk, function ($query) use ($service) {
                $metode = $service->metodeFromLevel((int) $this->level_pspk);
                if ($metode) {
                    $query->where('metode_tes_id', $metode);
                }
            })
            ->when($this->event, function ($query) {
                $query->where('id', $this->event);
            })
            ->when($this->tgl_mulai, function ($query) {
                $query->where('tgl_mulai', date('Y-m-d', strtotime($this->tgl_mulai)));
            })
            ->when($this->search, function ($query) {
                $query->where('nama_event', 'like', '%'.$this->search.'%');
            })
            ->orderByDesc('tgl_mulai')
            ->paginate(10);

        $option_level_pspk = RefLevelPspk::pluck('nama_pspk', 'id');

        $optionEventQuery = Event::query()
            ->whereIn('metode_tes_id', PspkJawabanService::PSPK_METODE_IDS)
            ->whereHas('ujianPspk')
            ->orderByDesc('tgl_mulai');

        if ($this->level_pspk) {
            $metode = $service->metodeFromLevel((int) $this->level_pspk);
            if ($metode) {
                $optionEventQuery->where('metode_tes_id', $metode);
            }
        }

        $option_event = $optionEventQuery->pluck('nama_event', 'id');

        return view('livewire.admin.jawaban-peserta.pspk.index', compact(
            'data',
            'option_event',
            'option_level_pspk'
        ));
    }
}
