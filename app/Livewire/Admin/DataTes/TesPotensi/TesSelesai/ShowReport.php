<?php

namespace App\Livewire\Admin\DataTes\TesPotensi\TesSelesai;

use App\Models\Event;
use App\Models\Peserta;
use App\Models\RefAlatTes;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.admin.app', ['title' => 'Laporan Hasil Penilaian Potensi'])]
class ShowReport extends Component
{
    public $nip;
    public $peserta;
    public $id_event;
    public $data;

    public function mount($identifier, $idEvent)
    {
        $this->peserta = Peserta::where(function ($q) use ($identifier) {
            $q->where('nip', $identifier)
                ->orWhere('nik', $identifier);
        });
        $this->id_event = $idEvent;

        $peserta = $this->peserta;
        $id_event = $this->id_event;
        $this->data = Event::with([
            'peserta' => function ($query) use ($peserta) {
                $query->where('id', $peserta->id);
            },
            'nomorLaporan' => function ($query) use ($id_event) {
                $query->where('event_id', $id_event);
            },
            'hasilIntelektual' => function ($query) use ($peserta) {
                $query->where('peserta_id', $peserta->id);
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
            }
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
            ->whereHas('peserta', function ($query) {
                $query->where('id', $this->peserta->id);
            })
            ->first();
    }

    public function render()
    {
        $aspek_potensi = RefAlatTes::orderBy('urutan')->get();

        return view('livewire.admin.data-tes.tes-potensi.tes-selesai.show-report', [
            'peserta' => $this->peserta,
            'aspek_potensi' => $aspek_potensi,
            'capaian_level_intelektual' => capaianLevel(optional($this->data->hasilIntelektual->first())->level ?? null),
            'capaian_level_interpersonal' => capaianLevel($this->data->hasilInterpersonal[0]->level_total),
            'capaian_level_kecerdasan_emosi' => capaianLevel($this->data->hasilKecerdasanEmosi[0]->level_total),
            'capaian_level_pengembangan_diri' => capaianLevel($this->data->hasilPengembanganDiri[0]->level_total),
            'capaian_level_problem_solving' => capaianLevel($this->data->hasilProblemSolving[0]->level_total),
            'capaian_level_motivasi_komitmen' => capaianLevel($this->data->hasilMotivasiKomitmen[0]->level_total),
            'capaian_level_berpikir_kritis' => capaianLevel($this->data->hasilBerpikirKritis[0]->level_total),
            'capaian_level_kesadaran_diri' => capaianLevel($this->data->hasilKesadaranDiri[0]->level_total),
            'data' => $this->data,
        ]);
    }
}
