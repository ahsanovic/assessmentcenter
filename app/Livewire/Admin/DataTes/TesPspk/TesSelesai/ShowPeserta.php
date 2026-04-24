<?php

namespace App\Livewire\Admin\DataTes\TesPspk\TesSelesai;

use App\Models\Event;
use App\Models\Peserta;
use App\Models\Pspk\HasilPspk;
use App\Models\Pspk\UjianPspk;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.admin.app', ['title' => 'Tes Selesai'])]
class ShowPeserta extends Component
{
    use WithPagination;

    public $event;
    public $id_event;
    public $selected_id;
    public $tanggal_tes;

    #[Url(as: 'q')]
    public ?string $search =  '';

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->reset(['search', 'tanggal_tes']);
        $this->resetPage();
        $this->render();
    }

    public function mount($idEvent)
    {
        $this->id_event = $idEvent;
        $this->event = Event::with(['pesertaTesPspk'])->findOrFail($this->id_event);
    }

    public function render()
    {
        $data = Peserta::join('hasil_pspk', 'hasil_pspk.peserta_id', '=', 'peserta.id')
            ->join('ujian_pspk', 'ujian_pspk.peserta_id', '=', 'peserta.id')
            ->whereIn('peserta.id', $this->event->pesertaIdTesPspk->pluck('peserta_id'))
            ->where('hasil_pspk.event_id', $this->id_event)
            ->where('ujian_pspk.event_id', $this->id_event)
            ->select(
                'peserta.*',
                'hasil_pspk.id as hasil_pspk_id',
                'hasil_pspk.created_at as waktu_selesai',
                'ujian_pspk.created_at as waktu_mulai',
                'ujian_pspk.id as ujian_pspk_id',
                'ujian_pspk.is_finished',
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
            ->when($this->tanggal_tes, function ($query) {
                $tanggal_tes = date('Y-m-d', strtotime($this->tanggal_tes));
                $query->whereDate('test_started_at', $tanggal_tes);
            })
            ->paginate(10);

        return view('livewire.admin.data-tes.tes-pspk.tes-selesai.show-peserta', [
            'data' => $data,
        ]);
    }

    public function setUjianKeBelumSelesaiConfirmation($id)
    {
        $this->selected_id = $id;
        $this->dispatch('set-ujian-ke-belum-selesai-confirmation');
    }

    public function setUjianKeBelumSelesaiMassalConfirmation()
    {
        $this->dispatch('set-ujian-ke-belum-selesai-massal-confirmation');
    }

    #[On('setUjianKeBelumSelesaiMassal')]
    public function setUjianKeBelumSelesaiMassal()
    {
        try {
            $updated = UjianPspk::where('event_id', $this->id_event)
                ->where('is_finished', 'true')
                ->update(['is_finished' => 'false']);

            if ($updated === 0) {
                $this->dispatch('toast', ['type' => 'info', 'message' => 'Tidak ada ujian dengan status selesai pada event ini']);

                return;
            }

            $this->dispatch('toast', [
                'type' => 'success',
                'message' => 'Berhasil menyetel ' . $updated . ' ujian ke belum selesai',
            ]);
        } catch (\Throwable $th) {
            $this->dispatch('toast', ['type' => 'error', 'message' => 'Gagal menyetel ujian secara massal']);
        } finally {
            $this->resetPage();
        }
    }

    #[On('setUjianKeBelumSelesai')]
    public function setUjianKeBelumSelesai()
    {
        try {
            $ujian = UjianPspk::find($this->selected_id);
            if (!$ujian) {
                $this->dispatch('toast', ['type' => 'error', 'message' => 'Data ujian tidak ditemukan']);

                return;
            }
            $ujian->is_finished = 'false';
            $ujian->save();
            $this->dispatch('toast', ['type' => 'success', 'message' => 'Berhasil menyetel ujian ke belum selesai']);
        } catch (\Throwable $th) {
            $this->dispatch('toast', ['type' => 'error', 'message' => 'Gagal menyetel ujian ke belum selesai']);
        } finally {
            $this->resetPage();
        }
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
            HasilPspk::find($this->selected_id)->delete();

            $this->dispatch('toast', ['type' => 'success', 'message' => 'berhasil menghapus data']);
        } catch (\Throwable $th) {
            // throw $th;
            $this->dispatch('toast', ['type' => 'error', 'message' => 'gagal menghapus data']);
        }
    }
}
