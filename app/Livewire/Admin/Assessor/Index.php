<?php

namespace App\Livewire\Admin\Assessor;

use App\Models\Assessor;
use App\Models\Event;
use App\Models\RefGolPangkat;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.admin.app', ['title' => 'Assessor'])]
class Index extends Component
{
    use WithPagination;

    public $selected_id;
    public $event;
    public $filter_is_active;
    public $filter_is_asn;

    // Modal state
    public $showModal = false;
    public $isUpdate = false;
    public $editId;

    // Form fields
    public $nama;
    public $nip;
    public $nik;
    public $jabatan;
    public $instansi;
    public $password;
    public $gol_pangkat_id;
    public $is_active;
    public $is_asn;

    #[Url(as: 'q')]
    public ?string $search = '';

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedFilterIsActive()
    {
        $this->resetPage();
    }

    public function updatedFilterIsAsn()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->reset(['search', 'event', 'filter_is_active', 'filter_is_asn']);
        $this->resetPage();
        $this->dispatch('reset-select2');
    }

    protected function rules()
    {
        $rules = [
            'nama' => ['required'],
            'is_asn' => ['required'],
            'jabatan' => ['required'],
            'instansi' => ['required'],
            'password' => $this->isUpdate ? ['nullable', 'min:8'] : ['required', 'min:8'],
        ];

        if ($this->is_asn == 'true') {
            $rules['nip'] = ['required', 'numeric', 'digits:18', Rule::unique('assessor', 'nip')->ignore($this->editId)];
            $rules['gol_pangkat_id'] = ['required'];
        } else if ($this->is_asn == 'false') {
            $rules['nik'] = ['required', 'numeric', 'digits:16', Rule::unique('assessor', 'nik')->ignore($this->editId)];
        }

        return $rules;
    }

    protected function messages()
    {
        return [
            'is_asn.required' => 'harus dipilih',
            'nama.required' => 'harus diisi',
            'nip.required' => 'harus diisi',
            'nip.numeric' => 'harus angka',
            'nip.digits' => 'nip harus 18 digit',
            'nip.unique' => 'nip sudah terdaftar',
            'nik.required' => 'harus diisi',
            'nik.numeric' => 'harus angka',
            'nik.digits' => 'nik harus 16 digit',
            'nik.unique' => 'nik sudah terdaftar',
            'instansi.required' => 'harus diisi',
            'jabatan.required' => 'harus diisi',
            'password.required' => 'harus diisi',
            'password.min' => 'minimal 8 karakter',
            'gol_pangkat_id.required' => 'harus dipilih',
        ];
    }

    public function render()
    {
        $data = Assessor::when($this->search, function ($query) {
            $query->where(function ($q) {
                $q->where('nama', 'like', '%' . $this->search . '%')
                    ->orWhere('nip', 'like', '%' . $this->search . '%')
                    ->orWhere('nik', 'like', '%' . $this->search . '%')
                    ->orWhere('jabatan', 'like', '%' . $this->search . '%')
                    ->orWhere('instansi', 'like', '%' . $this->search . '%');
            });
        })
            ->when($this->filter_is_asn, function ($query) {
                $query->where('is_asn', $this->filter_is_asn);
            })
            ->when($this->event, function ($query) {
                $query->whereHas('event', function ($query) {
                    $query->where('assessor_event.event_id', $this->event);
                });
            })
            ->when($this->filter_is_active, function ($query) {
                $query->where('is_active', $this->filter_is_active);
            })
            ->orderByDesc('id')
            ->paginate(10);

        $option_event = Event::where('metode_tes_id', 1)->pluck('nama_event', 'id');
        $option_status = ['true' => 'aktif', 'false' => 'tidak aktif'];
        $option_gol_pangkat = RefGolPangkat::all();

        return view('livewire.admin.assessor.index', compact('data', 'option_event', 'option_status', 'option_gol_pangkat'));
    }

    public function openModal()
    {
        $this->resetValidation();
        $this->reset(['nama', 'nip', 'nik', 'jabatan', 'instansi', 'password', 'gol_pangkat_id', 'is_active', 'is_asn', 'editId', 'isUpdate']);
        $this->showModal = true;
        $this->dispatch('modalOpened');
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetValidation();
        $this->reset(['nama', 'nip', 'nik', 'jabatan', 'instansi', 'password', 'gol_pangkat_id', 'is_active', 'is_asn', 'editId', 'isUpdate']);
    }

    public function edit($id)
    {
        try {
            $data = Assessor::findOrFail($id);
            $this->editId = $data->id;
            $this->nama = $data->nama;
            $this->nip = $data->nip;
            $this->nik = $data->nik;
            $this->jabatan = $data->jabatan;
            $this->instansi = $data->instansi;
            $this->gol_pangkat_id = $data->gol_pangkat_id;
            $this->is_active = $data->is_active;
            $this->is_asn = $data->is_asn;
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
            if ($this->isUpdate) {
                $data = Assessor::findOrFail($this->editId);
                $old_data = $data->getOriginal();

                $data->nama = $this->nama;

                if ($this->is_asn == 'true' && ($data->nik != null && $data->is_asn == 'false')) {
                    $data->nip = $this->nip;
                    $data->nik = null;
                    $data->gol_pangkat_id = $this->gol_pangkat_id;
                } else if ($this->is_asn == 'false' && ($data->nip != null && $data->is_asn == 'true')) {
                    $data->nik = $this->nik;
                    $data->nip = null;
                    $data->gol_pangkat_id = null;
                } else if ($this->is_asn == 'true') {
                    $data->nip = $this->nip;
                    $data->nik = null;
                    $data->gol_pangkat_id = $this->gol_pangkat_id;
                } else if ($this->is_asn == 'false') {
                    $data->nik = $this->nik;
                    $data->nip = null;
                    $data->gol_pangkat_id = null;
                }

                $data->jabatan = $this->jabatan;
                $data->instansi = $this->instansi;
                $data->is_active = $this->is_active;
                $data->is_asn = $this->is_asn;
                $data->password = $this->password != '' ? bcrypt($this->password) : $data->password;
                $data->save();

                activity_log($data, 'update', 'assessor', $old_data);
                $this->dispatch('toast', ['type' => 'success', 'message' => 'berhasil ubah data']);
            } else {
                $data = Assessor::create([
                    'nama' => $this->nama,
                    'is_asn' => $this->is_asn,
                    'nip' => $this->is_asn == 'true' ? $this->nip : null,
                    'nik' => $this->is_asn == 'false' ? $this->nik : null,
                    'jabatan' => $this->jabatan,
                    'instansi' => $this->instansi,
                    'gol_pangkat_id' => $this->is_asn == 'true' ? $this->gol_pangkat_id : null,
                    'password' => bcrypt($this->password),
                ]);

                activity_log($data, 'create', 'assessor');
                $this->dispatch('toast', ['type' => 'success', 'message' => 'berhasil tambah data']);
            }
            $this->closeModal();
            $this->resetPage();
        } catch (\Throwable $th) {
            $this->dispatch('toast', ['type' => 'error', 'message' => 'terjadi kesalahan']);
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
            $data = Assessor::find($this->selected_id);
            $old_data = $data->getOriginal();

            activity_log($data, 'delete', 'assessor', $old_data);

            $data->delete();

            $this->dispatch('toast', ['type' => 'success', 'message' => 'berhasil menghapus data']);
        } catch (\Throwable $th) {
            $this->dispatch('toast', ['type' => 'error', 'message' => 'gagal menghapus data']);
        }
    }
}
