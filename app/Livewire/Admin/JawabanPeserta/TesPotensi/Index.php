<?php

namespace App\Livewire\Admin\JawabanPeserta\TesPotensi;

use App\Models\Event;
use App\Services\TesPotensi\TesPotensiJawabanService;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.admin.app', ['title' => 'Analisis Jawaban — Tes Potensi'])]
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
        $service = app(TesPotensiJawabanService::class);

        $data = Event::query()
            ->with(['metodeTes'])
            ->whereIn('metode_tes_id', TesPotensiJawabanService::METODE_TES_IDS)
            ->where(function ($query) use ($service) {
                foreach ($service->subtestDefinitions() as $def) {
                    $relation = $this->eventRelationFromModel($def['ujian_model']);
                    if ($relation) {
                        $query->orWhereHas($relation);
                    }
                }
            })
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

        $pesertaCounts = [];
        foreach ($data as $event) {
            $pesertaCounts[$event->id] = $service->countParticipantsWithData((int) $event->id);
        }

        return view('livewire.admin.jawaban-peserta.tes-potensi.index', [
            'data' => $data,
            'pesertaCounts' => $pesertaCounts,
        ]);
    }

    private function eventRelationFromModel(string $ujianModel): ?string
    {
        return match ($ujianModel) {
            \App\Models\Interpersonal\UjianInterpersonal::class => 'ujianInterpersonal',
            \App\Models\KesadaranDiri\UjianKesadaranDiri::class => 'ujianKesadaranDiri',
            \App\Models\BerpikirKritis\UjianBerpikirKritis::class => 'ujianBerpikirKritis',
            \App\Models\ProblemSolving\UjianProblemSolving::class => 'ujianProblemSolving',
            \App\Models\KecerdasanEmosi\UjianKecerdasanEmosi::class => 'ujianKecerdasanEmosi',
            \App\Models\PengembanganDiri\UjianPengembanganDiri::class => 'ujianPengembanganDiri',
            \App\Models\MotivasiKomitmen\UjianMotivasiKomitmen::class => 'ujianMotivasiKomitmen',
            default => null,
        };
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
