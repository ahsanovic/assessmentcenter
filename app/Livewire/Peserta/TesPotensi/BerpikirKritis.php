<?php

namespace App\Livewire\Peserta\TesPotensi;

use App\Models\BerpikirKritis\HasilBerpikirKritis;
use App\Models\BerpikirKritis\RefIndikatorBerpikirKritis;
use App\Models\BerpikirKritis\SoalBerpikirKritis;
use App\Models\BerpikirKritis\UjianBerpikirKritis;
use App\Models\Settings;
use App\Traits\StartTestTrait;
use App\Traits\TimerTrait;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.peserta.app', ['title' => 'Tes Berpikir Kritis dan Strategis'])]
class BerpikirKritis extends Component
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
    public $current_sequence;

    public function mount($id)
    {
        $this->id_soal = $id;

        $data = UjianBerpikirKritis::select('id', 'soal_id', 'jawaban', 'created_at')
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
        $this->soal = SoalBerpikirKritis::find($this->nomor_soal[$this->id_soal - 1]);
        $this->jml_soal = SoalBerpikirKritis::count();
        $this->id_ujian = $data->id;

        $first_sequence = Settings::with('alatTes')->where('urutan', 1)->first();
        $this->timerTest($first_sequence->alatTes->alat_tes);

        $current_sequence = Settings::with('alatTes')->where('alat_tes_id', 8)->first();
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
        return view('livewire..peserta.tes-potensi.berpikir-kritis.ujian', [
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
        $data = UjianBerpikirKritis::where('peserta_id', Auth::guard('peserta')->user()->id)
            ->where('event_id', Auth::guard('peserta')->user()->event_id)
            ->where('is_finished', 'false')
            ->first();

        $soal_id = explode(',', $data->soal_id);

        // update jawaban
        $jawaban_user = explode(',', $data->jawaban);
        $jawaban_user[$index_array] = $this->jawaban_user[$index_array] ?? '0';
        $jawaban_user = implode(',', $jawaban_user);

        UjianBerpikirKritis::where('peserta_id', Auth::guard('peserta')->user()->id)
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

        $poin = SoalBerpikirKritis::find($soal_id[$index_array]);
        $poin_a = $poin->poin_opsi_a;
        $poin_b = $poin->poin_opsi_b;
        $poin_c = $poin->poin_opsi_c;
        $poin_d = $poin->poin_opsi_d;
        $poin_e = $poin->poin_opsi_e;

        $indikator_map = [
            [1, 1, 'nilai_indikator_1'],
            [2, 2, 'nilai_indikator_2'],
            [3, 4, 'nilai_indikator_3'],
            [5, 6, 'nilai_indikator_4'],
            [7, 7, 'nilai_indikator_5'],
            [8, 8, 'nilai_indikator_6'],
            [9, 9, 'nilai_indikator_7'],
            [10, 10, 'nilai_indikator_8'],
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
            $this->redirect(route('peserta.tes-potensi.berpikir-kritis', ['id' => $nomor_soal + 1]), true);
        } else if ($nomor_soal == $this->jml_soal) {
            $this->redirect(route('peserta.tes-potensi.berpikir-kritis', ['id' => $nomor_soal]), true);
        }
    }

    public function navigate($id)
    {
        if ($id >= 1 && $id <= $this->jml_soal) {
            $this->id_soal = $id;
            $this->soal = SoalBerpikirKritis::find($this->nomor_soal[$id - 1]);
            $this->redirect(route('peserta.tes-potensi.berpikir-kritis', ['id' => $id]), true);
        }
    }

    public function finish()
    {
        try {
            $data = UjianBerpikirKritis::findOrFail($this->id_ujian);
            $skor_total = $data->nilai_indikator_1 + $data->nilai_indikator_2 + $data->nilai_indikator_3 + $data->nilai_indikator_4 + $data->nilai_indikator_5 + $data->nilai_indikator_6 + $data->nilai_indikator_7 + $data->nilai_indikator_8;

            if (($skor_total == 0) || ($skor_total >= 10 && $skor_total <= 29)) {
                $level_total = '1';
                $kualifikasi_total = 'Sangat Kurang';
                $kategori_total = 'Rendah';
            } else if ($skor_total >= 30 && $skor_total <= 35) {
                $level_total = '2';
                $kualifikasi_total = 'Kurang';
                $kategori_total = 'Rendah';
            } else if ($skor_total >= 36 && $skor_total <= 37) {
                $level_total = '3-';
                $kualifikasi_total = 'Cukup';
                $kategori_total = 'Sedang';
            } else if ($skor_total == 38) {
                $level_total = '3';
                $kualifikasi_total = 'Cukup';
                $kategori_total = 'Sedang';
            } else if ($skor_total >= 39 && $skor_total <= 40) {
                $level_total = '3+';
                $kualifikasi_total = 'Cukup';
                $kategori_total = 'Sedang';
            } else if ($skor_total >= 41 && $skor_total <= 44) {
                $level_total = '4';
                $kualifikasi_total = 'Baik';
                $kategori_total = 'Tinggi';
            } else if ($skor_total >= 45 && $skor_total <= 50) {
                $level_total = '5';
                $kualifikasi_total = 'Sangat Baik';
                $kategori_total = 'Tinggi';
            }

            $skor = HasilBerpikirKritis::updateOrCreate(
                [
                    'event_id' => Auth::guard('peserta')->user()->event_id,
                    'peserta_id' => Auth::guard('peserta')->user()->id,
                    'ujian_id' => $data->id,
                ],
                [
                    // 'nilai' => $nilai,
                    'skor_total' => $skor_total,
                    'level_total' => $level_total,
                    'kualifikasi_total' => $kualifikasi_total,
                    'kategori_total' => $kategori_total,
                ]
            );
            
            $deskripsi_list = [];
            $nilai = [];
            $indikator = RefIndikatorBerpikirKritis::get(['indikator_nama', 'indikator_nomor']);
            foreach ($indikator as $value) {
                $kualifikasi_deskripsi = RefIndikatorBerpikirKritis::where('indikator_nomor', $value->indikator_nomor)->value('kualifikasi_deskripsi');
                $deskripsi_data = collect($kualifikasi_deskripsi);

                $nilai_indikator = $data->{'nilai_indikator_' . $value->indikator_nomor} ?? null;
                if (is_null($nilai_indikator)) {
                    continue;
                }

                if ($value->indikator_nomor == 1) {
                    $nilai[] = [
                        'indikator' => $value->indikator_nama,
                        'no_indikator' => $value->indikator_nomor,
                        'skor' => $data->nilai_indikator_1,
                    ];

                    if ($nilai_indikator >= 1 && $nilai_indikator <= 2) {
                        $kategori = 'Rendah';
                    } else if ($nilai_indikator >= 3 && $nilai_indikator <= 4) {
                        $kategori = 'Sedang';
                    } else if ($nilai_indikator == 5) {
                        $kategori = 'Tinggi';
                    }
                } else if ($value->indikator_nomor == 2) {
                    $nilai[] = [
                        'indikator' => $value->indikator_nama,
                        'no_indikator' => $value->indikator_nomor,
                        'skor' => $data->nilai_indikator_2,
                    ];

                    if ($nilai_indikator >= 1 && $nilai_indikator <= 2) {
                        $kategori = 'Rendah';
                    } else if ($nilai_indikator >= 3 && $nilai_indikator <= 4) {
                        $kategori = 'Sedang';
                    } else if ($nilai_indikator == 5) {
                        $kategori = 'Tinggi';
                    }
                } else if ($value->indikator_nomor == 3) {
                    $nilai[] = [
                        'indikator' => $value->indikator_nama,
                        'no_indikator' => $value->indikator_nomor,
                        'skor' => $data->nilai_indikator_3,
                    ];

                    if ($nilai_indikator >= 1 && $nilai_indikator <= 6) {
                        $kategori = 'Rendah';
                    } else if ($nilai_indikator >= 7 && $nilai_indikator <= 9) {
                        $kategori = 'Sedang';
                    } else if ($nilai_indikator == 10) {
                        $kategori = 'Tinggi';
                    }
                } else if ($value->indikator_nomor == 4) {
                    $nilai[] = [
                        'indikator' => $value->indikator_nama,
                        'no_indikator' => $value->indikator_nomor,
                        'skor' => $data->nilai_indikator_4,
                    ];

                    if ($nilai_indikator >= 1 && $nilai_indikator <= 4) {
                        $kategori = 'Rendah';
                    } else if ($nilai_indikator >= 5 && $nilai_indikator <= 8) {
                        $kategori = 'Sedang';
                    } else if ($nilai_indikator >= 9 && $nilai_indikator <= 10) {
                        $kategori = 'Tinggi';
                    }
                } else if ($value->indikator_nomor == 5) {
                    $nilai[] = [
                        'indikator' => $value->indikator_nama,
                        'no_indikator' => $value->indikator_nomor,
                        'skor' => $data->nilai_indikator_5,
                    ];

                    if ($nilai_indikator >= 1 && $nilai_indikator <= 2) {
                        $kategori = 'Rendah';
                    } else if ($nilai_indikator >= 3 && $nilai_indikator <= 4) {
                        $kategori = 'Sedang';
                    } else if ($nilai_indikator == 5) {
                        $kategori = 'Tinggi';
                    }
                } else if ($value->indikator_nomor == 6) {
                    $nilai[] = [
                        'indikator' => $value->indikator_nama,
                        'no_indikator' => $value->indikator_nomor,
                        'skor' => $data->nilai_indikator_6,
                    ];

                    if ($nilai_indikator >= 1 && $nilai_indikator <= 2) {
                        $kategori = 'Rendah';
                    } else if ($nilai_indikator >= 3 && $nilai_indikator <= 4) {
                        $kategori = 'Sedang';
                    } else if ($nilai_indikator == 5) {
                        $kategori = 'Tinggi';
                    }
                } else if ($value->indikator_nomor == 7) {
                    $nilai[] = [
                        'indikator' => $value->indikator_nama,
                        'no_indikator' => $value->indikator_nomor,
                        'skor' => $data->nilai_indikator_7,
                    ];

                    if ($nilai_indikator == 1) {
                        $kategori = 'Rendah';
                    } else if ($nilai_indikator >= 2 && $nilai_indikator <= 3) {
                        $kategori = 'Sedang';
                    } else if ($nilai_indikator >= 4 && $nilai_indikator <= 5) {
                        $kategori = 'Tinggi';
                    }
                } else if ($value->indikator_nomor == 8) {
                    $nilai[] = [
                        'indikator' => $value->indikator_nama,
                        'no_indikator' => $value->indikator_nomor,
                        'skor' => $data->nilai_indikator_8,
                    ];

                    if ($nilai_indikator == 1) {
                        $kategori = 'Rendah';
                    } else if ($nilai_indikator >= 2 && $nilai_indikator <= 4) {
                        $kategori = 'Sedang';
                    } else if ($nilai_indikator == 5) {
                        $kategori = 'Tinggi';
                    }
                }

                if ($kategori) {
                    $deskripsi = $deskripsi_data->firstWhere('kualifikasi', $kategori) ?? null;
                    if ($deskripsi) {
                        $deskripsi_list[] = $deskripsi;
                    }
                }
            }

            $skor->update([
                'nilai' => $nilai,
                'uraian_potensi_1' => $deskripsi_list[0] ?? null,
                'uraian_potensi_2' => $deskripsi_list[1] ?? null,
                'uraian_potensi_3' => $deskripsi_list[2] ?? null,
                'uraian_potensi_4' => $deskripsi_list[3] ?? null,
                'uraian_potensi_5' => $deskripsi_list[4] ?? null,
                'uraian_potensi_6' => $deskripsi_list[5] ?? null,
                'uraian_potensi_7' => $deskripsi_list[6] ?? null,
                'uraian_potensi_8' => $deskripsi_list[7] ?? null,
            ]);

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
            //throw $th;
            session()->flash('toast', [
                'type' => 'error',
                'message' => 'Terjadi kesalahan'
            ]);
        }
    }
}
