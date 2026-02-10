<?php

namespace App\Livewire\Admin\MetodeTes;

use App\Livewire\Forms\MetodeTesForm;
use App\Models\RefMetodeTes;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.admin.app', ['title' => 'Metode Tes'])]
class Index extends Component
{
    use WithPagination;

    public $selected_id;
    public $showModal = false;
    public $isUpdate = false;
    public MetodeTesForm $form;

    #[Locked]
    public $editId;

    #[Url(as: 'q')]
    public ?string $search = '';

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $data = RefMetodeTes::when($this->search, function ($query) {
            $query->where('metode_tes', 'like', '%' . $this->search . '%');
        })
            ->paginate(10);

        return view('livewire.admin.metode-tes.index', compact('data'));
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
            $data = RefMetodeTes::findOrFail($id);
            $this->editId = $data->id;
            $this->form->metode_tes = $data->metode_tes;
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
                $data = RefMetodeTes::findOrFail($this->editId);
                $old_data = $data->getOriginal();
                $data->metode_tes = $this->form->metode_tes;
                $data->save();

                activity_log($data, 'update', 'metode-tes', $old_data);

                $this->dispatch('toast', ['type' => 'success', 'message' => 'berhasil ubah data']);
            } else {
                $check_duplicate = RefMetodeTes::where('metode_tes', $this->form->metode_tes)->exists();
                if ($check_duplicate) {
                    $this->dispatch('toast', ['type' => 'error', 'message' => 'metode tes ' . $this->form->metode_tes . ' sudah ada!']);
                    return;
                }

                $data = RefMetodeTes::create([
                    'metode_tes' => $this->form->metode_tes,
                ]);

                activity_log($data, 'create', 'metode-tes');

                $this->dispatch('toast', ['type' => 'success', 'message' => 'berhasil tambah data']);
            }

            $this->closeModal();
            $this->resetPage();
        } catch (\Throwable $th) {
            // throw $th;
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
            $data = RefMetodeTes::find($this->selected_id);
            $old_data = $data->getOriginal();

            activity_log($data, 'delete', 'metode-tes', $old_data);

            $data->delete();

            $this->dispatch('toast', ['type' => 'success', 'message' => 'berhasil menghapus data']);
        } catch (\Throwable $th) {
            // throw $th;
            $this->dispatch('toast', ['type' => 'error', 'message' => 'gagal menghapus data']);
        }
    }
}
