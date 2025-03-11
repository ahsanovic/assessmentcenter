<?php

namespace App\Livewire\Admin\Assessor;

use App\Models\Assessor;
use App\Models\RefGolPangkat;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Component;

#[Layout('components.layouts.admin.app', ['title' => 'Assessor'])]
class Form extends Component
{
    public $previous_url;
    public $isUpdate = false;
    public $nama;
    public $nip;
    public $jabatan;
    public $instansi;
    public $event_id;
    public $password;
    public $gol_pangkat_id;
    public $is_active;

    #[Locked]
    public $id;
    
    public function mount($id = null)
    {
        try {
            if ($id) {
                $this->isUpdate = true;
                $this->previous_url = url()->previous();

                $data = Assessor::findOrFail($id);
                $this->nama = $data->nama;
                $this->nip = $data->nip;
                $this->jabatan = $data->jabatan;
                $this->instansi = $data->instansi;
                $this->gol_pangkat_id = $data->gol_pangkat_id;
                $this->is_active = $data->is_active;
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
            'nip' => ['required', 'numeric', 'digits_between:16,18'],
            // 'jabatan' => ['required'],
            // 'instansi' => ['required'],
            // 'gol_pangkat_id' => ['required'],
            'password' => $this->isUpdate ? ['nullable', 'min:8'] : ['required', 'min:8'],
        ];

        return $rules;
    }

    protected function messages()
    {
        return [
            'nama.required' => 'harus diisi',
            'nip.required' => 'harus diisi',
            'nip.numeric' => 'harus angka',
            'nip.digits_between' => 'nik harus 16 digit dan nip harus 18 digit',
            // 'instansi.required' => 'harus diisi',
            // 'jabatan.required' => 'harus diisi',
            'password.required' => 'harus  diisi',
            'password.min' => 'minimal 8 karakter',
            // 'gol_pangkat_id' => 'harus dipilih'
        ];
    }

    public function save()
    {
        $this->validate();
        try {
            if ($this->isUpdate) {
                $data = Assessor::whereId($this->id)->first();
                $data->nama = $this->nama;
                $data->nip = $this->nip;
                $data->jabatan = $this->jabatan;
                $data->instansi = $this->instansi;
                $data->gol_pangkat_id = $this->gol_pangkat_id;
                $data->is_active = $this->is_active;
                $data->password = $this->password != '' ? bcrypt($this->password) : $data->password;
                $data->save();
                
                session()->flash('toast', [
                    'type' => 'success',
                    'message' => 'berhasil ubah data'
                ]);
                
                $this->redirect($this->previous_url, true);
            } else {
                Assessor::create([
                    'nama' => $this->nama,
                    'nip' => $this->nip,
                    'jabatan' => $this->jabatan,
                    'instansi' => $this->instansi,
                    'gol_pangkat_id' => $this->gol_pangkat_id,
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
