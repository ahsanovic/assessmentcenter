<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\LiterasiDigital\HasilLiterasiDigital;
use App\Models\Peserta;
use App\Models\TtdLaporan;
use App\Services\LiterasiDigital\LaporanLiterasiDigitalService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use ZipStream\ZipStream;

class DownloadLaporanLiterasiDigitalController extends Controller
{
    public function __construct(private LaporanLiterasiDigitalService $laporan) {}

    public function createPdf($idEvent, $identifier)
    {
        $peserta = $this->cariPesertaSelesai((int) $idEvent, (string) $identifier);
        $event = Event::with('nomorLaporan')->findOrFail($idEvent);
        $hasil = $this->hasilTerbaru((int) $idEvent, (int) $peserta->id);

        if ($hasil === null) {
            abort(404, 'Hasil tes literasi digital tidak ditemukan');
        }

        $pdf = Pdf::loadView(
            'livewire.admin.data-tes.tes-literasi-digital.tes-selesai.download-pdf',
            $this->payload($peserta, $event, $hasil)
        )->setPaper('A4', 'portrait');

        return $pdf->stream($this->namaBerkas($peserta));
    }

    public function downloadAll($idEvent)
    {
        $tanggal = request()->query('tanggalTes');
        $event = Event::with('nomorLaporan')->findOrFail($idEvent);
        $tte = TtdLaporan::where('is_active', 't')->first();

        $semuaPeserta = Peserta::query()
            ->where('event_id', $idEvent)
            ->when($tanggal, function ($query) use ($tanggal) {
                $query->whereDate('test_started_at', $tanggal);
            })
            ->whereHas('ujianLiterasiDigital', function ($query) use ($idEvent) {
                $query->where('event_id', $idEvent)
                    ->where('is_finished', 'true');
            })
            ->whereHas('hasilLiterasiDigital', function ($query) use ($idEvent) {
                $query->where('event_id', $idEvent);
            })
            ->get();

        return response()->streamDownload(function () use ($semuaPeserta, $event, $tte) {
            ini_set('memory_limit', '1024M');
            ini_set('max_execution_time', '600');

            $zip = new ZipStream(sendHttpHeaders: false);

            foreach ($semuaPeserta as $peserta) {
                $hasil = $this->hasilTerbaru((int) $event->id, (int) $peserta->id);
                if ($hasil === null) {
                    continue;
                }

                $pdf = Pdf::loadView(
                    'livewire.admin.data-tes.tes-literasi-digital.tes-selesai.download-pdf',
                    $this->payload($peserta, $event, $hasil, $tte)
                )->setPaper('A4', 'portrait');

                $zip->addFile($this->namaBerkas($peserta), $pdf->output());
            }

            $zip->finish();
        }, 'laporan-semua-peserta-tes-literasi-digital.zip', [
            'Content-Type' => 'application/zip',
        ]);
    }

    private function cariPesertaSelesai(int $idEvent, string $identifier): Peserta
    {
        return Peserta::query()
            ->where('event_id', $idEvent)
            ->where(function ($query) use ($identifier) {
                $query->where('nip', $identifier)
                    ->orWhere('nik', $identifier);
            })
            ->whereHas('ujianLiterasiDigital', function ($query) use ($idEvent) {
                $query->where('event_id', $idEvent)
                    ->where('is_finished', 'true');
            })
            ->firstOrFail();
    }

    private function hasilTerbaru(int $idEvent, int $pesertaId): ?HasilLiterasiDigital
    {
        return HasilLiterasiDigital::query()
            ->where('event_id', $idEvent)
            ->where('peserta_id', $pesertaId)
            ->orderByDesc('id')
            ->first();
    }

    private function payload(Peserta $peserta, Event $event, HasilLiterasiDigital $hasil, $tte = null): array
    {
        return array_merge($this->laporan->susun((int) $event->id, $hasil), [
            'peserta' => $peserta,
            'nomor_laporan' => $this->nomorLaporan($peserta, $event->nomorLaporan),
            'tte' => $tte ?? TtdLaporan::where('is_active', 't')->first(),
        ]);
    }

    private function namaBerkas(Peserta $peserta): string
    {
        $tanggalTes = $peserta->test_started_at
            ? Carbon::parse($peserta->test_started_at)->format('d-m-Y')
            : '00-00-0000';

        $identifier = $peserta->nip ?: $peserta->nik;
        $nama = preg_replace('/[^A-Za-z0-9_\-]/', '_', strtoupper($peserta->nama)) ?: 'PESERTA';

        return $tanggalTes.'_'.$identifier.'_'.$nama.'.pdf';
    }

    private function nomorLaporan(Peserta $peserta, Collection $nomorLaporan): ?string
    {
        $items = $nomorLaporan->filter(fn ($baris) => filled($baris->nomor));
        if ($items->isEmpty()) {
            return null;
        }

        $hariTes = $peserta->test_started_at
            ? Carbon::parse($peserta->test_started_at)->startOfDay()
            : null;

        foreach ($items as $baris) {
            $tanggal = $baris->getRawOriginal('tanggal');
            if ($hariTes === null || ! $tanggal) {
                continue;
            }
            if (Carbon::parse($tanggal)->startOfDay()->equalTo($hariTes)) {
                return $baris->nomor;
            }
        }

        if ($items->count() === 1) {
            return $items->first()->nomor;
        }

        return $items->sort(function ($kiri, $kanan) {
            $tanggalKiri = $kiri->getRawOriginal('tanggal') ?? '';
            $tanggalKanan = $kanan->getRawOriginal('tanggal') ?? '';
            if ($tanggalKiri !== $tanggalKanan) {
                return strcmp($tanggalKanan, $tanggalKiri);
            }

            return (string) $kanan->getKey() <=> (string) $kiri->getKey();
        })->first()?->nomor;
    }
}
