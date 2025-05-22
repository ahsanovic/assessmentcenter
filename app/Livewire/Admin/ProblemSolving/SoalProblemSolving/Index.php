<?php

namespace App\Livewire\Admin\ProblemSolving\SoalProblemSolving;

use App\Models\ProblemSolving\RefAspekProblemSolving;
use App\Models\ProblemSolving\SoalProblemSolving;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.admin.app', ['title' => 'Soal Problem Solving'])]
class Index extends Component
{
    use WithPagination;

    public $jenis_indikator;
    public $aspek;
    public $selected_id;

    #[Url(as: 'q')]
    public ?string $search = '';

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedJenisIndikator()
    {
        $this->resetPage();
    }

    public function render()
    {
        $data = SoalProblemSolving::with('aspek')
            ->when($this->search, function ($query) {
                $query->where('soal', 'like', '%' . $this->search . '%');
            })
            ->when($this->aspek, function ($query, $aspek) {
                $query->where('aspek_id', $aspek);
            })
            ->orderByDesc('id')
            ->paginate(10);

        $aspek_option = RefAspekProblemSolving::pluck('aspek', 'id')->toArray();

        return view('livewire.admin.problem-solving.soal.index', compact('data', 'aspek_option'));
    }

    public function resetFilters()
    {
        $this->reset();
        $this->resetPage();
        $this->render();
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
            $data = SoalProblemSolving::find($this->selected_id);
            $old_data = $data->getOriginal();

            activity_log($data, 'delete', 'soal-problem-solving', $old_data);

            $data->delete();

            $this->dispatch('toast', ['type' => 'success', 'message' => 'berhasil menghapus data']);
        } catch (\Throwable $th) {
            // throw $th;
            $this->dispatch('toast', ['type' => 'error', 'message' => 'gagal menghapus data']);
        }
    }
}
