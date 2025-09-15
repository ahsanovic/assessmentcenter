<?php

namespace App\Livewire\Admin\KompetensiTeknis\SoalKompetensiTeknis;

use App\Models\KompetensiTeknis\SoalKompetensiTeknis;
use App\Models\RefJabatanDiuji;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.admin.app', ['title' => 'Kompetensi Teknis'])]
class Index extends Component
{
    use WithPagination;

    public $selected_id;
    public $jenis_jabatan;

    #[Url(as: 'q')]
    public ?string $search = '';

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $jenis_jabatan_options = RefJabatanDiuji::pluck('jenis', 'id');

        $data = SoalKompetensiTeknis::when($this->search, function ($query) {
            $query->where('soal', 'like', '%' . $this->search . '%');
        })
            ->when($this->jenis_jabatan, function ($query) {
                $query->where('jenis_jabatan_id', $this->jenis_jabatan);
            })
            ->orderByDesc('id')
            ->paginate(10);

        return view('livewire.admin.kompetensi-teknis.soal.index', compact('data', 'jenis_jabatan_options'));
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
            $data = SoalKompetensiTeknis::find($this->selected_id);
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
