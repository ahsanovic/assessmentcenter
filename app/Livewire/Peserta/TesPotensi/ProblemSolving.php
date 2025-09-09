<?php

namespace App\Livewire\Peserta\TesPotensi;

use App\Models\BerpikirKritis\RefAspekBerpikirKritis;
use App\Models\ProblemSolving\HasilProblemSolving;
use App\Models\ProblemSolving\RefAspekProblemSolving;
use App\Models\ProblemSolving\RefIndikatorProblemSolving;
use App\Models\ProblemSolving\SoalProblemSolving;
use App\Models\ProblemSolving\UjianProblemSolving;
use App\Models\Settings;
use App\Traits\PelanggaranTrait;
use App\Traits\StartTestTrait;
use App\Traits\TimerTrait;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.peserta.app', ['title' => 'Tes Problem Solving'])]
class ProblemSolving extends Component
{
    use StartTestTrait, TimerTrait, PelanggaranTrait;

    public $soal;
    public $jml_soal;
    public $id_soal;
    public $nomor_soal;
    public $jawaban_user = [];
    public $jawaban_kosong;
    public $id_ujian;
    public $timer;
    public $durasi_tes;
    public $waktu_tes_berakhir;
    public $current_sequence;

    public function mount($id)
    {
        $this->id_soal = $id;

        $data = UjianProblemSolving::select('id', 'soal_id', 'jawaban', 'created_at')
            ->where('peserta_id', Auth::guard('peserta')->user()->id)
            ->where('event_id', Auth::guard('peserta')->user()->event_id)
            ->first();

        if ($data->is_finished == 'true') {
            session()->flash('toast', [
                'type' => 'error',
                'message' => 'Anda sudah menyelesaikan tes ini.'
            ]);
            return $this->redirect(route('peserta.tes-potensi.home'), navigate: true);
        }

        $this->nomor_soal = explode(',', $data->soal_id);
        $this->jawaban_user = explode(',', $data->jawaban);
        $this->soal = SoalProblemSolving::find($this->nomor_soal[$this->id_soal - 1]);
        $this->jml_soal = SoalProblemSolving::count();
        $this->id_ujian = $data->id;

        $first_sequence = Settings::with('alatTes')->where('urutan', 1)->first();
        $this->timerTest($first_sequence->alatTes->alat_tes);

        $current_sequence = Settings::with('alatTes')->where('alat_tes_id', 6)->first();
        $this->current_sequence = $current_sequence->urutan;

        for ($i = 0, $j = 0; $i < $this->jml_soal; $i++) {
            if ($this->jawaban_user[$i] == '0') {
                $j = $j + 1;
                $this->jawaban_kosong = $j;
            }
        }
    }

    public function render()
    {
        return view('livewire..peserta.tes-potensi.problem-solving.ujian', [
            'nomor_sekarang' => $this->id_soal,
            'jawaban' => $this->jawaban_user,
            'jawaban_kosong' => $this->jawaban_kosong,
            'jml_soal' => $this->jml_soal,
            'soal' => $this->soal
        ]);
    }

    public function saveAndNext($nomor_soal)
    {
        $index_array = $nomor_soal - 1;
        $data = UjianProblemSolving::where('peserta_id', Auth::guard('peserta')->user()->id)
            ->where('event_id', Auth::guard('peserta')->user()->event_id)
            ->where('is_finished', 'false')
            ->first();

        $soal_id = explode(',', $data->soal_id);

        $jawaban_user = explode(',', $data->jawaban);
        $jawaban_user[$index_array] = $this->jawaban_user[$index_array] ?? '0';
        $jawaban_user_str = implode(',', $jawaban_user);

        // Simpan jawaban user
        $data->jawaban = $jawaban_user_str;
        $data->save();

        // Perbarui Livewire state
        $this->jawaban_user = $jawaban_user;
        $this->jawaban_kosong = collect($this->jawaban_user)->filter(fn($j) => $j == '0')->count();

        $indikator_map = [
            [1, 2, 'nilai_indikator_1'],
            [3, 3, 'nilai_indikator_2'],
            [4, 5, 'nilai_indikator_3'],
            [6, 6, 'nilai_indikator_4'],
            [7, 7, 'nilai_indikator_5'],
            [8, 8, 'nilai_indikator_6'],
            [9, 10, 'nilai_indikator_7'],
            [11, 11, 'nilai_indikator_8'],
        ];

        foreach ($indikator_map as [$start, $end, $indikator]) {
            if ($nomor_soal >= $start && $nomor_soal <= $end) {
                $total_skor = 0;

                for ($i = $start; $i <= $end; $i++) {
                    $idx = $i - 1;
                    $jawaban = $jawaban_user[$idx] ?? null;

                    // Ambil poin dari soal terkait
                    if (isset($soal_id[$idx])) {
                        $poin_soal = SoalProblemSolving::find($soal_id[$idx]);
                        if (!$poin_soal) continue;

                        switch ($jawaban) {
                            case 'A':
                                $total_skor += $poin_soal->poin_opsi_a;
                                break;
                            case 'B':
                                $total_skor += $poin_soal->poin_opsi_b;
                                break;
                            case 'C':
                                $total_skor += $poin_soal->poin_opsi_c;
                                break;
                            case 'D':
                                $total_skor += $poin_soal->poin_opsi_d;
                                break;
                            case 'E':
                                $total_skor += $poin_soal->poin_opsi_e;
                                break;
                            default:
                                $total_skor += 0;
                                break;
                        }
                    }
                }

                // Update skor indikator
                $data->{$indikator} = $total_skor;
                $data->save();
                break;
            }
        }

        if ($nomor_soal < $this->jml_soal) {
            $this->redirect(route('peserta.tes-potensi.problem-solving', ['id' => $nomor_soal + 1]), true);
        } else if ($nomor_soal == $this->jml_soal) {
            $this->redirect(route('peserta.tes-potensi.problem-solving', ['id' => $nomor_soal]), true);
        }
    }

    public function navigate($id)
    {
        if ($id >= 1 && $id <= $this->jml_soal) {
            $this->id_soal = $id;
            $this->soal = SoalProblemSolving::find($this->nomor_soal[$id - 1]);
            $this->redirect(route('peserta.tes-potensi.problem-solving', ['id' => $id]), true);
        }
    }

    public function finish()
    {
        try {
            $data = UjianProblemSolving::findOrFail($this->id_ujian);

            $indikator = [
                1 => $data->nilai_indikator_1,
                2 => $data->nilai_indikator_2,
                3 => $data->nilai_indikator_3,
                4 => $data->nilai_indikator_4,
                5 => $data->nilai_indikator_5,
                6 => $data->nilai_indikator_6,
                7 => $data->nilai_indikator_7,
                8 => $data->nilai_indikator_8,
            ];

            $skor_total = array_sum($indikator);

            if ($skor_total >= 11 && $skor_total <= 33) {
                $level_total = '1';
                $kualifikasi_total = 'Sangat Kurang';
                $kategori_total = 'Rendah';
            } else if ($skor_total >= 34 && $skor_total <= 39) {
                $level_total = '2';
                $kualifikasi_total = 'Kurang';
                $kategori_total = 'Rendah';
            } else if ($skor_total >= 40 && $skor_total <= 41) {
                $level_total = '3-';
                $kualifikasi_total = 'Cukup';
                $kategori_total = 'Sedang';
            } else if ($skor_total == 42) {
                $level_total = '3';
                $kualifikasi_total = 'Cukup';
                $kategori_total = 'Sedang';
            } else if ($skor_total >= 43 && $skor_total <= 44) {
                $level_total = '3+';
                $kualifikasi_total = 'Cukup';
                $kategori_total = 'Sedang';
            } else if ($skor_total >= 45 && $skor_total <= 49) {
                $level_total = '4';
                $kualifikasi_total = 'Baik';
                $kategori_total = 'Tinggi';
            } else if ($skor_total >= 50 && $skor_total <= 55) {
                $level_total = '5';
                $kualifikasi_total = 'Sangat Baik';
                $kategori_total = 'Tinggi';
            }

            // Mapping level ke indikator
            $aspek = RefAspekProblemSolving::get();

            $level_to_indikator = [];
            foreach ($aspek as $aspek_item) {
                $level_to_indikator[$aspek_item->aspek_nomor] = explode(',', $aspek_item->indikator_nomor);
            }

            $level_int = (int) filter_var($level_total, FILTER_SANITIZE_NUMBER_INT);
            $indikator_level = $level_to_indikator[$level_int] ?? [];

            // Ambil deskripsi dari indikator di DB
            $indikator_models = RefIndikatorProblemSolving::whereIn('indikator_nomor', $indikator_level)->get();

            $chosen_indikator = null;
            $chosen_deskripsi = null;
            $max_rank = 0;

            foreach ($indikator_models as $ind) {
                // cari kualifikasi dari skor indikator
                $skor_indikator = $indikator[$ind->indikator_nomor] ?? null;
                $kualifikasi_indikator = $this->_getKualifikasiPerIndikator($skor_indikator, $ind->indikator_nomor);

                // cari yang cocok dengan kualifikasi_indikator
                $found = collect($ind->kualifikasi_deskripsi)->firstWhere('kualifikasi', $kualifikasi_indikator);
                if ($found) {
                    $rank = $this->rank_kualifikasi[$found['kualifikasi']] ?? 0;

                    if ($rank > $max_rank || ($rank == $max_rank && $ind->indikator_nomor > $chosen_indikator)) {
                        $max_rank = $rank;
                        $chosen_indikator = $ind->indikator_nomor;
                        $chosen_deskripsi = $found['deskripsi'];
                    }
                }
            }

            HasilProblemSolving::updateOrCreate(
                [
                    'event_id' => Auth::guard('peserta')->user()->event_id,
                    'peserta_id' => Auth::guard('peserta')->user()->id,
                    'ujian_id' => $data->id,
                ],
                [
                    'skor_total' => $skor_total,
                    'level_total' => $level_total,
                    'kualifikasi_total' => $kualifikasi_total,
                    'kategori_total' => $kategori_total,
                    'uraian_potensi' => $chosen_deskripsi,
                ]
            );

            // change status ujian to true (finish)
            $data->is_finished = true;
            $data->save();

            $current_sequence_test = Settings::where('urutan', $data->urutan_tes)->first(['urutan']);
            if ($current_sequence_test && $current_sequence_test->urutan !== 7) {
                $next_test = Settings::with('alatTes')->where('urutan', $current_sequence_test->urutan + 1)->first();
                $this->startTest($next_test->alatTes->alat_tes, $next_test->urutan);
            } else if ($current_sequence_test && $current_sequence_test->urutan == 7) {
                return $this->redirect(route('peserta.kuesioner'), navigate: true);
            } else {
                return $this->redirect(route('peserta.tes-potensi.home'), navigate: true);
            }

            // return $this->redirect(route('peserta.tes-potensi'), navigate: true);
        } catch (\Throwable $th) {
            // throw $th;
            session()->flash('toast', [
                'type' => 'error',
                'message' => 'Terjadi kesalahan'
            ]);
        }
    }

    private function _getKualifikasiPerIndikator($skor_indikator, $indikator_nomor)
    {
        if (is_null($skor_indikator)) {
            return null;
        }

        // aturan skor per indikator
        $rules = [
            1 => [[1, 3, 'Kurang'], [4, 7, 'Cukup'], [8, 10, 'Baik']],
            2 => [[1, 2, 'Kurang'], [3, 4, 'Cukup'], [5, 5, 'Baik']],
            3 => [[1, 5, 'Kurang'], [6, 9, 'Cukup'], [10, 10, 'Baik']],
            4 => [[1, 2, 'Kurang'], [3, 4, 'Cukup'], [5, 5, 'Baik']],
            5 => [[1, 1, 'Kurang'], [2, 3, 'Cukup'], [4, 5, 'Baik']],
            6 => [[1, 2, 'Kurang'], [3, 4, 'Cukup'], [5, 5, 'Baik']],
            7 => [[1, 4, 'Kurang'], [5, 8, 'Cukup'], [9, 10, 'Baik']],
            8 => [[1, 1, 'Kurang'], [2, 4, 'Cukup'], [5, 5, 'Baik']],
        ];

        if (!isset($rules[$indikator_nomor])) {
            return null;
        }

        foreach ($rules[$indikator_nomor] as [$min, $max, $label]) {
            if ($skor_indikator >= $min && $skor_indikator <= $max) {
                return $label;
            }
        }

        return null;
    }

    // public function finish()
    // {
    //     try {
    //         $data = UjianProblemSolving::findOrFail($this->id_ujian);
    //         $skor_total = $data->nilai_indikator_1 + $data->nilai_indikator_2 + $data->nilai_indikator_3 + $data->nilai_indikator_4 + $data->nilai_indikator_5 + $data->nilai_indikator_6 + $data->nilai_indikator_7 + $data->nilai_indikator_8;

    //         if ($skor_total >= 11 && $skor_total <= 33) {
    //             $level_total = '1';
    //             $kualifikasi_total = 'Sangat Kurang';
    //             $kategori_total = 'Rendah';
    //         } else if ($skor_total >= 34 && $skor_total <= 39) {
    //             $level_total = '2';
    //             $kualifikasi_total = 'Kurang';
    //             $kategori_total = 'Rendah';
    //         } else if ($skor_total >= 40 && $skor_total <= 41) {
    //             $level_total = '3-';
    //             $kualifikasi_total = 'Cukup';
    //             $kategori_total = 'Sedang';
    //         } else if ($skor_total == 42) {
    //             $level_total = '3';
    //             $kualifikasi_total = 'Cukup';
    //             $kategori_total = 'Sedang';
    //         } else if ($skor_total >= 43 && $skor_total <= 44) {
    //             $level_total = '3+';
    //             $kualifikasi_total = 'Cukup';
    //             $kategori_total = 'Sedang';
    //         } else if ($skor_total >= 45 && $skor_total <= 49) {
    //             $level_total = '4';
    //             $kualifikasi_total = 'Baik';
    //             $kategori_total = 'Tinggi';
    //         } else if ($skor_total >= 50 && $skor_total <= 55) {
    //             $level_total = '5';
    //             $kualifikasi_total = 'Sangat Baik';
    //             $kategori_total = 'Tinggi';
    //         }

    //         $skor = HasilProblemSolving::updateOrCreate(
    //             [
    //                 'event_id' => Auth::guard('peserta')->user()->event_id,
    //                 'peserta_id' => Auth::guard('peserta')->user()->id,
    //                 'ujian_id' => $data->id,
    //             ],
    //             [
    //                 'skor_total' => $skor_total,
    //                 'level_total' => $level_total,
    //                 'kualifikasi_total' => $kualifikasi_total,
    //                 'kategori_total' => $kategori_total,
    //             ]
    //         );

    //         // if ($level_total == '3-' || $level_total == '3' || $level_total == '3+') {
    //         //     $level_norma_umum = '3';
    //         // } else {
    //         //     $level_norma_umum = $level_total;
    //         // }

    //         // $aspek = RefAspekProblemSolving::where('aspek_nomor', $level_norma_umum)->first();
    //         // $indikator_nomor = explode(',', $aspek->indikator_nomor);
    //         $indikator = RefIndikatorProblemSolving::get(['indikator_nama', 'indikator_nomor']);
    //         $deskripsi_list = [];
    //         $nilai = [];
    //         foreach ($indikator as $value) {
    //             $kualifikasi_deskripsi = RefIndikatorProblemSolving::where('indikator_nomor', $value->indikator_nomor)->value('kualifikasi_deskripsi');
    //             $deskripsi_data = collect($kualifikasi_deskripsi);

    //             $nilai_indikator = $data->{'nilai_indikator_' . $value->indikator_nomor} ?? null;
    //             if (is_null($nilai_indikator)) {
    //                 continue;
    //             }

    //             if ($value->indikator_nomor == 1) {
    //                 $nilai[] = [
    //                     'indikator' => $value->indikator_nama,
    //                     'no_indikator' => $value->indikator_nomor,
    //                     'skor' => $data->nilai_indikator_1,
    //                 ];

    //                 if ($nilai_indikator >= 1 && $nilai_indikator <= 3) {
    //                     $kategori = 'Rendah';
    //                 } else if ($nilai_indikator >= 4 && $nilai_indikator <= 7) {
    //                     $kategori = 'Sedang';
    //                 } else if ($nilai_indikator >= 8 && $nilai_indikator <= 10) {
    //                     $kategori = 'Tinggi';
    //                 }
    //             } else if ($value->indikator_nomor == 2) {
    //                 $nilai[] = [
    //                     'indikator' => $value->indikator_nama,
    //                     'no_indikator' => $value->indikator_nomor,
    //                     'skor' => $data->nilai_indikator_2,
    //                 ];

    //                 if ($nilai_indikator >= 1 && $nilai_indikator <= 2) {
    //                     $kategori = 'Rendah';
    //                 } else if ($nilai_indikator >= 3 && $nilai_indikator <= 4) {
    //                     $kategori = 'Sedang';
    //                 } else if ($nilai_indikator == 5) {
    //                     $kategori = 'Tinggi';
    //                 }
    //             } else if ($value->indikator_nomor == 3) {
    //                 $nilai[] = [
    //                     'indikator' => $value->indikator_nama,
    //                     'no_indikator' => $value->indikator_nomor,
    //                     'skor' => $data->nilai_indikator_3,
    //                 ];

    //                 if ($nilai_indikator >= 1 && $nilai_indikator <= 5) {
    //                     $kategori = 'Rendah';
    //                 } else if ($nilai_indikator >= 6 && $nilai_indikator <= 9) {
    //                     $kategori = 'Sedang';
    //                 } else if ($nilai_indikator == 10) {
    //                     $kategori = 'Tinggi';
    //                 }
    //             } else if ($value->indikator_nomor == 4) {
    //                 $nilai[] = [
    //                     'indikator' => $value->indikator_nama,
    //                     'no_indikator' => $value->indikator_nomor,
    //                     'skor' => $data->nilai_indikator_4,
    //                 ];

    //                 if ($nilai_indikator >= 1 && $nilai_indikator <= 2) {
    //                     $kategori = 'Rendah';
    //                 } else if ($nilai_indikator >= 3 && $nilai_indikator <= 4) {
    //                     $kategori = 'Sedang';
    //                 } else if ($nilai_indikator == 5) {
    //                     $kategori = 'Tinggi';
    //                 }
    //             } else if ($value->indikator_nomor == 5) {
    //                 $nilai[] = [
    //                     'indikator' => $value->indikator_nama,
    //                     'no_indikator' => $value->indikator_nomor,
    //                     'skor' => $data->nilai_indikator_5,
    //                 ];

    //                 if ($nilai_indikator >= 1 && $nilai_indikator <= 2) {
    //                     $kategori = 'Rendah';
    //                 } else if ($nilai_indikator >= 3 && $nilai_indikator <= 4) {
    //                     $kategori = 'Sedang';
    //                 } else if ($nilai_indikator == 5) {
    //                     $kategori = 'Tinggi';
    //                 }
    //             } else if ($value->indikator_nomor == 6) {
    //                 $nilai[] = [
    //                     'indikator' => $value->indikator_nama,
    //                     'no_indikator' => $value->indikator_nomor,
    //                     'skor' => $data->nilai_indikator_6,
    //                 ];

    //                 if ($nilai_indikator >= 1 && $nilai_indikator <= 2) {
    //                     $kategori = 'Rendah';
    //                 } else if ($nilai_indikator >= 3 && $nilai_indikator <= 4) {
    //                     $kategori = 'Sedang';
    //                 } else if ($nilai_indikator == 5) {
    //                     $kategori = 'Tinggi';
    //                 }
    //             } else if ($value->indikator_nomor == 7) {
    //                 $nilai[] = [
    //                     'indikator' => $value->indikator_nama,
    //                     'no_indikator' => $value->indikator_nomor,
    //                     'skor' => $data->nilai_indikator_7,
    //                 ];

    //                 if ($nilai_indikator >= 1 && $nilai_indikator <= 4) {
    //                     $kategori = 'Rendah';
    //                 } else if ($nilai_indikator >= 5 && $nilai_indikator <= 8) {
    //                     $kategori = 'Sedang';
    //                 } else if ($nilai_indikator >= 9 && $nilai_indikator <= 10) {
    //                     $kategori = 'Tinggi';
    //                 }
    //             } else if ($value->indikator_nomor == 8) {
    //                 $nilai[] = [
    //                     'indikator' => $value->indikator_nama,
    //                     'no_indikator' => $value->indikator_nomor,
    //                     'skor' => $data->nilai_indikator_8,
    //                 ];

    //                 if ($nilai_indikator >= 1 && $nilai_indikator <= 2) {
    //                     $kategori = 'Rendah';
    //                 } else if ($nilai_indikator >= 3 && $nilai_indikator <= 4) {
    //                     $kategori = 'Sedang';
    //                 } else if ($nilai_indikator == 5) {
    //                     $kategori = 'Tinggi';
    //                 }
    //             }

    //             if ($kategori) {
    //                 $deskripsi = $deskripsi_data->firstWhere('kualifikasi', $kategori) ?? null;
    //                 if ($deskripsi) {
    //                     $deskripsi_list[] = $deskripsi;
    //                 }
    //             }
    //         }

    //         $skor->update([
    //             'nilai' => $nilai,
    //             'uraian_potensi_1' => $deskripsi_list[0] ?? null,
    //             'uraian_potensi_2' => $deskripsi_list[1] ?? null,
    //             'uraian_potensi_3' => $deskripsi_list[2] ?? null,
    //             'uraian_potensi_4' => $deskripsi_list[3] ?? null,
    //             'uraian_potensi_5' => $deskripsi_list[4] ?? null,
    //             'uraian_potensi_6' => $deskripsi_list[5] ?? null,
    //             'uraian_potensi_7' => $deskripsi_list[6] ?? null,
    //             'uraian_potensi_8' => $deskripsi_list[7] ?? null,
    //         ]);

    //         // change status ujian to true (finish)
    //         $data->is_finished = true;
    //         $data->save();

    //         $current_sequence_test = Settings::where('urutan', $data->urutan_tes)->first(['urutan']);
    //         if ($current_sequence_test && $current_sequence_test->urutan !== 7) {
    //             $next_test = Settings::with('alatTes')->where('urutan', $current_sequence_test->urutan + 1)->first();
    //             $this->startTest($next_test->alatTes->alat_tes, $next_test->urutan);
    //         } else if ($current_sequence_test && $current_sequence_test->urutan == 7) {
    //             return $this->redirect(route('peserta.kuesioner'), navigate: true);
    //         } else {
    //             return $this->redirect(route('peserta.tes-potensi.home'), navigate: true);
    //         }

    //         // return $this->redirect(route('peserta.tes-potensi'), navigate: true);
    //     } catch (\Throwable $th) {
    //         //throw $th;
    //         session()->flash('toast', [
    //             'type' => 'error',
    //             'message' => 'Terjadi kesalahan'
    //         ]);
    //     }
    // }
}
