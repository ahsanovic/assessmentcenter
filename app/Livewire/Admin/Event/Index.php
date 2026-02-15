<?php

namespace App\Livewire\Admin\Event;

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
