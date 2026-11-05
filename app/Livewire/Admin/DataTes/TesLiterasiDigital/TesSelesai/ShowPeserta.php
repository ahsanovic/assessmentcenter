<?php

namespace App\Livewire\Admin\DataTes\TesLiterasiDigital\TesSelesai;

use App\Models\Event;
use App\Models\LiterasiDigital\HasilLiterasiDigital;
use App\Models\LiterasiDigital\UjianLiterasiDigital;
use App\Models\Peserta;
use App\Services\LiterasiDigital\LiterasiDigitalUjianService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.admin.app', ['title' => 'Hasil Tes'])]
class ShowPeserta extends Component
{
    use WithPagination;

    public $event;

    public $id_event;

    public $selected_id;

    public $tanggal_tes;

    #[Url(as: 'q')]
    public ?string $search = '';

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->reset(['search', 'tanggal_tes']);
        $this->resetPage();
    }

    public function mount($idEvent)
    {
        $this->id_event = $idEvent;
        $this->event = Event::findOrFail($this->id_event);
    }

    public function render()
    {
        $data = Peserta::join('hasil_literasi_digital', 'hasil_literasi_digital.peserta_id', '=', 'peserta.id')
            ->join('ujian_literasi_digital', 'ujian_literasi_digital.id', '=', 'hasil_literasi_digital.ujian_id')
            ->where('hasil_literasi_digital.event_id', $this->id_event)
            ->select(
                'peserta.*',
                'hasil_literasi_digital.id as hasil_literasi_digital_id',
                'hasil_literasi_digital.skor_budaya',
                'hasil_literasi_digital.level_budaya',
                'hasil_literasi_digital.skor_cakap',
                'hasil_literasi_digital.level_cakap',
                'hasil_literasi_digital.skor_keamanan',
                'hasil_literasi_digital.level_keamanan',
                'hasil_literasi_digital.skor_etika',
                'hasil_literasi_digital.level_etika',
                'hasil_literasi_digital.skor_total',
                'hasil_literasi_digital.jpm',
                'hasil_literasi_digital.kategori',
                'hasil_literasi_digital.created_at as waktu_selesai',
                'ujian_literasi_digital.created_at as waktu_mulai',
                'ujian_literasi_digital.id as ujian_literasi_digital_id',
                'ujian_literasi_digital.is_finished',
            )
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('nama', 'like', '%'.$this->search.'%')
                        ->orWhere('nip', 'like', '%'.$this->search.'%')
                        ->orWhere('nik', 'like', '%'.$this->search.'%')
                        ->orWhere('jabatan', 'like', '%'.$this->search.'%')
                        ->orWhere('instansi', 'like', '%'.$this->search.'%');
                });
            })
            ->when($this->tanggal_tes, function ($query) {
                $query->whereDate('test_started_at', date('Y-m-d', strtotime($this->tanggal_tes)));
            })
            ->paginate(10);

        return view('livewire.admin.data-tes.tes-literasi-digital.tes-selesai.show-peserta', [
            'data' => $data,
        ]);
    }

    public function hitungUlangConfirmation($id)
    {
        $this->selected_id = $id;
        $this->dispatch('hitung-ulang-confirmation');
    }

    public function hitungUlangMassalConfirmation()
    {
        $this->dispatch('hitung-ulang-massal-confirmation');
    }

    #[On('hitungUlangMassal')]
    public function hitungUlangMassal(LiterasiDigitalUjianService $ujianService)
    {
        $daftar = UjianLiterasiDigital::where('event_id', $this->id_event)
            ->where('is_finished', 'true')
            ->get();

        if ($daftar->isEmpty()) {
            $this->dispatch('toast', ['type' => 'info', 'message' => 'Tidak ada ujian selesai pada event ini']);

            return;
        }

        $berubah = 0;
        $gagal = 0;

        foreach ($daftar as $ujian) {
            try {
                $hasil = $ujianService->hitungUlangRumus($ujian);
                if ($hasil['berubah']) {
                    $berubah++;
                    activity_log($hasil['hasil'], 'update', 'hasil-literasi-digital', $hasil['sebelum']);
                }
            } catch (\Throwable $th) {
                $gagal++;
            }
        }

        $pesan = 'Dihitung ulang '.$daftar->count().' peserta, '.$berubah.' angkanya berubah';
        if ($gagal > 0) {
            $pesan .= ', '.$gagal.' gagal';
        }

        $this->dispatch('toast', [
            'type' => $gagal > 0 ? 'error' : 'success',
            'message' => $pesan,
        ]);
        $this->resetPage();
    }

    #[On('hitungUlang')]
    public function hitungUlang(LiterasiDigitalUjianService $ujianService)
    {
        try {
            $ujian = UjianLiterasiDigital::where('event_id', $this->id_event)
                ->where('is_finished', 'true')
                ->find($this->selected_id);

            if (! $ujian) {
                $this->dispatch('toast', ['type' => 'error', 'message' => 'Data ujian selesai tidak ditemukan']);

                return;
            }

            $hasil = $ujianService->hitungUlangRumus($ujian);
            if ($hasil['berubah']) {
                activity_log($hasil['hasil'], 'update', 'hasil-literasi-digital', $hasil['sebelum']);
                $this->dispatch('toast', ['type' => 'success', 'message' => 'JPM dan level peserta dihitung ulang']);
            } else {
                $this->dispatch('toast', ['type' => 'info', 'message' => 'Nilai peserta sudah sesuai rumus terbaru']);
            }
        } catch (\Throwable $th) {
            $this->dispatch('toast', ['type' => 'error', 'message' => 'Gagal menghitung ulang JPM dan level']);
        }
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
            $updated = UjianLiterasiDigital::where('event_id', $this->id_event)
                ->where('is_finished', 'true')
                ->update(['is_finished' => 'false']);

            if ($updated === 0) {
                $this->dispatch('toast', ['type' => 'info', 'message' => 'Tidak ada ujian dengan status selesai pada event ini']);

                return;
            }

            $this->dispatch('toast', [
                'type' => 'success',
                'message' => 'Berhasil menyetel '.$updated.' ujian ke belum selesai',
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
            $ujian = UjianLiterasiDigital::find($this->selected_id);
            if (! $ujian) {
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
            HasilLiterasiDigital::find($this->selected_id)?->delete();
            $this->dispatch('toast', ['type' => 'success', 'message' => 'berhasil menghapus data']);
        } catch (\Throwable $th) {
            $this->dispatch('toast', ['type' => 'error', 'message' => 'gagal menghapus data']);
        }
    }
}
