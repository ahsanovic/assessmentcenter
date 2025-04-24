<?php

namespace App\Livewire\Assessor\Event;

use App\Models\Event;
use App\Models\RefJabatanDiuji;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.assessor.app', ['title' => 'Event'])]
class Index extends Component
{
    use WithPagination;

    public $jabatan_diuji;
    public $tgl_mulai;
    public $selected_id;
    public $event;

    #[Url(as: 'q')]
    public ?string $search =  '';

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedJabatanDiuji()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->reset();
        $this->resetPage();
        $this->render();
        $this->dispatch('reset-select2');
    }

    public function render()
    {
        $assessor_id = auth()->guard('assessor')->user()->id;

        $data = Event::whereHas('assessor', function ($query) use ($assessor_id) {
                $query->where('assessor_id', $assessor_id);
            })
            ->when($this->event, function ($query) {
                $query->where('id', $this->event);
            })
            ->when($this->jabatan_diuji, function ($query,) {
                $query->where('jabatan_diuji_id', $this->jabatan_diuji);
            })
            ->when($this->tgl_mulai, function ($query) {
                $tgl_mulai = date('Y-m-d', strtotime($this->tgl_mulai));
                $query->where('tgl_mulai', $tgl_mulai);
            })
            ->with(['peserta', 'alatTes'])
            ->orderByDesc('id')
            ->paginate(10);

        // Ambil event yang terkait dengan assessor, lalu ambil unique jabatan_diuji_id
        $option_jabatan_diuji = RefJabatanDiuji::whereIn('id', function ($query) use ($assessor_id) {
            $query->select('jabatan_diuji_id')
                ->from('event')
                ->whereIn('id', function ($subQuery) use ($assessor_id) {
                    $subQuery->select('event_id')
                        ->from('assessor_event')
                        ->where('assessor_id', $assessor_id);
                });
        })->pluck('jenis', 'id');

        $option_event = Event::whereIn('id', function ($query) use ($assessor_id) {
            $query->select('event_id')
                ->from('assessor_event')
                ->where('assessor_id', $assessor_id);
        })->pluck('nama_event', 'id');
        
        return view('livewire.assessor.event.index', compact('data', 'option_jabatan_diuji', 'option_event'));
    }
}
