<?php

namespace App\Livewire\Admin\Interpersonal\SoalInterpersonal;

use App\Livewire\Forms\SoalInterpersonalForm;
use App\Models\Interpersonal\RefInterpersonal;
use App\Models\Interpersonal\SoalInterpersonal;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.admin.app', ['title' => 'Soal Interpersonal'])]
class Index extends Component
{
    use WithPagination;

    public $jenis_indikator;
    public $selected_id;
    public $showModal = false;
    public $isUpdate = false;
    public SoalInterpersonalForm $form;
    public $editId;

    #[Url(as: 'q')]
    public ?string $search = '';

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedJenisIndikator()
    {
        $this->resetPage();
    }

    public function render()
    {
        $data = SoalInterpersonal::with('jenisIndikator')
            ->when($this->search, function ($query) {
                $query->where('soal', 'like', '%' . $this->search . '%');
            })
            ->when($this->jenis_indikator, function ($query, $jenis_indikator) {
                $query->where('jenis_indikator_id', $jenis_indikator);
            })
            ->orderByDesc('id')
            ->paginate(10);

        $indikator = RefInterpersonal::pluck('indikator_nama', 'id')->toArray();

        return view('livewire.admin.interpersonal.soal.index', compact('data', 'indikator'));
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
            $data = SoalInterpersonal::find($this->selected_id);
            $old_data = $data->getOriginal();

            activity_log($data, 'delete', 'soal-interpersonal', $old_data);

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
            $data = SoalInterpersonal::findOrFail($id);
            $this->editId = $data->id;
            $this->form->jenis_indikator_id = $data->jenis_indikator_id;
            $this->form->soal = $data->soal;
            $this->form->opsi_a = $data->opsi_a;
            $this->form->poin_opsi_a = $data->poin_opsi_a;
            $this->form->opsi_b = $data->opsi_b;
            $this->form->poin_opsi_b = $data->poin_opsi_b;
            $this->form->opsi_c = $data->opsi_c;
            $this->form->poin_opsi_c = $data->poin_opsi_c;
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
                $data = SoalInterpersonal::findOrFail($this->editId);
                $old_data = $data->getOriginal();

                $data->jenis_indikator_id = $this->form->jenis_indikator_id;
                $data->soal = $this->form->soal;
                $data->opsi_a = $this->form->opsi_a;
                $data->poin_opsi_a = $this->form->poin_opsi_a;
                $data->opsi_b = $this->form->opsi_b;
                $data->poin_opsi_b = $this->form->poin_opsi_b;
                $data->opsi_c = $this->form->opsi_c;
                $data->poin_opsi_c = $this->form->poin_opsi_c;
                $data->save();

                activity_log($data, 'update', 'soal-interpersonal', $old_data);
                $this->dispatch('toast', ['type' => 'success', 'message' => 'berhasil ubah data']);
            } else {
                $model = SoalInterpersonal::create([
                    'jenis_indikator_id' => $this->form->jenis_indikator_id,
                    'soal' => $this->form->soal,
                    'opsi_a' => $this->form->opsi_a,
                    'poin_opsi_a' => $this->form->poin_opsi_a,
                    'opsi_b' => $this->form->opsi_b,
                    'poin_opsi_b' => $this->form->poin_opsi_b,
                    'opsi_c' => $this->form->opsi_c,
                    'poin_opsi_c' => $this->form->poin_opsi_c,
                ]);

                activity_log($model, 'create', 'soal-interpersonal');
                $this->dispatch('toast', ['type' => 'success', 'message' => 'berhasil tambah data']);
            }
            $this->closeModal();
            $this->resetPage();
        } catch (\Throwable $th) {
            $this->dispatch('toast', ['type' => 'error', 'message' => 'terjadi kesalahan']);
        }
    }
}
