<?php

namespace App\Livewire\Admin\Pspk\Ref;

use App\Livewire\Forms\DescPspkForm;
use App\Models\Pspk\RefDescPspk;
use App\Models\Pspk\RefLevelPspk;
use App\Models\RefAspekPspk;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.admin.app', ['title' => 'Referensi Deskripsi PSPK'])]
class Index extends Component
{
    use WithPagination;

    public $selected_id;
    public $aspek_id;
    public $level_pspk;
    public $showModal = false;
    public $isUpdate = false;
    public DescPspkForm $form;
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

        $data = RefDescPspk::when($this->search, function ($query) {
            $query->where('deskripsi', 'like', '%' . $this->search . '%')
                ->orWhere('deskripsi_min', 'like', '%' . $this->search . '%')
                ->orWhere('deskripsi_plus', 'like', '%' . $this->search . '%');
        })
            ->when($this->level_pspk, function ($query) {
                $query->where('level_pspk', $this->level_pspk);
            })
            ->when($this->aspek_id, function ($query) {
                $query->where('aspek_id', $this->aspek_id);
            })
            ->orderByDesc('id')
            ->paginate(10);

        return view('livewire.admin.pspk.ref-deskripsi.index', compact('data', 'aspek_options', 'level_pspk_options'));
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
            $data = RefDescPspk::findOrFail($id);
            $this->editId = $data->id;
            $this->form->level_pspk = $data->level_pspk;
            $this->form->aspek = $data->aspek_id;
            $this->form->deskripsi_min = $data->deskripsi_min;
            $this->form->deskripsi = $data->deskripsi;
            $this->form->deskripsi_plus = $data->deskripsi_plus;
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
                $data = RefDescPspk::findOrFail($this->editId);
                $old_data = $data->getOriginal();

                $data->level_pspk = $this->form->level_pspk;
                $data->aspek_id = $this->form->aspek;
                $data->deskripsi_min = $this->form->deskripsi_min;
                $data->deskripsi = $this->form->deskripsi;
                $data->deskripsi_plus = $this->form->deskripsi_plus;
                $data->save();

                activity_log($data, 'update', 'ref-pspk', $old_data);
                $this->dispatch('toast', ['type' => 'success', 'message' => 'berhasil ubah data']);
            } else {
                $model = RefDescPspk::create([
                    'level_pspk' => $this->form->level_pspk,
                    'aspek_id' => $this->form->aspek,
                    'deskripsi_min' => $this->form->deskripsi_min,
                    'deskripsi' => $this->form->deskripsi,
                    'deskripsi_plus' => $this->form->deskripsi_plus,
                ]);

                activity_log($model, 'create', 'ref-pspk');
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
            $data = RefDescPspk::find($this->selected_id);
            $old_data = $data->getOriginal();

            activity_log($data, 'delete', 'ref-pspk', $old_data);

            $data->delete();

            $this->dispatch('toast', ['type' => 'success', 'message' => 'berhasil menghapus data']);
        } catch (\Throwable $th) {
            // throw $th;
            $this->dispatch('toast', ['type' => 'error', 'message' => 'gagal menghapus data']);
        }
    }
}
