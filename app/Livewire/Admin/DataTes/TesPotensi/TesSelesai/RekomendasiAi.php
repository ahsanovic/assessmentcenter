<?php

namespace App\Livewire\Admin\DataTes\TesPotensi\TesSelesai;

use App\Models\NilaiJpm;
use App\Models\Peserta;
use App\Models\RekomendasiByAi;
use App\Services\AIService;
use League\CommonMark\CommonMarkConverter;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.admin.app', ['title' => 'Rekomendasi AI'])]
class RekomendasiAi extends Component
{
    public $peserta;
    public $nama;
    public $persentase;
    public $kategori;
    public $hasil_rekomendasi;
    public $id_event;
    public $jenis_peserta;
    public $jenis_jabatan;

    public function mount($idEvent, $identifier)
    {
        $this->id_event = $idEvent;

        // get data peserta
        $this->peserta = Peserta::with('event')
            ->where('nip', $identifier)
            ->orWhere('nik', $identifier)
            ->first();

        $this->nama = $this->peserta->nama;

        // get jpm
        $nilai_jpm = NilaiJpm::where('event_id', $this->id_event)
            ->where('peserta_id', $this->peserta->id)
            ->first(['jpm', 'kategori']);

        $this->persentase = $nilai_jpm->jpm;
        $this->kategori = $nilai_jpm->kategori;
        $this->jenis_peserta = $this->peserta->jenisPeserta->jenis_peserta;
        $this->jenis_jabatan = $this->peserta->event->jabatan_diuji_id;

        // get rekomendasi
        $rekomendasi = RekomendasiByAi::where('event_id', $this->id_event)
            ->where('peserta_id', $this->peserta->id)
            ->first(['rekomendasi']);

        if ($rekomendasi) {
            $this->hasil_rekomendasi = $rekomendasi->rekomendasi;
        } else {
            $this->hasil_rekomendasi = null;
        }
    }

    public function generateRekomendasi()
    {
        try {
            $ai = app(AIService::class);
            $this->hasil_rekomendasi = $ai->getRekomendasi(
                $this->jenis_jabatan,
                $this->jenis_peserta,
                $this->nama,
                $this->kategori,
                $this->persentase
            );

            $converter = new CommonMarkConverter();
            $this->hasil_rekomendasi = (string) $converter->convert($this->hasil_rekomendasi);

            $this->saveRekomendasi(
                $this->id_event,
                $this->peserta->id,
                $this->hasil_rekomendasi
            );
        } catch (\Throwable $th) {
            report($th);
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Gagal menghubungi AI. Coba lagi nanti.',
            ]);
        }
    }

    public function saveRekomendasi($event_id, $peserta_id, $rekomendasi)
    {
        RekomendasiByAi::updateOrCreate(
            [
                'event_id' => $event_id,
                'peserta_id' => $peserta_id,
            ],
            [
                'rekomendasi' => $rekomendasi,
            ]
        );
    }

    public function render()
    {
        return view('livewire.admin.data-tes.tes-potensi.tes-selesai.rekomendasi-ai');
    }
}
