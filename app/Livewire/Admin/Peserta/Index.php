<?php

namespace App\Livewire\Admin\Peserta;

use App\Models\Event;
use App\Models\Peserta;
use App\Models\RefJenisPeserta;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.admin.app', ['title' => 'Peserta'])]
class Index extends Component
{
    use WithPagination;

    public $selected_id;
    public $event;
    public $is_active;
    public $jenis_peserta_id;

    #[Url(as: 'q')]
    public ?string $search =  '';

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedEvent()
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
        $data = Peserta::with(['event', 'jenisPeserta'])->when($this->search, function($query) {
            $query->where('nama', 'like', '%' . $this->search . '%')
                ->orWhere('nip', 'like', '%' . $this->search . '%')
                ->orWhere('nik', 'like', '%' . $this->search . '%')
                ->orWhere('jabatan', 'like', '%' . $this->search . '%')
                ->orWhere('instansi', 'like', '%' . $this->search . '%');
        })
        ->when($this->jenis_peserta_id, function ($query) {
            $query->where('jenis_peserta_id', $this->jenis_peserta_id);
        })
        ->when($this->event, function($query) {
            $query->where('event_id', $this->event);
        })
        ->when($this->is_active, function($query) {
            $query->where('is_active', $this->is_active);
        })
        ->orderByDesc('id')
        ->paginate(10);

        $option_event = Event::pluck('nama_event', 'id');
        $option_status = [ 'true' => 'aktif', 'false' => 'tidak aktif'];
        $option_jenis_peserta = RefJenisPeserta::pluck('jenis_peserta', 'id');

        return view('livewire.admin.peserta.index', compact('data', 'option_event', 'option_status', 'option_jenis_peserta'));
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
            Peserta::find($this->selected_id)->delete();

            $this->dispatch('toast', ['type' => 'success', 'message' => 'berhasil menghapus data']);
        } catch (\Throwable $th) {
            // throw $th;
            $this->dispatch('toast', ['type' => 'error', 'message' => 'gagal menghapus data']);
        }
    }
}
