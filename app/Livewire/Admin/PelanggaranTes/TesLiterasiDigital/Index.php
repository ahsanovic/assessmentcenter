<?php

namespace App\Livewire\Admin\PelanggaranTes\TesLiterasiDigital;

use App\Models\Event;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.admin.app', ['title' => 'Log Pelanggaran Tes'])]
class Index extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public ?string $search = '';

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $data = Event::with('logPelanggaran')
            ->when($this->search, function ($query) {
                $query->where('nama_event', 'like', '%'.$this->search.'%');
            })
            ->where('metode_tes_id', 3)
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('livewire.admin.pelanggaran-tes.tes-literasi-digital.index', compact('data'));
    }

    public function resetFilters()
    {
        $this->reset();
        $this->resetPage();
    }
}
