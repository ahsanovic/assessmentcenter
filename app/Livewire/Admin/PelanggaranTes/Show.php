<?php

namespace App\Livewire\Admin\PelanggaranTes;

use App\Models\Event;
use App\Models\Kuesioner;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.admin.app', ['title' => 'Log Pelanggaran Tes'])]
class Show extends Component
{
    use WithPagination;

    public $event;
    public $id_event;
    public $pertanyaan = [];

    #[Url(as: 'q')]
    public ?string $search =  '';

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->reset(['search']);
        $this->resetPage();
        $this->render();
    }

    public function mount($idEvent)
    {
        $this->id_event = $idEvent;
        $this->event = Event::with('peserta')->findOrFail($this->id_event);
    }

    public function render()
    {
        $data = $this->event->peserta()
            ->with('logPelanggaran')
            ->whereHas('logPelanggaran')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('nama', 'like', '%' . $this->search . '%');
                });
            })
            ->paginate(5);

        return view('livewire.admin.pelanggaran-tes.show', [
            'data' => $data,
        ]);
    }
}
