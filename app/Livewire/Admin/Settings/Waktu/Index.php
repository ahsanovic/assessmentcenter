<?php

namespace App\Livewire\Admin\Settings\Waktu;

use App\Models\SettingWaktuTes;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.admin.app', ['title' => 'Waktu Tes'])]
class Index extends Component
{
    use WithPagination;

    public $selected_id;

    public function render()
    {
        $data = SettingWaktuTes::paginate(10);

        return view('livewire.admin.settings.waktu.index', compact('data'));
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
