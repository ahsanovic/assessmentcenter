<?php

namespace App\Livewire\Admin\BerpikirKritis\SoalBerpikirKritis;

use App\Models\BerpikirKritis\RefAspekBerpikirKritis;
use App\Models\BerpikirKritis\RefIndikatorBerpikirKritis;
use App\Models\BerpikirKritis\SoalBerpikirKritis;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.admin.app', ['title' => 'Soal Berpikir Kritis dan Strategis'])]
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
        $data = SoalBerpikirKritis::with('aspek')
                ->when($this->search, function($query) {
                    $query->where('soal', 'like', '%' . $this->search . '%');
                })
                ->when($this->aspek, function($query, $aspek) {
                    $query->where('aspek_id', $aspek);
                })
                ->orderByDesc('id')
                ->paginate(10);

        $aspek_option = RefAspekBerpikirKritis::pluck('aspek', 'id')->toArray();
        
        return view('livewire.admin.berpikir-kritis.soal.index', compact('data', 'aspek_option'));
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
            SoalBerpikirKritis::find($this->selected_id)->delete();

            $this->dispatch('toast', ['type' => 'success', 'message' => 'berhasil menghapus data']);
        } catch (\Throwable $th) {
            // throw $th;
            $this->dispatch('toast', ['type' => 'error', 'message' => 'gagal menghapus data']);
        }
    }
}
