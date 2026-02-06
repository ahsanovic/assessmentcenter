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
        $this->peserta = Peserta::where('event_id', $idEvent)
        ->where(function ($q) use ($identifier) {
            $q->where('nip', $identifier)
                ->orWhere('nik', $identifier);
        })->first();
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
            ->first();
    }

    public function render()
    {
        $aspek_potensi = RefAlatTes::orderBy('urutan')->get();
        $data = $this->data;

        return view('livewire.admin.data-tes.tes-potensi.tes-selesai.show-report', [
            'peserta' => $this->peserta,
            'aspek_potensi' => $aspek_potensi,
            'capaian_level_intelektual' => capaianLevel(optional(optional($data)->hasilIntelektual?->first())->level),
            'capaian_level_interpersonal' => capaianLevel(optional($data?->hasilInterpersonal->first())->level_total),
            'capaian_level_kecerdasan_emosi' => capaianLevel(optional($data?->hasilKecerdasanEmosi->first())->level_total),
            'capaian_level_pengembangan_diri' => capaianLevel(optional($data?->hasilPengembanganDiri->first())->level_total),
            'capaian_level_problem_solving' => capaianLevel(optional($data?->hasilProblemSolving->first())->level_total),
            'capaian_level_motivasi_komitmen' => capaianLevel(optional($data?->hasilMotivasiKomitmen->first())->level_total),
            'capaian_level_berpikir_kritis' => capaianLevel(optional($data?->hasilBerpikirKritis->first())->level_total),
            'capaian_level_kesadaran_diri' => capaianLevel(optional($data?->hasilKesadaranDiri->first())->level_total),
            'data' => $data,
        ]);
    }
}
