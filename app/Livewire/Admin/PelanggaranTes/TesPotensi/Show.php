<?php

namespace App\Livewire\Admin\PelanggaranTes\TesPotensi;

use App\Models\Event;
use App\Models\LogPelanggaran;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.admin.app', ['title' => 'Log Pelanggaran Tes'])]
class Show extends Component
{
    use WithPagination;

    public $event;
    public $id_event;
    public $pertanyaan = [];
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
            ->with('logPelanggaran')
            ->whereHas('logPelanggaran')
            ->where('event_id', $this->id_event)
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('nama', 'like', '%' . $this->search . '%');
                });
            })
            ->paginate(10);

        return view('livewire.admin.pelanggaran-tes.tes-potensi.show', [
            'data' => $data,
        ]);
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
            LogPelanggaran::where('event_id', $this->id_event)
                ->where('peserta_id', $this->selected_id)
                ->delete();

            $this->dispatch('toast', ['type' => 'success', 'message' => 'berhasil menghapus data']);
        } catch (\Throwable $th) {
            // throw $th;
            $this->dispatch('toast', ['type' => 'error', 'message' => 'gagal menghapus data']);
        }
    }
}
