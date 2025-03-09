<?php

namespace App\Livewire\Peserta\TesPotensi;

use App\Models\ProblemSolving\HasilProblemSolving;
use App\Models\ProblemSolving\RefAspekProblemSolving;
use App\Models\ProblemSolving\RefIndikatorProblemSolving;
use App\Models\ProblemSolving\SoalProblemSolving;
use App\Models\ProblemSolving\UjianProblemSolving;
use App\Models\Settings;
use App\Traits\StartTestTrait;
use App\Traits\TimerTrait;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.peserta.app', ['title' => 'Tes Problem Solving'])]
class ProblemSolving extends Component
{
    use StartTestTrait, TimerTrait;

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

        // update jawaban
        $jawaban_user = explode(',', $data->jawaban);
        $jawaban_user[$index_array] = $this->jawaban_user[$index_array] ?? '0';
        $jawaban_user = implode(',', $jawaban_user);

        UjianProblemSolving::where('peserta_id', Auth::guard('peserta')->user()->id)
            ->where('event_id', Auth::guard('peserta')->user()->event_id)
            ->where('is_finished', 'false')
            ->update(['jawaban' => $jawaban_user]);

        // perhitungan ulang soal yang belum dijawab
        $this->jawaban_user = explode(',', $jawaban_user); // Update state Livewire
        $this->jawaban_kosong = 0;

        foreach ($this->jawaban_user as $jawaban) {
            if ($jawaban == '0') {
                $this->jawaban_kosong++;
            }
        }

        if ($this->jawaban_kosong === 0) {
            $this->jawaban_kosong = 0;
        }

        $poin = SoalProblemSolving::find($soal_id[$index_array]);
        $poin_a = $poin->poin_opsi_a;
        $poin_b = $poin->poin_opsi_b;
        $poin_c = $poin->poin_opsi_c;
        $poin_d = $poin->poin_opsi_d;
        $poin_e = $poin->poin_opsi_e;

        $indikator_map = [
            [1, 2, 'nilai_indikator_1'],
            [3, 4, 'nilai_indikator_2'],
            [5, 6, 'nilai_indikator_3'],
            [7, 7, 'nilai_indikator_4'],
            [8, 9, 'nilai_indikator_5'],
            [10, 11, 'nilai_indikator_6'],
            [12, 13, 'nilai_indikator_7'],
            [14, 14, 'nilai_indikator_8'],
        ];

        foreach ($indikator_map as [$start, $end, $indikator]) {
            if ($nomor_soal >= $start && $nomor_soal <= $end) {
                $skor = $data->{$indikator};
                if ($this->jawaban_user[$index_array] === 'A') {
                    $skor += $poin_a;
                } elseif ($this->jawaban_user[$index_array] === 'B') {
                    $skor += $poin_b;
                } elseif ($this->jawaban_user[$index_array] === 'C') {
                    $skor += $poin_c;
                } elseif ($this->jawaban_user[$index_array] === 'D') {
                    $skor += $poin_d;
                } elseif ($this->jawaban_user[$index_array] === 'E') {
                    $skor += $poin_e;
                }
                $data->update([$indikator => $skor]);
                break;
            }
        }

        if ($nomor_soal < $this->jml_soal) {
            $this->redirect(route('peserta.tes-potensi.problem-solving', ['id' => $nomor_soal + 1]), true);
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
            $indikator = RefIndikatorProblemSolving::get(['indikator_nama', 'indikator_nomor']);
    
            // $skor = new HasilProblemSolving();
            // $skor->event_id = Auth::guard('peserta')->user()->event_id;
            // $skor->peserta_id = Auth::guard('peserta')->user()->id;
            // $skor->ujian_id = $data->id;
            $nilai = [];
            foreach ($indikator as $value) {
                if ($value->indikator_nomor == 1) {
                    $nilai[] = [
                        'indikator' => $value->indikator_nama,
                        'no_indikator' => $value->indikator_nomor,
                        'skor' => $data->nilai_indikator_1,
                    ];
                } else if ($value->indikator_nomor == 2) {
                    $nilai[] = [
                        'indikator' => $value->indikator_nama,
                        'no_indikator' => $value->indikator_nomor,
                        'skor' => $data->nilai_indikator_2,
                    ];
                } else if ($value->indikator_nomor == 3) {
                    $nilai[] = [
                        'indikator' => $value->indikator_nama,
                        'no_indikator' => $value->indikator_nomor,
                        'skor' => $data->nilai_indikator_3,
                    ];
                } else if ($value->indikator_nomor == 4) {
                    $nilai[] = [
                        'indikator' => $value->indikator_nama,
                        'no_indikator' => $value->indikator_nomor,
                        'skor' => $data->nilai_indikator_4,
                    ];
                } else if ($value->indikator_nomor == 5) {
                    $nilai[] = [
                        'indikator' => $value->indikator_nama,
                        'no_indikator' => $value->indikator_nomor,
                        'skor' => $data->nilai_indikator_5,
                    ];
                } else if ($value->indikator_nomor == 6) {
                    $nilai[] = [
                        'indikator' => $value->indikator_nama,
                        'no_indikator' => $value->indikator_nomor,
                        'skor' => $data->nilai_indikator_6,
                    ];
                } else if ($value->indikator_nomor == 7) {
                    $nilai[] = [
                        'indikator' => $value->indikator_nama,
                        'no_indikator' => $value->indikator_nomor,
                        'skor' => $data->nilai_indikator_7,
                    ];
                } else if ($value->indikator_nomor == 8) {
                    $nilai[] = [
                        'indikator' => $value->indikator_nama,
                        'no_indikator' => $value->indikator_nomor,
                        'skor' => $data->nilai_indikator_8,
                    ];
                }
            }
    
            // $skor->nilai = $nilai;
    
            $skor_total = $data->nilai_indikator_1 + $data->nilai_indikator_2 + $data->nilai_indikator_3 + $data->nilai_indikator_4 + $data->nilai_indikator_5 + $data->nilai_indikator_6 + $data->nilai_indikator_7 + $data->nilai_indikator_8;
            
            if (($skor_total == 0) || ($skor_total >= 14 && $skor_total <= 45)) {
                $level_total = '1';
                $kualifikasi_total = 'Sangat Kurang';
                $kategori_total = 'Rendah';
            } else if ($skor_total >= 46 && $skor_total <= 51) {
                $level_total = '2';
                $kualifikasi_total = 'Kurang';
                $kategori_total = 'Rendah';
            } else if ($skor_total >= 52 && $skor_total <= 53) {
                $level_total = '3-';
                $kualifikasi_total = 'Cukup';
                $kategori_total = 'Sedang';
            } else if ($skor_total >= 54 && $skor_total <= 56) {
                $level_total = '3';
                $kualifikasi_total = 'Cukup';
                $kategori_total = 'Sedang';
            } else if ($skor_total == 57) {
                $level_total = '3+';
                $kualifikasi_total = 'Cukup';
                $kategori_total = 'Sedang';
            } else if ($skor_total >= 58 && $skor_total <= 63) {
                $level_total = '4';
                $kualifikasi_total = 'Baik';
                $kategori_total = 'Tinggi';
            } else if ($skor_total == 64) {
                $level_total = '5';
                $kualifikasi_total = 'Sangat Baik';
                $kategori_total = 'Tinggi';
            }
    
            // $skor->level_total = $level_total;
            // $skor->kualifikasi_total = $kualifikasi_total;
            // $skor->kategori_total = $kategori_total;
            // $skor->skor_total = $skor_total;

            $skor = HasilProblemSolving::updateOrCreate(
                [
                    'event_id' => Auth::guard('peserta')->user()->event_id,
                    'peserta_id' => Auth::guard('peserta')->user()->id,
                    'ujian_id' => $data->id,
                ],
                [
                    'nilai' => $nilai,
                    'skor_total' => $skor_total,
                    'level_total' => $level_total,
                    'kualifikasi_total' => $kualifikasi_total,
                    'kategori_total' => $kategori_total,
                ]
            );
    
            if ($level_total == '3-' || $level_total == '3' || $level_total == '3+') {
                $level_norma_umum = '3';
            } else {
                $level_norma_umum = $level_total;
            }
    
            $aspek = RefAspekProblemSolving::where('aspek_nomor', $level_norma_umum)->first();
            $indikator_nomor = explode(',', $aspek->indikator_nomor);
            $deskripsi_list = [];
            foreach ($indikator_nomor as $indikator) {
                $kualifikasi_deskripsi = RefIndikatorProblemSolving::where('indikator_nomor', $indikator)->value('kualifikasi_deskripsi');
                $deskripsi_data = collect($kualifikasi_deskripsi);
    
                $nilai_indikator = $data->{'nilai_indikator_' . $indikator} ?? null;
                if (is_null($nilai_indikator)) {
                    continue;
                }
    
                if ($indikator == 1) {
                    if ($nilai_indikator >= 1 && $nilai_indikator <= 3) {
                        $kategori = 'Rendah';
                    } else if ($nilai_indikator >= 4 && $nilai_indikator <= 7) {
                        $kategori = 'Sedang';
                    } else if ($nilai_indikator >= 8 && $nilai_indikator <= 10) {
                        $kategori = 'Tinggi';
                    }
                } else if ($indikator == 2) {
                    if ($nilai_indikator >= 1 && $nilai_indikator <= 6) {
                        $kategori = 'Rendah';
                    } else if ($nilai_indikator >= 7 && $nilai_indikator <= 8) {
                        $kategori = 'Sedang';
                    } else if ($nilai_indikator >= 9 && $nilai_indikator <= 10) {
                        $kategori = 'Tinggi';
                    }
                } else if ($indikator == 3) {
                    if ($nilai_indikator >= 1 && $nilai_indikator <= 5) {
                        $kategori = 'Rendah';
                    } else if ($nilai_indikator >= 6 && $nilai_indikator <= 9) {
                        $kategori = 'Sedang';
                    } else if ($nilai_indikator == 10) {
                        $kategori = 'Tinggi';
                    }
                } else if ($indikator == 4) {
                    if ($nilai_indikator >= 1 && $nilai_indikator <= 2) {
                        $kategori = 'Rendah';
                    } else if ($nilai_indikator >= 3 && $nilai_indikator <= 4) {
                        $kategori = 'Sedang';
                    } else if ($nilai_indikator >= 5) {
                        $kategori = 'Tinggi';
                    }
                } else if ($indikator == 5) {
                    if ($nilai_indikator >= 1 && $nilai_indikator <= 3) {
                        $kategori = 'Rendah';
                    } else if ($nilai_indikator >= 4 && $nilai_indikator <= 7) {
                        $kategori = 'Sedang';
                    } else if ($nilai_indikator >= 8 && $nilai_indikator <= 10) {
                        $kategori = 'Tinggi';
                    }
                } else if ($indikator == 6) {
                    if ($nilai_indikator >= 1 && $nilai_indikator <= 5) {
                        $kategori = 'Rendah';
                    } else if ($nilai_indikator >= 6 && $nilai_indikator <= 9) {
                        $kategori = 'Sedang';
                    } else if ($nilai_indikator == 10) {
                        $kategori = 'Tinggi';
                    }
                } else if ($indikator == 7) {
                    if ($nilai_indikator >= 1 && $nilai_indikator <= 4) {
                        $kategori = 'Rendah';
                    } else if ($nilai_indikator >= 5 && $nilai_indikator <= 8) {
                        $kategori = 'Sedang';
                    } else if ($nilai_indikator >= 9 && $nilai_indikator <= 10) {
                        $kategori = 'Tinggi';
                    }
                } else if ($indikator == 8) {
                    if ($nilai_indikator >= 1 && $nilai_indikator <= 2) {
                        $kategori = 'Rendah';
                    } else if ($nilai_indikator >= 3 && $nilai_indikator <= 4) {
                        $kategori = 'Sedang';
                    } else if ($nilai_indikator == 5) {
                        $kategori = 'Tinggi';
                    }
                }
    
                if ($kategori) {
                    $deskripsi = $deskripsi_data->firstWhere('kualifikasi', $kategori)['deskripsi'] ?? null;
                    if ($deskripsi) {
                        $deskripsi_list[] = $deskripsi;
                    }
                }
            }
    
            // $skor->uraian_potensi_1 = $deskripsi_list[0] ?? null;
            // $skor->uraian_potensi_2 = $deskripsi_list[1] ?? null;
            // $skor->save();

            $skor->update([
                'uraian_potensi_1' => $deskripsi_list[0] ?? null,
                'uraian_potensi_2' => $deskripsi_list[1] ?? null,
            ]);
    
            // change status ujian to true (finish)
            $data->is_finished = true;
            $data->save();

            $current_sequence_test = Settings::where('urutan', $data->urutan_tes)->first(['urutan']);
            if ($current_sequence_test && $current_sequence_test->urutan !== 7) {
                $next_test = Settings::with('alatTes')->where('urutan', $current_sequence_test->urutan + 1)->first();
                $this->startTest($next_test->alatTes->alat_tes, $next_test->urutan);
            } else {
                return $this->redirect(route('peserta.tes-potensi.home'), navigate: true);
            }
    
            // return $this->redirect(route('peserta.tes-potensi'), navigate: true);
        } catch (\Throwable $th) {
            //throw $th;
            session()->flash('toast', [
                'type' => 'error',
                'message' => 'Terjadi kesalahan'
            ]);
        }
    }
}
