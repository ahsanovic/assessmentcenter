<?php

namespace App\Livewire\Admin\DataTes\TesIntelektual\TesBerlangsung;

use App\Models\Event;
use App\Models\Intelektual\UjianIntelektualSubTes3;
use App\Models\Peserta;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.admin.app', ['title' => 'Tes Berlangsung'])]
class ShowPesertaSubTes3 extends Component
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
        $this->event = Event::with(['pesertaTesIntelektualSubTes3'])->findOrFail($this->id_event);
    }

    public function render()
    {
        $data = Peserta::join('ujian_intelektual_subtes_3', 'ujian_intelektual_subtes_3.peserta_id', '=', 'peserta.id')
            ->whereIn('peserta.id', $this->event->pesertaIdTesIntelektualSubTes3->pluck('peserta_id'))
            ->where('ujian_intelektual_subtes_3.event_id', $this->id_event)
            ->select('peserta.*', 'soal_id', 'jawaban', 'ujian_intelektual_subtes_3.is_finished', 'ujian_intelektual_subtes_3.id as ujian_intelektual_subtes_3_id', 'ujian_intelektual_subtes_3.created_at as mulai_tes')
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

        return view('livewire.admin.data-tes.tes-intelektual.tes-berlangsung.show-peserta-subtes-3', [
            'data' => $data
        ]);
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
        $this->validate([
            'waktu' => 'required|numeric|min:1|max:10',
        ], [
            'waktu.required' => 'Waktu tes harus diisi',
            'waktu.numeric' => 'Waktu tes harus berupa angka',
            'waktu.min' => 'Tambahan waktu tes intelektual subtes 3 minimal 1 menit',
            'waktu.max' => 'Tambahan waktu tes intelektual subtes 3 maksimal 10 menit',
        ]);

        try {
            $ujian = UjianIntelektualSubTes3::find($this->selected_id);
            if (!$ujian) {
                $this->dispatch('toast', ['type' => 'error', 'message' => 'data tidak ditemukan']);
                $this->closeModal();
                return;
            }

            $this->applyTambahMenitKeUjian($ujian, (int) $this->waktu);
            $ujian->save();

            $this->dispatch('toast', ['type' => 'success', 'message' => 'berhasil menambah waktu']);
            $this->closeModal();
        } catch (\Throwable $th) {
            $this->dispatch('toast', ['type' => 'error', 'message' => 'gagal menambah waktu']);
        } finally {
            $this->resetPage();
        }
    }

    public function tambahWaktuMassal()
    {
        $this->validate([
            'waktuMassal' => 'required|numeric|min:1|max:10',
        ], [
            'waktuMassal.required' => 'Waktu tes harus diisi',
            'waktuMassal.numeric' => 'Waktu tes harus berupa angka',
            'waktuMassal.min' => 'Tambahan waktu tes intelektual subtes 3 minimal 1 menit',
            'waktuMassal.max' => 'Tambahan waktu tes intelektual subtes 3 maksimal 10 menit',
        ]);

        $menit = (int) $this->waktuMassal;

        try {
            $ujians = UjianIntelektualSubTes3::where('event_id', $this->id_event)
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
            $this->dispatch('toast', ['type' => 'error', 'message' => 'gagal menambah waktu massal']);
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
            UjianIntelektualSubTes3::find($this->selected_id)->delete();

            $this->dispatch('toast', ['type' => 'success', 'message' => 'berhasil menghapus data']);
        } catch (\Throwable $th) {
            // throw $th;
            $this->dispatch('toast', ['type' => 'error', 'message' => 'gagal menghapus data']);
        }
    }
}
