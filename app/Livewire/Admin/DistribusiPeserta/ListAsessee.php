<?php

namespace App\Livewire\Admin\DistribusiPeserta;

use App\Models\Assessor;
use App\Models\Event;
use App\Models\Peserta;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.admin.app', ['title' => 'Distribusi Peserta'])]
class ListAsessee extends Component
{
    use WithPagination;

    public $assessor;
    public $check = [];
    public $event_id;
    public $event;

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

    public function mount($idAssessor, $idEvent)
    {
        $this->event_id = $idEvent;
        $this->event = Event::findOrFail($idEvent);
        $this->assessor = Assessor::with(['peserta' => function ($query) use ($idEvent) {
            $query->where('peserta.event_id', $idEvent);
        }])
        ->findOrFail($idAssessor);

        $this->check = $this->assessor->peserta->pluck('id')->flip()->map(fn() => true)->toArray();
    }

    public function toggleCheck($idAsessee, $isChecked)
    {
        if ($isChecked) {
            // Tambahkan ke tabel pivot
            DB::table('assessor_peserta')->insertOrIgnore([
                'assessor_id' => $this->assessor->id,
                'peserta_id' => $idAsessee,
                'event_id' => $this->event_id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            // Hapus dari tabel pivot
            DB::table('assessor_peserta')
                ->where('assessor_id', $this->assessor->id)
                ->where('peserta_id', $idAsessee)
                ->where('event_id', $this->event_id)
                ->delete();
        }
    }

    public function render()
    {
        $data = Peserta::with('event')->when($this->search, function($query) {
                $query->where('nama', 'like', '%' . $this->search . '%')
                    ->orWhere('nip', 'like', '%' . $this->search . '%')
                    ->orWhere('jabatan', 'like', '%' . $this->search . '%')
                    ->orWhere('instansi', 'like', '%' . $this->search . '%');
            })
            ->where('event_id', $this->event_id)
            ->where('is_active', 'true')
            ->paginate(10);
        
        return view('livewire.admin.distribusi-peserta.list-asessee', [
            'data' => $data
        ]);
    }
}
