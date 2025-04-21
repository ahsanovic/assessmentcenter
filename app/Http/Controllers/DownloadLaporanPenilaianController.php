<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Peserta;
use App\Models\Settings;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use ZipArchive;

class DownloadLaporanPenilaianController extends Controller
{
    public function createPdf($idEvent, $nip)
    {
        $peserta = Peserta::where('nip', $nip)->firstOrFail();
        $aspek_potensi = Settings::with('alatTes')->orderBy('urutan')->get();

        $data = Event::with([
                'peserta' => function ($query) use ($peserta) {
                    $query->where('id', $peserta->id);
                },
                'nomorLaporan' => function ($query) use ($idEvent) {
                    $query->where('event_id', $idEvent);
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
                },
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
            ->whereHas('peserta', function ($query) use ($peserta) {
                $query->where('id', $peserta->id);
            })
            ->firstOrFail();

        $pdf = Pdf::loadView('livewire.admin.data-tes.tes-selesai.download-pdf', [
            'peserta' => $peserta,
            'aspek_potensi' => $aspek_potensi,
            'data' => $data,
            'capaian_level_interpersonal' => capaianLevel($data->hasilInterpersonal[0]->level_total),
            'capaian_level_kecerdasan_emosi' => capaianLevel($data->hasilKecerdasanEmosi[0]->level_total),
            'capaian_level_pengembangan_diri' => capaianLevel($data->hasilPengembanganDiri[0]->level_total),
            'capaian_level_problem_solving' => capaianLevel($data->hasilProblemSolving[0]->level_total),
            'capaian_level_motivasi_komitmen' => capaianLevel($data->hasilMotivasiKomitmen[0]->level_total),
            'capaian_level_berpikir_kritis' => capaianLevel($data->hasilBerpikirKritis[0]->level_total),
            'capaian_level_kesadaran_diri' => capaianLevel($data->hasilKesadaranDiri[0]->level_total),
        ])->setPaper('A4', 'portrait');

        // return $pdf->download('report-' . $peserta->nip . '-' . $peserta->nama . '.pdf');
        return $pdf->stream('report-' . $peserta->nip . '-' . strtoupper($peserta->nama) . '.pdf');
    }

    public function downloadAll($idEvent)
    {
        $aspek_potensi = Settings::with('alatTes')->orderBy('urutan')->get();
        $peserta = Peserta::with('event')
            ->where('event_id', $idEvent)
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
            ->get();

        foreach ($peserta as $peserta) {
            $data = Event::with([
                'peserta' => function ($query) use ($peserta) {
                    $query->where('id', $peserta->id);
                },
                'nomorLaporan' => function ($query) use ($idEvent) {
                    $query->where('event_id', $idEvent);
                },
                'hasilInterpersonal',
                'hasilKesadaranDiri',
                'hasilBerpikirKritis',
                'hasilProblemSolving',
                'hasilPengembanganDiri',
                'hasilKecerdasanEmosi',
                'hasilMotivasiKomitmen',
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
            ->whereHas('peserta', function ($query) use ($peserta) {
                $query->where('id', $peserta->id);
            })
            ->first();

            if (!$data) continue;
    
            $pdf = Pdf::loadView('livewire.admin.data-tes.tes-selesai.download-pdf', [
                'peserta' => $peserta,
                'aspek_potensi' => $aspek_potensi,
                'data' => $data,
                'capaian_level_interpersonal' => capaianLevel($data->hasilInterpersonal[0]->level_total),
                'capaian_level_kecerdasan_emosi' => capaianLevel($data->hasilKecerdasanEmosi[0]->level_total),
                'capaian_level_pengembangan_diri' => capaianLevel($data->hasilPengembanganDiri[0]->level_total),
                'capaian_level_problem_solving' => capaianLevel($data->hasilProblemSolving[0]->level_total),
                'capaian_level_motivasi_komitmen' => capaianLevel($data->hasilMotivasiKomitmen[0]->level_total),
                'capaian_level_berpikir_kritis' => capaianLevel($data->hasilBerpikirKritis[0]->level_total),
                'capaian_level_kesadaran_diri' => capaianLevel($data->hasilKesadaranDiri[0]->level_total),
            ])->setPaper('A4', 'portrait');
            
            $temp_folder = storage_path('app/private/laporan_temp');
            $pdf_paths = [];
            $filename = $peserta->nip . '-' . strtoupper($peserta->nama) . '.pdf';
            $pdf_path = $temp_folder . '/' . $filename;
            file_put_contents($pdf_path, $pdf->output());
            $pdf_paths[] = $pdf_path;
        }

        // ZIP semua file PDF
        $zip_filename = 'laporan-semua-peserta.zip';
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
