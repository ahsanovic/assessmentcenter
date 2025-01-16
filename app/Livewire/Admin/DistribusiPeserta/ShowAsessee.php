<?php

namespace App\Livewire\Admin\DistribusiPeserta;

use App\Models\Assessor;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.admin.app', ['title' => 'Distribusi Peserta'])]
class ShowAsessee extends Component
{
    use WithPagination;

    public $assessor;

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

    public function mount($idAssessor)
    {
        $this->assessor = Assessor::with('peserta')->findOrFail($idAssessor);
    }

    public function render()
    {
        $data = $this->assessor->peserta()
                ->when($this->search, function ($query) {
                    $query->where('nama', 'like', '%' . $this->search . '%')
                        ->orWhere('nip', 'like', '%' . $this->search . '%')
                        ->orWhere('jabatan', 'like', '%' . $this->search . '%')
                        ->orWhere('instansi', 'like', '%' . $this->search . '%');
                })
                ->paginate(10);
                
        return view('livewire.admin.distribusi-peserta.show-asessee', [
            'data' => $data
        ]);
    }
}
