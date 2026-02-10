<?php

namespace App\Livewire\Admin\Kuesioner;

use App\Livewire\Forms\KuesionerForm;
use App\Models\Kuesioner;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.admin.app', ['title' => 'Kuesioner'])]
class Index extends Component
{
    use WithPagination;

    public $selected_id;
    public $is_active;
    public $is_esai;
    public $showModal = false;
    public $isUpdate = false;
    public KuesionerForm $form;

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
        $data = Kuesioner::when($this->search, function ($query) {
            $query->where('deskripsi', 'like', '%' . $this->search . '%');
        })
            ->when($this->is_esai, function ($query) {
                $query->where('is_esai', $this->is_esai);
            })
            ->when($this->is_active, function ($query) {
                $query->where('is_active', $this->is_active);
            })
            ->orderByDesc('id')
            ->paginate(10);

        return view('livewire.admin.kuesioner.index', compact('data'));
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
            $data = Kuesioner::findOrFail($id);
            $this->editId = $data->id;
            $this->form->deskripsi = $data->deskripsi;
            $this->form->is_esai = $data->is_esai;
            $this->form->is_active = $data->is_active;
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
                $data = Kuesioner::findOrFail($this->editId);
                $old_data = $data->getOriginal();

                $data->deskripsi = $this->form->deskripsi;
                $data->is_esai = $this->form->is_esai;
                $data->is_active = $this->form->is_active;
                $data->save();

                activity_log($data, 'update', 'kuesioner', $old_data);

                $this->dispatch('toast', ['type' => 'success', 'message' => 'berhasil ubah data']);
            } else {
                $data = Kuesioner::create([
                    'deskripsi' => $this->form->deskripsi,
                    'is_esai' => $this->form->is_esai,
                    'is_active' => $this->form->is_active,
                ]);

                activity_log($data, 'create', 'kuesioner');

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
            $data = Kuesioner::find($this->selected_id);
            $old_data = $data->getOriginal();

            activity_log($data, 'delete', 'kuesioner', $old_data);

            $data->delete();

            $this->dispatch('toast', ['type' => 'success', 'message' => 'berhasil menghapus data']);
        } catch (\Throwable $th) {
            // throw $th;
            $this->dispatch('toast', ['type' => 'error', 'message' => 'gagal menghapus data']);
        }
    }
}
