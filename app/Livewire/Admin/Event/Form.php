<?php

namespace App\Livewire\Admin\Event;

use App\Models\Assessor;
use App\Models\Event;
use App\Models\RefAlatTes;
use App\Models\RefJabatanDiuji;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Component;

#[Layout('components.layouts.admin.app', ['title' => 'Event'])]
class Form extends Component
{
    public $previous_url;
    public $tgl_mulai;
    public $tgl_selesai;
    public $nama_event;
    public $jabatan_diuji_id;
    public $pin_ujian;
    public $jumlah_peserta;
    public $alat_tes_id = [];
    public $assessor = [];
    public $is_finished;

    public $isUpdate = false;

    #[Locked]
    public $id;
    
    public function mount($id = null)
    {
        try {
            if ($id) {
                $this->isUpdate = true;
                $this->previous_url = url()->previous();
    
                $data = Event::findOrFail($id);
                $this->id = $data->id;
                $this->nama_event = $data->nama_event;
                $this->jabatan_diuji_id = $data->jabatan_diuji_id;
                $this->tgl_mulai = $data->tgl_mulai;
                $this->tgl_selesai = $data->tgl_selesai;
                $this->jumlah_peserta = $data->jumlah_peserta;
                $this->alat_tes_id = $data->alatTes()->pluck('id')->toArray();
                $this->assessor = $data->assessor()->pluck('id')->toArray();
                $this->is_finished = $data->is_finished;
                $this->pin_ujian = $data->pin_ujian;
            }
        } catch (\Throwable $th) {
            // throw $th;
            $this->dispatch('toast', ['type' => 'error', 'message' => 'terjadi kesalahan']);
        }
    }
    
    public function render()
    {
        $option_jabatan_diuji = RefJabatanDiuji::pluck('jenis', 'id');
        $option_alat_tes = RefAlatTes::pluck('alat_tes', 'id');
        $option_assessor = Assessor::pluck('nama', 'id');

        return view('livewire.admin.event.form', compact('option_jabatan_diuji', 'option_alat_tes', 'option_assessor'));
    }

    protected function rules()
    {
        $rules = [
            'nama_event' => ['required'],
            'jabatan_diuji_id' => ['required'],
            'tgl_mulai' => ['required', 'date_format:d-m-Y'],
            'tgl_selesai' => ['required', 'date_format:d-m-Y'],
            'jumlah_peserta' => ['required', 'numeric'],
            'alat_tes_id' => 'required|array',
            'alat_tes_id.*' => 'exists:ref_alat_tes,id',
            'assessor' => 'required|array',
            'assessor.*' => 'exists:assessor,id',
            'pin_ujian' => ['required']
        ];

        // Validasi pin ujian hanya wajib saat create, tidak saat update
        // if (!$this->isUpdate) {
        //     $rules['pin_ujian'] = ['required'];
        // } else {
        //     $rules['pin_ujian'] = ['nullable'];
        // }

        return $rules;
    }

    protected function messages()
    {
        return [
            'nama_event.required' => 'harus diisi',
            'jabatan_diuji_id.required' => 'harus diisi',
            'tgl_mulai.required' => 'harus diisi',
            'tgl_mulai.date_format' => 'format tanggal mulai tidak valid',
            'tgl_selesai.required' => 'harus diisi',
            'tgl_selesai.date_format' => 'format tanggal selesai tidak valid',
            'jumlah_peserta.required' => 'harus diisi',
            'jumlah_peserta.numeric' => 'harus berupa angka',
            'pin_ujian.required' => 'harus diisi',
            'alat_tes_id.required' => 'harus dipilih',
            'assessor.required' => 'harus dipilih',
            'pin_ujian.required' => 'harus diisi'
        ];
    }

    public function save()
    {
        $this->validate();
        try {
            if ($this->isUpdate) {
                $data = Event::whereId($this->id)->first();
                $data->nama_event = $this->nama_event;
                $data->jabatan_diuji_id = $this->jabatan_diuji_id;
                $data->tgl_mulai = $this->tgl_mulai;
                $data->tgl_selesai = $this->tgl_selesai;
                $data->jumlah_peserta = $this->jumlah_peserta;
                $data->pin_ujian = $this->pin_ujian;
                $data->is_finished = $this->is_finished;
                $data->save();

                // Attach assessors
                $data->assessor()->sync($this->assessor);
                $data->alatTes()->sync($this->alat_tes_id);
                
                session()->flash('toast', [
                    'type' => 'success',
                    'message' => 'berhasil ubah data'
                ]);
                
                $this->redirect($this->previous_url, true);
            } else {
                $event = Event::create([
                    'nama_event' => $this->nama_event,
                    'jabatan_diuji_id' => $this->jabatan_diuji_id,
                    'tgl_mulai' => $this->tgl_mulai,
                    'tgl_selesai' => $this->tgl_selesai,
                    'jumlah_peserta' => $this->jumlah_peserta,
                    'pin_ujian' => $this->pin_ujian,
                ]);

                // Attach assessors
                $event->assessor()->attach($this->assessor);
                $event->alatTes()->attach($this->alat_tes_id);
    
                session()->flash('toast', [
                    'type' => 'success',
                    'message' => 'berhasil tambah data'
                ]);
    
                $this->redirect(route('admin.event'), true);
            }
        } catch (\Throwable $th) {
            // throw $th;
            $this->dispatch('toast', ['type' => 'error', 'message' => 'terjadi kesalahan']);
        }
    }
}
