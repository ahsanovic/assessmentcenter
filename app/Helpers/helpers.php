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
use App\Models\KompetensiTeknis\UjianKompetensiTeknis;
use App\Models\MotivasiKomitmen\UjianMotivasiKomitmen;
use App\Models\PengembanganDiri\UjianPengembanganDiri;
use App\Models\ProblemSolving\UjianProblemSolving;
use App\Models\Pspk\UjianPspk;
use Illuminate\Support\Facades\Auth;
use Ulid\Ulid;

if (!function_exists('countJpm')) {
    function countJpm($capaian_level)
    {
        $jpm = array_sum(array_map('floatval', $capaian_level)) / (5 * 8);
        return $jpm;
    }
}

if (!function_exists('getKategori')) {
    function getKategori($jpm)
    {
        // if ($jpm >= 0.9) {
        //     $kategori = 'Optimal';
        // } else if ($jpm >= 0.78) {
        //     $kategori = 'Cukup Optimal';
        // } else {
        //     $kategori = 'Kurang Optimal';
        // }

        if ($jpm >= 0.8 && $jpm <= 1) {
            $kategori = 'Tinggi';
        } else if ($jpm >= 0.6 && $jpm < 0.8) {
            $kategori = 'Menengah';
        } else if ($jpm < 0.6) {
            $kategori = 'Rendah';
        }

        return $kategori;
    }
}

// if (!function_exists('capaianLevel')) {
//     function capaianLevel($level_total)
//     {
//         switch ($level_total) {
//             case '5+':
//                 $capaian_level = '5.5';
//                 break;
//             case '5':
//                 $capaian_level =  '5';
//                 break;
//             case '5-':
//                 $capaian_level =  '4.5';
//                 break;
//             case '4+':
//                 $capaian_level =  '4.33';
//                 break;
//             case '4':
//                 $capaian_level =  '4';
//                 break;
//             case '4-':
//                 $capaian_level =  '3.67';
//                 break;
//             case '3+':
//                 $capaian_level =  '3.5';
//                 break;
//             case '3':
//                 $capaian_level =  '3';
//                 break;
//             case '3-':
//                 $capaian_level =  '2.5';
//                 break;
//             case '2+':
//                 $capaian_level =  '2.33';
//                 break;
//             case '2':
//                 $capaian_level =  '2';
//                 break;
//             case '2-':
//                 $capaian_level =  '1.67';
//                 break;
//             case '1+':
//                 $capaian_level =  '1.33';
//                 break;
//             case '1':
//                 $capaian_level =  '1';
//                 break;
//             case '1-':
//                 $capaian_level =  '0.67';
//                 break;
//             default:
//                 $capaian_level =  '0';
//                 break;
//         }

//         return $capaian_level;
//     }
// }

if (!function_exists('capaianLevel')) {
    function capaianLevel($level_total)
    {
        switch ($level_total) {
            case '5+':
                $capaian_level = '5';
                break;
            case '5':
                $capaian_level =  '5';
                break;
            case '5-':
                $capaian_level =  '4';
                break;
            case '4+':
                $capaian_level =  '4';
                break;
            case '4':
                $capaian_level =  '4';
                break;
            case '4-':
                $capaian_level =  '3';
                break;
            case '3+':
                $capaian_level =  '3';
                break;
            case '3':
                $capaian_level =  '3';
                break;
            case '3-':
                $capaian_level =  '2';
                break;
            case '2+':
                $capaian_level =  '2';
                break;
            case '2':
                $capaian_level =  '2';
                break;
            case '2-':
                $capaian_level =  '1';
                break;
            case '1+':
                $capaian_level =  '1';
                break;
            case '1':
                $capaian_level =  '1';
                break;
            case '1-':
                $capaian_level =  '1';
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

if (!function_exists('getFinishedTesKompetensiTeknis')) {
    function getFinishedTesKompetensiTeknis($event_id, $peserta_id)
    {
        return [
            'tes_kompetensi_teknis' => UjianKompetensiTeknis::where('event_id', $event_id)
                ->where('peserta_id', $peserta_id)
                ->where('is_finished', 'true')
                ->exists(),
        ];
    }
}

if (!function_exists('getFinishedTesPspk')) {
    function getFinishedTesPspk($event_id, $peserta_id)
    {
        return [
            'tes_pspk' => UjianPspk::where('event_id', $event_id)
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

if (!function_exists('parse_nama_gelar')) {
    /**
     * Parse nama lengkap dan ekstrak gelar depan, nama, dan gelar belakang
     * 
     * @param string $namaLengkap Nama lengkap dengan atau tanpa gelar
     * @return array ['gelar_depan' => string|null, 'nama' => string, 'gelar_belakang' => string|null]
     */
    function parse_nama_gelar($namaLengkap)
    {
        $namaLengkap = trim($namaLengkap);
        
        // Daftar gelar depan (case insensitive)
        $gelarDepanList = [
            // Akademik
            'Prof.', 'Prof', 'Dr.', 'Dr', 'Drs.', 'Drs', 'Dra.', 'Dra', 'Drg.', 'Drg', 'Ir.', 'Ir',
            // Medis
            'dr.', 'dr',
            // Keagamaan
            'H.', 'H', 'Hj.', 'Hj', 'KH.', 'KH', 'Ust.', 'Ust', 'Ustd.', 'Ustd', 'Ustadz', 'Ustadzah',
            // Bangsawan/Tradisional
            'R.', 'R', 'RA.', 'RA', 'Raden', 'R.M.', 'RM', 'R.A.', 'Tb.', 'Tb', 'Tubagus',
            // Sapaan
            'Sdr.', 'Sdr', 'Sdri.', 'Sdri',
            // Militer/Polisi (tanpa pangkat lengkap, hanya prefix umum)
            'Letjen', 'Mayjen', 'Brigjen', 'Kolonel', 'Letkol', 'Mayor', 'Kapten', 'Lettu', 'Letda',
            'Komjen', 'Irjen', 'Brigpol', 'Kompol', 'AKP', 'AKBP', 'IPTU', 'IPDA',
        ];
        
        // Daftar gelar belakang (case insensitive)
        $gelarBelakangList = [
            // Sarjana (S1)
            'S.H.', 'S.H', 'SH', 'S.E.', 'S.E', 'SE', 'S.T.', 'S.T', 'ST', 'S.Kom.', 'S.Kom', 'SKom',
            'S.Pd.', 'S.Pd', 'SPd', 'S.Sos.', 'S.Sos', 'SSos', 'S.Ag.', 'S.Ag', 'SAg',
            'S.Si.', 'S.Si', 'SSi', 'S.Ked.', 'S.Ked', 'SKed', 'S.Kep.', 'S.Kep', 'SKep',
            'S.Farm.', 'S.Farm', 'SFarm', 'S.Hut.', 'S.Hut', 'SHut', 'S.IP.', 'S.IP', 'SIP',
            'S.I.Kom.', 'S.I.Kom', 'SIKom', 'S.Psi.', 'S.Psi', 'SPsi', 'S.Kes.', 'S.Kes', 'SKes',
            'S.Keb.', 'S.Keb', 'SKeb', 'S.KM.', 'S.KM', 'SKM', 'S.Gz.', 'S.Gz', 'SGz',
            'S.Tr.', 'S.Tr', 'STr', 'S.Sn.', 'S.Sn', 'SSn', 'S.Ds.', 'S.Ds', 'SDs',
            'S.Ars.', 'S.Ars', 'SArs', 'S.Ak.', 'S.Ak', 'SAk', 'S.AB.', 'S.AB', 'SAB',
            'S.Pt.', 'S.Pt', 'SPt', 'S.Pi.', 'S.Pi', 'SPi', 'S.P.', 'S.P', 'SP',
            'S.Th.', 'S.Th', 'STh', 'S.Fil.', 'S.Fil', 'SFil', 'S.Mn.', 'S.Mn', 'SMn',
            'S.Stat.', 'S.Stat', 'SStat', 'S.Mat.', 'S.Mat', 'SMat',
            // Magister (S2)
            'M.H.', 'M.H', 'MH', 'M.M.', 'M.M', 'MM', 'M.T.', 'M.T', 'MT', 'M.Kom.', 'M.Kom', 'MKom',
            'M.Pd.', 'M.Pd', 'MPd', 'M.Si.', 'M.Si', 'MSi', 'M.Sc.', 'M.Sc', 'MSc',
            'M.A.', 'M.A', 'MA', 'M.Eng.', 'M.Eng', 'MEng', 'M.Kes.', 'M.Kes', 'MKes',
            'M.Kep.', 'M.Kep', 'MKep', 'M.Hum.', 'M.Hum', 'MHum', 'M.Sn.', 'M.Sn', 'MSn',
            'M.Sos.', 'M.Sos', 'MSos', 'M.Ak.', 'M.Ak', 'MAk', 'M.Fil.', 'M.Fil', 'MFil',
            'M.Psi.', 'M.Psi', 'MPsi', 'M.Ag.', 'M.Ag', 'MAg', 'M.I.Kom.', 'M.I.Kom', 'MIKom',
            'M.IP.', 'M.IP', 'MIP', 'M.AP.', 'M.AP', 'MAP', 'M.Par.', 'M.Par', 'MPar',
            // Doktor (S3)
            'Ph.D.', 'Ph.D', 'PhD', 'Dr.', 'Dr',
            // Profesi
            'Apt.', 'Apt', 'Ak.', 'Ak', 'Akt.', 'Akt',
            // Internasional
            'LL.M.', 'LL.M', 'LLM', 'LL.B.', 'LL.B', 'LLB', 'MBA.', 'MBA', 'MPA.', 'MPA',
            'MPH.', 'MPH', 'MPhil.', 'MPhil', 'M.Phil.', 'M.Phil',
            // Spesialis Kedokteran
            'Sp.A.', 'Sp.A', 'SpA', 'Sp.B.', 'Sp.B', 'SpB', 'Sp.OG.', 'Sp.OG', 'SpOG',
            'Sp.PD.', 'Sp.PD', 'SpPD', 'Sp.An.', 'Sp.An', 'SpAn', 'Sp.JP.', 'Sp.JP', 'SpJP',
            'Sp.KJ.', 'Sp.KJ', 'SpKJ', 'Sp.M.', 'Sp.M', 'SpM', 'Sp.S.', 'Sp.S', 'SpS',
            'Sp.THT.', 'Sp.THT', 'SpTHT', 'Sp.Rad.', 'Sp.Rad', 'SpRad', 'Sp.PA.', 'Sp.PA', 'SpPA',
            'Sp.PK.', 'Sp.PK', 'SpPK', 'Sp.KK.', 'Sp.KK', 'SpKK', 'Sp.U.', 'Sp.U', 'SpU',
            'Sp.BP-RE.', 'Sp.BP-RE', 'SpBP-RE', 'Sp.GK.', 'Sp.GK', 'SpGK', 'Sp.EM.', 'Sp.EM', 'SpEM',
            'Sp.F.', 'Sp.F', 'SpF', 'Sp.FK.', 'Sp.FK', 'SpFK', 'Sp.KFR.', 'Sp.KFR', 'SpKFR',
            'Sp.OK.', 'Sp.OK', 'SpOK', 'Sp.OT.', 'Sp.OT', 'SpOT', 'Sp.P.', 'Sp.P', 'SpP',
            'Sp.N.', 'Sp.N', 'SpN', 'Sp.BS.', 'Sp.BS', 'SpBS',
            'MARS', 'M.Ars', 'MArch',
            // Professional Certifications
            'CPA.', 'CPA', 'ACCA.', 'ACCA', 'CA.', 'CA', 'CFP.', 'CFP', 'CFA.', 'CFA',
            'CHRP.', 'CHRP', 'PMP.', 'PMP', 'CISA.', 'CISA', 'CISSP.', 'CISSP',
            // Keagamaan
            'M.Th.I.', 'M.Th.I', 'MThI', 'M.Pd.I.', 'M.Pd.I', 'MPdI', 'Lc.', 'Lc',
        ];
        
        $gelarDepan = null;
        $gelarBelakang = null;
        $nama = $namaLengkap;
        
        // 1. Ekstrak gelar depan
        $foundGelarDepan = [];
        $tempNama = $namaLengkap;
        
        // Cari gelar depan dari awal nama
        $continueSearch = true;
        while ($continueSearch) {
            $continueSearch = false;
            foreach ($gelarDepanList as $gelar) {
                // Buat pattern yang case insensitive
                $pattern = '/^' . preg_quote($gelar, '/') . '\.?\s+/i';
                if (preg_match($pattern, $tempNama, $matches)) {
                    $foundGelar = trim($matches[0]);
                    // Normalisasi gelar (tambahkan titik jika tidak ada)
                    if (!str_ends_with($foundGelar, '.')) {
                        $foundGelar = rtrim($foundGelar) . '.';
                    }
                    $foundGelarDepan[] = $foundGelar;
                    $tempNama = trim(preg_replace($pattern, '', $tempNama, 1));
                    $continueSearch = true;
                    break;
                }
            }
        }
        
        if (!empty($foundGelarDepan)) {
            $gelarDepan = implode(' ', $foundGelarDepan);
            $nama = $tempNama;
        }
        
        // 2. Ekstrak gelar belakang
        // Gelar belakang biasanya dipisahkan dengan koma atau spasi setelah nama
        $foundGelarBelakang = [];
        
        // Cek apakah ada koma di nama (pemisah gelar belakang)
        if (strpos($nama, ',') !== false) {
            $parts = explode(',', $nama, 2);
            $namaPart = trim($parts[0]);
            $gelarPart = isset($parts[1]) ? trim($parts[1]) : '';
            
            if (!empty($gelarPart)) {
                // Parse gelar belakang yang dipisah koma
                $gelarItems = preg_split('/[,\s]+/', $gelarPart);
                foreach ($gelarItems as $item) {
                    $item = trim($item);
                    if (empty($item)) continue;
                    
                    foreach ($gelarBelakangList as $gelar) {
                        if (strcasecmp($item, str_replace('.', '', $gelar)) === 0 || 
                            strcasecmp($item, $gelar) === 0) {
                            // Normalisasi gelar
                            $normalizedGelar = $item;
                            if (!str_contains($normalizedGelar, '.') && strlen($normalizedGelar) > 1) {
                                // Cari format yang benar dari list
                                foreach ($gelarBelakangList as $g) {
                                    if (strcasecmp(str_replace('.', '', $g), $normalizedGelar) === 0 && str_contains($g, '.')) {
                                        $normalizedGelar = $g;
                                        break;
                                    }
                                }
                            }
                            $foundGelarBelakang[] = $normalizedGelar;
                            break;
                        }
                    }
                }
                
                if (!empty($foundGelarBelakang)) {
                    $nama = $namaPart;
                    $gelarBelakang = implode(', ', $foundGelarBelakang);
                }
            }
        } else {
            // Cek gelar belakang tanpa koma (di akhir nama)
            $words = preg_split('/\s+/', $nama);
            $namaWords = [];
            $gelarWords = [];
            $inGelar = false;
            
            foreach ($words as $index => $word) {
                $isGelar = false;
                $cleanWord = trim($word, '.,');
                
                foreach ($gelarBelakangList as $gelar) {
                    $cleanGelar = trim($gelar, '.');
                    if (strcasecmp($cleanWord, $cleanGelar) === 0 || 
                        strcasecmp($cleanWord, str_replace('.', '', $gelar)) === 0) {
                        $isGelar = true;
                        // Normalisasi
                        $normalizedGelar = $cleanWord;
                        foreach ($gelarBelakangList as $g) {
                            if (strcasecmp(str_replace('.', '', $g), $cleanWord) === 0 && str_contains($g, '.')) {
                                $normalizedGelar = $g;
                                break;
                            }
                        }
                        $gelarWords[] = $normalizedGelar;
                        $inGelar = true;
                        break;
                    }
                }
                
                if (!$isGelar && !$inGelar) {
                    $namaWords[] = $word;
                } elseif (!$isGelar && $inGelar) {
                    // Setelah menemukan gelar, kata berikutnya juga kemungkinan gelar
                    // tapi jika bukan gelar, anggap bagian dari nama
                    $namaWords[] = $word;
                }
            }
            
            if (!empty($gelarWords)) {
                $nama = implode(' ', $namaWords);
                $gelarBelakang = implode(', ', $gelarWords);
            }
        }
        
        // Bersihkan nama dari spasi berlebih
        $nama = trim(preg_replace('/\s+/', ' ', $nama));
        
        return [
            'gelar_depan' => !empty($gelarDepan) ? $gelarDepan : null,
            'nama' => $nama,
            'gelar_belakang' => !empty($gelarBelakang) ? $gelarBelakang : null,
        ];
    }
}
