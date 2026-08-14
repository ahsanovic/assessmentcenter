<?php

namespace App\Livewire\Peserta\Portofolio;

use App\Models\Event;
use App\Models\Peserta;
use App\Models\RefPertanyaanPengalaman;
use App\Models\RefPertanyaanPenilaian;
use App\Models\RwKarir;
use App\Models\RwPelatihan;
use App\Models\RwPendidikan;
use App\Support\PortofolioProgress;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.peserta.app', ['title' => 'Portofolio'])]
class Portofolio extends Component
{
    public function render()
    {
        $biodata = Peserta::wherePesertaEvent(
            activePesertaId(),
            activeEventId()
        )
            ->first();

        $pendidikan = RwPendidikan::wherePesertaEvent(
            activePesertaId(),
            activeEventId()
        )
            ->orderByDesc('thn_lulus')
            ->get();

        $pelatihan = RwPelatihan::wherePesertaEvent(
            activePesertaId(),
            activeEventId()
        )
            ->orderByRaw('YEAR(tgl_selesai) IS NULL, YEAR(tgl_selesai) DESC')
            ->get();

        $karir = RwKarir::wherePesertaEvent(
            activePesertaId(),
            activeEventId()
        )
            ->orderByDesc('tahun_selesai')
            ->get();

        $pertanyaan = RefPertanyaanPengalaman::with(['jawaban' => function ($query) {
            $query->where('peserta_id', activePesertaId())
                ->where('event_id', activeEventId());
        }])
            ->orderBy('urutan', 'asc')
            ->get();

        $penilaian = RefPertanyaanPenilaian::with(['jawaban' => function ($query) {
            $query->where('peserta_id', activePesertaId())
                ->where('event_id', activeEventId());
        }])
            ->orderBy('urutan', 'asc')
            ->get();

        $portofolio = Event::where('id', $biodata->event_id)->first(['is_open', 'metode_tes_id']);

        return view('livewire..peserta.portofolio.index', [
            'biodata' => $biodata,
            'pendidikan' => $pendidikan,
            'pelatihan' => $pelatihan,
            'karir' => $karir,
            'pertanyaan' => $pertanyaan,
            'penilaian' => $penilaian,
            'portofolio' => $portofolio,
            'portofolioProgress' => PortofolioProgress::forPeserta($biodata),
        ]);
    }
}
