<?php

use App\Models\BerpikirKritis\UjianBerpikirKritis;
use App\Models\Interpersonal\UjianInterpersonal;
use App\Models\KecerdasanEmosi\UjianKecerdasanEmosi;
use App\Models\KesadaranDiri\UjianKesadaranDiri;
use App\Models\MotivasiKomitmen\UjianMotivasiKomitmen;
use App\Models\PengembanganDiri\UjianPengembanganDiri;
use App\Models\ProblemSolving\UjianProblemSolving;

if (!function_exists('countJpm')) {
    function countJpm($capaian_level)
    {
        $jpm = array_sum(array_map('floatval', $capaian_level)) / (3 * 7);
        return $jpm;
    }
}

if (!function_exists('getKategori')) {
    function getKategori($jpm)
    {
        if ($jpm >= 0.9) {
            $kategori = 'Optimal';
        } else if ($jpm >= 0.78) {
            $kategori = 'Cukup Optimal';
        } else {
            $kategori = 'Kurang Optimal';
        }

        return $kategori;
    }
}

if (!function_exists('capaianLevel')) {
    function capaianLevel($level_total)
    {
        switch ($level_total) {
            case '5+':
                $capaian_level = '5.5';
                break;
            case '5':
                $capaian_level =  '5';
                break;
            case '5-':
                $capaian_level =  '4.5';
                break;
            case '4+':
                $capaian_level =  '4.33';
                break;
            case '4':
                $capaian_level =  '4';
                break;
            case '4-':
                $capaian_level =  '3.67';
                break;
            case '3+':
                $capaian_level =  '3.5';
                break;
            case '3':
                $capaian_level =  '3';
                break;
            case '3-':
                $capaian_level =  '2.5';
                break;
            case '2+':
                $capaian_level =  '2.33';
                break;
            case '2':
                $capaian_level =  '2';
                break;
            case '2-':
                $capaian_level =  '1.67';
                break;
            case '1+':
                $capaian_level =  '1.33';
                break;
            case '1':
                $capaian_level =  '1';
                break;
            case '1-':
                $capaian_level =  '0.67';
                break;
            default:
                $capaian_level =  '0';
                break;
        }

        return $capaian_level;
    }
}

if (!function_exists('getFinishedTes')) {
    function getFinishedTes($event_id, $peserta_id)
    {
        return [
            'tes_interpersonal' => UjianInterpersonal::where('event_id', $event_id)
                ->where('peserta_id', $peserta_id)
                ->where('is_finished', 'true')
                ->exists(),
            'tes_kesadaran_diri' => UjianKesadaranDiri::where('event_id', $event_id)
                ->where('peserta_id', $peserta_id)
                ->where('is_finished', 'true')
                ->exists(),
            'tes_berpikir_kritis' => UjianBerpikirKritis::where('event_id', $event_id)
                ->where('peserta_id', $peserta_id)
                ->where('is_finished', 'true')
                ->exists(),
            'tes_problem_solving' => UjianProblemSolving::where('event_id', $event_id)
                ->where('peserta_id', $peserta_id)
                ->where('is_finished', 'true')
                ->exists(),
            'tes_kecerdasan_emosi' => UjianKecerdasanEmosi::where('event_id', $event_id)
                ->where('peserta_id', $peserta_id)
                ->where('is_finished', 'true')
                ->exists(),
            'tes_motivasi_komitmen' => UjianMotivasiKomitmen::where('event_id', $event_id)
                ->where('peserta_id', $peserta_id)
                ->where('is_finished', 'true')
                ->exists(),
            'tes_pengembangan_diri' => UjianPengembanganDiri::where('event_id', $event_id)
                ->where('peserta_id', $peserta_id)
                ->where('is_finished', 'true')
                ->exists(),
        ];
    }
}