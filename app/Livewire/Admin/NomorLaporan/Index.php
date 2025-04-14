<?php

namespace App\Livewire\Admin\NomorLaporan;

use App\Models\Event;
use App\Models\NomorLaporan;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.admin.app', ['title' => 'Nomor Laporan Penilaian'])]
class Index extends Component
{
    use WithPagination;

    public $selected_id;
    public $tanggal;
    public $event_id;

    #[Url(as: 'q')]
    public ?string $search = '';

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $data = NomorLaporan::with('event')->when($this->search, fn($query) => $query->where('nomor', 'like', '%' . $this->search . '%'))
            ->when($this->tanggal, function($query) {
                $tanggal = date('Y-m-d', strtotime($this->tanggal));
                $query->where('tanggal', $tanggal);
            })
            ->when($this->event_id, fn($query) => $query->where('event_id', $this->event_id))
            ->orderByDesc('id')
            ->paginate(10);

        $options_event = Event::pluck('nama_event', 'id');

        return view('livewire.admin.nomor-laporan.index', compact('data', 'options_event'));
    }

    public function resetFilters()
    {
        $this->reset();
        $this->resetPage();
        $this->render();
        $this->dispatch('reset-select2');
    }

    public function deleteConfirmation($id)
    {
        $this->selected_id = $id;
        $this->dispatch('show-delete-confirmation');
    }

    #[On('delete')]
    public function destroy()
    {
        try {
            NomorLaporan::find($this->selected_id)->delete();

            $this->dispatch('toast', ['type' => 'success', 'message' => 'berhasil menghapus data']);
        } catch (\Throwable $th) {
            // throw $th;
            $this->dispatch('toast', ['type' => 'error', 'message' => 'gagal menghapus data']);
        }
    }
}
