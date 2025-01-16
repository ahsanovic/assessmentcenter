<?php

namespace App\Livewire\Admin\KecerdasanEmosi\SoalKecerdasanEmosi;

use App\Models\KecerdasanEmosi\RefKecerdasanEmosi;
use App\Models\KecerdasanEmosi\SoalKecerdasanEmosi;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.admin.app', ['title' => 'Soal Kecerdasan Emosi'])]
class Index extends Component
{
    use WithPagination;

    public $jenis_indikator;
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
        $data = SoalKecerdasanEmosi::with('jenisIndikator')
                ->when($this->search, function($query) {
                    $query->where('soal', 'like', '%' . $this->search . '%');
                })
                ->when($this->jenis_indikator, function($query, $jenis_indikator) {
                    $query->where('jenis_indikator_id', $jenis_indikator);
                })
                ->orderByDesc('id')
                ->paginate(10);

        $indikator = RefKecerdasanEmosi::pluck('indikator_nama', 'id')->toArray();
        
        return view('livewire.admin.kecerdasan-emosi.soal.index', compact('data', 'indikator'));
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
            SoalKecerdasanEmosi::find($this->selected_id)->delete();

            $this->dispatch('toast', ['type' => 'success', 'message' => 'berhasil menghapus data']);
        } catch (\Throwable $th) {
            // throw $th;
            $this->dispatch('toast', ['type' => 'error', 'message' => 'gagal menghapus data']);
        }
    }
}
