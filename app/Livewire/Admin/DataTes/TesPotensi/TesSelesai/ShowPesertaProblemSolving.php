<?php

namespace App\Livewire\Admin\DataTes\TesPotensi\TesSelesai;

use App\Models\Event;
use App\Models\Peserta;
use App\Models\ProblemSolving\HasilProblemSolving;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.admin.app', ['title' => 'Tes Selesai'])]
class ShowPesertaProblemSolving extends Component
{
    use WithPagination;

    public $event;
    public $id_event;
    public $selected_id;

    #[Url(as: 'q')]
    public ?string $search =  '';

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->reset(['search']);
        $this->resetPage();
        $this->render();
    }

    public function mount($idEvent)
    {
        $this->id_event = $idEvent;
        $this->event = Event::with(['pesertaTesProblemSolving'])->findOrFail($this->id_event);
    }

    public function render()
    {
        $data = Peserta::join('hasil_problem_solving', 'hasil_problem_solving.peserta_id', '=', 'peserta.id')
            ->join('ujian_berpikir_kritis', 'ujian_berpikir_kritis.peserta_id', '=', 'peserta.id')
            ->whereIn('peserta.id', $this->event->pesertaIdTesProblemSolving->pluck('peserta_id'))
            ->select('peserta.*', 'hasil_problem_solving.id as hasil_problem_solving_id', 'hasil_problem_solving.created_at as waktu_selesai', 'is_finished')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('nama', 'like', '%' . $this->search . '%')
                        ->orWhere('nip', 'like', '%' . $this->search . '%')
                        ->orWhere('nik', 'like', '%' . $this->search . '%')
                        ->orWhere('jabatan', 'like', '%' . $this->search . '%')
                        ->orWhere('instansi', 'like', '%' . $this->search . '%');
                });
            })
            ->paginate(10);

        return view('livewire.admin.data-tes.tes-potensi.tes-selesai.show-peserta-problem-solving', [
            'data' => $data
        ]);
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
            HasilProblemSolving::find($this->selected_id)->delete();

            $this->dispatch('toast', ['type' => 'success', 'message' => 'berhasil menghapus data']);
        } catch (\Throwable $th) {
            // throw $th;
            $this->dispatch('toast', ['type' => 'error', 'message' => 'gagal menghapus data']);
        }
    }
}
