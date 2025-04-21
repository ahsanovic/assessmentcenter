<?php

namespace App\Livewire\Peserta\TesPotensi;

use App\Models\Event;
use App\Models\NilaiJpm;
use App\Models\Peserta;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.peserta.app', ['title' => 'Hasil Nilai Tes'])]
class HasilNilai extends Component
{
    public $data;
    public $peserta;
    public $id_event;
    public $nilai;

    public function mount()
    {
        $this->peserta = Peserta::where('id', Auth::guard('peserta')->user()->id)->firstOrFail();
        $idEvent = $this->peserta->event_id;
        $peserta = $this->peserta;

        $this->data = Event::with([
            'peserta' => function ($query) use ($peserta) {
                $query->where('id', $peserta->id);
            },
            'hasilInterpersonal' => function ($query) use ($peserta) {
                $query->where('peserta_id', $peserta->id);
            },
            'hasilKesadaranDiri' => function ($query) use ($peserta) {
                $query->where('peserta_id', $peserta->id);
            },
            'hasilBerpikirKritis' => function ($query) use ($peserta) {
                $query->where('peserta_id', $peserta->id);
            },
            'hasilProblemSolving' => function ($query) use ($peserta) {
                $query->where('peserta_id', $peserta->id);
            },
            'hasilPengembanganDiri' => function ($query) use ($peserta) {
                $query->where('peserta_id', $peserta->id);
            },
            'hasilKecerdasanEmosi' => function ($query) use ($peserta) {
                $query->where('peserta_id', $peserta->id);
            },
            'hasilMotivasiKomitmen' => function ($query) use ($peserta) {
                $query->where('peserta_id', $peserta->id);
            },
        ])
        ->where('id', $idEvent)
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
        ->whereHas('peserta', function ($query) use ($peserta) {
            $query->where('id', $peserta->id);
        })
        ->firstOrFail();

        $capaian_level = [
            'capaian_level_interpersonal' => capaianLevel($this->data->hasilInterpersonal[0]->level_total),
            'capaian_level_kecerdasan_emosi' => capaianLevel($this->data->hasilKecerdasanEmosi[0]->level_total),
            'capaian_level_pengembangan_diri' => capaianLevel($this->data->hasilPengembanganDiri[0]->level_total),
            'capaian_level_problem_solving' => capaianLevel($this->data->hasilProblemSolving[0]->level_total),
            'capaian_level_motivasi_komitmen' => capaianLevel($this->data->hasilMotivasiKomitmen[0]->level_total),
            'capaian_level_berpikir_kritis' => capaianLevel($this->data->hasilBerpikirKritis[0]->level_total),
            'capaian_level_kesadaran_diri' => capaianLevel($this->data->hasilKesadaranDiri[0]->level_total),
        ];

        $count_jpm = countJpm($capaian_level);
        $jpm = number_format($count_jpm * 100, 2);
        $kategori = getKategori(($count_jpm));
        
        NilaiJpm::updateOrCreate([
            'event_id' => $idEvent,
            'peserta_id' => $peserta->id
        ], [
            'jpm' => $jpm,
            'kategori' => $kategori
        ]);

        $this->nilai = NilaiJpm::where('event_id', $idEvent)
            ->where('peserta_id', $peserta->id)
            ->firstOrFail();
    }

    public function render()
    {
        return view('livewire..peserta.tes-potensi.hasil-nilai', [
            'nilai' => $this->nilai
        ]);
    }
}
