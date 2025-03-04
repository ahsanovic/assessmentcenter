<?php

namespace App\Livewire\Admin\DataTes\TesSelesai;

use App\Models\Event;
use App\Models\Interpersonal\HasilInterpersonal;
use App\Models\Peserta;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.admin.app', ['title' => 'Tes Selesai'])]
class ShowPeserta extends Component
{
    use WithPagination;

    public $event;
    public $id_event;
    public $selected_id;

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
                ->when($this->search, function($query) {
                    $query->where('nama', 'like', '%' . $this->search . '%')
                        ->orWhere('nip', 'like', '%' . $this->search . '%')
                        ->orWhere('jabatan', 'like', '%' . $this->search . '%')
                        ->orWhere('instansi', 'like', '%' . $this->search . '%');
                })
                ->whereHas('ujianInterpersonal', function($query) {
                    $query->where('is_finished', true);
                })
                ->whereHas('ujianKesadaranDiri', function($query) {
                    $query->where('is_finished', true);
                })
                ->whereHas('ujianBerpikirKritis', function($query) {
                    $query->where('is_finished', true);
                })
                ->whereHas('ujianProblemSolving', function($query) {
                    $query->where('is_finished', true);
                })
                ->whereHas('ujianPengembanganDiri', function($query) {
                    $query->where('is_finished', true);
                })
                ->whereHas('ujianKecerdasanEmosi', function($query) {
                    $query->where('is_finished', true);
                })
                ->whereHas('ujianMotivasiKomitmen', function($query) {
                    $query->where('is_finished', true);
                })
                ->paginate(10);

        return view('livewire.admin.data-tes.tes-selesai.show-peserta', [
            'data' => $data
        ]);
    }
}
