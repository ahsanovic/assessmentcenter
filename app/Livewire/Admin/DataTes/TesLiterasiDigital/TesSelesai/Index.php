<?php

namespace App\Livewire\Admin\DataTes\TesLiterasiDigital\TesSelesai;

use App\Models\Event;
use App\Models\RefJabatanDiuji;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.admin.app', ['title' => 'Tes Literasi Digital'])]
class Index extends Component
{
    use WithPagination;

    public $jabatan_diuji;

    public $tgl_mulai;

    #[Url(as: 'q')]
    public ?string $search = '';

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
        $this->reset(['jabatan_diuji', 'tgl_mulai', 'search']);
        $this->resetPage();
    }

    public function render()
    {
        $data = Event::withCount(['peserta', 'hasilLiterasiDigital'])
            ->where('metode_tes_id', 3)
            ->when($this->search, function ($query) {
                $query->where('nama_event', 'like', '%'.$this->search.'%');
            })
            ->when($this->jabatan_diuji, function ($query) {
                $query->where('jabatan_diuji_id', $this->jabatan_diuji);
            })
            ->when($this->tgl_mulai, function ($query) {
                $query->whereDate('tgl_mulai', date('Y-m-d', strtotime($this->tgl_mulai)));
            })
            ->orderByDesc('tgl_mulai')
            ->paginate(10);

        $option_jabatan_diuji = RefJabatanDiuji::pluck('jenis', 'id');

        return view('livewire.admin.data-tes.tes-literasi-digital.tes-selesai.index', compact('data', 'option_jabatan_diuji'));
    }
}
