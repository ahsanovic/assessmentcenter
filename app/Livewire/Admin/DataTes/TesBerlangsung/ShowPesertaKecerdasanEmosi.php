<?php

namespace App\Livewire\Admin\DataTes\TesBerlangsung;

use App\Models\Event;
use App\Models\KecerdasanEmosi\UjianKecerdasanEmosi;
use App\Models\Peserta;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.admin.app', ['title' => 'Tes Berlangsung'])]
class ShowPesertaKecerdasanEmosi extends Component
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
        $this->event = Event::with(['pesertaTesKecerdasanEmosi'])->findOrFail($this->id_event);
    }

    public function render()
    {
        $data = Peserta::join('ujian_kecerdasan_emosi', 'ujian_kecerdasan_emosi.peserta_id', '=', 'peserta.id')
                ->whereIn('peserta.id', $this->event->pesertaIdTesKecerdasanEmosi->pluck('peserta_id'))
                ->select('peserta.*', 'ujian_kecerdasan_emosi.is_finished', 'ujian_kecerdasan_emosi.id as ujian_kecerdasan_emosi_id', 'ujian_kecerdasan_emosi.created_at as mulai_tes')
                ->when($this->search, function($query) {
                    $query->where('nama', 'like', '%' . $this->search . '%')
                        ->orWhere('nip', 'like', '%' . $this->search . '%')
                        ->orWhere('jabatan', 'like', '%' . $this->search . '%')
                        ->orWhere('instansi', 'like', '%' . $this->search . '%');
                })
                ->paginate(10);

        return view('livewire.admin.data-tes.tes-berlangsung.show-peserta-kecerdasan-emosi', [
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
            UjianKecerdasanEmosi::find($this->selected_id)->delete();

            $this->dispatch('toast', ['type' => 'success', 'message' => 'berhasil menghapus data']);
        } catch (\Throwable $th) {
            // throw $th;
            $this->dispatch('toast', ['type' => 'error', 'message' => 'gagal menghapus data']);
        }
    }
}
