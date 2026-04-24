<?php

namespace App\Livewire\Admin\DataTes\TesPspk\TesBerlangsung;

use App\Models\Event;
use App\Models\Peserta;
use App\Models\Pspk\UjianPspk;
use App\Models\SettingWaktuTes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.admin.app', ['title' => 'Tes Berlangsung'])]
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
        $this->event = Event::with(['pesertaTesPspk'])->findOrFail($this->id_event);
    }

    public function render()
    {
        $data = Peserta::join('ujian_pspk', 'ujian_pspk.peserta_id', '=', 'peserta.id')
            ->whereIn('peserta.id', $this->event->pesertaIdTesPspk->pluck('peserta_id'))
            ->where('ujian_pspk.event_id', $this->id_event)
            ->select('peserta.*', 'ujian_pspk.is_finished', 'ujian_pspk.id as ujian_pspk_id', 'ujian_pspk.created_at as mulai_tes')
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

        return view('livewire.admin.data-tes.tes-pspk.tes-berlangsung.show-peserta', [
            'data' => $data,
            'maxTambahanMenit' => $this->maxTambahanMenit(),
        ]);
    }

    protected function maxTambahanMenit(): int
    {
        $metode = (int) ($this->event->metode_tes_id ?? 0);
        $jenisTes = match ($metode) {
            5 => 7,
            6 => 8,
            default => 7,
        };
        $waktu = SettingWaktuTes::where('is_active', 'true')
            ->where('jenis_tes', $jenisTes)
            ->value('waktu');

        return max(1, (int) ($waktu ?? 90));
    }

    public function openModal($id)
    {
        $this->showModalMassal = false;
        $this->waktuMassal = null;
        $this->selected_id = $id;
        $this->showModal = true;
        $this->waktu = null;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->waktu = null;
    }

    public function openModalMassal()
    {
        $this->showModal = false;
        $this->waktu = null;
        $this->showModalMassal = true;
        $this->waktuMassal = null;
    }

    public function closeModalMassal()
    {
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
        $max = $this->maxTambahanMenit();
        $this->validate([
            'waktu' => ['required', 'numeric', 'min:1', 'max:' . $max],
        ], [
            'waktu.required' => 'Waktu tes harus diisi',
            'waktu.numeric' => 'Waktu tes harus berupa angka',
            'waktu.min' => 'Tambahan waktu minimal 1 menit',
            'waktu.max' => 'Tambahan waktu maksimal ' . $max . ' menit',
        ]);

        try {
            $ujian = UjianPspk::find($this->selected_id);
            if (!$ujian) {
                $this->dispatch('toast', ['type' => 'error', 'message' => 'Data tidak ditemukan']);
                $this->closeModal();

                return;
            }

            $this->applyTambahMenitKeUjian($ujian, (int) $this->waktu);
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
        $max = $this->maxTambahanMenit();
        $this->validate([
            'waktuMassal' => ['required', 'numeric', 'min:1', 'max:' . $max],
        ], [
            'waktuMassal.required' => 'Waktu tes harus diisi',
            'waktuMassal.numeric' => 'Waktu tes harus berupa angka',
            'waktuMassal.min' => 'Tambahan waktu minimal 1 menit',
            'waktuMassal.max' => 'Tambahan waktu maksimal ' . $max . ' menit',
        ]);

        $menit = (int) $this->waktuMassal;

        try {
            $ujians = UjianPspk::where('event_id', $this->id_event)
                ->where('is_finished', 'false')
                ->get();

            if ($ujians->isEmpty()) {
                $this->dispatch('toast', ['type' => 'error', 'message' => 'Tidak ada ujian berlangsung untuk ditambah waktunya']);
                $this->closeModalMassal();

                return;
            }

            DB::transaction(function () use ($ujians, $menit) {
                foreach ($ujians as $ujian) {
                    $this->applyTambahMenitKeUjian($ujian, $menit);
                    $ujian->save();
                }
            });

            $this->dispatch('toast', [
                'type' => 'success',
                'message' => 'Berhasil menambah waktu untuk ' . $ujians->count() . ' peserta',
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
            UjianPspk::find($this->selected_id)->delete();

            $this->dispatch('toast', ['type' => 'success', 'message' => 'berhasil menghapus data']);
        } catch (\Throwable $th) {
            // throw $th;
            $this->dispatch('toast', ['type' => 'error', 'message' => 'gagal menghapus data']);
        }
    }
}
