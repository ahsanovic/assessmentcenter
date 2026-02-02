<?php

namespace App\Livewire\Admin\DataTes\TesPotensi\TesBerlangsung;

use App\Models\Event;
use App\Models\BerpikirKritis\UjianBerpikirKritis;
use App\Models\Peserta;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.admin.app', ['title' => 'Tes Berlangsung'])]
class ShowPesertaBerpikirKritis extends Component
{
    use WithPagination;

    public $event;
    public $id_event;
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
        $this->event = Event::with(['pesertaTesBerpikirKritis'])->findOrFail($this->id_event);
    }

    public function render()
    {
        $data = Peserta::join('ujian_berpikir_kritis', 'ujian_berpikir_kritis.peserta_id', '=', 'peserta.id')
            ->whereIn('peserta.id', $this->event->pesertaIdTesBerpikirKritis->pluck('peserta_id'))
            ->where('ujian_berpikir_kritis.event_id', $this->id_event)
            ->select('peserta.*', 'ujian_berpikir_kritis.is_finished', 'ujian_berpikir_kritis.id as ujian_berpikir_kritis_id', 'ujian_berpikir_kritis.created_at as mulai_tes')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('nama', 'like', '%' . $this->search . '%')
                        ->orWhere('nip', 'like', '%' . $this->search . '%')
                        ->orWhere('nik', 'like', '%' . $this->search . '%')
                        ->orWhere('jabatan', 'like', '%' . $this->search . '%')
                        ->orWhere('unit_kerja', 'like', '%' . $this->search . '%');
                });
            })
            ->paginate(10);

        return view('livewire.admin.data-tes.tes-potensi.tes-berlangsung.show-peserta-berpikir-kritis', [
            'data' => $data
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
            UjianBerpikirKritis::find($this->selected_id)->delete();

            $this->dispatch('toast', ['type' => 'success', 'message' => 'berhasil menghapus data']);
        } catch (\Throwable $th) {
            // throw $th;
            $this->dispatch('toast', ['type' => 'error', 'message' => 'gagal menghapus data']);
        }
    }
}
