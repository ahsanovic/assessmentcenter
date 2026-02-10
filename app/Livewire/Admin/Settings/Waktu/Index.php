<?php

namespace App\Livewire\Admin\Settings\Waktu;

use App\Livewire\Forms\SettingWaktuTesForm;
use App\Models\RefJenisTes;
use App\Models\SettingWaktuTes;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.admin.app', ['title' => 'Waktu Tes'])]
class Index extends Component
{
    use WithPagination;

    public $selected_id;
    public $showModal = false;
    public $isUpdate = false;
    public SettingWaktuTesForm $form;

    #[Locked]
    public $editId;

    public function render()
    {
        $data = SettingWaktuTes::paginate(10);
        $jenis_tes_options = RefJenisTes::pluck('jenis_tes', 'id')->toArray();

        return view('livewire.admin.settings.waktu.index', compact('data', 'jenis_tes_options'));
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
            $data = SettingWaktuTes::findOrFail($id);
            $this->editId = $data->id;
            $this->form->jenis_tes = $data->jenis_tes;
            $this->form->waktu = $data->waktu;
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
                $data = SettingWaktuTes::find($this->editId);
                $old_data = $data->getOriginal();

                $data->jenis_tes = $this->form->jenis_tes;
                $data->waktu = $this->form->waktu;
                $data->is_active = $this->form->is_active;
                $data->save();

                activity_log($data, 'update', 'waktu-tes', $old_data);

                $this->dispatch('toast', ['type' => 'success', 'message' => 'berhasil ubah data']);
            } else {
                $data = SettingWaktuTes::create([
                    'jenis_tes' => $this->form->jenis_tes,
                    'waktu' => $this->form->waktu,
                    'is_active' => $this->form->is_active,
                ]);

                activity_log($data, 'create', 'waktu-tes');

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

    public function changeStatusTimerConfirmation($id)
    {
        $this->selected_id = $id;
        $this->dispatch('change-status-timer-confirmation');
    }

    #[On('changeStatusTimer')]
    public function changeStatusTimer()
    {
        try {
            $data = SettingWaktuTes::find($this->selected_id);
            if ($data->is_active === 'true') {
                $data->update(['is_active' => 'false']);
            } else {
                $data->update(['is_active' => 'true']);
            }

            $this->dispatch('toast', ['type' => 'success', 'message' => 'berhasil merubah status']);
        } catch (\Throwable $th) {
            // throw $th;
            $this->dispatch('toast', ['type' => 'error', 'message' => 'gagal merubah status']);
        }
    }

    #[On('delete')]
    public function destroy()
    {
        try {
            $data = SettingWaktuTes::find($this->selected_id);
            $old_data = $data->getOriginal();

            activity_log($data, 'delete', 'waktu-tes', $old_data);

            $data->delete();

            $this->dispatch('toast', ['type' => 'success', 'message' => 'berhasil menghapus data']);
        } catch (\Throwable $th) {
            // throw $th;
            $this->dispatch('toast', ['type' => 'error', 'message' => 'gagal menghapus data']);
        }
    }
}
