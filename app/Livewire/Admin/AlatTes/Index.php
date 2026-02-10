<?php

namespace App\Livewire\Admin\AlatTes;

use App\Livewire\Forms\AlatTesForm;
use App\Models\RefAlatTes;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.admin.app', ['title' => 'Alat Tes'])]
class Index extends Component
{
    use WithPagination;

    public $selected_id;
    public $showModal = false;
    public $isUpdate = false;
    public AlatTesForm $form;

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
        $data = RefAlatTes::when($this->search, function ($query) {
            $query->where('alat_tes', 'like', '%' . $this->search . '%');
        })
            ->orderByDesc('id')
            ->paginate(10);

        return view('livewire.admin.alat-tes.index', compact('data'));
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
            $data = RefAlatTes::findOrFail($id);
            $this->editId = $data->id;
            $this->form->alat_tes = $data->alat_tes;
            $this->form->definisi_aspek_potensi = $data->definisi_aspek_potensi;
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
                $data = RefAlatTes::findOrFail($this->editId);
                $old_data = $data->getOriginal();

                $data->alat_tes = $this->form->alat_tes;
                $data->definisi_aspek_potensi = $this->form->definisi_aspek_potensi;
                $data->save();

                activity_log($data, 'update', 'alat-tes', $old_data);

                $this->dispatch('toast', ['type' => 'success', 'message' => 'berhasil ubah data']);
            } else {
                $check_duplicate = RefAlatTes::where('alat_tes', $this->form->alat_tes)->exists();
                if ($check_duplicate) {
                    $this->dispatch('toast', ['type' => 'error', 'message' => 'data dengan nama alat tes ' . $this->form->alat_tes . ' sudah ada!']);
                    return;
                }

                $model = RefAlatTes::create([
                    'alat_tes' => $this->form->alat_tes,
                    'definisi_aspek_potensi' => $this->form->definisi_aspek_potensi,
                ]);

                activity_log($model, 'create', 'alat-tes');

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
            $data = RefAlatTes::find($this->selected_id);
            $old_data = $data->getOriginal();

            activity_log($data, 'delete', 'alat-tes', $old_data);

            $data->delete();

            $this->dispatch('toast', ['type' => 'success', 'message' => 'berhasil menghapus data']);
        } catch (\Throwable $th) {
            // throw $th;
            $this->dispatch('toast', ['type' => 'error', 'message' => 'gagal menghapus data']);
        }
    }
}
