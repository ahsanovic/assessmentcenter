<?php

namespace App\Livewire\Admin\DistribusiPeserta;

use App\Models\Event;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.admin.app', ['title' => 'Distribusi Peserta'])]
class ShowAssessor extends Component
{
    use WithPagination;

    public $event;

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
        $this->event = Event::with('assessor')->findOrFail($idEvent);
    }

    public function render()
    {
        $data = $this->event->assessor()
                ->with(['peserta' => function ($query) {
                    $query->where('assessor_peserta.event_id', $this->event->id);
                }])
                ->withCount(['peserta' => function ($query) {
                    $query->where('assessor_peserta.event_id', $this->event->id);
                }])
                ->when($this->search, function ($query) {
                    $query->where('nama', 'like', '%' . $this->search . '%')
                        ->orWhere('nip', 'like', '%' . $this->search . '%')
                        ->orWhere('jabatan', 'like', '%' . $this->search . '%')
                        ->orWhere('instansi', 'like', '%' . $this->search . '%');
                })
                ->paginate(10);
                
        return view('livewire.admin.distribusi-peserta.show-assessor', [
            'data' => $data
        ]);
    }
}
