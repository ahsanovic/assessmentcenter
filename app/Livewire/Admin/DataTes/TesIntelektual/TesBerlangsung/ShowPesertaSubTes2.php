<?php

namespace App\Livewire\Admin\DataTes\TesIntelektual\TesBerlangsung;

use App\Models\Event;
use App\Models\Intelektual\UjianIntelektualSubTes2;
use App\Models\Peserta;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.admin.app', ['title' => 'Tes Berlangsung'])]
class ShowPesertaSubTes2 extends Component
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
        $this->event = Event::with(['pesertaTesIntelektualSubTes2'])->findOrFail($this->id_event);
    }

    public function render()
    {
        $data = Peserta::join('ujian_intelektual_subtes_2', 'ujian_intelektual_subtes_2.peserta_id', '=', 'peserta.id')
            ->whereIn('peserta.id', $this->event->pesertaIdTesIntelektualSubTes2->pluck('peserta_id'))
            ->where('ujian_intelektual_subtes_2.event_id', $this->id_event)
            ->select('peserta.*', 'ujian_intelektual_subtes_2.is_finished', 'ujian_intelektual_subtes_2.id as ujian_intelektual_subtes_2_id', 'ujian_intelektual_subtes_2.created_at as mulai_tes')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('nama', 'like', '%' . $this->search . '%')
                        ->orWhere('nip', 'like', '%' . $this->search . '%')
                        ->orWhere('nik', 'like', '%' . $this->search . '%')
                        ->orWhere('jabatan', 'like', '%' . $this->search . '%')
                        ->orWhere('instansi', 'like', '%' . $this->search . '%');
                });
            })
            ->paginate(10);

        return view('livewire.admin.data-tes.tes-intelektual.tes-berlangsung.show-peserta-subtes-2', [
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
            UjianIntelektualSubTes2::find($this->selected_id)->delete();

            $this->dispatch('toast', ['type' => 'success', 'message' => 'berhasil menghapus data']);
        } catch (\Throwable $th) {
            // throw $th;
            $this->dispatch('toast', ['type' => 'error', 'message' => 'gagal menghapus data']);
        }
    }
}
