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
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

#[Layout('components.layouts.admin.app', ['title' => 'Soal Sub Tes 3'])]
class Index extends Component
{
    use WithPagination, WithFileUploads;

    public $selected_id;
    public $selectedSoal; // untuk detail modal
    public $showDetailModal = false;
    public $showModal = false;
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
    public $editId;

    public function render()
    {
        $data = SoalIntelektual::where('sub_tes', 3)->paginate(10);
        $model_soal_options = RefModelIntelektual::pluck('jenis', 'id');

        return view('livewire.admin.intelektual.soal-subtes3.index', compact('data', 'model_soal_options'));
    }

    public function openModal()
    {
        $this->resetValidation();
        $this->form = [
            'model_id' => null,
            'image_soal' => null,
            'image_opsi_a' => null,
            'image_opsi_b' => null,
            'image_opsi_c' => null,
            'image_opsi_d' => null,
            'image_opsi_e' => null,
            'kunci_jawaban' => null,
        ];
        $this->isUpdate = false;
        $this->editId = null;
        $this->showModal = true;
        $this->dispatch('modalOpened');
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetValidation();
        $this->isUpdate = false;
        $this->editId = null;
    }

    public function edit($id)
    {
        try {
            $data = SoalIntelektual::findOrFail($id);
            $this->editId = $data->id;
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
            $this->isUpdate = true;
            $this->showModal = true;
            $this->resetValidation();
            $this->dispatch('modalOpened');
        } catch (\Throwable $th) {
            $this->dispatch('toast', ['type' => 'error', 'message' => 'terjadi kesalahan']);
        }
    }

    private function _storeImage($file, $oldFile = null, $folder = 'images-intelektual')
    {
        if (!$file || !($file instanceof \Illuminate\Http\UploadedFile)) {
            return $oldFile;
        }

        if ($oldFile && Storage::disk('public')->exists($oldFile)) {
            Storage::disk('public')->delete($oldFile);
        }

        $filename = Str::random(40) . '.' . $file->getClientOriginalExtension();
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
                $data = SoalIntelektual::findOrFail($this->editId);
                $old_data = $data->getOriginal();

                $data->model_id = $this->form['model_id'];
                $data->kunci_jawaban = $this->form['kunci_jawaban'];

                $data->image_soal   = $this->_storeImage($this->form['image_soal'], $data->image_soal);
                $data->image_opsi_a = $this->_storeImage($this->form['image_opsi_a'], $data->image_opsi_a);
                $data->image_opsi_b = $this->_storeImage($this->form['image_opsi_b'], $data->image_opsi_b);
                $data->image_opsi_c = $this->_storeImage($this->form['image_opsi_c'], $data->image_opsi_c);
                $data->image_opsi_d = $this->_storeImage($this->form['image_opsi_d'], $data->image_opsi_d);
                $data->image_opsi_e = $this->_storeImage($this->form['image_opsi_e'], $data->image_opsi_e);

                $data->save();

                activity_log($data, 'update', 'soal-intelektual-subtes3', $old_data);

                $this->dispatch('toast', ['type' => 'success', 'message' => 'berhasil ubah data']);
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

                $this->dispatch('toast', ['type' => 'success', 'message' => 'berhasil tambah data']);
            }

            $this->closeModal();
            $this->resetPage();
        } catch (\Throwable $th) {
            // throw $th;
            $this->dispatch('toast', ['type' => 'error', 'message' => 'terjadi kesalahan']);
        }
    }

    public function deleteImage($field)
    {
        try {
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

            if (!empty($this->form[$field]) && !($this->form[$field] instanceof \Illuminate\Http\UploadedFile)) {
                Storage::disk('public')->delete($this->form[$field]);
            }

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

    public function deleteConfirmation($id)
    {
        $this->selected_id = $id;
        $this->dispatch('show-delete-confirmation');
    }

    public function showDetail($id)
    {
        $this->selectedSoal = SoalIntelektual::with('modelSoal')->findOrFail($id);
        $this->showDetailModal = true;
    }

    #[On('delete')]
    public function destroy()
    {
        try {
            $data = SoalIntelektual::find($this->selected_id);
            $old_data = $data->getOriginal();

            $fields = [
                'image_soal',
                'image_opsi_a',
                'image_opsi_b',
                'image_opsi_c',
                'image_opsi_d',
                'image_opsi_e',
            ];

            foreach ($fields as $field) {
                if (!empty($data->$field) && Storage::disk('public')->exists($data->$field)) {
                    Storage::disk('public')->delete($data->$field);
                }
            }

            activity_log($data, 'delete', 'soal-intelektual-subtes3', $old_data);

            $data->delete();

            $this->dispatch('toast', ['type' => 'success', 'message' => 'berhasil menghapus data']);
        } catch (\Throwable $th) {
            // throw $th;
            $this->dispatch('toast', ['type' => 'error', 'message' => 'gagal menghapus data']);
        }
    }
}
