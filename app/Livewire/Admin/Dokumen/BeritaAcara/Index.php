<?php

namespace App\Livewire\Admin\Dokumen\BeritaAcara;

use App\Models\BeritaAcara;
use App\Models\Event;
use App\Models\RefPegawai;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.admin.app', ['title' => 'Berita Acara'])]
class Index extends Component
{
    use WithPagination;

    public $selected_id;

    public $showModal = false;

    public $isUpdate = false;

    // Filter
    public $event_id;

    public $tanggal;

    #[Url(as: 'q')]
    public ?string $search = '';

    #[Locked]
    public $editId;

    // Form modal
    public $event_id_modal;

    public string $judul = 'PENYELENGGARAAN UJI KOMPETENSI';

    public $hari;

    public $tanggal_modal;

    public $waktu_mulai;

    public $waktu_selesai;

    public $pejabat;

    public $di_lingkungan_pemerintah;

    public $ruang;

    public $jumlah_peserta_seharusnya;

    public $jumlah_peserta_tidak_hadir = 0;

    public $nomor_tidak_hadir;

    public $catatan;

    public $admin_nama;

    public $admin_nip;

    public $admin_pegawai_id;

    public $tester_nama;

    public $tester_nip;

    public $tester_pegawai_id;

    protected function rules(): array
    {
        return [
            'event_id_modal' => 'required|exists:event,id',
            'judul' => 'required|string|max:255',
            'hari' => 'nullable|string|max:50',
            'tanggal_modal' => 'nullable',
            'waktu_mulai' => 'nullable|string|max:20',
            'waktu_selesai' => 'nullable|string|max:20',
            'pejabat' => 'nullable|string|max:255',
            'di_lingkungan_pemerintah' => 'nullable|string|max:255',
            'ruang' => 'nullable|string|max:255',
            'jumlah_peserta_seharusnya' => 'nullable|integer|min:0',
            'jumlah_peserta_tidak_hadir' => 'nullable|integer|min:0',
            'nomor_tidak_hadir' => 'nullable|string',
            'catatan' => 'nullable|string',
            'admin_nama' => 'nullable|string|max:255',
            'admin_nip' => 'nullable|string|max:50',
            'admin_pegawai_id' => 'nullable|exists:ref_pegawai,id',
            'tester_nama' => 'nullable|string|max:255',
            'tester_nip' => 'nullable|string|max:50',
            'tester_pegawai_id' => 'nullable|exists:ref_pegawai,id',
        ];
    }

    protected $messages = [
        'event_id_modal.required' => 'Event harus dipilih',
        'event_id_modal.exists' => 'Event tidak valid',
        'judul.required' => 'Judul berita acara harus diisi',
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

    public function updatedEventIdModal($value): void
    {
        $event = Event::find($value);

        if (! $event) {
            return;
        }

        if (blank($this->jumlah_peserta_seharusnya)) {
            $this->jumlah_peserta_seharusnya = $event->jumlah_peserta;
        }

        $this->di_lingkungan_pemerintah = $this->di_lingkungan_pemerintah ?: 'Provinsi Jawa Timur';

        if (blank($this->tanggal_modal) && $event->tgl_mulai) {
            $this->tanggal_modal = $event->tgl_mulai;
        }
    }

    public function updatedAdminPegawaiId($value): void
    {
        $this->fillPegawaiFields('admin', $value);
    }

    public function updatedTesterPegawaiId($value): void
    {
        $this->fillPegawaiFields('tester', $value);
    }

    protected function fillPegawaiFields(string $role, $pegawaiId): void
    {
        if (blank($pegawaiId)) {
            if ($role === 'admin') {
                $this->admin_nama = '';
                $this->admin_nip = '';
            } else {
                $this->tester_nama = '';
                $this->tester_nip = '';
            }

            return;
        }

        $pegawai = RefPegawai::find($pegawaiId);
        if (! $pegawai) {
            return;
        }

        if ($role === 'admin') {
            $this->admin_nama = $pegawai->nama;
            $this->admin_nip = $pegawai->nip;
        } else {
            $this->tester_nama = $pegawai->nama;
            $this->tester_nip = $pegawai->nip;
        }
    }

    public function getJumlahPesertaHadirProperty(): int
    {
        return max((int) $this->jumlah_peserta_seharusnya - (int) $this->jumlah_peserta_tidak_hadir, 0);
    }

    public function openModal(): void
    {
        $this->resetForm();
        $this->showModal = true;
        $this->isUpdate = false;
        $this->editId = null;
        $this->resetValidation();
        $this->dispatch('modalOpened');
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetForm();
        $this->isUpdate = false;
        $this->editId = null;
        $this->resetValidation();
    }

    public function edit(int $id): void
    {
        try {
            $data = BeritaAcara::findOrFail($id);
            $this->editId = $data->id;
            $this->event_id_modal = $data->event_id;
            $this->judul = $data->judul;
            $this->hari = $data->hari;
            $this->tanggal_modal = $data->tanggal;
            $this->waktu_mulai = $data->waktu_mulai;
            $this->waktu_selesai = $data->waktu_selesai;
            $this->pejabat = $data->pejabat;
            $this->di_lingkungan_pemerintah = $data->di_lingkungan_pemerintah;
            $this->ruang = $data->ruang;
            $this->jumlah_peserta_seharusnya = $data->jumlah_peserta_seharusnya;
            $this->jumlah_peserta_tidak_hadir = $data->jumlah_peserta_tidak_hadir;
            $this->nomor_tidak_hadir = $data->nomor_tidak_hadir;
            $this->catatan = $data->catatan;
            $this->admin_nama = $data->admin_nama;
            $this->admin_nip = $data->admin_nip;
            $this->admin_pegawai_id = $data->admin_pegawai_id;
            $this->tester_nama = $data->tester_nama;
            $this->tester_nip = $data->tester_nip;
            $this->tester_pegawai_id = $data->tester_pegawai_id;
            $this->isUpdate = true;
            $this->showModal = true;
            $this->resetValidation();
            $this->dispatch('modalOpened');
            $this->dispatch('set-flatpickr', [
                'model' => 'tanggal_modal',
                'value' => $this->tanggal_modal,
            ]);
        } catch (\Throwable $th) {
            $this->dispatch('toast', ['type' => 'error', 'message' => 'terjadi kesalahan']);
        }
    }

    public function save(): void
    {
        $this->validate();

        try {
            $payload = [
                'event_id' => $this->event_id_modal,
                'judul' => $this->judul,
                'hari' => $this->hari,
                'tanggal' => $this->tanggal_modal,
                'waktu_mulai' => $this->waktu_mulai,
                'waktu_selesai' => $this->waktu_selesai,
                'pejabat' => $this->pejabat,
                'di_lingkungan_pemerintah' => $this->di_lingkungan_pemerintah,
                'ruang' => $this->ruang,
                'jumlah_peserta_seharusnya' => $this->jumlah_peserta_seharusnya,
                'jumlah_peserta_tidak_hadir' => $this->jumlah_peserta_tidak_hadir,
                'jumlah_peserta_hadir' => $this->jumlah_peserta_hadir,
                'nomor_tidak_hadir' => $this->nomor_tidak_hadir,
                'catatan' => $this->catatan,
                'admin_nama' => $this->admin_nama,
                'admin_nip' => $this->admin_nip,
                'admin_pegawai_id' => $this->admin_pegawai_id ?: null,
                'tester_nama' => $this->tester_nama,
                'tester_nip' => $this->tester_nip,
                'tester_pegawai_id' => $this->tester_pegawai_id ?: null,
            ];

            if ($this->isUpdate) {
                $data = BeritaAcara::findOrFail($this->editId);
                $old_data = $data->getOriginal();
                $data->update($payload);
                activity_log($data, 'update', 'berita-acara', $old_data);
                $this->dispatch('toast', ['type' => 'success', 'message' => 'berhasil ubah data']);
            } else {
                $payload['created_by'] = auth()->id();
                $data = BeritaAcara::create($payload);
                activity_log($data, 'create', 'berita-acara');
                $this->dispatch('toast', ['type' => 'success', 'message' => 'berhasil tambah data']);
            }

            $this->closeModal();
            $this->resetPage();
        } catch (\Throwable $th) {
            $this->dispatch('toast', ['type' => 'error', 'message' => 'terjadi kesalahan']);
        }
    }

    public function resetFilters(): void
    {
        $this->reset(['search', 'tanggal', 'event_id']);
        $this->resetPage();
        $this->dispatch('reset-select2');
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
            $data = BeritaAcara::find($this->selected_id);
            if (! $data) {
                return;
            }

            $old_data = $data->getOriginal();
            activity_log($data, 'delete', 'berita-acara', $old_data);
            $data->delete();

            $this->dispatch('toast', ['type' => 'success', 'message' => 'berhasil menghapus data']);
        } catch (\Throwable $th) {
            $this->dispatch('toast', ['type' => 'error', 'message' => 'gagal menghapus data']);
        }
    }

    protected function resetForm(): void
    {
        $this->event_id_modal = '';
        $this->judul = 'PENYELENGGARAAN UJI KOMPETENSI';
        $this->hari = '';
        $this->tanggal_modal = '';
        $this->waktu_mulai = '';
        $this->waktu_selesai = '';
        $this->pejabat = '';
        $this->di_lingkungan_pemerintah = '';
        $this->ruang = '';
        $this->jumlah_peserta_seharusnya = null;
        $this->jumlah_peserta_tidak_hadir = 0;
        $this->nomor_tidak_hadir = '';
        $this->catatan = '';
        $this->admin_nama = '';
        $this->admin_nip = '';
        $this->admin_pegawai_id = '';
        $this->tester_nama = '';
        $this->tester_nip = '';
        $this->tester_pegawai_id = '';
    }

    public function render()
    {
        $data = BeritaAcara::with(['event.metodeTes', 'creator'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('judul', 'like', '%'.$this->search.'%')
                        ->orWhereHas('event', fn ($event) => $event->where('nama_event', 'like', '%'.$this->search.'%'));
                });
            })
            ->when($this->tanggal, function ($query) {
                $tanggal = date('Y-m-d', strtotime($this->tanggal));
                $query->whereDate('tanggal', $tanggal);
            })
            ->when($this->event_id, fn ($query) => $query->where('event_id', $this->event_id))
            ->orderByDesc('id')
            ->paginate(10);

        $options_event = Event::query()
            ->with('metodeTes')
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

        $options_pegawai = RefPegawai::query()
            ->orderBy('nama')
            ->get()
            ->mapWithKeys(fn (RefPegawai $pegawai) => [$pegawai->id => $pegawai->label()]);

        return view('livewire.admin.dokumen.berita-acara.index', compact('data', 'options_event', 'options_pegawai'));
    }
}
