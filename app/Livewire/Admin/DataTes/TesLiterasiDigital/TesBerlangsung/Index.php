<?php

namespace App\Livewire\Admin\DataTes\TesLiterasiDigital\TesBerlangsung;

use App\Models\Event;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.admin.app', ['title' => 'Tes Literasi Digital'])]
class Index extends Component
{
    use WithPagination;

    public function render()
    {
        $data = Event::withCount([
            'peserta',
            'ujianLiterasiDigital as ujian_literasi_digital_count' => function ($query) {
                $query->select(DB::raw('count(distinct ujian_literasi_digital.peserta_id)'));
            },
        ])
            ->withPesertaMulaiUjian('literasi-digital')
            ->with('eventGroup')
            ->whereIsFinished('false')
            ->where('metode_tes_id', 3)
            ->orderByDesc('id')
            ->paginate(10);

        return view('livewire.admin.data-tes.tes-literasi-digital.tes-berlangsung.index', compact('data'));
    }
}
