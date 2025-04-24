<?php

namespace App\Livewire\Admin\DistribusiPeserta;

use App\Models\Event;
use App\Models\RefJabatanDiuji;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.admin.app', ['title' => 'Distribusi Peserta'])]
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
        $data = Event::withCount('assessor', 'peserta')
            ->where('is_finished', 'false')
            ->when($this->event, function($query) {
                $query->where('id', $this->event);
            })
            ->when($this->jabatan_diuji, function($query,) {
                $query->where('jabatan_diuji_id', $this->jabatan_diuji);
            })
            ->when($this->tgl_mulai, function($query) {
                $tgl_mulai = date('Y-m-d', strtotime($this->tgl_mulai));
                $query->where('tgl_mulai', $tgl_mulai);
            })
            ->with(['peserta', 'alatTes'])
            ->orderByDesc('id')
            ->paginate(10);

        $option_jabatan_diuji = RefJabatanDiuji::pluck('jenis', 'id');
        $option_event = Event::pluck('nama_event', 'id');

        return view('livewire.admin.distribusi-peserta.index', compact('data', 'option_jabatan_diuji', 'option_event'));
    }
}
