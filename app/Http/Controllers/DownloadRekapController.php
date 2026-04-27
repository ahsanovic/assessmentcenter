<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Peserta;
use App\Models\RefAspekPspk;
use Rap2hpoutre\FastExcel\FastExcel;

class DownloadRekapController extends Controller
{
    public function downloadRekap($idEvent)
    {
        $tanggal = request()->query('tanggalTes');

        $all_peserta = Peserta::with([
            'event',
            'hasilInterpersonal',
            'hasilKesadaranDiri',
            'hasilBerpikirKritis',
            'hasilPengembanganDiri',
            'hasilProblemSolving',
            'hasilKecerdasanEmosi',
            'hasilMotivasiKomitmen',
            'nilaiJpm'
        ])
            ->where('event_id', $idEvent)
            ->when($tanggal, function ($query) use ($tanggal) {
                $query->whereDate('test_started_at', $tanggal);
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

        $export_data = collect();

        foreach ($all_peserta as $peserta) {
            // Helper function for formatting array of lines
            $formatLines = function(array $lines) {
                $nonEmpty = array_filter($lines, function($desc) {
                    return !empty($desc) && $desc !== '-';
                });
                if (count($nonEmpty) > 1) {
                    return implode("\n", array_map(function($line) {
                        return '- ' . trim($line);
                    }, $lines));
                }
                // Single or all dash, still with "- " if not empty (per instruction, else just show what was there)
                if (isset($lines[0])) {
                    if (!empty($lines[0]) && $lines[0] != '-') {
                        return '- ' . trim($lines[0]);
                    } else {
                        return '-';
                    }
                }
                return '-';
            };

            // Berpikir Kritis & Strategis
            if (!empty($peserta->hasilBerpikirKritis->uraian_potensi)) {
                $deskripsiBerpikir = '- ' . trim($peserta->hasilBerpikirKritis->uraian_potensi ?? '-');
            } else {
                $lines = [];
                for ($i = 1; $i <= 8; $i++) {
                    $field = 'uraian_potensi_' . $i;
                    $descObj = isset($peserta->hasilBerpikirKritis->$field) ? json_decode($peserta->hasilBerpikirKritis->$field) : null;
                    $lines[] = $descObj->deskripsi ?? '-';
                }
                $deskripsiBerpikir = $formatLines($lines);
            }

            // Problem Solving
            if (!empty($peserta->hasilProblemSolving->uraian_potensi)) {
                $deskripsiProblem = '- ' . trim($peserta->hasilProblemSolving->uraian_potensi ?? '-');
            } else {
                $lines = [];
                for ($i = 1; $i <= 8; $i++) {
                    $field = 'uraian_potensi_' . $i;
                    $descObj = isset($peserta->hasilProblemSolving->$field) ? json_decode($peserta->hasilProblemSolving->$field) : null;
                    $lines[] = $descObj->deskripsi ?? '-';
                }
                $deskripsiProblem = $formatLines($lines);
            }

            // Interpersonal
            if (!empty($peserta->hasilInterpersonal->uraian_potensi)) {
                $uraian = json_decode($peserta->hasilInterpersonal->uraian_potensi)->uraian_potensi ?? '-';
                $deskripsiInterpersonal = '- ' . trim($uraian);
            } else {
                $lines = [];
                for ($i = 1; $i <= 5; $i++) {
                    $field = 'uraian_potensi_' . $i;
                    $descObj = isset($peserta->hasilInterpersonal->$field) ? json_decode($peserta->hasilInterpersonal->$field) : null;
                    $lines[] = $descObj->uraian_potensi ?? '-';
                }
                $deskripsiInterpersonal = $formatLines($lines);
            }

            // Kesadaran Diri
            if (!empty($peserta->hasilKesadaranDiri->uraian_potensi)) {
                $uraian = json_decode($peserta->hasilKesadaranDiri->uraian_potensi)->uraian_potensi ?? '-';
                $deskripsiKesadaran = '- ' . trim($uraian);
            } else {
                $lines = [];
                for ($i = 1; $i <= 3; $i++) {
                    $field = 'uraian_potensi_' . $i;
                    $descObj = isset($peserta->hasilKesadaranDiri->$field) ? json_decode($peserta->hasilKesadaranDiri->$field) : null;
                    $lines[] = $descObj->uraian_potensi ?? '-';
                }
                $deskripsiKesadaran = $formatLines($lines);
            }

            // EQ
            if (!empty($peserta->hasilKecerdasanEmosi->uraian_potensi)) {
                $uraian = json_decode($peserta->hasilKecerdasanEmosi->uraian_potensi)->uraian_potensi ?? '-';
                $deskripsiEQ = '- ' . trim($uraian);
            } else {
                $lines = [];
                for ($i = 1; $i <= 4; $i++) {
                    $field = 'uraian_potensi_' . $i;
                    $descObj = isset($peserta->hasilKecerdasanEmosi->$field) ? json_decode($peserta->hasilKecerdasanEmosi->$field) : null;
                    $lines[] = $descObj->uraian_potensi ?? '-';
                }
                $deskripsiEQ = $formatLines($lines);
            }

            // Belajar Cepat & Pengembangan Diri
            if (!empty($peserta->hasilPengembanganDiri->uraian_potensi)) {
                $uraian = json_decode($peserta->hasilPengembanganDiri->uraian_potensi)->uraian_potensi ?? '-';
                $deskripsiPengembangan = '- ' . trim($uraian);
            } else {
                $lines = [];
                for ($i = 1; $i <= 5; $i++) {
                    $field = 'uraian_potensi_' . $i;
                    $descObj = isset($peserta->hasilPengembanganDiri->$field) ? json_decode($peserta->hasilPengembanganDiri->$field) : null;
                    $lines[] = $descObj->uraian_potensi ?? '-';
                }
                $deskripsiPengembangan = $formatLines($lines);
            }

            // Deskripsi Intelektual
            $deskripsiIntelektual = [];
            if (
                !empty($peserta->hasilIntelektual->uraian_potensi_subtes_1) ||
                !empty($peserta->hasilIntelektual->uraian_potensi_subtes_2) ||
                !empty($peserta->hasilIntelektual->uraian_potensi_subtes_3)
            ) {
                if (!empty($peserta->hasilIntelektual->uraian_potensi_subtes_1))
                    $deskripsiIntelektual[] = $peserta->hasilIntelektual->uraian_potensi_subtes_1;
                if (!empty($peserta->hasilIntelektual->uraian_potensi_subtes_2))
                    $deskripsiIntelektual[] = $peserta->hasilIntelektual->uraian_potensi_subtes_2;
                if (!empty($peserta->hasilIntelektual->uraian_potensi_subtes_3))
                    $deskripsiIntelektual[] = $peserta->hasilIntelektual->uraian_potensi_subtes_3;
            }
            if (count($deskripsiIntelektual) > 1) {
                $deskripsiIntelektualFormatted = implode("\n", array_map(function($desc) {return '- ' . trim($desc);}, $deskripsiIntelektual));
            } elseif (count($deskripsiIntelektual) === 1) {
                $deskripsiIntelektualFormatted = '- ' . trim($deskripsiIntelektual[0]);
            } else {
                $deskripsiIntelektualFormatted = '-';
            }

            $export_data->push([
                'Nama Peserta' => $peserta->nama,
                'NIP/NIK' => $peserta->nip ?: $peserta->nik,
                'Jabatan Saat Ini' => $peserta->jabatan,
                'OPD' => $peserta->instansi . ' - ' . $peserta->unit_kerja,
                'Tanggal Tes' => \Carbon\Carbon::parse($peserta->test_started_at)->format('d/m/Y'),
                'Intelektual' => $peserta->hasilIntelektual->level ?? '-',
                'Berpikir Kritis dan Strategis' => $peserta->hasilBerpikirKritis->level_total ?? '-',
                'Problem Solving' => $peserta->hasilProblemSolving->level_total ?? '-',
                'Belajar Cepat dan Pengembangan Diri' => $peserta->hasilPengembanganDiri->level_total ?? '-',
                'Motivasi Komitmen' => $peserta->hasilMotivasiKomitmen->level_total ?? '-',
                'Interpersonal' => $peserta->hasilInterpersonal->level_total ?? '-',
                'Kesadaran Diri' => $peserta->hasilKesadaranDiri->level_total ?? '-',
                'EQ' => $peserta->hasilKecerdasanEmosi->level_total ?? '-',
                'Intelektual (Capaian Level)' => capaianLevel(optional($peserta->hasilIntelektual)->level) ?? '-',
                'Berpikir Kritis dan Strategis (Capaian Level)' => capaianLevel($peserta->hasilBerpikirKritis->level_total) ?? '-',
                'Problem Solving (Capaian Level)' => capaianLevel($peserta->hasilProblemSolving->level_total) ?? '-',
                'Belajar Cepat dan Pengembangan Diri (Capaian Level)' => capaianLevel($peserta->hasilPengembanganDiri->level_total) ?? '-',
                'Motivasi Komitmen (Capaian Level)' => capaianLevel($peserta->hasilMotivasiKomitmen->level_total) ?? '-',
                'Interpersonal (Capaian Level)' => capaianLevel($peserta->hasilInterpersonal->level_total) ?? '-',
                'Kesadaran Diri (Capaian Level)' => capaianLevel($peserta->hasilKesadaranDiri->level_total) ?? '-',
                'EQ (Capaian Level)' => capaianLevel($peserta->hasilKecerdasanEmosi->level_total) ?? '-',
                'JPM Potensi' => $peserta->nilaiJpm?->jpm . '%' ?? '-',
                'Kesimpulan' => $peserta->nilaiJpm?->kategori ?? '-',
                'Deskripsi Intelektual' => $deskripsiIntelektualFormatted,
                'Deskripsi Berpikir Kritis dan Strategis' => $deskripsiBerpikir,
                'Deskripsi Problem Solving' => $deskripsiProblem,
                'Deskripsi Belajar Cepat dan Pengembangan Diri' => $deskripsiPengembangan,
                'Deskripsi Motivasi Komitmen' => !empty($peserta->hasilMotivasiKomitmen->deskripsi) ? '- ' . trim($peserta->hasilMotivasiKomitmen->deskripsi) : '-',
                'Deskripsi Interpersonal' => $deskripsiInterpersonal,
                'Deskripsi Kesadaran Diri' => $deskripsiKesadaran,
                'Deskripsi EQ' => $deskripsiEQ,
            ]);
        }

        return (new FastExcel($export_data))->download('rekap-laporan.xlsx');
    }

    public function downloadRekapCakapDigital($idEvent)
    {
        $tanggal = request()->query('tanggalTes');

        $all_peserta = Peserta::with(['event', 'hasilCakapDigital'])
            ->where('event_id', $idEvent)
            ->when($tanggal, function ($query) use ($tanggal) {
                $query->whereDate('test_started_at', $tanggal);
            })
            ->whereHas('ujianCakapDigital', function ($query) {
                $query->where('is_finished', 'true');
            })
            ->get();

        $export_data = collect();

        foreach ($all_peserta as $peserta) {
            $export_data->push([
                'Nama Peserta' => $peserta->nama,
                'NIP/NIK' => $peserta->nip ?: $peserta->nik,
                'Jabatan' => $peserta->jabatan,
                'Unit Kerja' => $peserta->unit_kerja,
                'Instansi' => $peserta->instansi,
                'JPM LD' => $peserta->hasilCakapDigital?->jpm_literasi . '%' ?? '',
                'Kategori LD' => $peserta->hasilCakapDigital?->kesimpulan_literasi ?? '',
                'Deskripsi LD' => $peserta->hasilCakapDigital?->deskripsi_literasi ?? '',
                'JPM ES' => $peserta->hasilCakapDigital?->jpm_emerging . '%' ?? '',
                'Kategori ES' => $peserta->hasilCakapDigital?->kesimpulan_emerging ?? '',
                'Deskripsi ES' => $peserta->hasilCakapDigital?->deskripsi_emerging ?? '',
                'Tanggal Tes' => \Carbon\Carbon::parse($peserta->test_started_at)->format('d/m/Y'),
            ]);
        }

        return (new FastExcel($export_data))->download('rekap-laporan-cakap-digital.xlsx');
    }

    public function downloadRekapKompetensiTeknis($idEvent)
    {
        $tanggal = request()->query('tanggalTes');

        $all_peserta = Peserta::with([
            'event',
            'hasilKompetensiTeknis',
        ])
            ->where('event_id', $idEvent)
            ->when($tanggal, function ($query) use ($tanggal) {
                $query->whereDate('test_started_at', $tanggal);
            })
            ->whereHas('ujianKompetensiTeknis', function ($query) {
                $query->where('is_finished', 'true');
            })
            ->get();

        $export_data = collect();

        foreach ($all_peserta as $peserta) {
            $export_data->push([
                'Nama Peserta' => $peserta->nama,
                'NIP/NIK' => $peserta->nip ?: $peserta->nik,
                'Jabatan' => $peserta->jabatan,
                'Unit Kerja' => $peserta->unit_kerja,
                'Instansi' => $peserta->instansi,
                'Tanggal Tes' => \Carbon\Carbon::parse($peserta->test_started_at)->format('d/m/Y'),
                'JPM' => $peserta->hasilKompetensiTeknis?->jpm . '%' ?? '',
                'Kategori' => $peserta->hasilKompetensiTeknis?->kategori ?? '',
                'Deskripsi' => $peserta->hasilKompetensiTeknis?->deskripsi ?? ''
            ]);
        }

        return (new FastExcel($export_data))->download('rekap-laporan-kompetensi-teknis.xlsx');
    }

    public function downloadRekapPspk($idEvent)
    {
        $tanggal = request()->query('tanggalTes');

        $aspek_potensi = RefAspekPspk::all();

        $all_peserta = Peserta::with([
            'event',
            'hasilPspk',
        ])
            ->where('event_id', $idEvent)
            ->when($tanggal, function ($query) use ($tanggal) {
                $query->whereDate('test_started_at', $tanggal);
            })
            ->whereHas('ujianPspk', function ($query) {
                $query->where('is_finished', 'true');
            })
            ->get();

        $export_data = collect();

        foreach ($all_peserta as $peserta) {
            $nilaiCapaian = $peserta->hasilPspk?->nilai_capaian ?? [];

            $row = [
                'Nama Peserta' => $peserta->nama,
                'NIP/NIK' => $peserta->nip ?: $peserta->nik,
                'Jabatan' => $peserta->jabatan,
                'Unit Kerja' => $peserta->unit_kerja,
                'Instansi' => $peserta->instansi,
            ];

            foreach ($aspek_potensi as $index => $item) {
                $nilai = $nilaiCapaian[$index] ?? null;
                $row[$item->nama_aspek] = $nilai !== null && $nilai !== '' ? $nilai : '';
            }

            $row['JPM'] = $peserta->hasilPspk?->jpm . '%' ?? '';
            $row['Kategori'] = $peserta->hasilPspk?->kategori ?? '';
            $row['Tanggal Tes'] = \Carbon\Carbon::parse($peserta->test_started_at)->format('d/m/Y');

            $export_data->push($row);
        }

        return (new FastExcel($export_data))->download('rekap-laporan-pspk.xlsx');
    }
}
