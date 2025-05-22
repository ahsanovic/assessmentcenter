<?php

namespace App\Livewire\Admin\TtdLaporan;

use App\Models\Event;
use App\Models\TtdLaporan;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('components.layouts.admin.app', ['title' => 'Ttd Laporan Penilaian'])]
class Form extends Component
{
    use WithFileUploads;

    public $isUpdate = false;
    public $ttd_url;
    public $ttd;
    public $nama;
    public $nip;
    public $is_active;

    #[Locked]
    public $id;

    public function mount($id = null)
    {
        try {
            if ($id) {
                $this->isUpdate = true;

                $data = TtdLaporan::findOrFail($id);
                $this->id = $data->id;
                $this->nama = $data->nama;
                $this->nip = $data->nip;
                $this->is_active = $data->is_active;
            }
        } catch (\Throwable $th) {
            // throw $th;
            $this->dispatch('toast', ['type' => 'error', 'message' => 'terjadi kesalahan']);
        }
    }

    public function render()
    {
        $event = Event::pluck('nama_event', 'id');

        return view('livewire.admin.ttd-laporan.form', compact('event'));
    }

    protected function rules()
    {
        return [
            'nama' => 'required',
            'nip' => 'required|numeric|digits:18',
            'ttd' => $this->isUpdate
                ? 'nullable|image|mimes:jpeg,png,jpg|max:200'
                : 'required|image|mimes:jpeg,png,jpg|max:200',
        ];
    }

    protected function messages()
    {
        return [
            'nama.required' => 'harus diisi',
            'nip.required' => 'harus diisi',
            'nip.numeric' => 'harus berupa angka',
            'nip.digits' => 'panjang karakter harus 18',
            'ttd.required' => 'harus diisi',
            'ttd.image' => 'file harus berupa gambar',
            'ttd.mimes' => 'file harus berupa gambar dengan format jpeg, png, jpg',
            'ttd.max' => 'file maksimal 200 KB'
        ];
    }

    public function updatedTtd()
    {
        if ($this->ttd) {
            $this->ttd_url = $this->ttd->temporaryUrl();
        }
    }

    public function save()
    {
        $this->validate();
        try {
            if ($this->isUpdate) {
                $data = TtdLaporan::findOrFail($this->id);
                $old_data = $data->getOriginal();

                // Hapus file lama jika ada
                if ($this->ttd) {
                    if ($data->ttd && Storage::disk('public')->exists($data->ttd)) {
                        Storage::disk('public')->delete($data->ttd);
                    }

                    $path = $this->ttd->storeAs('tte', uniqid() . '.' . $this->ttd->extension(), 'public');
                }

                $data->nama = $this->nama;
                $data->nip = $this->nip;
                $data->is_active = $this->is_active;
                $data->ttd = $this->ttd ? 'tte/' . basename($path) : $data->ttd;
                $data->save();

                activity_log($data, 'update', 'ttd-laporan', $old_data);

                session()->flash('toast', [
                    'type' => 'success',
                    'message' => 'berhasil ubah data'
                ]);

                $this->redirect(route('admin.ttd-laporan'), true);
            } else {
                $path = $this->ttd->storeAs('tte', uniqid() . '.' . $this->ttd->extension(), 'public');

                $data = TtdLaporan::create([
                    'nama' => $this->nama,
                    'nip' => $this->nip,
                    'ttd' => 'tte/' . basename($path),
                ]);

                activity_log($data, 'create', 'ttd-laporan');

                session()->flash('toast', [
                    'type' => 'success',
                    'message' => 'berhasil tambah data'
                ]);

                $this->redirect(route('admin.ttd-laporan'), true);
            }
        } catch (\Throwable $th) {
            // throw $th;
            $this->dispatch('toast', ['type' => 'error', 'message' => 'terjadi kesalahan']);
        }
    }
}
