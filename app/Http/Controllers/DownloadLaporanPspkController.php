<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Peserta;
use App\Models\RefAlatTes;
use App\Models\RefAspekPspk;
use App\Models\TtdLaporan;
use Barryvdh\DomPDF\Facade\Pdf;
use ZipArchive;
use Symfony\Component\HttpFoundation\StreamedResponse;
use ZipStream\ZipStream;

class DownloadLaporanPspkController extends Controller
{
    public function createPdf($idEvent, $identifier)
    {
        $peserta = Peserta::with('golPangkat')
            ->where(function ($q) use ($identifier) {
                $q->where('nip', $identifier)
                    ->orWhere('nik', $identifier);
            })
            ->whereHas('ujianPspk', function ($q) use ($idEvent) {
                $q->where('event_id', $idEvent);
            })
            ->firstOrFail();

        $aspek_potensi = RefAspekPspk::all();
        $tte = TtdLaporan::where('is_active', 't')->first();

        $data = Event::with([
            'peserta' => function ($query) use ($peserta) {
                $query->where('id', $peserta->id);
            },
            'nomorLaporan' => function ($query) use ($idEvent) {
                $query->where('event_id', $idEvent);
            },
            'hasilPspk' => function ($query) use ($peserta) {
                $query->where('peserta_id', $peserta->id);
            },
        ])
            ->where('id', $idEvent)
            ->whereHas('ujianPspk', function ($query) {
                $query->where('is_finished', 'true');
            })
            ->first();

        foreach ($data->nomorLaporan as $nomorLaporan) {
            if ($nomorLaporan->tanggal == \Carbon\Carbon::parse($peserta->test_started_at)->format('d-m-Y')) {
                $nomor_laporan = $nomorLaporan->nomor;
            }
        }

        $pdf = Pdf::loadView('livewire.admin.data-tes.tes-pspk.tes-selesai.download-pdf', [
            'peserta' => $peserta,
            'data' => $data,
            'aspek_potensi' => $aspek_potensi,
            'tte' => $tte,
            'nomor_laporan' => $nomor_laporan ?? null,
        ])->setPaper('A4', 'portrait');

        return $pdf->stream('report-pspk-' . $peserta->nip ?: $peserta->nik . '-' . strtoupper($peserta->nama) . '.pdf');
    }

    public function downloadAll($idEvent)
    {
        $tanggal = request()->query('tanggalTes');

        $aspek_potensi = RefAspekPspk::all();
        $tte = TtdLaporan::where('is_active', 't')->first();

        $all_peserta = Peserta::with('event')
            ->where('event_id', $idEvent)
            ->when($tanggal, function ($query) use ($tanggal) {
                $query->whereDate('test_started_at', $tanggal);
            })
            ->whereHas('ujianPspk', function ($query) {
                $query->where('is_finished', 'true');
            })
            ->get();

        $response = new StreamedResponse(
            function () use ($all_peserta, $aspek_potensi, $tte, $idEvent) {
                if (ob_get_level()) {
                    ob_end_clean();
                }

                ini_set('memory_limit', '1024M');
                ini_set('max_execution_time', '600');

                $zip = new ZipStream(
                    outputName: 'laporan-semua-peserta-tes-pspk.zip',
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
                        'hasilPspk' => function ($query) use ($peserta) {
                            $query->where('peserta_id', $peserta->id);
                        },
                    ])
                        ->where('id', $idEvent)
                        ->whereHas('ujianPspk', function ($query) {
                            $query->where('is_finished', 'true');
                        })
                        ->first();

                    if (!$data) continue;

                    // ambil nomor laporan sesuai tanggal ujian
                    $nomor_laporan = null;
                    foreach ($data->nomorLaporan as $nl) {
                        if ($nl->tanggal == \Carbon\Carbon::parse($peserta->test_started_at)->format('d-m-Y')) {
                            $nomor_laporan = $nl->nomor;
                        }
                    }

                    // generate PDF
                    $pdf = Pdf::loadView('livewire.admin.data-tes.tes-pspk.tes-selesai.download-pdf', [
                        'peserta' => $peserta,
                        'aspek_potensi' => $aspek_potensi,
                        'data' => $data,
                        'tte' => $tte,
                        'nomor_laporan' => $nomor_laporan ?? null,
                    ])->setPaper('A4', 'portrait');

                    // nama file di dalam zip
                    $identifier = $peserta->nip ?: $peserta->nik;
                    $safeName = preg_replace('/[^A-Za-z0-9_\-]/', '_', strtoupper($peserta->nama));
                    $filename = $identifier . '-' . $safeName . '.pdf';

                    // masukkan langsung ke stream
                    $zip->addFile($filename, $pdf->output());
                }

                $zip->finish(); // kirim zip ke browser
            }
        );

        return $response;
    }
}
