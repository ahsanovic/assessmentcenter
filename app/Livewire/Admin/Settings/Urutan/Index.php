<?php

namespace App\Livewire\Admin\Settings\Urutan;

use App\Livewire\Forms\SettingUrutanForm;
use App\Models\RefAlatTes;
use App\Models\Settings;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.admin.app', ['title' => 'Urutan Tes'])]
class Index extends Component
{
    use WithPagination;

    public $selected_id;
    public $alat_tes;
    public $showModal = false;
    public $isUpdate = false;
    public SettingUrutanForm $form;

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
        $data = Settings::with('alatTes')->when($this->alat_tes, function ($query) {
            $query->where('alat_tes_id', $this->alat_tes);
        })
            ->orderByDesc('id')
            ->paginate(10);

        $option_alat_tes = RefAlatTes::pluck('alat_tes', 'id');

        return view('livewire.admin.settings.urutan.index', compact('data', 'option_alat_tes'));
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
            $data = Settings::findOrFail($id);
            $this->editId = $data->id;
            $this->form->alat_tes_id = $data->alat_tes_id;
            $this->form->urutan = $data->urutan;
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
                $check_duplicate = Settings::where('urutan', $this->form->urutan)
                    ->where('alat_tes_id', '=', $this->form->alat_tes_id)
                    ->where('id', '!=', $this->editId)
                    ->exists();
                if ($check_duplicate) {
                    $this->dispatch('toast', ['type' => 'error', 'message' => 'data dengan urutan tes ' . $this->form->urutan . ' dan alat tes ' . RefAlatTes::find($this->form->alat_tes_id)->alat_tes . ' sudah ada!']);
                    return;
                }

                $data = Settings::find($this->editId);
                $old_data = $data->getOriginal();

                $data->alat_tes_id = $this->form->alat_tes_id;
                $data->urutan = $this->form->urutan;
                $data->save();

                activity_log($data, 'update', 'urutan-tes', $old_data);

                $this->dispatch('toast', ['type' => 'success', 'message' => 'berhasil ubah data']);
            } else {
                $data = Settings::create([
                    'alat_tes_id' => $this->form->alat_tes_id,
                    'urutan' => $this->form->urutan,
                ]);

                activity_log($data, 'create', 'urutan-tes');

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
            $data = Settings::find($this->selected_id);
            $old_data = $data->getOriginal();

            activity_log($data, 'delete', 'urutan-tes', $old_data);

            $data->delete();

            $this->dispatch('toast', ['type' => 'success', 'message' => 'berhasil menghapus data']);
        } catch (\Throwable $th) {
            // throw $th;
            $this->dispatch('toast', ['type' => 'error', 'message' => 'gagal menghapus data']);
        }
    }
}
