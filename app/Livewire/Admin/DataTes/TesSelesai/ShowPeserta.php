<?php

namespace App\Livewire\Admin\DataTes\TesSelesai;

use App\Models\Event;
use Livewire\Attributes\Layout;
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
    public $tanggal_tes;

    #[Url(as: 'q')]
    public ?string $search =  '';

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->reset(['search', 'tanggal_tes']);
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
                ->with(['ujianInterpersonal' => function ($query) {
                    $query->select('id', 'peserta_id', 'created_at');
                }])
                ->when($this->search, function($query) {
                    $query->where(function ($q) {
                        $q->where('nama', 'like', '%' . $this->search . '%')
                            ->orWhere('nip', 'like', '%' . $this->search . '%')
                            ->orWhere('nik', 'like', '%' . $this->search . '%')
                            ->orWhere('jabatan', 'like', '%' . $this->search . '%')
                            ->orWhere('instansi', 'like', '%' . $this->search . '%');
                    });
                })
                ->when($this->tanggal_tes, function ($query) {
                    $tanggal_tes = date('Y-m-d', strtotime($this->tanggal_tes));
                    // $query->whereHas('ujianInterpersonal', function ($query) use ($tanggal_tes) {
                    //     $query->whereDate('created_at', $tanggal_tes);
                    // });
                    $query->whereDate('test_started_at', $tanggal_tes);
                })
                ->whereHas('ujianInterpersonal', function($query) {
                    $query->where('is_finished', 'true');
                })
                ->whereHas('ujianKesadaranDiri', function($query) {
                    $query->where('is_finished', 'true');
                })
                ->whereHas('ujianBerpikirKritis', function($query) {
                    $query->where('is_finished', 'true');
                })
                ->whereHas('ujianProblemSolving', function($query) {
                    $query->where('is_finished', 'true');
                })
                ->whereHas('ujianPengembanganDiri', function($query) {
                    $query->where('is_finished', 'true');
                })
                ->whereHas('ujianKecerdasanEmosi', function($query) {
                    $query->where('is_finished', 'true');
                })
                ->whereHas('ujianMotivasiKomitmen', function($query) {
                    $query->where('is_finished', 'true');
                })
                ->paginate(10);

        return view('livewire.admin.data-tes.tes-selesai.show-peserta', [
            'data' => $data
        ]);
    }
}
