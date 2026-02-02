<?php

namespace App\Livewire\Admin\DataTes\TesPotensi\TesSelesai;

use App\Models\Event;
use App\Models\PengembanganDiri\HasilPengembanganDiri;
use App\Models\Peserta;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.admin.app', ['title' => 'Tes Selesai'])]
class ShowPesertaPengembanganDiri extends Component
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
        $this->event = Event::with(['pesertaTesPengembanganDiri'])->findOrFail($this->id_event);
    }

    public function render()
    {
        $data = Peserta::join('hasil_pengembangan_diri', 'hasil_pengembangan_diri.peserta_id', '=', 'peserta.id')
            ->join('ujian_pengembangan_diri', 'ujian_pengembangan_diri.peserta_id', '=', 'peserta.id')
            ->whereIn('peserta.id', $this->event->pesertaIdTesPengembanganDiri->pluck('peserta_id'))
            ->where('hasil_pengembangan_diri.event_id', $this->id_event)
            ->where('ujian_pengembangan_diri.event_id', $this->id_event)
            ->select('peserta.*', 'hasil_pengembangan_diri.id as hasil_pengembangan_diri_id', 'hasil_pengembangan_diri.created_at as waktu_selesai', 'is_finished')
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

        return view('livewire.admin.data-tes.tes-potensi.tes-selesai.show-peserta-pengembangan-diri', [
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
            HasilPengembanganDiri::find($this->selected_id)->delete();

            $this->dispatch('toast', ['type' => 'success', 'message' => 'berhasil menghapus data']);
        } catch (\Throwable $th) {
            // throw $th;
            $this->dispatch('toast', ['type' => 'error', 'message' => 'gagal menghapus data']);
        }
    }
}
