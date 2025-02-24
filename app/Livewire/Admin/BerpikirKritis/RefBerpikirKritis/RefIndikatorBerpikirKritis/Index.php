<?php

namespace App\Livewire\Admin\BerpikirKritis\RefBerpikirKritis\RefIndikatorBerpikirKritis;

use App\Models\BerpikirKritis\RefIndikatorBerpikirKritis;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.admin.app', ['title' => 'Referensi Indikator Berpikir Kritis dan Strategis'])]
class Index extends Component
{
    use WithPagination;

    public $selected_id;
    
    public function render()
    {
        $data = RefIndikatorBerpikirKritis::paginate(10);
        
        return view('livewire.admin.berpikir-kritis.referensi.indikator.index', compact('data'));
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
            RefIndikatorBerpikirKritis::find($this->selected_id)->delete();

            $this->dispatch('toast', ['type' => 'success', 'message' => 'berhasil menghapus data']);
        } catch (\Throwable $th) {
            // throw $th;
            $this->dispatch('toast', ['type' => 'error', 'message' => 'gagal menghapus data']);
        }
    }
}
