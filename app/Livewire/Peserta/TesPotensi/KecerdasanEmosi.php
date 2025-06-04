<?php

namespace App\Livewire\Peserta\TesPotensi;

use App\Models\KecerdasanEmosi\HasilKecerdasanEmosi;
use App\Models\KecerdasanEmosi\RefKecerdasanEmosi;
use App\Models\KecerdasanEmosi\SoalKecerdasanEmosi;
use App\Models\KecerdasanEmosi\UjianKecerdasanEmosi;
use App\Models\Settings;
use App\Traits\StartTestTrait;
use App\Traits\TimerTrait;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.peserta.app', ['title' => 'Tes Kecerdasan Emosi'])]
class KecerdasanEmosi extends Component
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

        $data = UjianKecerdasanEmosi::select('id', 'soal_id', 'jawaban', 'created_at')
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
        $this->soal = SoalKecerdasanEmosi::find($this->nomor_soal[$this->id_soal - 1]);
        $this->jml_soal = SoalKecerdasanEmosi::count();
        $this->id_ujian = $data->id;

        $first_sequence = Settings::with('alatTes')->where('urutan', 1)->first();
        $this->timerTest($first_sequence->alatTes->alat_tes);

        $current_sequence = Settings::with('alatTes')->where('alat_tes_id', 4)->first();
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
        return view('livewire..peserta.tes-potensi.kecerdasan-emosi.ujian', [
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
        $data = UjianKecerdasanEmosi::where('peserta_id', Auth::guard('peserta')->user()->id)
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
            [1, 5, 'nilai_indikator_kd'],
            [6, 11, 'nilai_indikator_mpd'],
            [12, 17, 'nilai_indikator_ke'],
            [18, 23, 'nilai_indikator_ks'],
        ];

        foreach ($indikator_map as [$start, $end, $indikator]) {
            if ($nomor_soal >= $start && $nomor_soal <= $end) {
                $total_skor = 0;

                for ($i = $start; $i <= $end; $i++) {
                    $idx = $i - 1;
                    $jawaban = $jawaban_user[$idx] ?? null;

                    // Ambil poin dari soal terkait
                    if (isset($soal_id[$idx])) {
                        $poin_soal = SoalKecerdasanEmosi::find($soal_id[$idx]);
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
            $this->redirect(route('peserta.tes-potensi.kecerdasan-emosi', ['id' => $nomor_soal + 1]), true);
        } else if ($nomor_soal == $this->jml_soal) {
            $this->redirect(route('peserta.tes-potensi.kecerdasan-emosi', ['id' => $nomor_soal]), true);
        }
    }

    public function navigate($id)
    {
        if ($id >= 1 && $id <= $this->jml_soal) {
            $this->id_soal = $id;
            $this->soal = SoalKecerdasanEmosi::find($this->nomor_soal[$id - 1]);
            $this->redirect(route('peserta.tes-potensi.kecerdasan-emosi', ['id' => $id]), true);
        }
    }

    public function finish()
    {
        try {
            $data = UjianKecerdasanEmosi::findOrFail($this->id_ujian);

            // indikator kesadaran diri
            if ($data->nilai_indikator_kd >= 0 && $data->nilai_indikator_kd <= 3) {
                $standard_kd = '1';
                $kualifikasi_kd = 'SK';
            } else if ($data->nilai_indikator_kd >= 4 && $data->nilai_indikator_kd <= 5) {
                $standard_kd = '2';
                $kualifikasi_kd = 'K';
            } else if ($data->nilai_indikator_kd == 6) {
                $standard_kd = '3-';
                $kualifikasi_kd = 'C-';
            } else if ($data->nilai_indikator_kd == 7) {
                $standard_kd = '3';
                $kualifikasi_kd = 'C';
            } else if ($data->nilai_indikator_kd == 8) {
                $standard_kd = '3+';
                $kualifikasi_kd = 'C+';
            } else if ($data->nilai_indikator_kd == 9) {
                $standard_kd = '4';
                $kualifikasi_kd = 'B';
            } else if ($data->nilai_indikator_kd == 10) {
                $standard_kd = '5';
                $kualifikasi_kd = 'SB';
            }

            // indikator motivasi dan pengaturan diri
            if ($data->nilai_indikator_mpd >= 0 && $data->nilai_indikator_mpd <= 5) {
                $standard_mpd = '1';
                $kualifikasi_mpd = 'SK';
            } else if ($data->nilai_indikator_mpd >= 6 && $data->nilai_indikator_mpd <= 7) {
                $standard_mpd = '2';
                $kualifikasi_mpd = 'K';
            } else if ($data->nilai_indikator_mpd == 8) {
                $standard_mpd = '3-';
                $kualifikasi_mpd = 'C-';
            } else if ($data->nilai_indikator_mpd == 9) {
                $standard_mpd = '3';
                $kualifikasi_mpd = 'C';
            } else if ($data->nilai_indikator_mpd == 10) {
                $standard_mpd = '3+';
                $kualifikasi_mpd = 'C+';
            } else if ($data->nilai_indikator_mpd == 11) {
                $standard_mpd = '4';
                $kualifikasi_mpd = 'B';
            } else if ($data->nilai_indikator_mpd == 12) {
                $standard_mpd = '5';
                $kualifikasi_mpd = 'SB';
            }

            // indikator kesadaran emosional
            if ($data->nilai_indikator_ke >= 0 && $data->nilai_indikator_ke <= 3) {
                $standard_ke = '1';
                $kualifikasi_ke = 'SK';
            } else if ($data->nilai_indikator_ke == 4) {
                $standard_ke = '2';
                $kualifikasi_ke = 'K';
            } else if ($data->nilai_indikator_ke == 5) {
                $standard_ke = '3-';
                $kualifikasi_ke = 'C-';
            } else if ($data->nilai_indikator_ke == 6) {
                $standard_ke = '3';
                $kualifikasi_ke = 'C';
            } else if ($data->nilai_indikator_ke == 7) {
                $standard_ke = '3+';
                $kualifikasi_ke = 'C+';
            } else if ($data->nilai_indikator_ke >= 8 && $data->nilai_indikator_ke <= 9) {
                $standard_ke = '4';
                $kualifikasi_ke = 'B';
            } else if ($data->nilai_indikator_ke >= 10 && $data->nilai_indikator_ke <= 12) {
                $standard_ke = '5';
                $kualifikasi_ke = 'SB';
            }

            // indikator ketrampilan sosial
            if ($data->nilai_indikator_ks >= 0 && $data->nilai_indikator_ks <= 3) {
                $standard_ks = '1';
                $kualifikasi_ks = 'SK';
            } else if ($data->nilai_indikator_ks >= 4 && $data->nilai_indikator_ks <= 5) {
                $standard_ks = '2';
                $kualifikasi_ks = 'K';
            } else if ($data->nilai_indikator_ks == 6) {
                $standard_ks = '3-';
                $kualifikasi_ks = 'C-';
            } else if ($data->nilai_indikator_ks == 7) {
                $standard_ks = '3';
                $kualifikasi_ks = 'C';
            } else if ($data->nilai_indikator_ks == 8) {
                $standard_ks = '3+';
                $kualifikasi_ks = 'C+';
            } else if ($data->nilai_indikator_ks >= 9 && $data->nilai_indikator_ks <= 10) {
                $standard_ks = '4';
                $kualifikasi_ks = 'B';
            } else if ($data->nilai_indikator_ks >= 11 && $data->nilai_indikator_ke <= 12) {
                $standard_ks = '5';
                $kualifikasi_ks = 'SB';
            }

            $indikator = RefKecerdasanEmosi::get(['indikator_nama', 'indikator_nomor']);

            $nilai = [];
            foreach ($indikator as $value) {
                if ($value->indikator_nomor == 1) {
                    $nilai[] = [
                        'indikator' => $value->indikator_nama,
                        'ranking' => $value->indikator_nomor,
                        'skor' => $data->nilai_indikator_kd,
                        'standard' => $standard_kd ?? '',
                        'kualifikasi' => $kualifikasi_kd ?? ''
                    ];
                } else if ($value->indikator_nomor == 2) {
                    $nilai[] = [
                        'indikator' => $value->indikator_nama,
                        'ranking' => $value->indikator_nomor,
                        'skor' => $data->nilai_indikator_mpd,
                        'standard' => $standard_mpd ?? '',
                        'kualifikasi' => $kualifikasi_mpd ?? ''
                    ];
                } else if ($value->indikator_nomor == 3) {
                    $nilai[] = [
                        'indikator' => $value->indikator_nama,
                        'ranking' => $value->indikator_nomor,
                        'skor' => $data->nilai_indikator_ke,
                        'standard' => $standard_ke ?? '',
                        'kualifikasi' => $kualifikasi_ke ?? ''
                    ];
                } else if ($value->indikator_nomor == 4) {
                    $nilai[] = [
                        'indikator' => $value->indikator_nama,
                        'ranking' => $value->indikator_nomor,
                        'skor' => $data->nilai_indikator_ks,
                        'standard' => $standard_ks ?? '',
                        'kualifikasi' => $kualifikasi_ks ?? ''
                    ];
                }
            }


            $skor_total = $data->nilai_indikator_kd + $data->nilai_indikator_mpd + $data->nilai_indikator_ke + $data->nilai_indikator_ks;
            if ($skor_total >= 0 && $skor_total <= 21) {
                $level_total = '1';
                $kualifikasi_total = 'Sangat Kurang';
            } else if ($skor_total >= 22 && $skor_total <= 26) {
                $level_total = '2';
                $kualifikasi_total = 'Kurang';
            } else if ($skor_total == 27) {
                $level_total = '3-';
                $kualifikasi_total = 'Cukup';
            } else if ($skor_total >= 28 && $skor_total <= 29) {
                $level_total = '3';
                $kualifikasi_total = 'Cukup';
            } else if ($skor_total == 30) {
                $level_total = '3+';
                $kualifikasi_total = 'Cukup';
            } else if ($skor_total >= 31 && $skor_total <= 35) {
                $level_total = '4';
                $kualifikasi_total = 'Baik';
            } else if ($skor_total >= 36 && $skor_total <= 46) {
                $level_total = '5';
                $kualifikasi_total = 'Sangat Baik';
            }

            $skor = HasilKecerdasanEmosi::updateOrCreate(
                [
                    'event_id' => Auth::guard('peserta')->user()->event_id,
                    'peserta_id' => Auth::guard('peserta')->user()->id,
                    'ujian_id' => $data->id,
                ],
                [
                    'nilai' => $nilai,
                    'skor_total' => $skor_total,
                    'level_total' => $level_total,
                    'kualifikasi_total' => $kualifikasi_total
                ]
            );

            $priority = ['SB', 'B', 'C+', 'C', 'C-', 'K', 'SK'];

            usort($nilai, function ($a, $b) use ($priority) {
                $posA = array_search($a['kualifikasi'], $priority);
                $posB = array_search($b['kualifikasi'], $priority);
                return $posA - $posB;
            });

            // Ambil 4 data setelah diurutkan, kemudian urutkan berdasar ranking (nomor indikator)
            $top_data = array_slice($nilai, 0, 4);
            usort($top_data, function ($a, $b) {
                return $a['ranking'] - $b['ranking'];
            });

            // Ambil nilai indikator nama, indikator nomor, dan kualifikasi dari hasil
            $indikator_nomor = array_column($top_data, 'ranking');
            $kualifikasi_array = array_column($top_data, 'kualifikasi');

            $data_to_save = [];

            foreach ($indikator_nomor as $index => $nomor) {
                $data_kualifikasi = RefKecerdasanEmosi::whereIndikatorNomor($nomor)->first();

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
        } catch (\Throwable $th) {
            // throw $th;
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
