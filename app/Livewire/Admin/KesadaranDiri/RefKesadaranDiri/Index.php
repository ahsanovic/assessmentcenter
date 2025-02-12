<?php

namespace App\Livewire\Admin\KesadaranDiri\RefKesadaranDiri;

use App\Models\KesadaranDiri\RefKesadaranDiri;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.admin.app', ['title' => 'Referensi Kecerdasan Emosi'])]
class Index extends Component
{
    use WithPagination;

    public $selected_id;
    
    public function render()
    {
        $data = RefKesadaranDiri::paginate(10);
        
        return view('livewire.admin.kesadaran-diri.referensi.index', compact('data'));
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
            RefKesadaranDiri::find($this->selected_id)->delete();

            $this->dispatch('toast', ['type' => 'success', 'message' => 'berhasil menghapus data']);
        } catch (\Throwable $th) {
            // throw $th;
            $this->dispatch('toast', ['type' => 'error', 'message' => 'gagal menghapus data']);
        }
    }
}
