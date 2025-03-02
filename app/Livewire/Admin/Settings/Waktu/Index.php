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
        $data = SettingWaktuTes::get();

        return view('livewire.admin.settings.waktu.index', compact('data'));
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
            SettingWaktuTes::find($this->selected_id)->delete();

            $this->dispatch('toast', ['type' => 'success', 'message' => 'berhasil menghapus data']);
        } catch (\Throwable $th) {
            // throw $th;
            $this->dispatch('toast', ['type' => 'error', 'message' => 'gagal menghapus data']);
        }
    }
}
