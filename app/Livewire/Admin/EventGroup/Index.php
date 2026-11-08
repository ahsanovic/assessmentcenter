<?php

namespace App\Livewire\Admin\EventGroup;

use App\Models\Event;
use App\Models\EventGroup;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.admin.app', ['title' => 'Grup Assessment'])]
class Index extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public ?string $search = '';

    public bool $showModal = false;

    #[Locked]
    public ?int $editId = null;

    public string $nama = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset(['search']);
        $this->resetPage();
    }

    public function edit(int $id): void
    {
        $group = EventGroup::query()->findOrFail($id);
        $this->editId = $group->id;
        $this->nama = $group->nama;
        $this->showModal = true;
        $this->resetValidation();
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->editId = null;
        $this->nama = '';
        $this->resetValidation();
    }

    public function save(): void
    {
        $this->nama = trim($this->nama);

        $this->validate([
            'nama' => [
                'required',
                'string',
                'max:255',
                Rule::unique('event_group', 'nama')->ignore($this->editId),
            ],
        ], [
            'nama.required' => 'nama grup harus diisi',
            'nama.max' => 'nama grup maksimal 255 karakter',
            'nama.unique' => 'nama grup sudah digunakan',
        ]);

        $group = EventGroup::query()->findOrFail($this->editId);
        $oldData = $group->getOriginal();
        $group->update(['nama' => $this->nama]);
        activity_log($group, 'update', 'event-group', $oldData);

        $this->closeModal();
        $this->dispatch('toast', ['type' => 'success', 'message' => 'berhasil ubah nama grup']);
    }

    public function render()
    {
        $groups = EventGroup::query()
            ->withCount('events')
            ->with(['events.metodeTes'])
            ->when($this->search, fn ($q) => $q->where('nama', 'like', '%'.$this->search.'%'))
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
