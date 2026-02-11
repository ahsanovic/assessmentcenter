<?php

namespace App\Livewire\Admin\ProblemSolving\SoalProblemSolving;

use App\Livewire\Forms\SoalProblemSolvingForm;
use App\Models\ProblemSolving\RefAspekProblemSolving;
use App\Models\ProblemSolving\RefIndikatorProblemSolving;
use App\Models\ProblemSolving\SoalProblemSolving;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.admin.app', ['title' => 'Soal Problem Solving'])]
class Index extends Component
{
    use WithPagination;

    public $jenis_indikator;
    public $aspek;
    public $selected_id;
    public $showModal = false;
    public $isUpdate = false;
    public SoalProblemSolvingForm $form;
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
        $data = SoalProblemSolving::with('aspek')
            ->when($this->search, function ($query) {
                $query->where('soal', 'like', '%' . $this->search . '%');
            })
            ->when($this->aspek, function ($query, $aspek) {
                $query->where('aspek_id', $aspek);
            })
            ->orderByDesc('id')
            ->paginate(10);

        $aspek_option = RefAspekProblemSolving::pluck('aspek', 'id')->toArray();
        $indikator_option = RefIndikatorProblemSolving::pluck('indikator_nama', 'indikator_nomor')->toArray();

        return view('livewire.admin.problem-solving.soal.index', compact('data', 'aspek_option', 'indikator_option'));
    }

    public function resetFilters()
    {
        $this->reset();
        $this->resetPage();
        $this->render();
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
            $data = SoalProblemSolving::findOrFail($id);
            $this->editId = $data->id;
            $this->form->aspek_id = $data->aspek_id;
            $this->form->indikator_nomor = $data->indikator_nomor;
            $this->form->soal = $data->soal;
            $this->form->opsi_a = $data->opsi_a;
            $this->form->poin_opsi_a = $data->poin_opsi_a;
            $this->form->opsi_b = $data->opsi_b;
            $this->form->poin_opsi_b = $data->poin_opsi_b;
            $this->form->opsi_c = $data->opsi_c;
            $this->form->poin_opsi_c = $data->poin_opsi_c;
            $this->form->opsi_d = $data->opsi_d;
            $this->form->poin_opsi_d = $data->poin_opsi_d;
            $this->form->opsi_e = $data->opsi_e;
            $this->form->poin_opsi_e = $data->poin_opsi_e;
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
                $data = SoalProblemSolving::findOrFail($this->editId);
                $old_data = $data->getOriginal();

                $data->aspek_id = $this->form->aspek_id;
                $data->indikator_nomor = $this->form->indikator_nomor;
                $data->soal = $this->form->soal;
                $data->opsi_a = $this->form->opsi_a;
                $data->poin_opsi_a = $this->form->poin_opsi_a;
                $data->opsi_b = $this->form->opsi_b;
                $data->poin_opsi_b = $this->form->poin_opsi_b;
                $data->opsi_c = $this->form->opsi_c;
                $data->poin_opsi_c = $this->form->poin_opsi_c;
                $data->opsi_d = $this->form->opsi_d;
                $data->poin_opsi_d = $this->form->poin_opsi_d;
                $data->opsi_e = $this->form->opsi_e;
                $data->poin_opsi_e = $this->form->poin_opsi_e;
                $data->save();

                activity_log($data, 'update', 'soal-problem-solving', $old_data);
                $this->dispatch('toast', ['type' => 'success', 'message' => 'berhasil ubah data']);
            } else {
                $model = SoalProblemSolving::create([
                    'aspek_id' => $this->form->aspek_id,
                    'indikator_nomor' => $this->form->indikator_nomor,
                    'soal' => $this->form->soal,
                    'opsi_a' => $this->form->opsi_a,
                    'poin_opsi_a' => $this->form->poin_opsi_a,
                    'opsi_b' => $this->form->opsi_b,
                    'poin_opsi_b' => $this->form->poin_opsi_b,
                    'opsi_c' => $this->form->opsi_c,
                    'poin_opsi_c' => $this->form->poin_opsi_c,
                    'opsi_d' => $this->form->opsi_d,
                    'poin_opsi_d' => $this->form->poin_opsi_d,
                    'opsi_e' => $this->form->opsi_e,
                    'poin_opsi_e' => $this->form->poin_opsi_e,
                ]);

                activity_log($model, 'create', 'soal-problem-solving');
                $this->dispatch('toast', ['type' => 'success', 'message' => 'berhasil tambah data']);
            }
            $this->closeModal();
            $this->resetPage();
        } catch (\Throwable $th) {
            $this->dispatch('toast', ['type' => 'error', 'message' => 'terjadi kesalahan']);
        }
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
            $data = SoalProblemSolving::find($this->selected_id);
            $old_data = $data->getOriginal();

            activity_log($data, 'delete', 'soal-problem-solving', $old_data);

            $data->delete();

            $this->dispatch('toast', ['type' => 'success', 'message' => 'berhasil menghapus data']);
        } catch (\Throwable $th) {
            // throw $th;
            $this->dispatch('toast', ['type' => 'error', 'message' => 'gagal menghapus data']);
        }
    }
}
