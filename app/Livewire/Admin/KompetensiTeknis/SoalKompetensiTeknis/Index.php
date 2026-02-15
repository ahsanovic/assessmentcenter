<?php

namespace App\Livewire\Admin\KompetensiTeknis\SoalKompetensiTeknis;

use App\Livewire\Forms\SoalKompetensiTeknisForm;
use App\Models\KompetensiTeknis\SoalKompetensiTeknis;
use App\Models\RefJabatanDiuji;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.admin.app', ['title' => 'Kompetensi Teknis'])]
class Index extends Component
{
    use WithPagination;

    public $selected_id;
    public $jenis_jabatan;
    public $showModal = false;
    public $isUpdate = false;
    public SoalKompetensiTeknisForm $form;
    public $editId;

    #[Url(as: 'q')]
    public ?string $search = '';

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedJenisJabatan()
    {
        $this->resetPage();
    }

    public function render()
    {
        $jenis_jabatan_options = RefJabatanDiuji::pluck('jenis', 'id');

        $data = SoalKompetensiTeknis::when($this->search, function ($query) {
            $query->where('soal', 'like', '%' . $this->search . '%');
        })
            ->when($this->jenis_jabatan, function ($query) {
                $query->where('jenis_jabatan_id', $this->jenis_jabatan);
            })
            ->orderByDesc('id')
            ->paginate(10);

        return view('livewire.admin.kompetensi-teknis.soal.index', compact('data', 'jenis_jabatan_options'));
    }

    public function resetFilters()
    {
        $this->reset();
        $this->resetPage();
        $this->render();
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
            $data = SoalKompetensiTeknis::find($this->selected_id);
            $old_data = $data->getOriginal();

            activity_log($data, 'delete', 'soal-kompetensi-teknis', $old_data);

            $data->delete();

            $this->dispatch('toast', ['type' => 'success', 'message' => 'berhasil menghapus data']);
        } catch (\Throwable $th) {
            // throw $th;
            $this->dispatch('toast', ['type' => 'error', 'message' => 'gagal menghapus data']);
        }
    }

    public function openModal()
    {
        $this->resetValidation();
        $this->form->reset();
        $this->isUpdate = false;
        $this->editId = null;
        $this->showModal = true;
        $this->dispatch('modalOpened');
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetValidation();
        $this->form->reset();
        $this->isUpdate = false;
        $this->editId = null;
    }

    public function edit($id)
    {
        try {
            $data = SoalKompetensiTeknis::findOrFail($id);
            $this->editId = $data->id;
            $this->form->jenis_jabatan = $data->jenis_jabatan_id;
            $this->form->soal = $data->soal;
            $this->form->opsi_a = $data->opsi_a;
            $this->form->opsi_b = $data->opsi_b;
            $this->form->opsi_c = $data->opsi_c;
            $this->form->opsi_d = $data->opsi_d;
            $this->form->kunci_jawaban = $data->kunci_jawaban;
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
                $data = SoalKompetensiTeknis::findOrFail($this->editId);
                $old_data = $data->getOriginal();

                $data->jenis_jabatan_id = $this->form->jenis_jabatan;
                $data->soal = $this->form->soal;
                $data->opsi_a = $this->form->opsi_a;
                $data->opsi_b = $this->form->opsi_b;
                $data->opsi_c = $this->form->opsi_c;
                $data->opsi_d = $this->form->opsi_d;
                $data->kunci_jawaban = $this->form->kunci_jawaban;
                $data->save();

                activity_log($data, 'update', 'soal-kompetensi-teknis', $old_data);
                $this->dispatch('toast', ['type' => 'success', 'message' => 'berhasil ubah data']);
            } else {
                $model = SoalKompetensiTeknis::create([
                    'jenis_jabatan_id' => $this->form->jenis_jabatan,
                    'soal' => $this->form->soal,
                    'opsi_a' => $this->form->opsi_a,
                    'opsi_b' => $this->form->opsi_b,
                    'opsi_c' => $this->form->opsi_c,
                    'opsi_d' => $this->form->opsi_d,
                    'kunci_jawaban' => $this->form->kunci_jawaban,
                ]);

                activity_log($model, 'create', 'soal-kompetensi-teknis');
                $this->dispatch('toast', ['type' => 'success', 'message' => 'berhasil tambah data']);
            }
            $this->closeModal();
            $this->resetPage();
        } catch (\Throwable $th) {
            $this->dispatch('toast', ['type' => 'error', 'message' => 'terjadi kesalahan']);
        }
    }
}
