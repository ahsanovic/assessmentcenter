<?php

namespace App\Livewire\Admin\Dokumen\Absensi;

use App\Models\AbsensiEvent;
use App\Models\Event;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.admin.app', ['title' => 'Absensi'])]
class Index extends Component
{
    use WithPagination;

    public $selected_id;

    public $showModal = false;

    #[Locked]
    public $editId;

    public $event_id;

    public $tanggal;

    #[Url(as: 'q')]
    public ?string $search = '';

    public $event_nama;

    public $judul;

    public $hari;

    public $tanggal_modal;

    public $sesi;

    public $peserta_dari;

    public $peserta_sampai;

    public $jumlah_peserta_sesi;

    public $baris_tambahan = 10;

    public $peserta_range_label;

    public $total_peserta = 0;

    public $waktu_mulai;

    public $waktu_selesai;

    public $zona_waktu;

    public $tempat;

    protected function rules(): array
    {
        return [
            'judul' => ['required', 'string', 'max:1000'],
            'tanggal_modal' => ['required', 'date_format:d-m-Y'],
            'sesi' => ['required', 'integer', 'min:1', 'max:99'],
            'jumlah_peserta_sesi' => ['required', 'integer', 'min:1'],
            'baris_tambahan' => ['required', 'integer', 'min:0', 'max:100'],
            'peserta_dari' => ['required', 'integer', 'min:1'],
            'peserta_sampai' => ['required', 'integer', 'min:1', 'gte:peserta_dari'],
            'waktu_mulai' => ['required', 'string', 'max:20'],
            'waktu_selesai' => ['nullable', 'string', 'max:20'],
            'zona_waktu' => ['required', 'in:WIB,WITA,WIT'],
            'tempat' => ['required', 'string', 'max:255'],
        ];
    }

    protected $messages = [
        'judul.required' => 'judul presensi wajib diisi',
        'sesi.required' => 'sesi wajib diisi',
        'jumlah_peserta_sesi.required' => 'jumlah peserta per sesi wajib diisi',
        'baris_tambahan.required' => 'jumlah baris tambahan wajib diisi',
        'peserta_dari.required' => 'nomor peserta awal wajib diisi',
        'peserta_sampai.required' => 'nomor peserta akhir wajib diisi',
        'peserta_sampai.gte' => 'nomor peserta akhir harus lebih besar atau sama dengan nomor awal',
        'waktu_mulai.required' => 'waktu mulai wajib diisi',
        'zona_waktu.required' => 'zona waktu wajib dipilih',
        'tempat.required' => 'tempat wajib diisi',
    ];

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedEventId(): void
    {
        $this->resetPage();
    }

    public function updatedTanggal(): void
    {
        $this->resetPage();
    }

    public function updatedTanggalModal($value): void
    {
        $this->hari = $this->detectHari($value);
    }

    public function resetFilters(): void
    {
        $this->reset(['search', 'tanggal', 'event_id']);
        $this->resetPage();
        $this->dispatch('reset-select2');
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetForm();
        $this->resetValidation();
    }

    public function updatedJumlahPesertaSesi(): void
    {
        $this->syncPesertaRangeFromJumlah();
    }

    protected function syncPesertaRangeFromJumlah(): void
    {
        if (blank($this->jumlah_peserta_sesi) || (int) $this->jumlah_peserta_sesi < 1 || blank($this->peserta_dari)) {
            return;
        }

        $jumlah = max(1, (int) $this->jumlah_peserta_sesi);
        $this->peserta_sampai = min(
            (int) $this->peserta_dari + $jumlah - 1,
            $this->total_peserta ?: PHP_INT_MAX
        );
    }

    public function edit(int $id): void
    {
        try {
            $data = AbsensiEvent::with(['event' => fn ($q) => $q->withCount('peserta')])->findOrFail($id);
            $this->editId = $data->id;
            $this->event_nama = $data->event?->nama_event;
            $this->total_peserta = $data->event?->peserta_count ?? 0;
            $this->peserta_range_label = $data->pesertaRangeLabel();
            $this->judul = $data->judul;
            $this->hari = $data->hari ?? $this->detectHari($data->tanggal);
            $this->tanggal_modal = $data->tanggal;
            $this->sesi = $data->sesi;
            $this->peserta_dari = $data->peserta_dari;
            $this->peserta_sampai = $data->peserta_sampai;
            $this->jumlah_peserta_sesi = $data->jumlah_peserta_sesi
                ?? (($data->peserta_dari && $data->peserta_sampai)
                    ? $data->peserta_sampai - $data->peserta_dari + 1
                    : null);
            $this->baris_tambahan = $data->baris_tambahan ?? 10;
            $this->waktu_mulai = $data->waktu_mulai;
            $this->waktu_selesai = $data->waktu_selesai;
            $this->zona_waktu = $data->zona_waktu;
            $this->tempat = $data->tempat;
            $this->showModal = true;
            $this->resetValidation();
            $this->dispatch('modalOpened');
            $this->dispatch('set-flatpickr', [
                'model' => 'tanggal_modal',
                'value' => $this->tanggal_modal,
            ]);
            $this->dispatch('set-flatpickr-time', [
                'model' => 'waktu_mulai',
                'value' => $this->waktu_mulai,
            ]);
            $this->dispatch('set-flatpickr-time', [
                'model' => 'waktu_selesai',
                'value' => $this->waktu_selesai,
            ]);
        } catch (\Throwable $th) {
            $this->dispatch('toast', ['type' => 'error', 'message' => 'terjadi kesalahan']);
        }
    }

    public function save(): void
    {
        $this->syncPesertaRangeFromJumlah();

        $this->validate();

        if ($this->peserta_sampai > $this->total_peserta) {
            $this->addError('peserta_sampai', 'nomor peserta akhir melebihi total peserta ('.$this->total_peserta.')');

            return;
        }

        try {
            $data = AbsensiEvent::findOrFail($this->editId);
            $old_data = $data->getOriginal();

            $data->update([
                'judul' => $this->judul,
                'hari' => $this->detectHari($this->tanggal_modal),
                'tanggal' => $this->tanggal_modal,
                'sesi' => $this->sesi,
                'peserta_dari' => $this->peserta_dari,
                'peserta_sampai' => $this->peserta_sampai,
                'jumlah_peserta_sesi' => $this->jumlah_peserta_sesi,
                'baris_tambahan' => $this->baris_tambahan,
                'waktu_mulai' => $this->waktu_mulai,
                'waktu_selesai' => $this->waktu_selesai,
                'zona_waktu' => $this->zona_waktu,
                'tempat' => $this->tempat,
            ]);

            activity_log($data, 'update', 'absensi-event', $old_data);

            $this->dispatch('toast', ['type' => 'success', 'message' => 'berhasil ubah data absensi']);
            $this->closeModal();
            $this->resetPage();
        } catch (\Throwable $th) {
            $this->dispatch('toast', ['type' => 'error', 'message' => 'terjadi kesalahan']);
        }
    }

    public function deleteConfirmation(int $id): void
    {
        $this->selected_id = $id;
        $this->dispatch('show-delete-confirmation');
    }

    #[On('delete')]
    public function destroy(): void
    {
        try {
            $data = AbsensiEvent::find($this->selected_id);
            if (! $data) {
                return;
            }

            $old_data = $data->getOriginal();
            activity_log($data, 'delete', 'absensi-event', $old_data);
            $data->delete();

            $this->dispatch('toast', ['type' => 'success', 'message' => 'berhasil menghapus data absensi']);
        } catch (\Throwable $th) {
            $this->dispatch('toast', ['type' => 'error', 'message' => 'gagal menghapus data absensi']);
        }
    }

    protected function resetForm(): void
    {
        $this->reset([
            'editId',
            'event_nama',
            'judul',
            'hari',
            'tanggal_modal',
            'sesi',
            'peserta_dari',
            'peserta_sampai',
            'jumlah_peserta_sesi',
            'baris_tambahan',
            'peserta_range_label',
            'total_peserta',
            'waktu_mulai',
            'waktu_selesai',
            'zona_waktu',
            'tempat',
        ]);
    }

    protected function detectHari($value): ?string
    {
        if (blank($value)) {
            return null;
        }

        $hariId = [
            'Sunday' => 'Minggu',
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu',
        ];

        $timestamp = strtotime((string) $value);

        if ($timestamp === false) {
            return null;
        }

        return $hariId[date('l', $timestamp)] ?? null;
    }

    public function render()
    {
        $data = AbsensiEvent::with(['event.metodeTes', 'creator'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('judul', 'like', '%'.$this->search.'%')
                        ->orWhereHas('event', fn ($event) => $event->where('nama_event', 'like', '%'.$this->search.'%'));
                });
            })
            ->when($this->tanggal, function ($query) {
                try {
                    $tanggal = \Carbon\Carbon::createFromFormat('d-m-Y', $this->tanggal)->format('Y-m-d');
                    $query->whereDate('tanggal', $tanggal);
                } catch (\Throwable) {
                    // abaikan format tanggal tidak valid
                }
            })
            ->when($this->event_id, fn ($query) => $query->where('event_id', $this->event_id))
            ->orderByDesc('id')
            ->paginate(10);

        $options_event = Event::query()
            ->with('metodeTes')
            ->whereIn('id', AbsensiEvent::query()->select('event_id'))
            ->orderBy('nama_event')
            ->get()
            ->mapWithKeys(function (Event $event) {
                $label = $event->nama_event;
                $metode = $event->metodeTes?->metode_tes;
                if ($metode) {
                    $label .= ' — '.$metode;
                }

                return [$event->id => $label];
            });

        return view('livewire.admin.dokumen.absensi.index', compact('data', 'options_event'));
    }
}
