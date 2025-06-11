<?php

namespace App\Livewire\Peserta\TesPotensi;

use App\Models\KesadaranDiri\HasilKesadaranDiri;
use App\Models\KesadaranDiri\RefKesadaranDiri;
use App\Models\KesadaranDiri\SoalKesadaranDiri;
use App\Models\KesadaranDiri\UjianKesadaranDiri;
use App\Models\Settings;
use App\Traits\PelanggaranTrait;
use App\Traits\StartTestTrait;
use App\Traits\TimerTrait;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.peserta.app', ['title' => 'Tes Kesadaran Diri'])]
class KesadaranDiri extends Component
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
    public $current_sequence;

    public function mount($id)
    {
        $this->id_soal = $id;

        $data = UjianKesadaranDiri::select('id', 'soal_id', 'jawaban', 'created_at')
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
        $this->soal = SoalKesadaranDiri::find($this->nomor_soal[$this->id_soal - 1]);
        $this->jml_soal = SoalKesadaranDiri::count();
        $this->id_ujian = $data->id;

        $first_sequence = Settings::with('alatTes')->where('urutan', 1)->first();
        $this->timerTest($first_sequence->alatTes->alat_tes);

        $current_sequence = Settings::with('alatTes')->where('alat_tes_id', 9)->first();
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
        return view('livewire..peserta.tes-potensi.kesadaran-diri.ujian', [
            'nomor_sekarang' => $this->id_soal,
            'jawaban' => $this->jawaban_user,
            'jawaban_kosong' => $this->jawaban_kosong,
            'jml_soal' => $this->jml_soal,
            'soal' => $this->soal,
        ]);
    }

    public function saveAndNext($nomor_soal)
    {
        $index_array = $nomor_soal - 1;
        $data = UjianKesadaranDiri::where('peserta_id', Auth::guard('peserta')->user()->id)
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
            [1, 22, 'nilai_indikator_1'],
            [23, 46, 'nilai_indikator_2'],
            [47, 65, 'nilai_indikator_3']
        ];

        foreach ($indikator_map as [$start, $end, $indikator]) {
            if ($nomor_soal >= $start && $nomor_soal <= $end) {
                $total_skor = 0;

                for ($i = $start; $i <= $end; $i++) {
                    $idx = $i - 1;
                    $jawaban = $jawaban_user[$idx] ?? null;

                    // Ambil poin dari soal terkait
                    if (isset($soal_id[$idx])) {
                        $poin_soal = SoalKesadaranDiri::find($soal_id[$idx]);
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
            $this->redirect(route('peserta.tes-potensi.kesadaran-diri', ['id' => $nomor_soal + 1]), true);
        } else if ($nomor_soal == $this->jml_soal) {
            $this->redirect(route('peserta.tes-potensi.kesadaran-diri', ['id' => $nomor_soal]), true);
        }
    }

    public function navigate($id)
    {
        if ($id >= 1 && $id <= $this->jml_soal) {
            $this->id_soal = $id;
            $this->soal = SoalKesadaranDiri::find($this->nomor_soal[$id - 1]);
            $this->redirect(route('peserta.tes-potensi.kesadaran-diri', ['id' => $id]), true);
        }
    }

    public function finish()
    {
        try {
            $data = UjianKesadaranDiri::findOrFail($this->id_ujian);

            // indikator 1
            if ($data->nilai_indikator_1 >= 1 && $data->nilai_indikator_1 <= 49) {
                $kategori_1 = 'SK';
                $kategori_kualifikasi_1 = 'Sangat Kurang';
                $kualifikasi_1 = 'Rendah';
            } else if ($data->nilai_indikator_1 >= 50 && $data->nilai_indikator_1 <= 55) {
                $kategori_1 = 'K';
                $kategori_kualifikasi_1 = 'Kurang';
                $kualifikasi_1 = 'Rendah';
            } else if ($data->nilai_indikator_1 == 56) {
                $kategori_1 = 'C-';
                $kategori_kualifikasi_1 = 'Cukup';
                $kualifikasi_1 = 'Sedang';
            } else if ($data->nilai_indikator_1 >= 57 && $data->nilai_indikator_1 <= 60) {
                $kategori_1 = 'C';
                $kategori_kualifikasi_1 = 'Cukup';
                $kualifikasi_1 = 'Sedang';
            } else if ($data->nilai_indikator_1 == 61) {
                $kategori_1 = 'C+';
                $kategori_kualifikasi_1 = 'Cukup';
                $kualifikasi_1 = 'Sedang';
            } else if ($data->nilai_indikator_1 >= 62 && $data->nilai_indikator_1 <= 65) {
                $kategori_1 = 'B';
                $kategori_kualifikasi_1 = 'Baik';
                $kualifikasi_1 = 'Tinggi';
            } else if ($data->nilai_indikator_1 == 66) {
                $kategori_1 = 'SB';
                $kategori_kualifikasi_1 = 'Sangat Baik';
                $kualifikasi_1 = 'Tinggi';
            }

            // indikator 2
            if ($data->nilai_indikator_2 >= 1 && $data->nilai_indikator_2 <= 50) {
                $kategori_2 = 'SK';
                $kategori_kualifikasi_2 = 'Sangat Kurang';
                $kualifikasi_2 = 'Rendah';
            } else if ($data->nilai_indikator_2 >= 51 && $data->nilai_indikator_2 <= 58) {
                $kategori_2 = 'K';
                $kategori_kualifikasi_2 = 'Kurang';
                $kualifikasi_2 = 'Rendah';
            } else if ($data->nilai_indikator_2 == 59) {
                $kategori_2 = 'C-';
                $kategori_kualifikasi_2 = 'Cukup';
                $kualifikasi_2 = 'Sedang';
            } else if ($data->nilai_indikator_2 >= 60 && $data->nilai_indikator_2 <= 65) {
                $kategori_2 = 'C';
                $kategori_kualifikasi_2 = 'Cukup';
                $kualifikasi_2 = 'Sedang';
            } else if ($data->nilai_indikator_2 == 66) {
                $kategori_2 = 'C+';
                $kategori_kualifikasi_2 = 'Cukup';
                $kualifikasi_2 = 'Sedang';
            } else if ($data->nilai_indikator_2 >= 67 && $data->nilai_indikator_2 <= 71) {
                $kategori_2 = 'B';
                $kategori_kualifikasi_2 = 'Baik';
                $kualifikasi_2 = 'Tinggi';
            } else if ($data->nilai_indikator_2 == 72) {
                $kategori_2 = 'SB';
                $kategori_kualifikasi_2 = 'Sangat Baik';
                $kualifikasi_2 = 'Tinggi';
            }

            // indikator 3
            if ($data->nilai_indikator_3 >= 1 && $data->nilai_indikator_3 <= 41) {
                $kategori_3 = 'SK';
                $kategori_kualifikasi_3 = 'Sangat Kurang';
                $kualifikasi_3 = 'Rendah';
            } else if ($data->nilai_indikator_3 >= 42 && $data->nilai_indikator_3 <= 46) {
                $kategori_3 = 'K';
                $kategori_kualifikasi_3 = 'Kurang';
                $kualifikasi_3 = 'Rendah';
            } else if ($data->nilai_indikator_3 == 47) {
                $kategori_3 = 'C-';
                $kategori_kualifikasi_3 = 'Cukup';
                $kualifikasi_3 = 'Sedang';
            } else if ($data->nilai_indikator_3 >= 48 && $data->nilai_indikator_3 <= 50) {
                $kategori_3 = 'C';
                $kategori_kualifikasi_3 = 'Cukup';
                $kualifikasi_3 = 'Sedang';
            } else if ($data->nilai_indikator_3 == 51) {
                $kategori_3 = 'C+';
                $kategori_kualifikasi_3 = 'Cukup';
                $kualifikasi_3 = 'Sedang';
            } else if ($data->nilai_indikator_3 >= 52 && $data->nilai_indikator_3 <= 56) {
                $kategori_3 = 'B';
                $kategori_kualifikasi_3 = 'Baik';
                $kualifikasi_3 = 'Tinggi';
            } else if ($data->nilai_indikator_3 == 57) {
                $kategori_3 = 'SB';
                $kategori_kualifikasi_3 = 'Sangat Baik';
                $kualifikasi_3 = 'Tinggi';
            }

            $indikator = RefKesadaranDiri::get(['indikator_nama', 'indikator_nomor']);

            $nilai = [];
            foreach ($indikator as $value) {
                if ($value->indikator_nomor == 1) {
                    $nilai[] = [
                        'indikator' => $value->indikator_nama,
                        'no_indikator' => $value->indikator_nomor,
                        'skor' => $data->nilai_indikator_1,
                        'kategori' => $kategori_1,
                        'kategori_kualifikasi' => $kategori_kualifikasi_1,
                        'kualifikasi' => $kualifikasi_1
                    ];
                } else if ($value->indikator_nomor == 2) {
                    $nilai[] = [
                        'indikator' => $value->indikator_nama,
                        'no_indikator' => $value->indikator_nomor,
                        'skor' => $data->nilai_indikator_2,
                        'kategori' => $kategori_2,
                        'kategori_kualifikasi' => $kategori_kualifikasi_2,
                        'kualifikasi' => $kualifikasi_2
                    ];
                } else if ($value->indikator_nomor == 3) {
                    $nilai[] = [
                        'indikator' => $value->indikator_nama,
                        'no_indikator' => $value->indikator_nomor,
                        'skor' => $data->nilai_indikator_3,
                        'kategori' => $kategori_3,
                        'kategori_kualifikasi' => $kategori_kualifikasi_3,
                        'kualifikasi' => $kualifikasi_3
                    ];
                }
            }

            $skor_total = $data->nilai_indikator_1 + $data->nilai_indikator_2 + $data->nilai_indikator_3;

            if (($skor_total >= 1 || $skor_total <= 143)) {
                $level_total = '1';
                $kualifikasi_total = 'Sangat Kurang';
                $kategori_total = 'SK';
            } else if ($skor_total >= 144 && $skor_total <= 160) {
                $level_total = '2';
                $kualifikasi_total = 'Kurang';
                $kategori_total = 'K';
            } else if ($skor_total >= 161 && $skor_total <= 165) {
                $level_total = '3-';
                $kualifikasi_total = 'Cukup';
                $kategori_total = 'C-';
            } else if ($skor_total >= 166 && $skor_total <= 171) {
                $level_total = '3';
                $kualifikasi_total = 'Cukup';
                $kategori_total = 'C';
            } else if ($skor_total >= 172 && $skor_total <= 177) {
                $level_total = '3+';
                $kualifikasi_total = 'Cukup';
                $kategori_total = 'C+';
            } else if ($skor_total >= 178 && $skor_total <= 194) {
                $level_total = '4';
                $kualifikasi_total = 'Baik';
                $kategori_total = 'B';
            } else if ($skor_total == 195) {
                $level_total = '5';
                $kualifikasi_total = 'Sangat Baik';
                $kategori_total = 'SB';
            }

            $skor = HasilKesadaranDiri::updateOrCreate(
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

            $priority = ['SB', 'B', 'C+', 'C', 'C-', 'K', 'SK'];

            // menyortir data berdasarkan urutan kategori
            usort($nilai, function ($a, $b) use ($priority) {
                $posA = array_search($a['kategori'], $priority);
                $posB = array_search($b['kategori'], $priority);
                return $posA - $posB;
            });

            // Ambil 3 data setelah diurutkan, kemudian urutkan berdasar nomor indikator
            $top_data = array_slice($nilai, 0, 3);
            usort($top_data, function ($a, $b) {
                return $a['no_indikator'] - $b['no_indikator'];
            });
            // dd($top_data);

            // Ambil nilai indikator nama, indikator nomor, dan kualifikasi dari hasil
            $indikator_nomor = array_column($top_data, 'no_indikator'); // 1
            $kualifikasi_array = array_column($top_data, 'kategori'); // Tinggi
            // dd($kualifikasi_array);

            $data_to_save = [];

            foreach ($indikator_nomor as $index => $nomor) {
                $data_kualifikasi = RefKesadaranDiri::whereIndikatorNomor($nomor)->first();

                if ($data_kualifikasi) {
                    $kualifikasi_data = $data_kualifikasi->kualifikasi;
                    $selected_kualifikasi = $this->_getKualifikasi($kualifikasi_array[$index]);
                    $uraian_potensi = collect($kualifikasi_data)->firstWhere('kualifikasi', $selected_kualifikasi);

                    // Simpan dalam format field indikator_potensi_1, uraian_potensi_1, dst
                    // $field_indikator = "indikator_potensi_" . ($index + 1);
                    $field_uraian_potensi = "uraian_potensi_" . ($index + 1);

                    // $data_to_save[$field_indikator] = $data_kualifikasi->indikator_nama;
                    $data_to_save[$field_uraian_potensi] = $uraian_potensi;
                }
            }

            $skor->update($data_to_save);

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

    private function _getKualifikasi($value)
    {
        switch ($value) {
            case 'SB':
                $kualifikasi = 'Sangat Baik';
                break;
            case 'B':
                $kualifikasi = 'Baik';
                break;
            case 'C+':
                $kualifikasi = 'Cukup';
                break;
            case 'C':
                $kualifikasi = 'Cukup';
                break;
            case 'C-':
                $kualifikasi = 'Cukup';
                break;
            case 'K':
                $kualifikasi = 'Kurang/Sangat Kurang';
                break;
            case 'SK':
                $kualifikasi = 'Kurang/Sangat Kurang';
                break;
            default:
                $kualifikasi = '';
                break;
        }

        return $kualifikasi;
    }
}
