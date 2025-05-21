<?php

namespace App\Livewire\Admin\HasilResponden;

use App\Models\Event;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.admin.app', ['title' => 'Hasil Responden'])]
class Index extends Component
{
    use WithPagination;

    public $selected_id;

    #[Url(as: 'q')]
    public ?string $search = '';

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $data = Event::with('jawabanResponden')
            ->withCount(['jawabanResponden as jawaban_responden_count' => function ($query) {
                $query->where('is_finished', 'true');
            }])
            ->when($this->search, function ($query) {
                $query->where('nama_event', 'like', '%' . $this->search . '%');
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.admin.hasil-responden.index', compact('data'));
    }

    public function resetFilters()
    {
        $this->reset();
        $this->resetPage();
        $this->render();
    }
}
