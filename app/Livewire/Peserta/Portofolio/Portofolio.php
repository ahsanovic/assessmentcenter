<?php

namespace App\Livewire\Peserta\Portofolio;

use App\Models\Event;
use App\Models\Peserta;
use App\Models\RefPertanyaanPengalaman;
use App\Models\RefPertanyaanPenilaian;
use App\Models\RwKarir;
use App\Models\RwPelatihan;
use App\Models\RwPendidikan;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.peserta.app', ['title' => 'Portofolio'])]
class Portofolio extends Component
{
    public function render()
    {
        $biodata = Peserta::wherePesertaEvent(
            Auth::guard('peserta')->user()->id,
            Auth::guard('peserta')->user()->event_id
        )
            ->first();

        $pendidikan = RwPendidikan::wherePesertaEvent(
            Auth::guard('peserta')->user()->id,
            Auth::guard('peserta')->user()->event_id
        )
            ->orderByDesc('thn_lulus')
            ->get();

        $pelatihan = RwPelatihan::wherePesertaEvent(
            Auth::guard('peserta')->user()->id,
            Auth::guard('peserta')->user()->event_id
        )
            ->orderByRaw('YEAR(tgl_selesai) IS NULL, YEAR(tgl_selesai) DESC')
            ->get();

        $karir = RwKarir::wherePesertaEvent(
            Auth::guard('peserta')->user()->id,
            Auth::guard('peserta')->user()->event_id
        )
            ->orderByDesc('tahun_selesai')
            ->get();

        $pertanyaan = RefPertanyaanPengalaman::with(['jawaban' => function ($query) {
            $query->where('peserta_id', Auth::guard('peserta')->user()->id)
                ->where('event_id', Auth::guard('peserta')->user()->event_id);
        }])
            ->orderBy('urutan', 'asc')
            ->get();

        $penilaian = RefPertanyaanPenilaian::with(['jawaban' => function ($query) {
            $query->where('peserta_id', Auth::guard('peserta')->user()->id)
                ->where('event_id', Auth::guard('peserta')->user()->event_id);
        }])
            ->orderBy('urutan', 'asc')
            ->get();

        $portofolio_is_open = Event::where('is_open', 'true')->where('id', Auth::guard('peserta')->user()->event_id)->first();

        return view('livewire..peserta.portofolio.index', [
            'biodata' => $biodata,
            'pendidikan' => $pendidikan,
            'pelatihan' => $pelatihan,
            'karir' => $karir,
            'pertanyaan' => $pertanyaan,
            'penilaian' => $penilaian,
            'portofolio_is_open' => $portofolio_is_open
        ]);
    }
}
