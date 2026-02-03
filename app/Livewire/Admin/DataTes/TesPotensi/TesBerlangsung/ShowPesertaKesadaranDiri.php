<?php

namespace App\Livewire\Admin\DataTes\TesPotensi\TesBerlangsung;

use App\Models\Event;
use App\Models\KesadaranDiri\UjianKesadaranDiri;
use App\Models\Peserta;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.admin.app', ['title' => 'Tes Berlangsung'])]
class ShowPesertaKesadaranDiri extends Component
{
    use WithPagination;

    public $event;
    public $id_event;
    public $selected_id;

    #[Url(as: 'q')]
    public ?string $search =  '';

    public ?string $filterSoalBelumDijawab = '';

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedFilterSoalBelumDijawab()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->reset(['search', 'filterSoalBelumDijawab']);
        $this->resetPage();
        $this->render();
    }

    public function mount($idEvent)
    {
        $this->id_event = $idEvent;
        $this->event = Event::with(['pesertaTesKesadaranDiri'])->findOrFail($this->id_event);
    }

    public function render()
    {
        $data = Peserta::join('ujian_kesadaran_diri', 'ujian_kesadaran_diri.peserta_id', '=', 'peserta.id')
            ->whereIn('peserta.id', $this->event->pesertaIdTesKesadaranDiri->pluck('peserta_id'))
            ->where('ujian_kesadaran_diri.event_id', $this->id_event)
            ->select('peserta.*', 'soal_id', 'jawaban','ujian_kesadaran_diri.is_finished', 'ujian_kesadaran_diri.id as ujian_kesadaran_diri_id', 'ujian_kesadaran_diri.created_at as mulai_tes')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('nama', 'like', '%' . $this->search . '%')
                        ->orWhere('nip', 'like', '%' . $this->search . '%')
                        ->orWhere('nik', 'like', '%' . $this->search . '%')
                        ->orWhere('jabatan', 'like', '%' . $this->search . '%')
                        ->orWhere('unit_kerja', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->filterSoalBelumDijawab === 'ada', function ($query) {
                // Filter peserta yang memiliki soal belum dijawab
                $query->where(function($q) {
                    $q->whereRaw('ujian_kesadaran_diri.jawaban LIKE \'%,0,%\'')
                      ->orWhereRaw('ujian_kesadaran_diri.jawaban LIKE \'0,%\'')
                      ->orWhereRaw('ujian_kesadaran_diri.jawaban LIKE \'%,0\'')
                      ->orWhereRaw('ujian_kesadaran_diri.jawaban = \'0\'')
                      ->orWhereRaw('ujian_kesadaran_diri.jawaban LIKE \'%,,\'')
                      ->orWhereRaw('ujian_kesadaran_diri.jawaban LIKE \'%,,,%\'');
                });
            })
            ->when($this->filterSoalBelumDijawab === 'semua_terjawab', function ($query) {
                // Filter peserta yang semua soalnya sudah dijawab
                $query->whereRaw('ujian_kesadaran_diri.jawaban NOT LIKE \'%,0,%\'')
                      ->whereRaw('ujian_kesadaran_diri.jawaban NOT LIKE \'0,%\'')
                      ->whereRaw('ujian_kesadaran_diri.jawaban NOT LIKE \'%,0\'')
                      ->whereRaw('ujian_kesadaran_diri.jawaban != \'0\'')
                      ->whereRaw('ujian_kesadaran_diri.jawaban NOT LIKE \'%,,%\'');
            })
            ->paginate(10);

        return view('livewire.admin.data-tes.tes-potensi.tes-berlangsung.show-peserta-kesadaran-diri', [
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
            UjianKesadaranDiri::find($this->selected_id)->delete();

            $this->dispatch('toast', ['type' => 'success', 'message' => 'berhasil menghapus data']);
        } catch (\Throwable $th) {
            // throw $th;
            $this->dispatch('toast', ['type' => 'error', 'message' => 'gagal menghapus data']);
        }
    }
}
