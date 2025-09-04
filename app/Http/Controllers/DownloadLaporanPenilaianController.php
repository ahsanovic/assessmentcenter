<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Peserta;
use App\Models\RefAlatTes;
use App\Models\Settings;
use App\Models\TtdLaporan;
use Barryvdh\DomPDF\Facade\Pdf;
use ZipArchive;

class DownloadLaporanPenilaianController extends Controller
{
    public function createPdf($idEvent, $nip)
    {
        $peserta = Peserta::where('nip', $nip)->firstOrFail();
        $aspek_potensi = RefAlatTes::orderBy('urutan')->get();
        $tte = TtdLaporan::where('is_active', 't')->first();

        $data = Event::with([
            'peserta' => function ($query) use ($peserta) {
                $query->where('id', $peserta->id);
            },
            'nomorLaporan' => function ($query) use ($idEvent) {
                $query->where('event_id', $idEvent);
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
            },
        ])
            ->where('id', $idEvent)
            ->whereHas('ujianIntelektualSubTes1', function ($query) {
                $query->where('is_finished', 'true');
            })
            ->whereHas('ujianIntelektualSubTes2', function ($query) {
                $query->where('is_finished', 'true');
            })
            ->whereHas('ujianIntelektualSubTes3', function ($query) {
                $query->where('is_finished', 'true');
            })
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

        foreach ($data->nomorLaporan as $nomorLaporan) {
            if ($nomorLaporan->tanggal == \Carbon\Carbon::parse($peserta->test_started_at)->format('d-m-Y')) {
                $nomor_laporan = $nomorLaporan->nomor;
            }
        }

        $pdf = Pdf::loadView('livewire.admin.data-tes.tes-potensi.tes-selesai.download-pdf', [
            'peserta' => $peserta,
            'aspek_potensi' => $aspek_potensi,
            'data' => $data,
            'tte' => $tte,
            'nomor_laporan' => $nomor_laporan ?? null,
            'capaian_level_intelektual' => capaianLevel(optional($data->hasilIntelektual->first())->level ?? null),
            'capaian_level_interpersonal' => capaianLevel(optional($data->hasilInterpersonal->first())->level_total ?? null),
            'capaian_level_kecerdasan_emosi' => capaianLevel(optional($data->hasilKecerdasanEmosi->first())->level_total ?? null),
            'capaian_level_pengembangan_diri' => capaianLevel(optional($data->hasilPengembanganDiri->first())->level_total ?? null),
            'capaian_level_problem_solving' => capaianLevel(optional($data->hasilProblemSolving->first())->level_total ?? null),
            'capaian_level_motivasi_komitmen' => capaianLevel(optional($data->hasilMotivasiKomitmen->first())->level_total ?? null),
            'capaian_level_berpikir_kritis' => capaianLevel(optional($data->hasilBerpikirKritis->first())->level_total ?? null),
            'capaian_level_kesadaran_diri' => capaianLevel(optional($data->hasilKesadaranDiri->first())->level_total ?? null),
        ])->setPaper('A4', 'portrait');

        // return $pdf->download('report-' . $peserta->nip . '-' . $peserta->nama . '.pdf');
        return $pdf->stream('report-' . $peserta->nip . '-' . strtoupper($peserta->nama) . '.pdf');
    }

    public function downloadAll($idEvent)
    {
        $tanggal = request()->query('tanggalTes');

        $aspek_potensi = RefAlatTes::orderBy('urutan')->get();
        $tte = TtdLaporan::where('is_active', 't')->first();
        $all_peserta = Peserta::with('event')
            ->where('event_id', $idEvent)
            ->when($tanggal, function ($query) use ($tanggal) {
                $query->whereDate('test_started_at', $tanggal);
            })
            ->whereHas('ujianIntelektualSubTes1', function ($query) {
                $query->where('is_finished', 'true');
            })
            ->whereHas('ujianIntelektualSubTes2', function ($query) {
                $query->where('is_finished', 'true');
            })
            ->whereHas('ujianIntelektualSubTes3', function ($query) {
                $query->where('is_finished', 'true');
            })
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

        $pdf_paths = [];

        foreach ($all_peserta as $peserta) {
            $data = Event::with([
                'peserta' => function ($query) use ($peserta) {
                    $query->where('id', $peserta->id);
                },
                'nomorLaporan' => function ($query) use ($idEvent) {
                    $query->where('event_id', $idEvent);
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
                ->whereHas('ujianIntelektualSubTes1', function ($query) {
                    $query->where('is_finished', 'true');
                })
                ->whereHas('ujianIntelektualSubTes2', function ($query) {
                    $query->where('is_finished', 'true');
                })
                ->whereHas('ujianIntelektualSubTes3', function ($query) {
                    $query->where('is_finished', 'true');
                })
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

            foreach ($data->nomorLaporan as $nomorLaporan) {
                if ($nomorLaporan->tanggal == \Carbon\Carbon::parse($peserta->test_started_at)->format('d-m-Y')) {
                    $nomor_laporan = $nomorLaporan->nomor;
                }
            }

            $pdf = Pdf::loadView('livewire.admin.data-tes.tes-potensi.tes-selesai.download-pdf', [
                'peserta' => $peserta,
                'aspek_potensi' => $aspek_potensi,
                'data' => $data,
                'tte' => $tte,
                'nomor_laporan' => $nomor_laporan ?? null,
                'capaian_level_intelektual' => capaianLevel(optional($data->hasilIntelektual->first())->level ?? null),
                'capaian_level_interpersonal' => capaianLevel(optional($data->hasilInterpersonal->first())->level_total ?? null),
                'capaian_level_kecerdasan_emosi' => capaianLevel(optional($data->hasilKecerdasanEmosi->first())->level_total ?? null),
                'capaian_level_pengembangan_diri' => capaianLevel(optional($data->hasilPengembanganDiri->first())->level_total ?? null),
                'capaian_level_problem_solving' => capaianLevel(optional($data->hasilProblemSolving->first())->level_total ?? null),
                'capaian_level_motivasi_komitmen' => capaianLevel(optional($data->hasilMotivasiKomitmen->first())->level_total ?? null),
                'capaian_level_berpikir_kritis' => capaianLevel(optional($data->hasilBerpikirKritis->first())->level_total ?? null),
                'capaian_level_kesadaran_diri' => capaianLevel(optional($data->hasilKesadaranDiri->first())->level_total ?? null),
            ])->setPaper('A4', 'portrait');

            $temp_folder = storage_path('app/private/laporan_temp');
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
