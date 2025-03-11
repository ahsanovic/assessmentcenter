<?php

namespace App\Livewire\Admin\Event;

use App\Models\Assessor;
use App\Models\Event;
use App\Models\RefAlatTes;
use App\Models\RefJabatanDiuji;
use App\Models\RefMetodeTes;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Component;

#[Layout('components.layouts.admin.app', ['title' => 'Event'])]
class Form extends Component
{
    public $tgl_mulai;
    public $tgl_selesai;
    public $nama_event;
    public $jabatan_diuji_id;
    public $metode_tes_id;
    public $pin_ujian;
    public $jumlah_peserta;
    // public array $alat_tes_id = [];
    public array $assessor = [];
    public $is_finished;
    public $is_open;
    public bool $isUpdate = false;

    #[Locked]
    public $id;
    
    public function mount($id = null)
    {
        try {
            $this->isUpdate = false;

            if ($id) {
                $this->isUpdate = true;
    
                $data = Event::with(['assessor'])->findOrFail($id);
                $this->id = $data->id;
                $this->nama_event = $data->nama_event;
                $this->metode_tes_id = $data->metode_tes_id;
                $this->jabatan_diuji_id = $data->jabatan_diuji_id;
                $this->tgl_mulai = $data->tgl_mulai;
                $this->tgl_selesai = $data->tgl_selesai;
                $this->jumlah_peserta = $data->jumlah_peserta;
                // $this->alat_tes_id = $data->alatTes()->pluck('id')->toArray() ?? [];
                $this->assessor = $data->assessor()->pluck('id')->toArray() ?? [];
                $this->is_finished = $data->is_finished;
                $this->is_open = $data->is_open;
                $this->pin_ujian = $data->pin_ujian;
            }

            $this->dispatch('choices:reinit');
        } catch (\Throwable $th) {
            // throw $th;
            $this->dispatch('toast', ['type' => 'error', 'message' => 'terjadi kesalahan']);
        }
    }
    
    public function render()
    {
        $option_jabatan_diuji = RefJabatanDiuji::pluck('jenis', 'id');
        // $option_alat_tes = RefAlatTes::pluck('alat_tes', 'id');
        $option_assessor = Assessor::pluck('nama', 'id');
        $option_metode_tes = RefMetodeTes::pluck('metode_tes', 'id');

        return view('livewire.admin.event.form', compact('option_jabatan_diuji', 'option_assessor', 'option_metode_tes'));
    }

    protected function rules()
    {
        $rules = [
            'nama_event' => ['required'],
            'metode_tes_id' => ['required'],
            'jabatan_diuji_id' => ['required'],
            'tgl_mulai' => ['required', 'date_format:d-m-Y'],
            'tgl_selesai' => ['required', 'date_format:d-m-Y', 'after_or_equal:tgl_mulai'],
            'jumlah_peserta' => ['required', 'numeric'],
            // 'alat_tes_id' => 'required|array',
            // 'alat_tes_id.*' => 'exists:ref_alat_tes,id',
            'assessor' => 'required|array',
            'assessor.*' => 'exists:assessor,id',
            'pin_ujian' => ['required'],
            'is_open' => ['required']
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
            'metode_tes_id.required' => 'harus diisi',
            'jabatan_diuji_id.required' => 'harus diisi',
            'tgl_mulai.required' => 'harus diisi',
            'tgl_mulai.date_format' => 'format tanggal mulai tidak valid',
            'tgl_selesai.required' => 'harus diisi',
            'tgl_selesai.date_format' => 'format tanggal selesai tidak valid',
            'tgl_selesai.after_or_equal' => 'tanggal selesai tidak boleh sebelum tanggal mulai',
            'jumlah_peserta.required' => 'harus diisi',
            'jumlah_peserta.numeric' => 'harus berupa angka',
            // 'alat_tes_id.required' => 'harus dipilih',
            'assessor.required' => 'harus dipilih',
            'pin_ujian.required' => 'harus diisi',
            'is_open.required' => 'harus dipilih'
        ];
    }

    public function save()
    {
        $this->validate();
        try {
            DB::beginTransaction();

            if ($this->isUpdate) {
                $data = Event::findOrFail($this->id);
                $data->fill([
                    'nama_event' => $this->nama_event,
                    'metode_tes_id' => $this->metode_tes_id,
                    'jabatan_diuji_id' => $this->jabatan_diuji_id,
                    'tgl_mulai' => $this->tgl_mulai,
                    'tgl_selesai' => $this->tgl_selesai,
                    'jumlah_peserta' => $this->jumlah_peserta,
                    'pin_ujian' => $this->pin_ujian,
                    'is_finished' => $this->is_finished,
                    'is_open' => $this->is_open,
                ]);
                $data->save();

                // sync pivot tables
                $data->assessor()->sync(is_array($this->assessor) ? $this->assessor : []);
                // $data->alatTes()->sync(is_array($this->alat_tes_id) ? $this->alat_tes_id : []);

                DB::commit();
                session()->flash('toast', [
                    'type' => 'success',
                    'message' => 'berhasil ubah data'
                ]);

                $this->redirect(route('admin.event'), true);
            } else {
                $event = Event::create([
                    'nama_event' => $this->nama_event,
                    'metode_tes_id' => $this->metode_tes_id,
                    'jabatan_diuji_id' => $this->jabatan_diuji_id,
                    'tgl_mulai' => $this->tgl_mulai,
                    'tgl_selesai' => $this->tgl_selesai,
                    'jumlah_peserta' => $this->jumlah_peserta,
                    'pin_ujian' => $this->pin_ujian,
                    'is_open' => $this->is_open,
                ]);

                // Attach assessors
                // $event->assessor()->attach($this->assessor);
                // $event->alatTes()->attach($this->alat_tes_id);
                
                // Menggunakan syncWithoutDetaching() agar tidak duplikasi
                $event->assessor()->syncWithoutDetaching(is_array($this->assessor) ? $this->assessor : []);
                // $event->alatTes()->syncWithoutDetaching(is_array($this->alat_tes_id) ? $this->alat_tes_id : []);
    
                DB::commit();
                session()->flash('toast', [
                    'type' => 'success',
                    'message' => 'berhasil tambah data'
                ]);
                
                $this->redirect(route('admin.event'), true);
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
            $this->dispatch('toast', ['type' => 'error', 'message' => 'terjadi kesalahan']);
        }
    }
}
