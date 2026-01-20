<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Peserta;
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
            // Berpikir Kritis & Strategis
            if (!empty($peserta->hasilBerpikirKritis->uraian_potensi)) {
                $deskripsiBerpikir = $peserta->hasilBerpikirKritis->uraian_potensi ?? '-';
            } else {
                $deskripsiBerpikir = '';
                for ($i = 1; $i <= 8; $i++) {
                    $field = 'uraian_potensi_' . $i;
                    $deskripsiBerpikir .= json_decode($peserta->hasilBerpikirKritis->$field)->deskripsi ?? '-';
                }
            }

            // Problem Solving
            if (!empty($peserta->hasilProblemSolving->uraian_potensi)) {
                $deskripsiProblem = $peserta->hasilProblemSolving->uraian_potensi ?? '-';
            } else {
                $deskripsiProblem = '';
                for ($i = 1; $i <= 8; $i++) {
                    $field = 'uraian_potensi_' . $i;
                    $deskripsiProblem .= json_decode($peserta->hasilProblemSolving->$field)->deskripsi ?? '-';
                }
            }

            // Interpersonal
            if (!empty($peserta->hasilInterpersonal->uraian_potensi)) {
                $deskripsiInterpersonal = json_decode($peserta->hasilInterpersonal->uraian_potensi)->uraian_potensi ?? '-';
            } else {
                $deskripsiInterpersonal = '';
                for ($i = 1; $i <= 5; $i++) {
                    $field = 'uraian_potensi_' . $i;
                    $deskripsiInterpersonal .= json_decode($peserta->hasilInterpersonal->$field)->uraian_potensi ?? '-';
                }
            }

            // Kesadaran Diri
            if (!empty($peserta->hasilKesadaranDiri->uraian_potensi)) {
                $deskripsiKesadaran = json_decode($peserta->hasilKesadaranDiri->uraian_potensi)->uraian_potensi ?? '-';
            } else {
                $deskripsiKesadaran = '';
                for ($i = 1; $i <= 3; $i++) {
                    $field = 'uraian_potensi_' . $i;
                    $deskripsiKesadaran .= json_decode($peserta->hasilKesadaranDiri->$field)->uraian_potensi ?? '-';
                }
            }

            // EQ
            if (!empty($peserta->hasilKecerdasanEmosi->uraian_potensi)) {
                $deskripsiEQ = json_decode($peserta->hasilKecerdasanEmosi->uraian_potensi)->uraian_potensi ?? '-';
            } else {
                $deskripsiEQ = '';
                for ($i = 1; $i <= 4; $i++) {
                    $field = 'uraian_potensi_' . $i;
                    $deskripsiEQ .= json_decode($peserta->hasilKecerdasanEmosi->$field)->uraian_potensi ?? '-';
                }
            }

            // Belajar Cepat & Pengembangan Diri
            if (!empty($peserta->hasilPengembanganDiri->uraian_potensi)) {
                $deskripsiPengembangan = json_decode($peserta->hasilPengembanganDiri->uraian_potensi)->uraian_potensi ?? '-';
            } else {
                $deskripsiPengembangan = '';
                for ($i = 1; $i <= 5; $i++) {
                    $field = 'uraian_potensi_' . $i;
                    $deskripsiPengembangan .= json_decode($peserta->hasilPengembanganDiri->$field)->uraian_potensi ?? '-';
                }
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
                // 'Deskripsi Intelektual' => ($peserta->hasilIntelektual->uraian_potensi_subtes_1 ?? '-') .
                //     ($peserta->hasilIntelektual->uraian_potensi_subtes_2 ?? '-') .
                //     ($peserta->hasilIntelektual->uraian_potensi_subtes_3 ?? '-'),
                // 'Deskripsi Interpersonal' => (json_decode($peserta->hasilInterpersonal->uraian_potensi_1)->uraian_potensi ?? '-') .
                //     (json_decode($peserta->hasilInterpersonal->uraian_potensi_2)->uraian_potensi ?? '-') .
                //     (json_decode($peserta->hasilInterpersonal->uraian_potensi_3)->uraian_potensi ?? '-') .
                //     (json_decode($peserta->hasilInterpersonal->uraian_potensi_4)->uraian_potensi ?? '-') .
                //     (json_decode($peserta->hasilInterpersonal->uraian_potensi_5)->uraian_potensi ?? '-'),
                // 'Deskripsi Kesadaran Diri' => (json_decode($peserta->hasilKesadaranDiri->uraian_potensi_1)->uraian_potensi ?? '-') .
                //     (json_decode($peserta->hasilKesadaranDiri->uraian_potensi_2)->uraian_potensi ?? '-') .
                //     (json_decode($peserta->hasilKesadaranDiri->uraian_potensi_3)->uraian_potensi ?? '-'),
                // 'Deskripsi Berpikir Kritis dan Strategis' => (json_decode($peserta->hasilBerpikirKritis->uraian_potensi_1)->deskripsi ?? '-') .
                //     (json_decode($peserta->hasilBerpikirKritis->uraian_potensi_2)->deskripsi ?? '-') .
                //     (json_decode($peserta->hasilBerpikirKritis->uraian_potensi_3)->deskripsi ?? '-') .
                //     (json_decode($peserta->hasilBerpikirKritis->uraian_potensi_4)->deskripsi ?? '-') .
                //     (json_decode($peserta->hasilBerpikirKritis->uraian_potensi_5)->deskripsi ?? '-') .
                //     (json_decode($peserta->hasilBerpikirKritis->uraian_potensi_6)->deskripsi ?? '-') .
                //     (json_decode($peserta->hasilBerpikirKritis->uraian_potensi_7)->deskripsi ?? '-') .
                //     (json_decode($peserta->hasilBerpikirKritis->uraian_potensi_8)->deskripsi ?? '-'),
                // 'Deskripsi Problem Solving' => (json_decode($peserta->hasilProblemSolving->uraian_potensi_1)->deskripsi ?? '-') .
                //     (json_decode($peserta->hasilProblemSolving->uraian_potensi_2)->deskripsi ?? '-') .
                //     (json_decode($peserta->hasilProblemSolving->uraian_potensi_3)->deskripsi ?? '-') .
                //     (json_decode($peserta->hasilProblemSolving->uraian_potensi_4)->deskripsi ?? '-') .
                //     (json_decode($peserta->hasilProblemSolving->uraian_potensi_5)->deskripsi ?? '-') .
                //     (json_decode($peserta->hasilProblemSolving->uraian_potensi_6)->deskripsi ?? '-') .
                //     (json_decode($peserta->hasilProblemSolving->uraian_potensi_7)->deskripsi ?? '-') .
                //     (json_decode($peserta->hasilProblemSolving->uraian_potensi_8)->deskripsi ?? '-'),
                // 'Deskripsi EQ' => (json_decode($peserta->hasilKecerdasanEmosi->uraian_potensi_1)->uraian_potensi ?? '-') .
                //     (json_decode($peserta->hasilKecerdasanEmosi->uraian_potensi_2)->uraian_potensi ?? '-') .
                //     (json_decode($peserta->hasilKecerdasanEmosi->uraian_potensi_3)->uraian_potensi ?? '-') .
                //     (json_decode($peserta->hasilKecerdasanEmosi->uraian_potensi_4)->uraian_potensi ?? '-'),
                // 'Deskripsi Belajar Cepat dan Pengembangan Diri' => (json_decode($peserta->hasilPengembanganDiri->uraian_potensi_1)->uraian_potensi ?? '-') .
                //     (json_decode($peserta->hasilPengembanganDiri->uraian_potensi_2)->uraian_potensi ?? '-') .
                //     (json_decode($peserta->hasilPengembanganDiri->uraian_potensi_3)->uraian_potensi ?? '-') .
                //     (json_decode($peserta->hasilPengembanganDiri->uraian_potensi_4)->uraian_potensi ?? '-') .
                //     (json_decode($peserta->hasilPengembanganDiri->uraian_potensi_5)->uraian_potensi ?? '-'),
                // 'Deskripsi Motivasi Komitmen' => $peserta->hasilMotivasiKomitmen->deskripsi ?? '-'
                'Deskripsi Intelektual' => ($peserta->hasilIntelektual->uraian_potensi_subtes_1 ?? '-') .
                    ($peserta->hasilIntelektual->uraian_potensi_subtes_2 ?? '-') .
                    ($peserta->hasilIntelektual->uraian_potensi_subtes_3 ?? '-'),

                'Deskripsi Berpikir Kritis dan Strategis' => $deskripsiBerpikir,
                'Deskripsi Problem Solving' => $deskripsiProblem,
                'Deskripsi Belajar Cepat dan Pengembangan Diri' => $deskripsiPengembangan,
                'Deskripsi Motivasi Komitmen' => $peserta->hasilMotivasiKomitmen->deskripsi ?? '-',
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
            $export_data->push([
                'Nama Peserta' => $peserta->nama,
                'NIP/NIK' => $peserta->nip ?: $peserta->nik,
                'Jabatan' => $peserta->jabatan,
                'Unit Kerja' => $peserta->unit_kerja,
                'Instansi' => $peserta->instansi,
                'JPM' => $peserta->hasilPspk?->jpm . '%' ?? '',
                'Kategori' => $peserta->hasilPspk?->kategori ?? '',
                'Tanggal Tes' => \Carbon\Carbon::parse($peserta->test_started_at)->format('d/m/Y'),
            ]);
        }

        return (new FastExcel($export_data))->download('rekap-laporan-pspk.xlsx');
    }
}
