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
            $export_data->push([
                'Nama Peserta' => $peserta->nama,
                'NIP/NIK' => $peserta->nip ?: $peserta->nik,
                'Jabatan Saat Ini' => $peserta->jabatan,
                'OPD' => $peserta->instansi . ' - ' . $peserta->unit_kerja,
                'Tanggal Tes' => \Carbon\Carbon::parse($peserta->test_started_at)->format('d/m/Y'),
                'Intelektual' => $peserta->hasilIntelektual->level ?? '-',
                'Interpersonal' => $peserta->hasilInterpersonal->level_total ?? '-',
                'Kesadaran Diri' => $peserta->hasilKesadaranDiri->level_total ?? '-',
                'Berpikir Kritis dan Strategis' => $peserta->hasilBerpikirKritis->level_total ?? '-',
                'Problem Solving' => $peserta->hasilProblemSolving->level_total ?? '-',
                'EQ' => $peserta->hasilKecerdasanEmosi->level_total ?? '-',
                'Belajar Cepat dan Pengembangan Diri' => $peserta->hasilPengembanganDiri->level_total ?? '-',
                'Motivasi Komitmen' => $peserta->hasilMotivasiKomitmen->level_total ?? '-',
                'Intelektual (Capaian Level)' => capaianLevel(optional($peserta->hasilIntelektual)->level) ?? '-',
                'Interpersonal (Capaian Level)' => capaianLevel($peserta->hasilInterpersonal->level_total) ?? '-',
                'Kesadaran Diri (Capaian Level)' => capaianLevel($peserta->hasilKesadaranDiri->level_total) ?? '-',
                'Berpikir Kritis dan Strategis (Capaian Level)' => capaianLevel($peserta->hasilBerpikirKritis->level_total) ?? '-',
                'Problem Solving (Capaian Level)' => capaianLevel($peserta->hasilProblemSolving->level_total) ?? '-',
                'EQ (Capaian Level)' => capaianLevel($peserta->hasilKecerdasanEmosi->level_total) ?? '-',
                'Belajar Cepat dan Pengembangan Diri (Capaian Level)' => capaianLevel($peserta->hasilPengembanganDiri->level_total) ?? '-',
                'Motivasi Komitmen (Capaian Level)' => capaianLevel($peserta->hasilMotivasiKomitmen->level_total) ?? '-',
                'JPM Potensi' => $peserta->nilaiJpm->jpm . '%' ?? '-',
                'Kesimpulan' => $peserta->nilaiJpm->kategori ?? '-',
                'Deskripsi Intelektual' => ($peserta->hasilIntelektual->uraian_potensi_subtes_1 ?? '-') .
                    ($peserta->hasilIntelektual->uraian_potensi_subtes_2 ?? '-') .
                    ($peserta->hasilIntelektual->uraian_potensi_subtes_3 ?? '-'),
                'Deskripsi Interpersonal' => (json_decode($peserta->hasilInterpersonal->uraian_potensi_1)->uraian_potensi ?? '-') .
                    (json_decode($peserta->hasilInterpersonal->uraian_potensi_2)->uraian_potensi ?? '-') .
                    (json_decode($peserta->hasilInterpersonal->uraian_potensi_3)->uraian_potensi ?? '-') .
                    (json_decode($peserta->hasilInterpersonal->uraian_potensi_4)->uraian_potensi ?? '-') .
                    (json_decode($peserta->hasilInterpersonal->uraian_potensi_5)->uraian_potensi ?? '-'),
                'Deskripsi Kesadaran Diri' => (json_decode($peserta->hasilKesadaranDiri->uraian_potensi_1)->uraian_potensi ?? '-') .
                    (json_decode($peserta->hasilKesadaranDiri->uraian_potensi_2)->uraian_potensi ?? '-') .
                    (json_decode($peserta->hasilKesadaranDiri->uraian_potensi_3)->uraian_potensi ?? '-'),
                'Deskripsi Berpikir Kritis dan Strategis' => (json_decode($peserta->hasilBerpikirKritis->uraian_potensi_1)->deskripsi ?? '-') .
                    (json_decode($peserta->hasilBerpikirKritis->uraian_potensi_2)->deskripsi ?? '-') .
                    (json_decode($peserta->hasilBerpikirKritis->uraian_potensi_3)->deskripsi ?? '-') .
                    (json_decode($peserta->hasilBerpikirKritis->uraian_potensi_4)->deskripsi ?? '-') .
                    (json_decode($peserta->hasilBerpikirKritis->uraian_potensi_5)->deskripsi ?? '-') .
                    (json_decode($peserta->hasilBerpikirKritis->uraian_potensi_6)->deskripsi ?? '-') .
                    (json_decode($peserta->hasilBerpikirKritis->uraian_potensi_7)->deskripsi ?? '-') .
                    (json_decode($peserta->hasilBerpikirKritis->uraian_potensi_8)->deskripsi ?? '-'),
                'Deskripsi Problem Solving' => (json_decode($peserta->hasilProblemSolving->uraian_potensi_1)->deskripsi ?? '-') .
                    (json_decode($peserta->hasilProblemSolving->uraian_potensi_2)->deskripsi ?? '-') .
                    (json_decode($peserta->hasilProblemSolving->uraian_potensi_3)->deskripsi ?? '-') .
                    (json_decode($peserta->hasilProblemSolving->uraian_potensi_4)->deskripsi ?? '-') .
                    (json_decode($peserta->hasilProblemSolving->uraian_potensi_5)->deskripsi ?? '-') .
                    (json_decode($peserta->hasilProblemSolving->uraian_potensi_6)->deskripsi ?? '-') .
                    (json_decode($peserta->hasilProblemSolving->uraian_potensi_7)->deskripsi ?? '-') .
                    (json_decode($peserta->hasilProblemSolving->uraian_potensi_8)->deskripsi ?? '-'),
                'Deskripsi EQ' => (json_decode($peserta->hasilKecerdasanEmosi->uraian_potensi_1)->uraian_potensi ?? '-') .
                    (json_decode($peserta->hasilKecerdasanEmosi->uraian_potensi_2)->uraian_potensi ?? '-') .
                    (json_decode($peserta->hasilKecerdasanEmosi->uraian_potensi_3)->uraian_potensi ?? '-') .
                    (json_decode($peserta->hasilKecerdasanEmosi->uraian_potensi_4)->uraian_potensi ?? '-'),
                'Deskripsi Belajar Cepat dan Pengembangan Diri' => (json_decode($peserta->hasilPengembanganDiri->uraian_potensi_1)->uraian_potensi ?? '-') .
                    (json_decode($peserta->hasilPengembanganDiri->uraian_potensi_2)->uraian_potensi ?? '-') .
                    (json_decode($peserta->hasilPengembanganDiri->uraian_potensi_3)->uraian_potensi ?? '-') .
                    (json_decode($peserta->hasilPengembanganDiri->uraian_potensi_4)->uraian_potensi ?? '-') .
                    (json_decode($peserta->hasilPengembanganDiri->uraian_potensi_5)->uraian_potensi ?? '-'),
                'Deskripsi Motivasi Komitmen' => $peserta->hasilMotivasiKomitmen->deskripsi ?? '-'
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
                'Jabatan Saat Ini' => $peserta->jabatan,
                'OPD' => $peserta->instansi . ' - ' . $peserta->unit_kerja,
                'Level Literasi Digital' => $peserta->hasilCakapDigital->kategori_literasi ?? '-',
                'Level Emerging Skill' => $peserta->hasilCakapDigital->kategori_emerging ?? '-',
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
                'OPD' => $peserta->instansi . ' - ' . $peserta->unit_kerja,
                'Tanggal Tes' => \Carbon\Carbon::parse($peserta->test_started_at)->format('d/m/Y'),
                'JPM' => $peserta->hasilKompetensiTeknis->jpm . '%' ?? '-',
                'Kategori' => $peserta->hasilKompetensiTeknis->kategori ?? '-',
                'Deskripsi' => $peserta->hasilKompetensiTeknis->deskripsi ?? '-'
            ]);
        }

        return (new FastExcel($export_data))->download('rekap-laporan-kompetensi-teknis.xlsx');
    }
}
