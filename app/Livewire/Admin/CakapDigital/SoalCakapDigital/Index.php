<?php

namespace App\Livewire\Admin\CakapDigital\SoalCakapDigital;

use App\Models\CakapDigital\SoalCakapDigital;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.admin.app', ['title' => 'Cakap Digital'])]
class Index extends Component
{
    use WithPagination;

    public $selected_id;
    public $jenis_soal;

    #[Url(as: 'q')]
    public ?string $search = '';

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $data = SoalCakapDigital::when($this->search, function ($query) {
            $query->where('soal', 'like', '%' . $this->search . '%');
        })
            ->when($this->jenis_soal, function ($query) {
                $query->where('jenis_soal', $this->jenis_soal);
            })
            ->orderByDesc('id')
            ->paginate(10);

        return view('livewire.admin.cakap-digital.soal.index', compact('data'));
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
            $data = SoalCakapDigital::find($this->selected_id);
            $old_data = $data->getOriginal();

            activity_log($data, 'delete', 'soal-cakap-digital', $old_data);

            $data->delete();

            $this->dispatch('toast', ['type' => 'success', 'message' => 'berhasil menghapus data']);
        } catch (\Throwable $th) {
            // throw $th;
            $this->dispatch('toast', ['type' => 'error', 'message' => 'gagal menghapus data']);
        }
    }
}
