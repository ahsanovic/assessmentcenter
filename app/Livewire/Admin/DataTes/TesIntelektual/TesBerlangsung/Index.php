<?php

namespace App\Livewire\Admin\DataTes\TesIntelektual\TesBerlangsung;

use App\Models\Event;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.admin.app', ['title' => 'Tes Intelektual'])]
class Index extends Component
{
    use WithPagination;

    public $jabatan_diuji;
    public $tgl_mulai;
    public $selected_id;

    public function render()
    {
        $data = Event::withCount([
            'peserta',
            'peserta as subtes1_berlangsung_count' => function ($query) {
                $query->whereHas('ujianIntelektualSubTes1', function ($q) {
                    $q->where('is_finished', 'false');
                });
            },
            'peserta as subtes2_berlangsung_count' => function ($query) {
                $query->whereHas('ujianIntelektualSubTes1', function ($q) {
                    $q->where('is_finished', 'true');
                })->whereHas('ujianIntelektualSubTes2', function ($q) {
                    $q->where('is_finished', 'false');
                });
            },
            'peserta as subtes3_berlangsung_count' => function ($query) {
                $query->whereHas('ujianIntelektualSubTes1', function ($q) {
                    $q->where('is_finished', 'true');
                })->whereHas('ujianIntelektualSubTes2', function ($q) {
                    $q->where('is_finished', 'true');
                })->whereHas('ujianIntelektualSubTes3', function ($q) {
                    $q->where('is_finished', 'false');
                });
            },
        ])
            ->with(['peserta', 'ujianIntelektualSubTes1', 'ujianIntelektualSubTes2', 'ujianIntelektualSubTes3'])
            ->whereIsFinished('false')
            ->whereIn('metode_tes_id', [1,2])
            ->orderByDesc('id')
            ->paginate(10);

        return view('livewire.admin.data-tes.tes-intelektual.tes-berlangsung.index', compact('data'));
    }
}
