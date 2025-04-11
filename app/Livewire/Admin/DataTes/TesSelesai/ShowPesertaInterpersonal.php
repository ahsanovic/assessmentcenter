<?php

namespace App\Livewire\Admin\DataTes\TesSelesai;

use App\Models\Event;
use App\Models\Interpersonal\HasilInterpersonal;
use App\Models\Peserta;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.admin.app', ['title' => 'Tes Selesai'])]
class ShowPesertaInterpersonal extends Component
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
        $this->event = Event::with(['pesertaTesInterpersonal'])->findOrFail($this->id_event);
    }

    public function render()
    {
        $data = Peserta::join('hasil_interpersonal', 'hasil_interpersonal.peserta_id', '=', 'peserta.id')
                ->join('ujian_interpersonal', 'ujian_interpersonal.peserta_id', '=', 'peserta.id')
                ->whereIn('peserta.id', $this->event->pesertaIdTesInterpersonal->pluck('peserta_id'))
                ->select('peserta.*',
                    'hasil_interpersonal.id as hasil_interpersonal_id',
                    'hasil_interpersonal.created_at as waktu_selesai',
                    'ujian_interpersonal.created_at as waktu_mulai',
                    'is_finished'
                )
                ->when($this->search, function($query) {
                    $query->where('nama', 'like', '%' . $this->search . '%')
                        ->orWhere('nip', 'like', '%' . $this->search . '%')
                        ->orWhere('jabatan', 'like', '%' . $this->search . '%')
                        ->orWhere('instansi', 'like', '%' . $this->search . '%');
                })
                ->paginate(10);

        return view('livewire.admin.data-tes.tes-selesai.show-peserta-interpersonal', [
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
            HasilInterpersonal::find($this->selected_id)->delete();

            $this->dispatch('toast', ['type' => 'success', 'message' => 'berhasil menghapus data']);
        } catch (\Throwable $th) {
            // throw $th;
            $this->dispatch('toast', ['type' => 'error', 'message' => 'gagal menghapus data']);
        }
    }
}
