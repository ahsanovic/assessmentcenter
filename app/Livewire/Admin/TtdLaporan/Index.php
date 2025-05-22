<?php

namespace App\Livewire\Admin\TtdLaporan;

use App\Models\TtdLaporan;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.admin.app', ['title' => 'Ttd Laporan Penilaian'])]
class Index extends Component
{
    use WithPagination;

    public $selected_id;
    public $is_active;

    public function render()
    {
        $data = TtdLaporan::when($this->is_active, function ($query) {
            $query->where('is_active', $this->is_active);
        })
            ->orderByDesc('id')
            ->paginate(10);

        return view('livewire.admin.ttd-laporan.index', compact('data'));
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
            $data = TtdLaporan::find($this->selected_id);
            $old_data = $data->getOriginal();

            if ($data) {
                // Hapus file ttd jika ada
                if ($data->ttd && Storage::disk('public')->exists($data->ttd)) {
                    Storage::disk('public')->delete($data->ttd);
                }

                activity_log($data, 'delete', 'ttd-laporan', $old_data);

                $data->delete();
            }

            $this->dispatch('toast', ['type' => 'success', 'message' => 'berhasil menghapus data']);
        } catch (\Throwable $th) {
            // throw $th;
            $this->dispatch('toast', ['type' => 'error', 'message' => 'gagal menghapus data']);
        }
    }
}
