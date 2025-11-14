<?php

namespace App\Livewire\Admin\DataTes\TesPspk\TesSelesai;

use App\Models\Event;
use App\Models\Pspk\RefLevelPspk;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.admin.app', ['title' => 'Tes PSPK'])]
class Index extends Component
{
    use WithPagination;

    public $level_pspk;
    public $tgl_mulai;
    public $selected_id;
    public $event;

    #[Url(as: 'q')]
    public ?string $search =  '';

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedJabatanDiuji()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->reset(['level_pspk', 'tgl_mulai', 'search']);
        $this->resetPage();
        $this->render();
        $this->dispatch('reset-select2');
    }

    public function render()
    {
        $levelToMetode = [
            1 => 5,
            2 => 6,
        ];

        $data = Event::withCount(['peserta', 'hasilPspk', 'peserta as peserta_selesai_count' => function ($query) {
            $query->whereHas('ujianPspk', function ($query) {
                $query->where('is_finished', 'true');
            });
        }])
            ->with(['peserta', 'hasilPspk'])
            ->whereIn('metode_tes_id', [5, 6])
            ->when($this->level_pspk, function ($query) use ($levelToMetode) {
                $metode = $levelToMetode[$this->level_pspk] ?? null;

                if ($metode) {
                    $query->where('metode_tes_id', $metode);
                }
            })
            ->when($this->event, function ($query) {
                $query->where('id', $this->event);
            })
            ->when($this->tgl_mulai, function ($query) {
                $tgl_mulai = date('Y-m-d', strtotime($this->tgl_mulai));
                $query->where('tgl_mulai', $tgl_mulai);
            })
            ->orderByDesc('tgl_mulai')
            ->paginate(10);

        $option_level_pspk = RefLevelPspk::pluck('nama_pspk', 'id');
        $option_event = Event::where('metode_tes_id', 5)->pluck('nama_event', 'id');

        return view('livewire.admin.data-tes.tes-pspk.tes-selesai.index', compact('data', 'option_event', 'option_level_pspk'));
    }
}
