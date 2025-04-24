<?php

namespace App\Livewire\Admin\Assessor;

use App\Models\Assessor;
use App\Models\RefGolPangkat;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Component;

#[Layout('components.layouts.admin.app', ['title' => 'Assessor'])]
class Form extends Component
{
    public $isUpdate = false;
    public $nama;
    public $nip;
    public $nik;
    public $jabatan;
    public $instansi;
    public $event_id;
    public $password;
    public $gol_pangkat_id;
    public $is_active;
    public $is_asn;

    #[Locked]
    public $id;
    
    public function mount($id = null)
    {
        try {
            if ($id) {
                $this->isUpdate = true;

                $data = Assessor::findOrFail($id);
                $this->nama = $data->nama;
                $this->nip = $data->nip;
                $this->nik = $data->nik;
                $this->jabatan = $data->jabatan;
                $this->instansi = $data->instansi;
                $this->gol_pangkat_id = $data->gol_pangkat_id;
                $this->is_active = $data->is_active;
                $this->is_asn = $data->is_asn;
            }
        } catch (\Throwable $th) {
            // throw $th;
            $this->dispatch('toast', ['type' => 'error', 'message' => 'terjadi kesalahan']);
        }
    }
    
    public function render()
    {
        // $option_event = Event::where('is_finished', 'false')->pluck('nama_event', 'id');
        $option_gol_pangkat = RefGolPangkat::all();

        return view('livewire.admin.assessor.form', compact('option_gol_pangkat'));
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
            $rules['nip'] = ['required', 'numeric', 'digits:18', Rule::unique('assessor', 'nip')->ignore($this->id)];
            $rules['gol_pangkat_id'] = ['required'];
        } else if ($this->is_asn == 'false') {
            $rules['nik'] = ['required', 'numeric', 'digits:16', Rule::unique('assessor', 'nik')->ignore($this->id)];
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
            'password.required' => 'harus  diisi',
            'password.min' => 'minimal 8 karakter',
            'gol_pangkat_id' => 'harus dipilih'
        ];
    }

    public function save()
    {
        $this->validate();
        try {
            if ($this->isUpdate) {
                $data = Assessor::whereId($this->id)->first();
                $data->nama = $this->nama;

                if ($this->is_asn == 'true' && ($data->nik != null && $data->is_asn == 'false')) {
                    $data->nip = $this->nip;
                    $data->nik = null;
                    $data->gol_pangkat_id = $this->gol_pangkat_id;
                } else if ($this->is_asn == 'false' && ($data->nip != null && $data->is_asn == 'true')) {
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
                
                session()->flash('toast', [
                    'type' => 'success',
                    'message' => 'berhasil ubah data'
                ]);
                
                $this->redirect(route('admin.assessor'), true);
            } else {
                Assessor::create([
                    'nama' => $this->nama,
                    'is_asn' => $this->is_asn,
                    'nip' => $this->is_asn == 'true' ? $this->nip : null,
                    'nik' => $this->is_asn == 'false' ? $this->nik : null,
                    'jabatan' => $this->jabatan,
                    'instansi' => $this->instansi,
                    'gol_pangkat_id' => $this->is_asn == 'true' ? $this->gol_pangkat_id : null,
                    'password' => bcrypt($this->password)
                ]);
    
                session()->flash('toast', [
                    'type' => 'success',
                    'message' => 'berhasil tambah data'
                ]);
    
                $this->redirect(route('admin.assessor'), true);
            }
        } catch (\Throwable $th) {
            // throw $th;
            $this->dispatch('toast', ['type' => 'error', 'message' => 'terjadi kesalahan']);
        }
    }
}
