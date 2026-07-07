<?php

namespace App\Livewire\Admin\Event;

use App\Models\AbsensiEvent;
use App\Models\Assessor;
use App\Models\Event;
use App\Models\RefJabatanDiuji;
use App\Models\RefMetodeTes;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.admin.app', ['title' => 'Event'])]
class Index extends Component
{
    use WithPagination;

    public $jabatan_diuji;
    public $tgl_mulai;
    public $selected_id;

    // Modal state
    public $showModal = false;
    public $showAttendanceModal = false;
    public $isUpdate = false;
    public $editId;

    // Form fields
    public $nama_event;
    public $metode_tes_id;
    public $jabatan_diuji_id;
    public $form_tgl_mulai;
    public $form_tgl_selesai;
    public $jumlah_peserta;
    public array $assessor = [];
    public $pin_ujian;
    public $is_open;
    public $is_finished;
    public $attendance_event_id;

    public $attendance_event_nama;

    public $attendance_judul;

    public $attendance_hari;

    public $attendance_tanggal;

    public $attendance_sesi;

    public $attendance_peserta_dari;

    public $attendance_peserta_sampai;

    public $attendance_jumlah_peserta_sesi;

    public $attendance_baris_tambahan = 10;

    public $attendance_total_peserta = 0;

    public $attendance_existing_sesi = [];

    public $attendance_waktu_mulai;

    public $attendance_waktu_selesai;

    public $attendance_zona_waktu;

    public $attendance_tempat;

    #[Url(as: 'q')]
    public ?string $search = '';

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedJabatanDiuji()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->reset(['search', 'jabatan_diuji', 'tgl_mulai']);
        $this->resetPage();
    }

    public function updatedMetodeTesId()
    {
        // Reset field assessor dan is_open saat metode tes berubah
        if ($this->metode_tes_id != 1) {
            $this->assessor = [];
            $this->is_open = null;
        }
        $this->resetValidation(['assessor', 'is_open']);
    }

    protected function rules()
    {
        $rules = [
            'nama_event' => ['required'],
            'metode_tes_id' => ['required'],
            'jabatan_diuji_id' => ['required'],
            'form_tgl_mulai' => ['required', 'date_format:d-m-Y'],
            'form_tgl_selesai' => ['required', 'date_format:d-m-Y', 'after_or_equal:form_tgl_mulai'],
            'jumlah_peserta' => ['required', 'numeric'],
            'pin_ujian' => ['required', 'min:4', 'max:4', 'regex:/^[A-Za-z0-9]+$/'],
        ];

        // Assessor dan Portofolio hanya wajib jika metode tes = Assessment Center (id: 1)
        if ($this->metode_tes_id == 1) {
            $rules['assessor'] = 'array';
            $rules['assessor.*'] = 'exists:assessor,id';
            $rules['is_open'] = ['required'];
        }

        return $rules;
    }

    protected function messages()
    {
        return [
            'nama_event.required' => 'harus diisi',
            'metode_tes_id.required' => 'harus diisi',
            'jabatan_diuji_id.required' => 'harus diisi',
            'form_tgl_mulai.required' => 'harus diisi',
            'form_tgl_mulai.date_format' => 'format tanggal mulai tidak valid',
            'form_tgl_selesai.required' => 'harus diisi',
            'form_tgl_selesai.date_format' => 'format tanggal selesai tidak valid',
            'form_tgl_selesai.after_or_equal' => 'tanggal selesai tidak boleh sebelum tanggal mulai',
            'jumlah_peserta.required' => 'harus diisi',
            'jumlah_peserta.numeric' => 'harus berupa angka',
            'pin_ujian.required' => 'harus diisi',
            'pin_ujian.min' => 'minimal 4 digit',
            'pin_ujian.max' => 'maksimal 4 digit',
            'pin_ujian.regex' => 'pin hanya boleh terdiri dari huruf kecil, huruf besar, dan angka',
            'is_open.required' => 'harus dipilih',
        ];
    }

    #[Computed]
    public function stats()
    {
        return Event::selectRaw("
            COUNT(*) as total,
            SUM(CASE WHEN is_finished = 'false' THEN 1 ELSE 0 END) as berlangsung,
            SUM(CASE WHEN is_finished = 'true' THEN 1 ELSE 0 END) as selesai
        ")->first();
    }

    #[Computed]
    public function events()
    {
        return Event::query()
            ->withCount(['assessor', 'peserta'])
            ->when($this->search, fn($q) =>
                $q->where('nama_event', 'like', '%' . $this->search . '%')
            )
            ->when($this->jabatan_diuji, fn($q) =>
                $q->where('jabatan_diuji_id', $this->jabatan_diuji)
            )
            ->when($this->tgl_mulai, function ($query) {
                $tgl_mulai = date('Y-m-d', strtotime($this->tgl_mulai));
                $query->where('tgl_mulai', $tgl_mulai);
            })
            ->with(['peserta', 'alatTes', 'metodeTes'])
            ->orderByDesc('id')
            ->paginate(10);
    }

    public function render()
    {
        return view('livewire.admin.event.index', [
            'data' => $this->events,
            'stats' => $this->stats,
            'option_jabatan_diuji' => RefJabatanDiuji::pluck('jenis', 'id'),
            'option_metode_tes' => RefMetodeTes::pluck('metode_tes', 'id'),
            'option_assessor' => Assessor::pluck('nama', 'id'),
        ]);
    }

    public function openModal()
    {
        $this->resetValidation();
        $this->reset(['nama_event', 'metode_tes_id', 'jabatan_diuji_id', 'form_tgl_mulai', 'form_tgl_selesai', 'jumlah_peserta', 'assessor', 'pin_ujian', 'is_open', 'is_finished', 'editId', 'isUpdate']);
        $this->showModal = true;
        $this->dispatch('modalOpened');
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetValidation();
        $this->reset(['nama_event', 'metode_tes_id', 'jabatan_diuji_id', 'form_tgl_mulai', 'form_tgl_selesai', 'jumlah_peserta', 'assessor', 'pin_ujian', 'is_open', 'is_finished', 'editId', 'isUpdate']);
    }

    public function openAttendanceModal(int $id): void
    {
        $event = Event::withCount('peserta')->findOrFail($id);

        if ($event->peserta_count < 1) {
            $this->dispatch('toast', ['type' => 'warning', 'message' => 'belum ada peserta yang diimport']);

            return;
        }

        $this->resetValidation();
        $this->reset([
            'attendance_event_id',
            'attendance_event_nama',
            'attendance_judul',
            'attendance_hari',
            'attendance_tanggal',
            'attendance_sesi',
            'attendance_peserta_dari',
            'attendance_peserta_sampai',
            'attendance_jumlah_peserta_sesi',
            'attendance_baris_tambahan',
            'attendance_total_peserta',
            'attendance_existing_sesi',
            'attendance_waktu_mulai',
            'attendance_waktu_selesai',
            'attendance_zona_waktu',
            'attendance_tempat',
        ]);

        $this->attendance_event_id = $event->id;
        $this->attendance_event_nama = $event->nama_event;
        $this->attendance_total_peserta = $event->peserta_count;
        $this->attendance_existing_sesi = $this->loadExistingAttendanceSesi($event->id);
        $this->attendance_tanggal = $event->tgl_mulai;
        $this->attendance_hari = $this->detectHari($event->tgl_mulai);
        $this->attendance_zona_waktu = 'WIB';
        $this->attendance_baris_tambahan = 10;

        $this->showAttendanceModal = true;
        $this->dispatchAttendancePickers();
    }

    public function selectAttendanceSesi(int $sesi): void
    {
        $this->attendance_sesi = $sesi;
        $this->loadAttendanceFormForSesi($sesi);
        $this->dispatchAttendancePickers();
    }

    public function loadAttendanceRecord(int $id): void
    {
        $absensi = AbsensiEvent::where('event_id', $this->attendance_event_id)->findOrFail($id);

        $this->attendance_sesi = $absensi->sesi;
        $this->attendance_tanggal = $absensi->tanggal;
        $this->attendance_hari = $absensi->hari ?? $this->detectHari($absensi->tanggal);
        $this->fillAttendanceFormFromModel($absensi);
        $this->dispatchAttendancePickers();
    }

    public function updatedAttendanceSesi($value): void
    {
        if (blank($value) || blank($this->attendance_event_id)) {
            return;
        }

        $this->loadAttendanceFormForSesi((int) $value);
        $this->dispatchAttendancePickers();
    }

    protected function loadExistingAttendanceSesi(int $eventId): array
    {
        return AbsensiEvent::query()
            ->where('event_id', $eventId)
            ->orderBy('tanggal')
            ->orderBy('sesi')
            ->get()
            ->map(fn ($item) => [
                'id' => $item->id,
                'sesi' => $item->sesi,
                'label' => $item->sesiLabel(),
            ])
            ->all();
    }

    public function updatedAttendanceJumlahPesertaSesi(): void
    {
        $this->syncPesertaRangeFromJumlah();
    }

    protected function suggestAttendanceRangeForNewSesi(): void
    {
        $this->attendance_peserta_dari = AbsensiEvent::nextPesertaDari($this->attendance_event_id);
        $this->syncPesertaRangeFromJumlah();
    }

    protected function syncPesertaRangeFromJumlah(): void
    {
        if (blank($this->attendance_jumlah_peserta_sesi) || (int) $this->attendance_jumlah_peserta_sesi < 1) {
            return;
        }

        if (blank($this->attendance_peserta_dari) && filled($this->attendance_event_id)) {
            $this->attendance_peserta_dari = AbsensiEvent::nextPesertaDari($this->attendance_event_id);
        }

        if (blank($this->attendance_peserta_dari)) {
            return;
        }

        $jumlah = max(1, (int) $this->attendance_jumlah_peserta_sesi);
        $this->attendance_peserta_sampai = min(
            (int) $this->attendance_peserta_dari + $jumlah - 1,
            $this->attendance_total_peserta ?: PHP_INT_MAX
        );
    }

    protected function findAbsensiForCurrentContext(int $sesi): ?AbsensiEvent
    {
        if (blank($this->attendance_tanggal)) {
            return null;
        }

        return AbsensiEvent::query()
            ->where('event_id', $this->attendance_event_id)
            ->where('tanggal', AbsensiEvent::tanggalToDatabase($this->attendance_tanggal))
            ->where('sesi', $sesi)
            ->first();
    }

    protected function fillAttendanceFormFromModel(AbsensiEvent $absensi): void
    {
        $this->attendance_judul = $absensi->judul;
        $this->attendance_peserta_dari = $absensi->peserta_dari;
        $this->attendance_peserta_sampai = $absensi->peserta_sampai;
        $this->attendance_jumlah_peserta_sesi = $absensi->jumlah_peserta_sesi
            ?? (($absensi->peserta_dari && $absensi->peserta_sampai)
                ? $absensi->peserta_sampai - $absensi->peserta_dari + 1
                : null);
        $this->attendance_baris_tambahan = $absensi->baris_tambahan ?? 10;
        $this->attendance_waktu_mulai = $absensi->waktu_mulai;
        $this->attendance_waktu_selesai = $absensi->waktu_selesai;
        $this->attendance_zona_waktu = $absensi->zona_waktu;
        $this->attendance_tempat = $absensi->tempat;
    }

    protected function loadAttendanceFormForSesi(int $sesi): void
    {
        $absensi = $this->findAbsensiForCurrentContext($sesi);

        if ($absensi) {
            $this->fillAttendanceFormFromModel($absensi);

            return;
        }

        if (blank($this->attendance_zona_waktu)) {
            $this->attendance_zona_waktu = 'WIB';
        }

        $this->suggestAttendanceRangeForNewSesi();
    }

    protected function dispatchAttendancePickers(): void
    {
        $this->dispatch('modalOpened');
        $this->dispatch('set-flatpickr', [
            'model' => 'attendance_tanggal',
            'value' => $this->attendance_tanggal,
        ]);

        if ($this->attendance_waktu_mulai) {
            $this->dispatch('set-flatpickr-time', [
                'model' => 'attendance_waktu_mulai',
                'value' => $this->attendance_waktu_mulai,
            ]);
        }

        if ($this->attendance_waktu_selesai) {
            $this->dispatch('set-flatpickr-time', [
                'model' => 'attendance_waktu_selesai',
                'value' => $this->attendance_waktu_selesai,
            ]);
        }
    }

    public function closeAttendanceModal(): void
    {
        $this->showAttendanceModal = false;
        $this->resetValidation();
        $this->reset([
            'attendance_event_id',
            'attendance_event_nama',
            'attendance_judul',
            'attendance_hari',
            'attendance_tanggal',
            'attendance_sesi',
            'attendance_peserta_dari',
            'attendance_peserta_sampai',
            'attendance_jumlah_peserta_sesi',
            'attendance_baris_tambahan',
            'attendance_total_peserta',
            'attendance_existing_sesi',
            'attendance_waktu_mulai',
            'attendance_waktu_selesai',
            'attendance_zona_waktu',
            'attendance_tempat',
        ]);
    }

    public function updatedAttendanceTanggal($value): void
    {
        $this->attendance_hari = $this->detectHari($value);

        if (filled($this->attendance_sesi)) {
            $this->loadAttendanceFormForSesi((int) $this->attendance_sesi);
            $this->dispatchAttendancePickers();
        }
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

    public function saveAndPrintAttendance()
    {
        $this->syncPesertaRangeFromJumlah();

        $validated = $this->validate([
            'attendance_event_id' => ['required', 'exists:event,id'],
            'attendance_judul' => ['required', 'string', 'max:1000'],
            'attendance_tanggal' => ['required', 'date_format:d-m-Y'],
            'attendance_sesi' => ['required', 'integer', 'min:1', 'max:99'],
            'attendance_jumlah_peserta_sesi' => ['required', 'integer', 'min:1'],
            'attendance_baris_tambahan' => ['required', 'integer', 'min:0', 'max:100'],
            'attendance_peserta_dari' => ['required', 'integer', 'min:1'],
            'attendance_peserta_sampai' => ['required', 'integer', 'min:1', 'gte:attendance_peserta_dari'],
            'attendance_waktu_mulai' => ['required', 'string', 'max:20'],
            'attendance_waktu_selesai' => ['nullable', 'string', 'max:20'],
            'attendance_zona_waktu' => ['required', 'in:WIB,WITA,WIT'],
            'attendance_tempat' => ['required', 'string', 'max:255'],
        ], [
            'attendance_event_id.required' => 'event tidak valid',
            'attendance_judul.required' => 'judul presensi wajib diisi',
            'attendance_tanggal.required' => 'tanggal wajib diisi',
            'attendance_tanggal.date_format' => 'format tanggal tidak valid',
            'attendance_sesi.required' => 'sesi wajib diisi',
            'attendance_jumlah_peserta_sesi.required' => 'jumlah peserta per sesi wajib diisi',
            'attendance_baris_tambahan.required' => 'jumlah baris tambahan wajib diisi',
            'attendance_peserta_dari.required' => 'nomor peserta awal wajib diisi',
            'attendance_peserta_sampai.required' => 'nomor peserta akhir wajib diisi',
            'attendance_peserta_sampai.gte' => 'nomor peserta akhir harus lebih besar atau sama dengan nomor awal',
            'attendance_waktu_mulai.required' => 'waktu mulai wajib diisi',
            'attendance_zona_waktu.required' => 'zona waktu wajib dipilih',
            'attendance_tempat.required' => 'tempat wajib diisi',
        ]);

        $pesertaDari = (int) $this->attendance_peserta_dari;
        $pesertaSampai = (int) $this->attendance_peserta_sampai;

        if ($pesertaSampai > $this->attendance_total_peserta) {
            $this->addError('attendance_peserta_sampai', 'nomor peserta akhir melebihi total peserta ('.$this->attendance_total_peserta.')');

            return;
        }

        try {
            $absensi = AbsensiEvent::firstOrNew([
                'event_id' => $validated['attendance_event_id'],
                'tanggal' => AbsensiEvent::tanggalToDatabase($validated['attendance_tanggal']),
                'sesi' => $validated['attendance_sesi'],
            ]);
            $isNew = ! $absensi->exists;
            $oldData = $isNew ? null : $absensi->getOriginal();

            $absensi->fill([
                'judul' => $validated['attendance_judul'],
                'hari' => $this->detectHari($validated['attendance_tanggal']),
                'tanggal' => $validated['attendance_tanggal'],
                'sesi' => $validated['attendance_sesi'],
                'peserta_dari' => $pesertaDari,
                'peserta_sampai' => $pesertaSampai,
                'jumlah_peserta_sesi' => $validated['attendance_jumlah_peserta_sesi'],
                'baris_tambahan' => $validated['attendance_baris_tambahan'],
                'waktu_mulai' => $validated['attendance_waktu_mulai'],
                'waktu_selesai' => $validated['attendance_waktu_selesai'],
                'zona_waktu' => $validated['attendance_zona_waktu'],
                'tempat' => $validated['attendance_tempat'],
                'created_by' => $absensi->created_by ?? auth()->id(),
            ]);
            $absensi->save();
            $absensi->refresh();

            activity_log($absensi, $isNew ? 'create' : 'update', 'absensi-event', $oldData);

            $pdfUrl = route('admin.dokumen.absensi.download', [
                'id' => $absensi->id,
            ]).'?v='.$absensi->updated_at->timestamp;

            $this->dispatch('download-attendance', url: $pdfUrl);
            $this->closeAttendanceModal();
        } catch (\Throwable $th) {
            $this->dispatch('toast', ['type' => 'error', 'message' => 'terjadi kesalahan saat menyimpan absensi']);
        }
    }

    public function edit($id)
    {
        try {
            $data = Event::with(['assessor'])->findOrFail($id);
            $this->editId = $data->id;
            $this->nama_event = $data->nama_event;
            $this->metode_tes_id = $data->metode_tes_id;
            $this->jabatan_diuji_id = $data->jabatan_diuji_id;
            $this->form_tgl_mulai = $data->tgl_mulai;
            $this->form_tgl_selesai = $data->tgl_selesai;
            $this->jumlah_peserta = $data->jumlah_peserta;
            $this->assessor = $data->assessor()->pluck('id')->toArray() ?? [];
            $this->is_finished = $data->is_finished;
            $this->is_open = $data->is_open;
            $this->pin_ujian = $data->pin_ujian;
            $this->isUpdate = true;
            $this->showModal = true;
            $this->resetValidation();
            $this->dispatch('modalOpened');
        } catch (\Throwable $th) {
            $this->dispatch('toast', ['type' => 'error', 'message' => 'terjadi kesalahan']);
        }
    }

    public function save()
    {
        $this->validate();
        try {
            DB::beginTransaction();

            if ($this->isUpdate) {
                $data = Event::findOrFail($this->editId);
                $old_data = $data->getOriginal();

                $fillData = [
                    'nama_event' => $this->nama_event,
                    'metode_tes_id' => $this->metode_tes_id,
                    'jabatan_diuji_id' => $this->jabatan_diuji_id,
                    'tgl_mulai' => $this->form_tgl_mulai,
                    'tgl_selesai' => $this->form_tgl_selesai,
                    'jumlah_peserta' => $this->jumlah_peserta,
                    'pin_ujian' => $this->pin_ujian,
                    'is_finished' => $this->is_finished,
                ];

                // is_open hanya untuk Assessment Center
                if ($this->metode_tes_id == 1) {
                    $fillData['is_open'] = $this->is_open;
                }

                $data->fill($fillData);
                $data->save();

                // Assessor hanya untuk Assessment Center
                if ($this->metode_tes_id == 1) {
                    $data->assessor()->sync(is_array($this->assessor) ? $this->assessor : []);
                } else {
                    $data->assessor()->detach();
                }

                activity_log($data, 'update', 'event', $old_data);

                DB::commit();
                $this->dispatch('toast', ['type' => 'success', 'message' => 'berhasil ubah data']);
            } else {
                $createData = [
                    'nama_event' => $this->nama_event,
                    'metode_tes_id' => $this->metode_tes_id,
                    'jabatan_diuji_id' => $this->jabatan_diuji_id,
                    'tgl_mulai' => $this->form_tgl_mulai,
                    'tgl_selesai' => $this->form_tgl_selesai,
                    'jumlah_peserta' => $this->jumlah_peserta,
                    'pin_ujian' => $this->pin_ujian,
                ];

                // is_open hanya untuk Assessment Center
                if ($this->metode_tes_id == 1) {
                    $createData['is_open'] = $this->is_open;
                }

                $event = Event::create($createData);

                // Assessor hanya untuk Assessment Center
                if ($this->metode_tes_id == 1) {
                    $event->assessor()->syncWithoutDetaching(is_array($this->assessor) ? $this->assessor : []);
                }

                activity_log($event, 'create', 'event');

                DB::commit();
                $this->dispatch('toast', ['type' => 'success', 'message' => 'berhasil tambah data']);
            }

            $this->closeModal();
            $this->resetPage();
        } catch (\Throwable $th) {
            DB::rollBack();
            $this->dispatch('toast', ['type' => 'error', 'message' => 'terjadi kesalahan']);
        }
    }

    public function deleteConfirmation($id)
    {
        $this->selected_id = $id;
        $this->dispatch('show-delete-confirmation');
    }

    public function changeStatusPortofolioConfirmation($id)
    {
        $this->selected_id = $id;
        $this->dispatch('change-status-portofolio-confirmation');
    }

    public function changeStatusEventConfirmation($id)
    {
        $this->selected_id = $id;
        $this->dispatch('change-status-event-confirmation');
    }

    #[On('changeStatusPortofolio')]
    public function changeStatusPortofolio()
    {
        try {
            $data = Event::find($this->selected_id);

            if ($data->is_open === 'true') {
                $data->update(['is_open' => 'false']);
            } else {
                $data->update(['is_open' => 'true']);
            }

            $this->dispatch('toast', ['type' => 'success', 'message' => 'berhasil merubah status']);
        } catch (\Throwable $th) {
            $this->dispatch('toast', ['type' => 'error', 'message' => 'gagal merubah status']);
        }
    }

    #[On('changeStatusEvent')]
    public function changeStatusEvent()
    {
        try {
            $data = Event::find($this->selected_id);

            if ($data->is_finished === 'true') {
                $data->update(['is_finished' => 'false']);
            } else {
                $data->update(['is_finished' => 'true']);
            }

            $this->dispatch('toast', ['type' => 'success', 'message' => 'berhasil merubah status']);
        } catch (\Throwable $th) {
            $this->dispatch('toast', ['type' => 'error', 'message' => 'gagal merubah status']);
        }
    }

    #[On('delete')]
    public function destroy()
    {
        try {
            $data = Event::find($this->selected_id);
            $old_data = $data->getOriginal();

            activity_log($data, 'delete', 'event', $old_data);

            $data->delete();

            $this->dispatch('toast', ['type' => 'success', 'message' => 'berhasil menghapus data']);
        } catch (\Throwable $th) {
            $this->dispatch('toast', ['type' => 'error', 'message' => 'gagal menghapus data']);
        }
    }
}
