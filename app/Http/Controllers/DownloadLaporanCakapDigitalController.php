<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Peserta;
use App\Models\TtdLaporan;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\StreamedResponse;
use ZipStream\ZipStream;

class DownloadLaporanCakapDigitalController extends Controller
{
    public function createPdf($idEvent, $identifier)
    {
        $peserta = Peserta::where(function ($q) use ($identifier) {
            $q->where('nip', $identifier)
                ->orWhere('nik', $identifier);
        })
            ->whereHas('ujianCakapDigital', function ($q) use ($idEvent) {
                $q->where('event_id', $idEvent)
                    ->where('is_finished', 'true');
            })
            ->firstOrFail();

        $tte = TtdLaporan::where('is_active', 't')->first();

        $data = Event::with([
            'peserta' => function ($query) use ($peserta) {
                $query->where('id', $peserta->id);
            },
            'nomorLaporan' => function ($query) use ($idEvent) {
                $query->where('event_id', $idEvent);
            },
            'hasilCakapDigital' => function ($query) use ($peserta) {
                $query->where('peserta_id', $peserta->id);
            },
        ])->findOrFail($idEvent);

        if ($data->hasilCakapDigital->isEmpty()) {
            abort(404, 'Hasil tes cakap digital tidak ditemukan');
        }

        $nomor_laporan = $this->resolveNomorLaporan($peserta, $data->nomorLaporan);

        $pdf = Pdf::loadView('livewire.admin.data-tes.tes-cakap-digital.tes-selesai.download-pdf', [
            'peserta' => $peserta,
            'data' => $data,
            'tte' => $tte,
            'nomor_laporan' => $nomor_laporan,
        ])->setPaper('A4', 'portrait');

        return $pdf->stream($this->buildPdfFilename($peserta));
    }

    public function downloadAll($idEvent)
    {
        $tanggal = request()->query('tanggalTes');
        $tte = TtdLaporan::where('is_active', 't')->first();

        $all_peserta = Peserta::with('event')
            ->where('event_id', $idEvent)
            ->when($tanggal, function ($query) use ($tanggal) {
                $query->whereDate('test_started_at', $tanggal);
            })
            ->whereHas('ujianCakapDigital', function ($query) {
                $query->where('is_finished', 'true');
            })
            ->whereHas('hasilCakapDigital')
            ->get();

        $response = new StreamedResponse(
            function () use ($all_peserta, $tte, $idEvent) {
                if (ob_get_level()) {
                    ob_end_clean();
                }

                ini_set('memory_limit', '1024M');
                ini_set('max_execution_time', '600');

                $zip = new ZipStream(
                    outputName: 'laporan-semua-peserta-tes-cakap-digital.zip',
                    sendHttpHeaders: true
                );

                foreach ($all_peserta as $peserta) {
                    $data = Event::with([
                        'peserta' => function ($query) use ($peserta) {
                            $query->where('id', $peserta->id);
                        },
                        'nomorLaporan' => function ($query) use ($idEvent) {
                            $query->where('event_id', $idEvent);
                        },
                        'hasilCakapDigital' => function ($query) use ($peserta) {
                            $query->where('peserta_id', $peserta->id);
                        },
                    ])->find($idEvent);

                    if (! $data || $data->hasilCakapDigital->isEmpty()) {
                        continue;
                    }

                    $nomor_laporan = $this->resolveNomorLaporan($peserta, $data->nomorLaporan);

                    $pdf = Pdf::loadView('livewire.admin.data-tes.tes-cakap-digital.tes-selesai.download-pdf', [
                        'peserta' => $peserta,
                        'data' => $data,
                        'tte' => $tte,
                        'nomor_laporan' => $nomor_laporan,
                    ])->setPaper('A4', 'portrait');

                    $zip->addFile($this->buildPdfFilename($peserta), $pdf->output());
                }

                $zip->finish();
            }
        );

        return $response;
    }

    private function buildPdfFilename(Peserta $peserta): string
    {
        $tanggalTes = $peserta->test_started_at
            ? Carbon::parse($peserta->test_started_at)->format('d-m-Y')
            : '00-00-0000';

        $identifier = $peserta->nip ?: $peserta->nik;
        $safeName = preg_replace('/[^A-Za-z0-9_\-]/', '_', strtoupper($peserta->nama));

        return $tanggalTes.'_'.$identifier.'_'.$safeName.'.pdf';
    }

    private function resolveNomorLaporan(Peserta $peserta, Collection $nomorLaporans): ?string
    {
        $items = $nomorLaporans->filter(fn ($nl) => filled($nl->nomor));
        if ($items->isEmpty()) {
            return null;
        }

        $tesDay = $peserta->test_started_at
            ? Carbon::parse($peserta->test_started_at)->startOfDay()
            : null;

        foreach ($items as $nl) {
            $rawTgl = $nl->getRawOriginal('tanggal');
            if ($tesDay === null || ! $rawTgl) {
                continue;
            }
            if (Carbon::parse($rawTgl)->startOfDay()->equalTo($tesDay)) {
                return $nl->nomor;
            }
        }

        if ($items->count() === 1) {
            return $items->first()->nomor;
        }

        return $items->sort(function ($a, $b) {
            $ta = $a->getRawOriginal('tanggal') ?? '';
            $tb = $b->getRawOriginal('tanggal') ?? '';
            if ($ta !== $tb) {
                return strcmp($tb, $ta);
            }

            return (string) $b->getKey() <=> (string) $a->getKey();
        })->first()?->nomor;
    }
}
