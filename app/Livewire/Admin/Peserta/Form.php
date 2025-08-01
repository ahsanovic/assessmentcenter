<?php

namespace App\Livewire\Admin\Peserta;

use App\Models\Event;
use App\Models\Peserta;
use App\Models\RefGolPangkat;
use App\Models\RefJenisPeserta;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Component;

#[Layout('components.layouts.admin.app', ['title' => 'Peserta'])]
class Form extends Component
{
    public $isUpdate = false;
    public $nama;
    public $nip;
    public $nik;
    public $jabatan;
    public $instansi;
    public $event_id;
    public $jenis_peserta_id;
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

                $data = Peserta::findOrFail($id);
                $this->nama = $data->nama;
                $this->event_id = $data->event_id;
                $this->jenis_peserta_id = $data->jenis_peserta_id;
                $this->nip = $data->nip;
                $this->nik = $data->nik;
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
        $option_jenis_peserta = RefJenisPeserta::pluck('jenis_peserta', 'id');

        return view('livewire.admin.peserta.form', compact('option_event', 'option_gol_pangkat', 'option_jenis_peserta'));
    }

    protected function rules()
    {
        $rules = [
            'nama' => ['required'],
            'instansi' => ['required'],
            'unit_kerja' => ['required'],
            'event_id' => ['required'],
            'jenis_peserta_id' => ['required'],
            'password' => $this->isUpdate ? ['nullable', 'min:8'] : ['required', 'min:8'],
        ];

        if ($this->jenis_peserta_id == 1) {
            $rules['nip'] = ['required', 'numeric', 'digits:18'];
            $rules['jabatan'] = ['required'];
        } else if ($this->jenis_peserta_id == 2) {
            $rules['nik'] = ['required', 'numeric', 'digits:16'];
        }

        return $rules;
    }

    protected function messages()
    {
        return [
            'nama.required' => 'harus diisi',
            'nip.required' => 'harus diisi',
            'nip.numeric' => 'harus angka',
            'nip.digits' => 'nip harus 18 digit',
            'nik.required' => 'harus diisi',
            'nik.numeric' => 'harus angka',
            'nik.digits' => 'nip harus 16 digit',
            'instansi.required' => 'harus diisi',
            'jabatan.required' => 'harus diisi',
            'password.required' => 'harus  diisi',
            'password.min' => 'minimal 8 karakter',
            'event_id.required' => 'harus dipilih',
            'jenis_peserta_id.required' => 'harus dipilih',
            'unit_kerja' => 'harus dipilih'
        ];
    }

    public function save()
    {
        $this->validate();
        try {
            if ($this->isUpdate) {
                $data = Peserta::whereId($this->id)->first();
                $old_data = $data->getOriginal();

                $data->nama = $this->nama;
                $data->event_id = $this->event_id;

                if ($this->jenis_peserta_id == 2 && ($data->jenis_peserta_id == 1 && $data->nip != null)) {
                    $data->nip = null;
                    $data->nik = $this->nik;
                    $data->jabatan = null;
                } else if ($this->jenis_peserta_id == 1 && ($data->jenis_peserta_id == 2 && $data->nik != null)) {
                    $data->nik = null;
                    $data->nip = $this->nip;
                    $data->jabatan = $this->jabatan;
                } else if ($this->jenis_peserta_id == 1 && $data->nip != null) {
                    $data->nik = null;
                    $data->nip = $this->nip;
                    $data->jabatan = $this->jabatan;
                } else if ($this->jenis_peserta_id == 2 && $data->nik != null) {
                    $data->nip = null;
                    $data->nik = $this->nik;
                    $data->jabatan = null;
                }
                $data->jenis_peserta_id = $this->jenis_peserta_id;
                $data->instansi = $this->instansi;
                $data->unit_kerja = $this->unit_kerja;
                $data->is_active = $this->is_active;
                $data->password = $this->password != '' ? bcrypt($this->password) : $data->password;
                $data->save();

                activity_log($data, 'update', 'peserta', $old_data);

                session()->flash('toast', [
                    'type' => 'success',
                    'message' => 'berhasil ubah data'
                ]);

                $this->redirect(route('admin.peserta'), true);
            } else {
                $data = Peserta::create([
                    'nama' => $this->nama,
                    'event_id' => $this->event_id,
                    'jenis_peserta_id' => $this->jenis_peserta_id,
                    'nip' => $this->nip,
                    'nik' => $this->nik,
                    'jabatan' => $this->jenis_peserta_id == 1 ? $this->jabatan : null,
                    'instansi' => $this->instansi,
                    'unit_kerja' => $this->unit_kerja,
                    'password' => bcrypt($this->password)
                ]);

                activity_log($data, 'create', 'peserta');

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
