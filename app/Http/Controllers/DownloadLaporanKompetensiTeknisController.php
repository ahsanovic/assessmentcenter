<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Peserta;
use App\Models\RefAlatTes;
use App\Models\TtdLaporan;
use Barryvdh\DomPDF\Facade\Pdf;
use ZipArchive;

class DownloadLaporanKompetensiTeknisController extends Controller
{
    public function createPdf($idEvent, $identifier)
    {
        $peserta = Peserta::with('golPangkat')
            ->where(function ($q) use ($identifier) {
                $q->where('nip', $identifier)
                    ->orWhere('nik', $identifier);
            })
            ->whereHas('ujianKompetensiTeknis', function ($q) use ($idEvent) {
                $q->where('event_id', $idEvent);
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
            'hasilKompetensiTeknis' => function ($query) use ($peserta) {
                $query->where('peserta_id', $peserta->id);
            },
        ])
            ->where('id', $idEvent)
            ->whereHas('ujianKompetensiTeknis', function ($query) {
                $query->where('is_finished', 'true');
            })
            ->first();

        foreach ($data->nomorLaporan as $nomorLaporan) {
            if ($nomorLaporan->tanggal == \Carbon\Carbon::parse($peserta->test_started_at)->format('d-m-Y')) {
                $nomor_laporan = $nomorLaporan->nomor;
            }
        }

        $pdf = Pdf::loadView('livewire.admin.data-tes.tes-kompetensi-teknis.tes-selesai.download-pdf', [
            'peserta' => $peserta,
            'data' => $data,
            'tte' => $tte,
            'nomor_laporan' => $nomor_laporan ?? null,
        ])->setPaper('A4', 'portrait');

        // return $pdf->download('report-' . $peserta->nip . '-' . $peserta->nama . '.pdf');
        return $pdf->stream('report-kompetensi-teknis-' . $peserta->nip ?: $peserta->nik . '-' . strtoupper($peserta->nama) . '.pdf');
    }

    public function downloadAll($idEvent)
    {
        ini_set('max_execution_time', 300);
        $tanggal = request()->query('tanggalTes');

        $tte = TtdLaporan::where('is_active', 't')->first();
        $all_peserta = Peserta::with('event')
            ->where('event_id', $idEvent)
            ->when($tanggal, function ($query) use ($tanggal) {
                $query->whereDate('test_started_at', $tanggal);
            })
            ->whereHas('ujianKompetensiTeknis', function ($query) {
                $query->where('is_finished', 'true');
            })
            ->get();

        $pdf_paths = [];

        foreach ($all_peserta as $peserta) {
            $data = Event::with([
                'peserta' => function ($query) use ($peserta) {
                    $query->where('id', $peserta->id);
                },
                'nomorLaporan' => function ($query) use ($idEvent) {
                    $query->where('event_id', $idEvent);
                },
                'hasilKompetensiTeknis' => function ($query) use ($peserta) {
                    $query->where('peserta_id', $peserta->id);
                },
            ])
                ->where('id', $idEvent)
                ->whereHas('ujianKompetensiTeknis', function ($query) {
                    $query->where('is_finished', 'true');
                })
                ->first();

            if (!$data) continue;

            foreach ($data->nomorLaporan as $nomorLaporan) {
                if ($nomorLaporan->tanggal == \Carbon\Carbon::parse($peserta->test_started_at)->format('d-m-Y')) {
                    $nomor_laporan = $nomorLaporan->nomor;
                }
            }

            $pdf = Pdf::loadView('livewire.admin.data-tes.tes-kompetensi-teknis.tes-selesai.download-pdf', [
                'peserta' => $peserta,
                'data' => $data,
                'tte' => $tte,
                'nomor_laporan' => $nomor_laporan ?? null,
            ])->setPaper('A4', 'portrait');

            $temp_folder = storage_path('app/private/laporan_temp');
            $identifier = $peserta->nip ?: $peserta->nik;
            $filename = $identifier . '-' . strtoupper($peserta->nama) . '.pdf';
            $pdf_path = $temp_folder . '/' . $filename;
            file_put_contents($pdf_path, $pdf->output());
            $pdf_paths[] = $pdf_path;
        }

        // ZIP semua file PDF
        $zip_filename = 'laporan-kompetensi-teknis-semua-peserta.zip';
        $zip_path = storage_path('app/private/' . $zip_filename);

        $zip = new ZipArchive;
        if ($zip->open($zip_path, ZipArchive::CREATE | ZipArchive::OVERWRITE)) {
            foreach ($pdf_paths as $file) {
                $zip->addFile($file, basename($file));
            }
            $zip->close();
        }

        // Hapus file PDF setelah ZIP selesai
        foreach ($pdf_paths as $file) {
            unlink($file);
        }

        // Kirim file ZIP sebagai download response
        return response()->download($zip_path)->deleteFileAfterSend(true);
    }
}
