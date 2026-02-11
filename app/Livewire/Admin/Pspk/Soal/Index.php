<?php

namespace App\Livewire\Admin\Pspk\Soal;

use App\Livewire\Forms\SoalPspkForm;
use App\Models\Pspk\RefLevelPspk;
use App\Models\Pspk\SoalPspk;
use App\Models\RefAspekPspk;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.admin.app', ['title' => 'PSPK'])]
class Index extends Component
{
    use WithPagination;

    public $selected_id;
    public $aspek_id;
    public $level_pspk_id;
    public $showModal = false;
    public $isUpdate = false;
    public SoalPspkForm $form;
    public $editId;

    #[Url(as: 'q')]
    public ?string $search = '';

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $aspek_options = RefAspekPspk::pluck('nama_aspek', 'id');
        $level_pspk_options = RefLevelPspk::pluck('level_pspk', 'id');

        $data = SoalPspk::when($this->search, function ($query) {
            $query->where('soal', 'like', '%' . $this->search . '%');
        })
            ->when($this->level_pspk_id, function ($query) {
                $query->where('level_pspk_id', $this->level_pspk_id);
            })
            ->when($this->aspek_id, function ($query) {
                $query->where('aspek_id', $this->aspek_id);
            })
            ->orderByDesc('id')
            ->paginate(10);

        return view('livewire.admin.pspk.soal.index', compact('data', 'aspek_options', 'level_pspk_options'));
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
            $data = SoalPspk::findOrFail($id);
            $this->editId = $data->id;
            $this->form->level_pspk_id = $data->level_pspk_id;
            $this->form->aspek = $data->aspek_id;
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
                $data = SoalPspk::findOrFail($this->editId);
                $old_data = $data->getOriginal();

                $data->level_pspk_id = $this->form->level_pspk_id;
                $data->aspek_id = $this->form->aspek;
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
                $data->kunci_jawaban = $this->form->kunci_jawaban;
                $data->save();

                activity_log($data, 'update', 'soal-pspk', $old_data);
                $this->dispatch('toast', ['type' => 'success', 'message' => 'berhasil ubah data']);
            } else {
                $model = SoalPspk::create([
                    'level_pspk_id' => $this->form->level_pspk_id,
                    'aspek_id' => $this->form->aspek,
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
                    'kunci_jawaban' => $this->form->kunci_jawaban,
                ]);

                activity_log($model, 'create', 'soal-pspk');
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
            $data = SoalPspk::find($this->selected_id);
            $old_data = $data->getOriginal();

            activity_log($data, 'delete', 'soal-pspk', $old_data);

            $data->delete();

            $this->dispatch('toast', ['type' => 'success', 'message' => 'berhasil menghapus data']);
        } catch (\Throwable $th) {
            // throw $th;
            $this->dispatch('toast', ['type' => 'error', 'message' => 'gagal menghapus data']);
        }
    }
}
