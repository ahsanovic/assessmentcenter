<?php

namespace App\Livewire\Admin\DataTes\TesLiterasiDigital\TesBerlangsung;

use App\Models\Event;
use App\Models\LiterasiDigital\UjianLiterasiDigital;
use App\Models\Peserta;
use App\Services\LiterasiDigital\LiterasiDigitalUjianService;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.admin.app', ['title' => 'Monitoring Tes'])]
class ShowPeserta extends Component
{
    use WithPagination;

    public $event;

    public $id_event;

    public $selected_id;

    public $showModal = false;

    public $waktu;

    public $showModalMassal = false;

    public $waktuMassal;

    #[Url(as: 'q')]
    public ?string $search = '';

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->reset(['search']);
        $this->resetPage();
    }

    public function mount($idEvent)
    {
        $this->id_event = $idEvent;
        $this->event = Event::with('eventGroup')->findOrFail($this->id_event);
    }

    public function render()
    {
        $ujianTerbaru = UjianLiterasiDigital::query()
            ->selectRaw('MAX(id) as id')
            ->where('event_id', $this->id_event)
            ->groupBy('peserta_id');

        $data = Peserta::query()
            ->join('ujian_literasi_digital', 'ujian_literasi_digital.peserta_id', '=', 'peserta.id')
            ->whereIn('ujian_literasi_digital.id', $ujianTerbaru)
            ->where('ujian_literasi_digital.event_id', $this->id_event)
            ->where('peserta.event_id', $this->id_event)
            ->select(
                'peserta.id',
                'peserta.jenis_peserta_id',
                'peserta.nama',
                'peserta.gelar_depan',
                'peserta.gelar_belakang',
                'peserta.nip',
                'peserta.nik',
                'peserta.gol_pangkat_id',
                'peserta.jabatan',
                'peserta.unit_kerja',
                'peserta.instansi',
                'ujian_literasi_digital.is_finished',
                'ujian_literasi_digital.id as ujian_literasi_digital_id',
                'ujian_literasi_digital.created_at as mulai_tes',
            )
            ->when($this->search, function ($query) {
                $term = '%'.$this->search.'%';
                $query->where(function ($q) use ($term) {
                    $q->where('peserta.nama', 'like', $term)
                        ->orWhere('peserta.nip', 'like', $term)
                        ->orWhere('peserta.nik', 'like', $term)
                        ->orWhere('peserta.jabatan', 'like', $term)
                        ->orWhere('peserta.instansi', 'like', $term);
                });
            })
            ->orderBy('peserta.id')
            ->paginate(10);

        return view('livewire.admin.data-tes.tes-literasi-digital.tes-berlangsung.show-peserta', [
            'data' => $data,
            'maxTambahanMenit' => $this->durasiUjianMenit(),
        ]);
    }

    protected function durasiUjianMenit(): int
    {
        return app(LiterasiDigitalUjianService::class)->durasiMenit();
    }

    protected function menitSisaUjianBerjalan(UjianLiterasiDigital $ujian): float
    {
        $akhir = $ujian->waktu_tes_berakhir;
        if (! $akhir instanceof CarbonInterface || $akhir->lte(now())) {
            return 0.0;
        }

        return max(0, $akhir->getTimestamp() - now()->getTimestamp()) / 60;
    }

    protected function bolehTambahanMenit(UjianLiterasiDigital $ujian, int $tambahan): bool
    {
        return $tambahan + (int) ceil($this->menitSisaUjianBerjalan($ujian)) <= $this->durasiUjianMenit();
    }

    public function openModal($id)
    {
        $this->dispatch('hide-tooltips');
        $this->showModalMassal = false;
        $this->waktuMassal = null;
        $this->selected_id = $id;
        $this->showModal = true;
        $this->waktu = null;
    }

    public function closeModal()
    {
        $this->dispatch('hide-tooltips');
        $this->showModal = false;
        $this->waktu = null;
    }

    public function openModalMassal()
    {
        $this->dispatch('hide-tooltips');
        $this->showModal = false;
        $this->waktu = null;
        $this->showModalMassal = true;
        $this->waktuMassal = null;
    }

    public function closeModalMassal()
    {
        $this->dispatch('hide-tooltips');
        $this->showModalMassal = false;
        $this->waktuMassal = null;
    }

    private function applyTambahMenitKeUjian(Model $ujian, int $menit): void
    {
        $base = $ujian->waktu_tes_berakhir
            ? $ujian->waktu_tes_berakhir->max(now())
            : now();
        $ujian->waktu_tes_berakhir = $base->copy()->addMinutes($menit);
    }

    public function tambahWaktu()
    {
        $durasiSetting = $this->durasiUjianMenit();
        $this->validate([
            'waktu' => ['required', 'numeric', 'min:1'],
        ], [
            'waktu.required' => 'Waktu tes harus diisi',
            'waktu.numeric' => 'Waktu tes harus berupa angka',
            'waktu.min' => 'Tambahan waktu minimal 1 menit',
        ]);

        try {
            $ujian = UjianLiterasiDigital::find($this->selected_id);
            if (! $ujian) {
                $this->dispatch('toast', ['type' => 'error', 'message' => 'Data tidak ditemukan']);
                $this->closeModal();

                return;
            }

            $menitInput = (int) $this->waktu;

            if (! $this->bolehTambahanMenit($ujian, $menitInput)) {
                $sisa = (int) ceil($this->menitSisaUjianBerjalan($ujian));
                $this->dispatch('toast', [
                    'type' => 'warning',
                    'message' => "Tambahan waktu tidak boleh melewati pengaturan ({$durasiSetting} menit). Sisa waktu tes berjalan: {$sisa} menit.",
                ]);

                return;
            }

            $this->applyTambahMenitKeUjian($ujian, $menitInput);
            $ujian->save();
            $this->dispatch('toast', ['type' => 'success', 'message' => 'Berhasil menambah waktu']);
            $this->closeModal();
        } catch (\Throwable $th) {
            $this->dispatch('toast', ['type' => 'error', 'message' => 'Gagal menambah waktu']);
        } finally {
            $this->resetPage();
        }
    }

    public function tambahWaktuMassal()
    {
        $durasiSetting = $this->durasiUjianMenit();
        $this->validate([
            'waktuMassal' => ['required', 'numeric', 'min:1'],
        ], [
            'waktuMassal.required' => 'Waktu tes harus diisi',
            'waktuMassal.numeric' => 'Waktu tes harus berupa angka',
            'waktuMassal.min' => 'Tambahan waktu minimal 1 menit',
        ]);

        $menit = (int) $this->waktuMassal;

        try {
            $ujians = UjianLiterasiDigital::with('peserta')
                ->where('event_id', $this->id_event)
                ->where('is_finished', 'false')
                ->get();

            if ($ujians->isEmpty()) {
                $this->dispatch('toast', ['type' => 'error', 'message' => 'Tidak ada ujian berlangsung untuk ditambah waktunya']);
                $this->closeModalMassal();

                return;
            }

            foreach ($ujians as $ujian) {
                if (! $this->bolehTambahanMenit($ujian, $menit)) {
                    $sisa = (int) ceil($this->menitSisaUjianBerjalan($ujian));
                    $namaPeserta = $ujian->peserta->nama ?? 'Peserta';
                    $this->dispatch('toast', [
                        'type' => 'warning',
                        'message' => "Tambahan {$menit} menit melewati pengaturan ({$durasiSetting} menit) bagi {$namaPeserta}: sisa hitungan sekarang {$sisa} menit.",
                    ]);

                    return;
                }
            }

            DB::transaction(function () use ($ujians, $menit) {
                foreach ($ujians as $ujian) {
                    $this->applyTambahMenitKeUjian($ujian, $menit);
                    $ujian->save();
                }
            });

            $this->dispatch('toast', [
                'type' => 'success',
                'message' => 'Berhasil menambah waktu untuk '.$ujians->count().' peserta',
            ]);
            $this->closeModalMassal();
        } catch (\Throwable $th) {
            $this->dispatch('toast', ['type' => 'error', 'message' => 'Gagal menambah waktu massal']);
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
            UjianLiterasiDigital::find($this->selected_id)?->delete();
            $this->dispatch('toast', ['type' => 'success', 'message' => 'berhasil menghapus data']);
        } catch (\Throwable $th) {
            $this->dispatch('toast', ['type' => 'error', 'message' => 'gagal menghapus data']);
        }
    }
}
