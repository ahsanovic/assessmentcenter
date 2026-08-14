<?php

namespace App\Livewire\Admin\EventGroup;

use App\Models\Event;
use App\Models\EventGroup;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.admin.app', ['title' => 'Grup Assessment'])]
class Index extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public ?string $search = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset(['search']);
        $this->resetPage();
    }

    public function render()
    {
        $groups = EventGroup::query()
            ->withCount('events')
            ->when($this->search, fn ($q) => $q->where('nama', 'like', '%' . $this->search . '%'))
            ->orderByDesc('id')
            ->paginate(10);

        $eventIdsForDownload = Event::query()
            ->whereIn('event_group_id', $groups->pluck('id'))
            ->orderBy('event_group_id')
            ->orderBy('id')
            ->get()
            ->groupBy('event_group_id')
            ->map(fn ($events) => $events->first()?->id);

        return view('livewire.admin.event-group.index', [
            'groups' => $groups,
            'eventIdsForDownload' => $eventIdsForDownload,
        ]);
    }
}
