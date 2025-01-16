<?php

namespace App\Livewire\Admin\Peserta;

use App\Models\Event;
use App\Models\Peserta;
use App\Models\RefGolPangkat;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Component;

#[Layout('components.layouts.admin.app', ['title' => 'Peserta'])]
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
    public $is_active;
    public $unit_kerja;

    #[Locked]
    public $id;
    
    public function mount($id = null)
    {
        try {
            if ($id) {
                $this->isUpdate = true;
                $this->previous_url = url()->previous();

                $data = Peserta::findOrFail($id);
                $this->nama = $data->nama;
                $this->event_id = $data->event_id;
                $this->nip = $data->nip;
                $this->jabatan = $data->jabatan;
                $this->instansi = $data->instansi;
                $this->unit_kerja = $data->unit_kerja;
                $this->is_active = $data->is_active;
            }
        } catch (\Throwable $th) {
            // throw $th;
            $this->dispatch('toast', ['type' => 'error', 'message' => 'terjadi kesalahan']);
        }
    }
    
    public function render()
    {
        $option_event = Event::where('is_finished', 'false')->pluck('nama_event', 'id');
        $option_gol_pangkat = RefGolPangkat::all();

        return view('livewire.admin.peserta.form', compact('option_event', 'option_gol_pangkat'));
    }

    protected function rules()
    {
        $rules = [
            'nama' => ['required'],
            'nip' => ['required', 'numeric', 'digits:18'],
            'jabatan' => ['required'],
            'instansi' => ['required'],
            'unit_kerja' => ['required'],
            'event_id' => ['required'],
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
            'nip.digits' => 'nip harus 18 digit',
            'instansi.required' => 'harus diisi',
            'jabatan.required' => 'harus diisi',
            'password.required' => 'harus  diisi',
            'password.min' => 'minimal 8 karakter',
            'event_id.required' => 'harus diisi',
            'unit_kerja' => 'harus dipilih'
        ];
    }

    public function save()
    {
        $this->validate();
        try {
            if ($this->isUpdate) {
                $data = Peserta::whereId($this->id)->first();
                $data->nama = $this->nama;
                $data->event_id = $this->event_id;
                $data->nip = $this->nip;
                $data->jabatan = $this->jabatan;
                $data->instansi = $this->instansi;
                $data->unit_kerja = $this->unit_kerja;
                $data->is_active = $this->is_active;
                $data->password = $this->password != '' ? bcrypt($this->password) : $data->password;
                $data->save();
                
                session()->flash('toast', [
                    'type' => 'success',
                    'message' => 'berhasil ubah data'
                ]);
                
                $this->redirect($this->previous_url, true);
            } else {
                Peserta::create([
                    'nama' => $this->nama,
                    'event_id' => $this->event_id,
                    'nip' => $this->nip,
                    'jabatan' => $this->jabatan,
                    'instansi' => $this->instansi,
                    'unit_kerja' => $this->unit_kerja,
                    'password' => bcrypt($this->password)
                ]);
    
                session()->flash('toast', [
                    'type' => 'success',
                    'message' => 'berhasil tambah data'
                ]);
    
                $this->redirect(route('admin.peserta'), true);
            }
        } catch (\Throwable $th) {
            // throw $th;
            $this->dispatch('toast', ['type' => 'error', 'message' => 'terjadi kesalahan']);
        }
    }
}
