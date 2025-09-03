<?php

use App\Models\ActivityLog;
use App\Models\BerpikirKritis\UjianBerpikirKritis;
use App\Models\CakapDigital\UjianCakapDigital;
use App\Models\Intelektual\UjianIntelektualSubTes1;
use App\Models\Intelektual\UjianIntelektualSubTes2;
use App\Models\Intelektual\UjianIntelektualSubTes3;
use App\Models\Interpersonal\UjianInterpersonal;
use App\Models\KecerdasanEmosi\UjianKecerdasanEmosi;
use App\Models\KesadaranDiri\UjianKesadaranDiri;
use App\Models\MotivasiKomitmen\UjianMotivasiKomitmen;
use App\Models\PengembanganDiri\UjianPengembanganDiri;
use App\Models\ProblemSolving\UjianProblemSolving;
use Illuminate\Support\Facades\Auth;
use Ulid\Ulid;

if (!function_exists('countJpm')) {
    function countJpm($capaian_level)
    {
        $jpm = array_sum(array_map('floatval', $capaian_level)) / (3 * 8);
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

if (!function_exists('getFinishedTesCakapDigital')) {
    function getFinishedTesCakapDigital($event_id, $peserta_id)
    {
        return [
            'tes_cakap_digital' => UjianCakapDigital::where('event_id', $event_id)
                ->where('peserta_id', $peserta_id)
                ->where('is_finished', 'true')
                ->exists(),
        ];
    }
}

if (!function_exists('getFinishedTesIntelektual')) {
    function getFinishedTesIntelektual($event_id, $peserta_id)
    {
        return [
            'sub_tes_1' => UjianIntelektualSubTes1::where('event_id', $event_id)
                ->where('peserta_id', $peserta_id)
                ->where('is_finished', 'true')
                ->exists(),
            'sub_tes_2' => UjianIntelektualSubTes2::where('event_id', $event_id)
                ->where('peserta_id', $peserta_id)
                ->where('is_finished', 'true')
                ->exists(),
            'sub_tes_3' => UjianIntelektualSubTes3::where('event_id', $event_id)
                ->where('peserta_id', $peserta_id)
                ->where('is_finished', 'true')
                ->exists(),
        ];
    }
}

if (!function_exists('activity_log')) {
    function activity_log($model, $action, $modul, $old_data = null)
    {
        if (!$model || !method_exists($model, 'getAttributes')) {
            return;
        }

        if (!in_array($action, ['create', 'update', 'delete'])) {
            return;
        }

        ActivityLog::create([
            'id' => Ulid::generate(true),
            'user_id' => Auth::id(),
            'modul' => $modul,
            'action' => $action,
            'model_id' => $model->id,
            'old_data'  => in_array($action, ['update', 'delete']) ? json_encode($old_data) : null,
            'new_data'  => in_array($action, ['create', 'update']) ? json_encode($model->getAttributes()) : null,
            'ip_address' => request()->ip(),
            'user_agent' => request()->header('User-Agent'),
        ]);
    }
}

if (!function_exists('sanitize_log_data')) {
    function sanitize_log_data(array $data)
    {
        $sensitive = ['password', 'id'];
        foreach ($sensitive as $key) {
            if (array_key_exists($key, $data)) {
                unset($data[$key]);
            }
        }
        return $data;
    }
}
