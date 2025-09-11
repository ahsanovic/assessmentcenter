<?php

namespace App\Livewire\Admin\DataTes\TesIntelektual\TesSelesai;

use App\Models\Event;
use App\Models\Intelektual\HasilIntelektual;
use App\Models\Intelektual\UjianIntelektualSubTes3;
use App\Models\Peserta;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.admin.app', ['title' => 'Tes Selesai'])]
class ShowPesertaSubTes3 extends Component
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
        $this->event = Event::with(['pesertaTesIntelektualSubTes3'])->findOrFail($this->id_event);
    }

    public function render()
    {
        $data = Peserta::join('hasil_intelektual', 'hasil_intelektual.peserta_id', '=', 'peserta.id')
            ->join('ujian_intelektual_subtes_3', 'ujian_intelektual_subtes_3.peserta_id', '=', 'peserta.id')
            ->whereIn('peserta.id', $this->event->pesertaIdTesIntelektualSubTes3->pluck('peserta_id'))
            ->where('hasil_intelektual.event_id', $this->id_event)
            ->where('ujian_intelektual_subtes_3.event_id', $this->id_event)
            ->select(
                'peserta.*',
                'hasil_intelektual.id as hasil_intelektual_id',
                'hasil_intelektual.created_at as waktu_selesai',
                'ujian_intelektual_subtes_3.created_at as waktu_mulai',
                'is_finished'
            )
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

        return view('livewire.admin.data-tes.tes-intelektual.tes-selesai.show-peserta-subtes-3', [
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
            $hasil_subtes_3 = HasilIntelektual::findOrFail($this->selected_id);

            UjianIntelektualSubTes3::where('peserta_id', $hasil_subtes_3->peserta_id)
                ->where('event_id', $hasil_subtes_3->event_id)
                ->delete();

            $hasil_subtes_3->update([
                'nilai_subtes_3' => null
            ]);

            $this->dispatch('toast', ['type' => 'success', 'message' => 'berhasil menghapus data']);
        } catch (\Throwable $th) {
            // throw $th;
            $this->dispatch('toast', ['type' => 'error', 'message' => 'gagal menghapus data']);
        }
    }
}
