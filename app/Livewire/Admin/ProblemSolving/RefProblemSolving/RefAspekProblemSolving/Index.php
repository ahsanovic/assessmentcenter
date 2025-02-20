<?php

namespace App\Livewire\Admin\ProblemSolving\RefProblemSolving\RefAspekProblemSolving;

use App\Models\ProblemSolving\RefAspekProblemSolving;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.admin.app', ['title' => 'Referensi Aspek Problem Solving'])]
class Index extends Component
{
    use WithPagination;

    public $selected_id;
    
    public function render()
    {
        $data = RefAspekProblemSolving::paginate(10);
        
        return view('livewire.admin.problem-solving.referensi.aspek.index', compact('data'));
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
            RefAspekProblemSolving::find($this->selected_id)->delete();

            $this->dispatch('toast', ['type' => 'success', 'message' => 'berhasil menghapus data']);
        } catch (\Throwable $th) {
            // throw $th;
            $this->dispatch('toast', ['type' => 'error', 'message' => 'gagal menghapus data']);
        }
    }
}
