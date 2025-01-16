<?php

namespace App\Livewire\Assessor\Event;

use App\Models\Peserta;
use App\Models\RefPertanyaanPengalaman;
use App\Models\RefPertanyaanPenilaian;
use App\Models\RwKarir;
use App\Models\RwPelatihan;
use App\Models\RwPendidikan;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.assessor.app', ['title' => 'Portofolio'])]
class ShowPortofolio extends Component
{
    public $id_event;
    public $id_peserta;

    public function mount($idEvent, $idPeserta)
    {
        $this->id_event = $idEvent;
        $this->id_peserta = $idPeserta;
        $assessor_id = auth()->guard('assessor')->user()->id;

        // Validasi bahwa assessor hanya bisa melihat peserta dan event terkait
        $is_authorized = Peserta::where('id', $this->id_peserta)
            ->whereHas('assessor', function ($query) use ($assessor_id) {
                $query->where('assessor_id', $assessor_id)
                    ->where('assessor_peserta.event_id', $this->id_event);
            })
            ->exists();

        if (!$is_authorized) {
            abort(403, 'Anda tidak memiliki akses melihat portofolio peserta ini!');
        }
    }

    public function render()
    {
        $biodata = Peserta::wherePesertaEvent(
            $this->id_peserta,
            $this->id_event
        )
            ->first();

        $pendidikan = RwPendidikan::wherePesertaEvent(
            $this->id_peserta,
            $this->id_event
        )
            ->orderByDesc('thn_lulus')
            ->get();

        $pelatihan = RwPelatihan::wherePesertaEvent(
            $this->id_peserta,
            $this->id_event
        )
            ->orderByRaw('YEAR(tgl_selesai) IS NULL, YEAR(tgl_selesai) DESC')
            ->get();

        $karir = RwKarir::wherePesertaEvent(
            $this->id_peserta,
            $this->id_event
        )
            ->orderByDesc('tahun_selesai')
            ->get();

        $pertanyaan = RefPertanyaanPengalaman::with(['jawaban' => function ($query) {
            $query->where('peserta_id', $this->id_peserta)
                ->where('event_id', $this->id_event);
        }])
            ->orderBy('urutan', 'asc')
            ->get();

        $penilaian = RefPertanyaanPenilaian::with(['jawaban' => function ($query) {
            $query->where('peserta_id', $this->id_peserta)
                ->where('event_id', $this->id_event);
        }])
            ->orderBy('urutan', 'asc')
            ->get();

        return view('livewire..assessor.event.show-portofolio', [
            'biodata' => $biodata,
            'pendidikan' => $pendidikan,
            'pelatihan' => $pelatihan,
            'karir' => $karir,
            'pertanyaan' => $pertanyaan,
            'penilaian' => $penilaian,
        ]);
    }
}
