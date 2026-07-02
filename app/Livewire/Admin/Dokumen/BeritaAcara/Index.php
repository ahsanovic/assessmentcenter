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

    public string $mekanisme_penkom = 'tusi';

    public $jenis_penkom;

    public $nama_kegiatan;

    public string $judul = 'PENYELENGGARAAN UJI KOMPETENSI';

    public $nomor_surat;

    public $hari;

    public $tanggal_modal;

    public $waktu_mulai;

    public $waktu_selesai;

    public $zona_waktu;

    public $pejabat;

    public $pejabat_dinilai;

    public $di_lingkungan_pemerintah;

    public $ruang;

    public $tempat;

    public $jumlah_peserta_seharusnya;

    public $jumlah_peserta_tidak_hadir = 0;

    public $nomor_tidak_hadir;

    public $alasan_tidak_hadir;

    public $catatan;

    public $tanggal_penyerahan_rekap;

    public $tanggal_penyerahan_laporan;

    public $admin_nama;

    public $admin_nip;

    public $admin_pegawai_id;

    public $panitia1_instansi;

    public $tester_nama;

    public $tester_nip;

    public $tester_pegawai_id;

    public $panitia2_instansi;

    protected function rules(): array
    {
        return [
            'event_id_modal' => 'required|exists:event,id',
            'mekanisme_penkom' => 'required|in:tusi,retribusi',
            'jenis_penkom' => 'nullable|in:Uji Kompetensi,Uji Potensi,Uji Kompetensi dan Potensi',
            'nama_kegiatan' => 'nullable|string|max:255',
            'judul' => 'required|string|max:255',
            'nomor_surat' => 'nullable|string|max:255',
            'hari' => 'nullable|string|max:50',
            'tanggal_modal' => 'nullable',
            'waktu_mulai' => 'nullable|string|max:20',
            'waktu_selesai' => 'nullable|string|max:20',
            'zona_waktu' => 'nullable|in:WIB,WITA,WIT',
            'pejabat' => 'nullable|string|max:255',
            'pejabat_dinilai' => 'nullable|integer|min:0',
            'di_lingkungan_pemerintah' => 'nullable|string|max:255',
            'ruang' => 'nullable|string|max:255',
            'tempat' => 'nullable|string|max:255',
            'jumlah_peserta_seharusnya' => 'nullable|integer|min:0',
            'jumlah_peserta_tidak_hadir' => 'nullable|integer|min:0',
            'nomor_tidak_hadir' => 'nullable|string',
            'alasan_tidak_hadir' => 'nullable|string',
            'catatan' => 'nullable|string',
            'tanggal_penyerahan_rekap' => 'nullable',
            'tanggal_penyerahan_laporan' => 'nullable',
            'admin_nama' => 'nullable|string|max:255',
            'admin_nip' => 'nullable|string|max:50',
            'admin_pegawai_id' => 'nullable|exists:ref_pegawai,id',
            'panitia1_instansi' => 'nullable|string|max:255',
            'tester_nama' => 'nullable|string|max:255',
            'tester_nip' => 'nullable|string|max:50',
            'tester_pegawai_id' => 'nullable|exists:ref_pegawai,id',
            'panitia2_instansi' => 'nullable|string|max:255',
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
            $this->hari = $this->detectHari($this->tanggal_modal);
        }
    }

    public function updatedTanggalModal($value): void
    {
        $this->hari = $this->detectHari($value);
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
        $this->dispatchMarkdownEditors();
    }

    public function updatedMekanismePenkom(): void
    {
        $this->dispatch('modalOpened');
        $this->dispatchMarkdownEditors();
    }

    protected function dispatchMarkdownEditors(): void
    {
        foreach (['catatan', 'nomor_tidak_hadir', 'alasan_tidak_hadir'] as $field) {
            $this->dispatch('set-markdown', [
                'model' => $field,
                'value' => (string) ($this->{$field} ?? ''),
            ]);
        }
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
            $this->mekanisme_penkom = $data->mekanisme_penkom ?: 'tusi';
            $this->jenis_penkom = $data->jenis_penkom;
            $this->nama_kegiatan = $data->nama_kegiatan;
            $this->judul = $data->judul;
            $this->nomor_surat = $data->nomor_surat;
            $this->hari = $this->detectHari($data->tanggal);
            $this->tanggal_modal = $data->tanggal;
            $this->waktu_mulai = $data->waktu_mulai;
            $this->waktu_selesai = $data->waktu_selesai;
            $this->zona_waktu = $data->zona_waktu;
            $this->pejabat = $data->pejabat;
            $this->pejabat_dinilai = $data->pejabat_dinilai;
            $this->di_lingkungan_pemerintah = $data->di_lingkungan_pemerintah;
            $this->ruang = $data->ruang;
            $this->tempat = $data->tempat;
            $this->jumlah_peserta_seharusnya = $data->jumlah_peserta_seharusnya;
            $this->jumlah_peserta_tidak_hadir = $data->jumlah_peserta_tidak_hadir;
            $this->nomor_tidak_hadir = $data->nomor_tidak_hadir;
            $this->alasan_tidak_hadir = $data->alasan_tidak_hadir;
            $this->catatan = $data->catatan;
            $this->tanggal_penyerahan_rekap = $data->tanggal_penyerahan_rekap;
            $this->tanggal_penyerahan_laporan = $data->tanggal_penyerahan_laporan;
            $this->admin_nama = $data->admin_nama;
            $this->admin_nip = $data->admin_nip;
            $this->admin_pegawai_id = $data->admin_pegawai_id;
            $this->panitia1_instansi = $data->panitia1_instansi;
            $this->tester_nama = $data->tester_nama;
            $this->tester_nip = $data->tester_nip;
            $this->tester_pegawai_id = $data->tester_pegawai_id;
            $this->panitia2_instansi = $data->panitia2_instansi;
            $this->isUpdate = true;
            $this->showModal = true;
            $this->resetValidation();
            $this->dispatch('modalOpened');
            $this->dispatch('set-flatpickr', [
                'model' => 'tanggal_modal',
                'value' => $this->tanggal_modal,
            ]);
            $this->dispatch('set-flatpickr', [
                'model' => 'tanggal_penyerahan_rekap',
                'value' => $this->tanggal_penyerahan_rekap,
            ]);
            $this->dispatch('set-flatpickr', [
                'model' => 'tanggal_penyerahan_laporan',
                'value' => $this->tanggal_penyerahan_laporan,
            ]);
            $this->dispatch('set-flatpickr-time', [
                'model' => 'waktu_mulai',
                'value' => $this->waktu_mulai,
            ]);
            $this->dispatch('set-flatpickr-time', [
                'model' => 'waktu_selesai',
                'value' => $this->waktu_selesai,
            ]);
            $this->dispatchMarkdownEditors();
        } catch (\Throwable $th) {
            $this->dispatch('toast', ['type' => 'error', 'message' => 'terjadi kesalahan']);
        }
    }

    public function save(): void
    {
        $this->validate();

        try {
            $isRetribusi = $this->mekanisme_penkom === 'retribusi';

            $payload = [
                'event_id' => $this->event_id_modal,
                'mekanisme_penkom' => $this->mekanisme_penkom,
                'jenis_penkom' => $isRetribusi ? null : $this->jenis_penkom,
                'nama_kegiatan' => $isRetribusi ? $this->nama_kegiatan : null,
                'judul' => $this->judul,
                'nomor_surat' => $this->nomor_surat,
                'hari' => $this->detectHari($this->tanggal_modal),
                'tanggal' => $this->tanggal_modal,
                'waktu_mulai' => $this->waktu_mulai,
                'waktu_selesai' => $this->waktu_selesai,
                'zona_waktu' => $this->zona_waktu,
                'pejabat' => $this->pejabat,
                'pejabat_dinilai' => $this->pejabat_dinilai,
                'di_lingkungan_pemerintah' => $this->di_lingkungan_pemerintah,
                'ruang' => $this->ruang,
                'tempat' => $this->tempat,
                'jumlah_peserta_seharusnya' => $this->jumlah_peserta_seharusnya,
                'jumlah_peserta_tidak_hadir' => $this->jumlah_peserta_tidak_hadir,
                'jumlah_peserta_hadir' => $this->jumlah_peserta_hadir,
                'nomor_tidak_hadir' => $this->nomor_tidak_hadir,
                'alasan_tidak_hadir' => $isRetribusi ? $this->alasan_tidak_hadir : null,
                'catatan' => $this->catatan,
                'tanggal_penyerahan_rekap' => $this->tanggal_penyerahan_rekap,
                'tanggal_penyerahan_laporan' => $this->tanggal_penyerahan_laporan,
                'admin_nama' => $this->admin_nama,
                'admin_nip' => $this->admin_nip,
                'admin_pegawai_id' => $isRetribusi ? null : ($this->admin_pegawai_id ?: null),
                'panitia1_instansi' => $this->panitia1_instansi,
                'tester_nama' => $this->tester_nama,
                'tester_nip' => $this->tester_nip,
                'tester_pegawai_id' => $isRetribusi ? null : ($this->tester_pegawai_id ?: null),
                'panitia2_instansi' => $this->panitia2_instansi,
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
        $this->mekanisme_penkom = 'tusi';
        $this->jenis_penkom = '';
        $this->nama_kegiatan = '';
        $this->judul = 'PENYELENGGARAAN UJI KOMPETENSI';
        $this->nomor_surat = '';
        $this->hari = '';
        $this->tanggal_modal = '';
        $this->waktu_mulai = '';
        $this->waktu_selesai = '';
        $this->zona_waktu = '';
        $this->pejabat = '';
        $this->pejabat_dinilai = null;
        $this->di_lingkungan_pemerintah = '';
        $this->ruang = '';
        $this->tempat = '';
        $this->jumlah_peserta_seharusnya = null;
        $this->jumlah_peserta_tidak_hadir = 0;
        $this->nomor_tidak_hadir = '';
        $this->alasan_tidak_hadir = '';
        $this->catatan = '';
        $this->tanggal_penyerahan_rekap = '';
        $this->tanggal_penyerahan_laporan = '';
        $this->admin_nama = '';
        $this->admin_nip = '';
        $this->admin_pegawai_id = '';
        $this->panitia1_instansi = '';
        $this->tester_nama = '';
        $this->tester_nip = '';
        $this->tester_pegawai_id = '';
        $this->panitia2_instansi = '';
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
