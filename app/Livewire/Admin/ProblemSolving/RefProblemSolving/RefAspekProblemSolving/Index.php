<?php

namespace App\Livewire\Admin\ProblemSolving\RefProblemSolving\RefAspekProblemSolving;

use App\Http\Requests\RefAspekProblemSolvingRequest;
use App\Models\ProblemSolving\RefAspekProblemSolving;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.admin.app', ['title' => 'Referensi Aspek Problem Solving'])]
class Index extends Component
{
    use WithPagination;

    public $selected_id;
    public $showModal = false;
    public $isUpdate = false;
    public $aspek;
    public $aspek_nomor;
    public $indikator_nomor = [];
    public $editId;

    public function render()
    {
        $data = RefAspekProblemSolving::paginate(10);

        return view('livewire.admin.problem-solving.referensi.aspek.index', compact('data'));
    }

    public function openModal()
    {
        $this->resetValidation();
        $this->reset(['aspek', 'aspek_nomor', 'indikator_nomor']);
        $this->isUpdate = false;
        $this->editId = null;
        $this->showModal = true;
        $this->dispatch('modalOpened');
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetValidation();
        $this->reset(['aspek', 'aspek_nomor', 'indikator_nomor']);
        $this->isUpdate = false;
        $this->editId = null;
    }

    public function edit($id)
    {
        try {
            $data = RefAspekProblemSolving::findOrFail($id);
            $this->editId = $data->id;
            $this->aspek = $data->aspek;
            $this->aspek_nomor = $data->aspek_nomor;
            $this->indikator_nomor = explode(",", $data->indikator_nomor);
            $this->isUpdate = true;
            $this->showModal = true;
            $this->resetValidation();
            $this->dispatch('modalOpened');
        } catch (\Throwable $th) {
            $this->dispatch('toast', ['type' => 'error', 'message' => 'terjadi kesalahan']);
        }
    }

    protected function rules()
    {
        $request = new RefAspekProblemSolvingRequest();
        return $request->rules();
    }

    protected function messages()
    {
        $request = new RefAspekProblemSolvingRequest();
        return $request->messages();
    }

    public function save()
    {
        $this->validate();
        try {
            if ($this->isUpdate) {
                $data = RefAspekProblemSolving::findOrFail($this->editId);
                $old_data = $data->getOriginal();

                $data->aspek = $this->aspek;
                $data->aspek_nomor = $this->aspek_nomor;
                $data->indikator_nomor = implode(',', $this->indikator_nomor);
                $data->save();

                activity_log($data, 'update', 'ref-aspek-problem-solving', $old_data);
                $this->dispatch('toast', ['type' => 'success', 'message' => 'berhasil ubah data']);
            } else {
                $check_duplicate = RefAspekProblemSolving::where('aspek_nomor', $this->aspek_nomor)->exists();
                if ($check_duplicate) {
                    $this->dispatch('toast', ['type' => 'error', 'message' => 'data dengan nomor aspek ' . $this->aspek_nomor . ' sudah ada!']);
                    return;
                }

                $model = new RefAspekProblemSolving();
                $model->aspek = $this->aspek;
                $model->aspek_nomor = $this->aspek_nomor;
                $model->indikator_nomor = implode(',', $this->indikator_nomor);
                $model->save();

                activity_log($model, 'create', 'ref-aspek-problem-solving');
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
            $data = RefAspekProblemSolving::find($this->selected_id);
            $old_data = $data->getOriginal();

            activity_log($data, 'delete', 'ref-aspek-problem-solving', $old_data);

            $data->delete();

            $this->dispatch('toast', ['type' => 'success', 'message' => 'berhasil menghapus data']);
        } catch (\Throwable $th) {
            // throw $th;
            $this->dispatch('toast', ['type' => 'error', 'message' => 'gagal menghapus data']);
        }
    }
}
