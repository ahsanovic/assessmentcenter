<?php

namespace App\Livewire\Admin\Assessor;

use App\Models\Assessor;
use App\Models\Event;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.admin.app', ['title' => 'Assessor'])]
class Index extends Component
{
    use WithPagination;

    public $selected_id;
    public $event;
    public $is_active;
    public $is_asn;

    #[Url(as: 'q')]
    public ?string $search =  '';

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedIsActive()
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
        $data = Assessor::when($this->search, function($query) {
            $query->where(function ($q) {
                $q->where('nama', 'like', '%' . $this->search . '%')
                    ->orWhere('nip', 'like', '%' . $this->search . '%')
                    ->orWhere('nik', 'like', '%' . $this->search . '%')
                    ->orWhere('jabatan', 'like', '%' . $this->search . '%')
                    ->orWhere('instansi', 'like', '%' . $this->search . '%');
            });
        })
        ->when($this->is_asn, function ($query) {
            $query->where('is_asn', $this->is_asn);
        })
        ->when($this->event, function($query) {
            $query->whereHas('event', function($query) {
                $query->where('assessor_event.event_id', $this->event);
            });
        })
        ->when($this->is_active, function($query) {
            $query->where('is_active', $this->is_active);
        })
        ->orderByDesc('id')
        ->paginate(10);

        $option_event = Event::pluck('nama_event', 'id');
        $option_status = [ 'true' => 'aktif', 'false' => 'tidak aktif'];

        return view('livewire.admin.assessor.index', compact('data', 'option_event', 'option_status'));
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
            Assessor::find($this->selected_id)->delete();

            $this->dispatch('toast', ['type' => 'success', 'message' => 'berhasil menghapus data']);
        } catch (\Throwable $th) {
            // throw $th;
            $this->dispatch('toast', ['type' => 'error', 'message' => 'gagal menghapus data']);
        }
    }
}
