<?php

namespace App\Livewire\Admin\HasilResponden;

use App\Models\Event;
use App\Models\JawabanResponden;
use App\Models\RefMetodeTes;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.admin.app', ['title' => 'Hasil Responden'])]
class Index extends Component
{
    use WithPagination;

    public $selected_id;

    #[Url(as: 'q')]
    public ?string $search = '';

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $totalQuery = JawabanResponden::query()
            ->join('event', 'event.id', '=', 'jawaban_responden.event_id')
            ->where('event.metode_tes_id', Event::METODE_KUESIONER_RESPONDEN);

        if ($this->search !== '') {
            $totalQuery->where('event.nama_event', 'like', '%' . $this->search . '%');
        }

        $totalResponden = $totalQuery->count();

        $metodeKuesionerLabel = RefMetodeTes::query()
            ->whereKey(Event::METODE_KUESIONER_RESPONDEN)
            ->value('metode_tes')
            ?: 'Lainnya';

        $data = Event::query()
            ->with(['metodeTes'])
            ->withCount('jawabanResponden')
            ->where('metode_tes_id', Event::METODE_KUESIONER_RESPONDEN)
            ->whereHas('jawabanResponden')
            ->when($this->search, function ($query) {
                $query->where('nama_event', 'like', '%' . $this->search . '%');
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.admin.hasil-responden.index', compact('data', 'totalResponden', 'metodeKuesionerLabel'));
    }

    public function resetFilters()
    {
        $this->reset();
        $this->resetPage();
        $this->render();
    }
}
