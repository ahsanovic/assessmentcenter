<?php

namespace App\Services\Rekap;

use App\Models\Peserta;
use App\Models\RefAspekPspk;
use Illuminate\Support\Collection;

class EventRekapRowBuilder
{
    public function buildPotensiRows(Collection $pesertaList): Collection
    {
        $export_data = collect();

        foreach ($pesertaList as $peserta) {
            $formatLines = function (array $lines) {
                $nonEmpty = array_filter($lines, function ($desc) {
                    return ! empty($desc) && $desc !== '-';
                });
                if (count($nonEmpty) > 1) {
                    return implode("\n", array_map(function ($line) {
                        return '- ' . trim($line);
                    }, $lines));
                }
                if (isset($lines[0])) {
                    if (! empty($lines[0]) && $lines[0] != '-') {
                        return '- ' . trim($lines[0]);
                    }

                    return '-';
                }

                return '-';
            };

            if (! empty($peserta->hasilBerpikirKritis->uraian_potensi)) {
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

            if (! empty($peserta->hasilProblemSolving->uraian_potensi)) {
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

            if (! empty($peserta->hasilInterpersonal->uraian_potensi)) {
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

            if (! empty($peserta->hasilKesadaranDiri->uraian_potensi)) {
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

            if (! empty($peserta->hasilKecerdasanEmosi->uraian_potensi)) {
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

            if (! empty($peserta->hasilPengembanganDiri->uraian_potensi)) {
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

            $deskripsiIntelektual = [];
            if (
                ! empty($peserta->hasilIntelektual->uraian_potensi_subtes_1) ||
                ! empty($peserta->hasilIntelektual->uraian_potensi_subtes_2) ||
                ! empty($peserta->hasilIntelektual->uraian_potensi_subtes_3)
            ) {
                if (! empty($peserta->hasilIntelektual->uraian_potensi_subtes_1)) {
                    $deskripsiIntelektual[] = $peserta->hasilIntelektual->uraian_potensi_subtes_1;
                }
                if (! empty($peserta->hasilIntelektual->uraian_potensi_subtes_2)) {
                    $deskripsiIntelektual[] = $peserta->hasilIntelektual->uraian_potensi_subtes_2;
                }
                if (! empty($peserta->hasilIntelektual->uraian_potensi_subtes_3)) {
                    $deskripsiIntelektual[] = $peserta->hasilIntelektual->uraian_potensi_subtes_3;
                }
            }
            if (count($deskripsiIntelektual) > 1) {
                $deskripsiIntelektualFormatted = implode("\n", array_map(function ($desc) {
                    return '- ' . trim($desc);
                }, $deskripsiIntelektual));
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
                'Tanggal Tes' => $peserta->test_started_at ? \Carbon\Carbon::parse($peserta->test_started_at)->format('d/m/Y') : '-',
                'Intelektual (Capaian Level)' => capaianLevel(optional($peserta->hasilIntelektual)->level) ?? '-',
                'Interpersonal (Capaian Level)' => capaianLevel($peserta->hasilInterpersonal->level_total) ?? '-',
                'Kesadaran Diri (Capaian Level)' => capaianLevel($peserta->hasilKesadaranDiri->level_total) ?? '-',
                'Berpikir Kritis dan Strategis (Capaian Level)' => capaianLevel($peserta->hasilBerpikirKritis->level_total) ?? '-',
                'Problem Solving (Capaian Level)' => capaianLevel($peserta->hasilProblemSolving->level_total) ?? '-',
                'EQ (Capaian Level)' => capaianLevel($peserta->hasilKecerdasanEmosi->level_total) ?? '-',
                'Belajar Cepat dan Pengembangan Diri (Capaian Level)' => capaianLevel($peserta->hasilPengembanganDiri->level_total) ?? '-',
                'Motivasi Komitmen (Capaian Level)' => capaianLevel($peserta->hasilMotivasiKomitmen->level_total) ?? '-',
                'JPM Potensi' => $peserta->nilaiJpm?->jpm !== null ? $peserta->nilaiJpm->jpm . '%' : '-',
                'Kesimpulan' => $peserta->nilaiJpm?->kategori ?? '-',
                'Deskripsi Intelektual' => $deskripsiIntelektualFormatted,
                'Deskripsi Interpersonal' => $deskripsiInterpersonal,
                'Deskripsi Kesadaran Diri' => $deskripsiKesadaran,
                'Deskripsi Berpikir Kritis dan Strategis' => $deskripsiBerpikir,
                'Deskripsi Problem Solving' => $deskripsiProblem,
                'Deskripsi EQ' => $deskripsiEQ,
                'Deskripsi Belajar Cepat dan Pengembangan Diri' => $deskripsiPengembangan,
                'Deskripsi Motivasi Komitmen' => ! empty($peserta->hasilMotivasiKomitmen->deskripsi) ? '- ' . trim($peserta->hasilMotivasiKomitmen->deskripsi) : '-',
            ]);
        }

        return $export_data;
    }

    public function buildCakapDigitalRows(Collection $pesertaList): Collection
    {
        return $pesertaList->map(function (Peserta $peserta) {
            return [
                'Nama Peserta' => $peserta->nama,
                'NIP/NIK' => $peserta->nip ?: $peserta->nik,
                'Jabatan' => $peserta->jabatan,
                'Unit Kerja' => $peserta->unit_kerja,
                'Instansi' => $peserta->instansi,
                'JPM LD' => $peserta->hasilCakapDigital?->jpm_literasi !== null ? $peserta->hasilCakapDigital->jpm_literasi . '%' : '',
                'Kategori LD' => $peserta->hasilCakapDigital?->kesimpulan_literasi ?? '',
                'Deskripsi LD' => $peserta->hasilCakapDigital?->deskripsi_literasi ?? '',
                'JPM ES' => $peserta->hasilCakapDigital?->jpm_emerging !== null ? $peserta->hasilCakapDigital->jpm_emerging . '%' : '',
                'Kategori ES' => $peserta->hasilCakapDigital?->kesimpulan_emerging ?? '',
                'Deskripsi ES' => $peserta->hasilCakapDigital?->deskripsi_emerging ?? '',
                'Tanggal Tes' => $peserta->test_started_at ? \Carbon\Carbon::parse($peserta->test_started_at)->format('d/m/Y') : '-',
            ];
        });
    }

    public function buildKompetensiTeknisRows(Collection $pesertaList): Collection
    {
        return $pesertaList->map(function (Peserta $peserta) {
            return [
                'Nama Peserta' => $peserta->nama,
                'NIP/NIK' => $peserta->nip ?: $peserta->nik,
                'Jabatan' => $peserta->jabatan,
                'Unit Kerja' => $peserta->unit_kerja,
                'Instansi' => $peserta->instansi,
                'Tanggal Tes' => $peserta->test_started_at ? \Carbon\Carbon::parse($peserta->test_started_at)->format('d/m/Y') : '-',
                'JPM' => $peserta->hasilKompetensiTeknis?->jpm !== null ? $peserta->hasilKompetensiTeknis->jpm . '%' : '',
                'Kategori' => $peserta->hasilKompetensiTeknis?->kategori ?? '',
                'Deskripsi' => $peserta->hasilKompetensiTeknis?->deskripsi ?? '',
            ];
        });
    }

    public function buildPspkRows(Collection $pesertaList): Collection
    {
        $aspek_potensi = RefAspekPspk::all();
        $export_data = collect();

        foreach ($pesertaList as $peserta) {
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

            $row['JPM'] = $peserta->hasilPspk?->jpm !== null ? $peserta->hasilPspk->jpm . '%' : '';
            $row['Kategori'] = $peserta->hasilPspk?->kategori ?? '';
            $row['Tanggal Tes'] = $peserta->test_started_at ? \Carbon\Carbon::parse($peserta->test_started_at)->format('d/m/Y') : '-';

            $export_data->push($row);
        }

        return $export_data;
    }

    public function buildSummarySnippet(Peserta $peserta, int $metodeTesId): array
    {
        return match ($metodeTesId) {
            2 => [
                'JPM Potensi' => $peserta->nilaiJpm?->jpm !== null ? $peserta->nilaiJpm->jpm . '%' : '',
                'Kesimpulan Potensi' => $peserta->nilaiJpm?->kategori ?? '',
            ],
            3 => [
                'JPM Literasi Digital' => $peserta->hasilCakapDigital?->jpm_literasi !== null ? $peserta->hasilCakapDigital->jpm_literasi . '%' : '',
                'Kategori Literasi Digital' => $peserta->hasilCakapDigital?->kesimpulan_literasi ?? '',
                'JPM Emerging Skill' => $peserta->hasilCakapDigital?->jpm_emerging !== null ? $peserta->hasilCakapDigital->jpm_emerging . '%' : '',
                'Kategori Emerging Skill' => $peserta->hasilCakapDigital?->kesimpulan_emerging ?? '',
            ],
            4 => [
                'JPM Kompetensi Teknis' => $peserta->hasilKompetensiTeknis?->jpm !== null ? $peserta->hasilKompetensiTeknis->jpm . '%' : '',
                'Kesimpulan Kompetensi Teknis' => $peserta->hasilKompetensiTeknis?->kategori ?? '',
            ],
            5, 6, 7, 8 => [
                'JPM PSPK' => $peserta->hasilPspk?->jpm !== null ? $peserta->hasilPspk->jpm . '%' : '',
                'Kesimpulan PSPK' => $peserta->hasilPspk?->kategori ?? '',
            ],
            default => [],
        };
    }
}
