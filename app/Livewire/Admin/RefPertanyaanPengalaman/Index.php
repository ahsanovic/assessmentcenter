<?php

namespace App\Livewire\Admin\RefPertanyaanPengalaman;

use App\Models\RefPertanyaanPengalaman;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.admin.app', ['title' => 'Referensi Pertanyaan'])]
class Index extends Component
{
    use WithPagination;

    public $selected_id;

    #[Url(as: 'q')]
    public ?string $search = '';

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $option_kode = [
            1 => 'OPH',
            2 => 'MP',
            3 => 'INT',
            4 => 'KS',
            5 => 'PP',
            6 => 'KOM',
            7 => 'PB',
            8 => 'PDOL',
            9 => 'PK',
        ];

        $data = RefPertanyaanPengalaman::when($this->search, function ($query) {
            $query->where('pertanyaan', 'like', '%' . $this->search . '%');
        })
            ->orderByDesc('id')
            ->paginate(10);

        return view('livewire.admin.pertanyaan-pengalaman.index', compact('data', 'option_kode'));
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
            $data = RefPertanyaanPengalaman::find($this->selected_id);
            $old_data = $data->getOriginal();

            activity_log($data, 'delete', 'pertanyaan', $old_data);

            $data->delete();

            $this->dispatch('toast', ['type' => 'success', 'message' => 'berhasil menghapus data']);
        } catch (\Throwable $th) {
            // throw $th;
            $this->dispatch('toast', ['type' => 'error', 'message' => 'gagal menghapus data']);
        }
    }
}
