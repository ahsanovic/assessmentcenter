<?php

namespace App\Livewire\Admin\Intelektual\SoalIntelektual\SubTes3;

use App\Models\Intelektual\RefModelIntelektual;
use App\Models\Intelektual\SoalIntelektual;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('components.layouts.admin.app', ['title' => 'Soal Sub Tes 3'])]
class Form extends Component
{
    use WithFileUploads;

    public $isUpdate = false;
    public $form = [
        'model_id' => null,
        'image_soal' => null,
        'image_opsi_a' => null,
        'image_opsi_b' => null,
        'image_opsi_c' => null,
        'image_opsi_d' => null,
        'image_opsi_e' => null,
        'kunci_jawaban' => null,
    ];

    #[Locked]
    public $id;

    public function mount($id = null)
    {
        try {
            if ($id) {
                $this->isUpdate = true;
                $data = SoalIntelektual::findOrFail($id);
                $this->id = $id;
                $this->form = [
                    'model_id' => $data->model_id,
                    'image_soal' => $data->image_soal,
                    'image_opsi_a' => $data->image_opsi_a,
                    'image_opsi_b' => $data->image_opsi_b,
                    'image_opsi_c' => $data->image_opsi_c,
                    'image_opsi_d' => $data->image_opsi_d,
                    'image_opsi_e' => $data->image_opsi_e,
                    'kunci_jawaban' => $data->kunci_jawaban,
                ];
            }
        } catch (\Throwable $th) {
            // throw $th;
            $this->dispatch('toast', ['type' => 'error', 'message' => 'terjadi kesalahan']);
        }
    }

    public function render()
    {
        $model_soal_options = RefModelIntelektual::pluck('jenis', 'id');
        return view('livewire.admin.intelektual.soal-subtes3.form', compact('model_soal_options'));
    }

    private function _storeImage($file, $oldFile = null, $folder = 'images-intelektual')
    {
        if (!$file || !($file instanceof \Illuminate\Http\UploadedFile)) {
            // jika tidak ada file baru, kembalikan file lama
            return $oldFile;
        }

        // hapus file lama kalau ada
        if ($oldFile && Storage::disk('public')->exists($oldFile)) {
            Storage::disk('public')->delete($oldFile);
        }

        // nama file random
        $filename = Str::random(40) . '.' . $file->getClientOriginalExtension();

        // simpan file baru
        return $file->storeAs($folder, $filename, 'public');
    }

    public function save()
    {
        $rules = [
            'form.model_id' => 'required',
            'form.kunci_jawaban' => 'required|in:A,B,C,D,E',
        ];

        $messages = [
            'form.model_id.required' => 'model soal wajib dipilih',
            'form.kunci_jawaban.required' => 'kunci jawaban wajib dipilih',
            'form.kunci_jawaban.in' => 'kunci jawaban tidak valid',
        ];

        $imageFields = [
            'image_soal',
            'image_opsi_a',
            'image_opsi_b',
            'image_opsi_c',
            'image_opsi_d',
            'image_opsi_e',
        ];

        foreach ($imageFields as $field) {
            $rules["form.$field"] = Rule::when(
                $this->form[$field] instanceof UploadedFile,
                ['image', 'max:512'],
                ['nullable']
            );
        }

        $this->validate($rules, $messages);

        try {
            if ($this->isUpdate) {
                $data = SoalIntelektual::findOrFail($this->id);
                $old_data = $data->getOriginal();

                $data->model_id = $this->form['model_id'];
                $data->kunci_jawaban = $this->form['kunci_jawaban'];

                // simpan file baru jika ada, hapus lama kalau diganti
                $data->image_soal   = $this->_storeImage($this->form['image_soal'], $data->image_soal);
                $data->image_opsi_a = $this->_storeImage($this->form['image_opsi_a'], $data->image_opsi_a);
                $data->image_opsi_b = $this->_storeImage($this->form['image_opsi_b'], $data->image_opsi_b);
                $data->image_opsi_c = $this->_storeImage($this->form['image_opsi_c'], $data->image_opsi_c);
                $data->image_opsi_d = $this->_storeImage($this->form['image_opsi_d'], $data->image_opsi_d);
                $data->image_opsi_e = $this->_storeImage($this->form['image_opsi_e'], $data->image_opsi_e);

                $data->save();

                activity_log($data, 'update', 'soal-intelektual-subtes3', $old_data);

                session()->flash('toast', [
                    'type' => 'success',
                    'message' => 'berhasil ubah data'
                ]);

                $this->redirect(route('admin.soal-intelektual-subtes3'), true);
            } else {
                $model = SoalIntelektual::create([
                    'model_id' => $this->form['model_id'],
                    'sub_tes' => 3,
                    'kunci_jawaban' => $this->form['kunci_jawaban'],
                    'image_soal'   => $this->_storeImage($this->form['image_soal']),
                    'image_opsi_a' => $this->_storeImage($this->form['image_opsi_a']),
                    'image_opsi_b' => $this->_storeImage($this->form['image_opsi_b']),
                    'image_opsi_c' => $this->_storeImage($this->form['image_opsi_c']),
                    'image_opsi_d' => $this->_storeImage($this->form['image_opsi_d']),
                    'image_opsi_e' => $this->_storeImage($this->form['image_opsi_e']),
                ]);

                activity_log($model, 'create', 'soal-intelektual-subtes3');

                session()->flash('toast', [
                    'type' => 'success',
                    'message' => 'berhasil tambah data'
                ]);

                $this->redirect(route('admin.soal-intelektual-subtes3'), true);
            }
        } catch (\Throwable $th) {
            // throw $th;
            $this->dispatch('toast', ['type' => 'error', 'message' => 'terjadi kesalahan']);
        }
    }

    public function deleteImage($field)
    {
        try {
            // Pastikan field yang dikirim valid
            $allowed = [
                'image_soal',
                'image_opsi_a',
                'image_opsi_b',
                'image_opsi_c',
                'image_opsi_d',
                'image_opsi_e',
            ];

            if (! in_array($field, $allowed)) {
                return;
            }

            // jika ada path lama, hapus filenya dari storage
            if (!empty($this->form[$field]) && !($this->form[$field] instanceof \Illuminate\Http\UploadedFile)) {
                Storage::disk('public')->delete($this->form[$field]);
            }

            // Reset field di form
            $this->form[$field] = null;

            $this->dispatch('toast', [
                'type' => 'success',
                'message' => 'Gambar berhasil dihapus'
            ]);
        } catch (\Throwable $th) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Gagal menghapus gambar'
            ]);
        }
    }
}
