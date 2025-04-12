<?php

namespace App\Livewire\Admin\DataTes\TesSelesai;

use App\Models\Event;
use App\Models\Peserta;
use App\Models\Settings;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.admin.app', ['title' => 'Laporan Hasil Penilaian Potensi'])]
class ShowReport extends Component
{
    public $nip;
    public $peserta;
    public $id_event;

    public function mount($nip, $idEvent)
    {
        $this->nip = $nip;
        $this->peserta = Peserta::whereNip($this->nip)->first();
        $this->id_event = $idEvent;
    }

    public function render()
    {
        $aspek_potensi = Settings::with('alatTes')->orderBy('urutan')->get();
        $data = Event::with(['peserta' => function ($query) {
                        $query->where('id', $this->peserta->id);
                    }, 
                    'hasilInterpersonal',
                    'hasilKesadaranDiri',
                    'hasilBerpikirKritis',
                    'hasilProblemSolving',
                    'hasilPengembanganDiri',
                    'hasilKecerdasanEmosi',
                    'hasilMotivasiKomitmen'
                    ])
                    ->whereHas('ujianInterpersonal', function ($query) {
                        $query->where('is_finished', 'true');
                    })
                    ->whereHas('ujianKesadaranDiri', function ($query) {
                        $query->where('is_finished', 'true');
                    })
                    ->whereHas('ujianBerpikirKritis', function ($query) {
                        $query->where('is_finished', 'true');
                    })
                    ->whereHas('ujianPengembanganDiri', function ($query) {
                        $query->where('is_finished', 'true');
                    })
                    ->whereHas('ujianProblemSolving', function ($query) {
                        $query->where('is_finished', 'true');
                    })
                    ->whereHas('ujianKecerdasanEmosi', function ($query) {
                        $query->where('is_finished', 'true');
                    })
                    ->whereHas('ujianMotivasiKomitmen', function ($query) {
                        $query->where('is_finished', 'true');
                    })
                    ->whereHas('peserta', function ($query) {
                        $query->where('id', $this->peserta->id);
                    })
                ->first();

        return view('livewire.admin.data-tes.tes-selesai.show-report', [
            'peserta' => $this->peserta,
            'aspek_potensi' => $aspek_potensi,
            'data' => $data,
        ]);
    }
}
