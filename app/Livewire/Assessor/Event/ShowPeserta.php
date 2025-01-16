<?php

namespace App\Livewire\Assessor\Event;

use App\Models\Event;
use App\Models\Peserta;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.assessor.app', ['title' => 'Event'])]
class ShowPeserta extends Component
{
    use WithPagination;

    public $id_event;
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
        $this->id_event = $idEvent;
        $assessor_id = auth()->guard('assessor')->user()->id;
        
        $this->event = Event::whereHas('assessor', function ($query) use ($assessor_id) {
            $query->where('assessor_id', $assessor_id);
        })->findOrFail($this->id_event);
    }

    public function render()
    {
        $assessor_id = auth()->guard('assessor')->user()->id;
        $data = Peserta::whereHas('assessor', function ($query) use ($assessor_id) {
            $query->where('assessor_id', $assessor_id)
                ->where('assessor_peserta.event_id', $this->id_event); // Menggunakan filter pada tabel pivot
        })
            ->when($this->search, function ($query) {
                $query->where('nama', 'like', '%' . $this->search . '%')
                    ->orWhere('nip', 'like', '%' . $this->search . '%')
                    ->orWhere('jabatan', 'like', '%' . $this->search . '%')
                    ->orWhere('instansi', 'like', '%' . $this->search . '%');
            })
            ->paginate(10);

        return view('livewire.assessor.event.show-peserta', [
            'data' => $data
        ]);
    }
}
