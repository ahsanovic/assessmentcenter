<?php

namespace App\Livewire\Peserta\TesPotensi;

use App\Models\MotivasiKomitmen\HasilMotivasiKomitmen;
use App\Models\MotivasiKomitmen\RefDeskripsiMotivasiKomitmen;
use App\Models\MotivasiKomitmen\RefMotivasiKomitmen;
use App\Models\MotivasiKomitmen\SoalMotivasiKomitmen;
use App\Models\MotivasiKomitmen\UjianMotivasiKomitmen;
use App\Models\NilaiJpm;
use App\Models\Settings;
use App\Traits\StartTestTrait;
use App\Traits\TimerTrait;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.peserta.app', ['title' => 'Tes Motivasi dan Komitmen'])]
class MotivasiKomitmen extends Component
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

    public function mount($id)
    {
        $this->id_soal = $id;

        $data = UjianMotivasiKomitmen::select('id', 'soal_id', 'jawaban', 'created_at')
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
        $this->soal = SoalMotivasiKomitmen::find($this->nomor_soal[$this->id_soal - 1]);
        $this->jml_soal = SoalMotivasiKomitmen::count();
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
        return view('livewire..peserta.tes-potensi.motivasi-komitmen.ujian', [
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
        $data = UjianMotivasiKomitmen::where('peserta_id', Auth::guard('peserta')->user()->id)
            ->where('event_id', Auth::guard('peserta')->user()->event_id)
            ->where('is_finished', 'false')
            ->first();

        $soal_id = explode(',', $data->soal_id);

        // update jawaban
        $jawaban_user = explode(',', $data->jawaban);
        $jawaban_user[$index_array] = $this->jawaban_user[$index_array] ?? '0';
        $jawaban_user = implode(',', $jawaban_user);

        UjianMotivasiKomitmen::where('peserta_id', Auth::guard('peserta')->user()->id)
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

        $poin = SoalMotivasiKomitmen::find($soal_id[$index_array]);
        $poin_a = $poin->poin_opsi_a;
        $poin_b = $poin->poin_opsi_b;

        $indikator_map = [
            [1, 15, 'nilai_indikator_1'],
            [16, 24, 'nilai_indikator_2'],
            [25, 34, 'nilai_indikator_3'],
            [35, 44, 'nilai_indikator_4'],
            [45, 55, 'nilai_indikator_5'],
        ];

        foreach ($indikator_map as [$start, $end, $indikator]) {
            if ($nomor_soal >= $start && $nomor_soal <= $end) {
                $skor = $data->{$indikator};
                if ($this->jawaban_user[$index_array] === 'A') {
                    $skor += $poin_a;
                } elseif ($this->jawaban_user[$index_array] === 'B') {
                    $skor += $poin_b;
                }

                $data->update([$indikator => $skor]);
                break;
            }
        }

        if ($nomor_soal < $this->jml_soal) {
            $this->redirect(route('peserta.tes-potensi.motivasi-komitmen', ['id' => $nomor_soal + 1]), true);
        } else if ($nomor_soal == $this->jml_soal) {
            $this->redirect(route('peserta.tes-potensi.motivasi-komitmen', ['id' => $nomor_soal]), true);
        }
    }

    public function navigate($id)
    {
        if ($id >= 1 && $id <= $this->jml_soal) {
            $this->id_soal = $id;
            $this->soal = SoalMotivasiKomitmen::find($this->nomor_soal[$id - 1]);
            $this->redirect(route('peserta.tes-potensi.motivasi-komitmen', ['id' => $id]), true);
        }
    }

    public function finish()
    {
        try {
            $data = UjianMotivasiKomitmen::findOrFail($this->id_ujian);
    
            // indikator motivasi keterdekatan dengan rekan kerja
            if ($data->nilai_indikator_1 >= 1 && $data->nilai_indikator_1 <= 6) {
                $standard_1 = '1';
                $kualifikasi_1 = '1/SK';
            } else if ($data->nilai_indikator_1 >= 7 && $data->nilai_indikator_1 <= 9) {
                $standard_1 = '2';
                $kualifikasi_1 = '1/K';
            } else if ($data->nilai_indikator_1 == 10) {
                $standard_1 = '3-';
                $kualifikasi_1 = '1/C-';
            } else if ($data->nilai_indikator_1 == 11) {
                $standard_1 = '3';
                $kualifikasi_1 = '1/C';
            } else if ($data->nilai_indikator_1 == 12) {
                $standard_1 = '3+';
                $kualifikasi_1 = '1/C+';
            } else if ($data->nilai_indikator_1 >= 13 && $data->nilai_indikator_1 <= 14) {
                $standard_1 = '4';
                $kualifikasi_1 = '1/B';
            } else if ($data->nilai_indikator_1 == 15) {
                $standard_1 = '5';
                $kualifikasi_1 = '1/SB';
            }
    
            // indikator motivasi mengidentifikasi diri
            if ($data->nilai_indikator_2 >= 1 && $data->nilai_indikator_2 <= 8) {
                $standard_2 = '1';
                $kualifikasi_2 = '2/SK';
            } else if ($data->nilai_indikator_2 >= 9 && $data->nilai_indikator_2 <= 10) {
                $standard_2 = '2';
                $kualifikasi_2 = '2/K';
            } else if ($data->nilai_indikator_2 == 11) {
                $standard_2 = '3-';
                $kualifikasi_2 = '2/C-';
            } else if ($data->nilai_indikator_2 == 12) {
                $standard_2 = '3';
                $kualifikasi_2 = '2/C';
            } else if ($data->nilai_indikator_2 == 13) {
                $standard_2 = '3+';
                $kualifikasi_2 = '2/C+';
            } else if ($data->nilai_indikator_2 >= 14 && $data->nilai_indikator_2 <= 16) {
                $standard_2 = '4';
                $kualifikasi_2 = '2/B';
            } else if ($data->nilai_indikator_2 >= 17) {
                $standard_2 = '5';
                $kualifikasi_2 = '2/SB';
            }
    
            // indikator motivasi mempertimbangkan keuntungan
            if ($data->nilai_indikator_3 >= 1 && $data->nilai_indikator_3 <= 6) {
                $standard_3 = '1';
                $kualifikasi_3 = '3/SK';
            } else if ($data->nilai_indikator_3 >= 7 && $data->nilai_indikator_3 <= 11) {
                $standard_3 = '2';
                $kualifikasi_3 = '3/K';
            } else if ($data->nilai_indikator_3 == 12) {
                $standard_3 = '3-';
                $kualifikasi_3 = '3/C-';
            } else if ($data->nilai_indikator_3 >= 13 && $data->nilai_indikator_3 <= 14) {
                $standard_3 = '3';
                $kualifikasi_3 = '3/C';
            } else if ($data->nilai_indikator_3 == 15) {
                $standard_3 = '3+';
                $kualifikasi_3 = '3/C+';
            } else if ($data->nilai_indikator_3 >= 16 && $data->nilai_indikator_3 <= 19) {
                $standard_3 = '4';
                $kualifikasi_3 = '3/B';
            } else if ($data->nilai_indikator_3 >= 20) {
                $standard_3 = '5';
                $kualifikasi_3 = '3/SB';
            }
    
            // indikator motivasi adapatsi dalam tim
            if ($data->nilai_indikator_4 >= 1 && $data->nilai_indikator_4 <= 12) {
                $standard_4 = '1';
                $kualifikasi_4 = '4/SK';
            } else if ($data->nilai_indikator_4 >= 13 && $data->nilai_indikator_4 <= 19) {
                $standard_4 = '2';
                $kualifikasi_4 = '4/K';
            } else if ($data->nilai_indikator_4 >= 20 && $data->nilai_indikator_4 <= 21) {
                $standard_4 = '3-';
                $kualifikasi_4 = '4/C-';
            } else if ($data->nilai_indikator_4 >= 22 && $data->nilai_indikator_4 <= 24) {
                $standard_4 = '3';
                $kualifikasi_4 = '4/C';
            } else if ($data->nilai_indikator_4 >= 25 && $data->nilai_indikator_4 <= 26) {
                $standard_4 = '3+';
                $kualifikasi_4 = '4/C+';
            } else if ($data->nilai_indikator_4 >= 27 && $data->nilai_indikator_4 <= 32) {
                $standard_4 = '4';
                $kualifikasi_4 = '4/B';
            } else if ($data->nilai_indikator_4 >= 33) {
                $standard_4 = '5';
                $kualifikasi_4 = '4/SB';
            }
    
            // indikator motivasi memiliki loyalitas
            if ($data->nilai_indikator_5 >= 1 && $data->nilai_indikator_5 <= 18) {
                $standard_5 = '1';
                $kualifikasi_5 = '5/SK';
            } else if ($data->nilai_indikator_5 >= 19 && $data->nilai_indikator_5 <= 26) {
                $standard_5 = '2';
                $kualifikasi_5 = '5/K';
            } else if ($data->nilai_indikator_5 >= 27 && $data->nilai_indikator_5 <= 29) {
                $standard_5 = '3-';
                $kualifikasi_5 = '5/C-';
            } else if ($data->nilai_indikator_5 == 30) {
                $standard_5 = '3';
                $kualifikasi_5 = '5/C';
            } else if ($data->nilai_indikator_5 >= 31 && $data->nilai_indikator_5 <= 32) {
                $standard_5 = '3+';
                $kualifikasi_5 = '5/C+';
            } else if ($data->nilai_indikator_5 >= 33 && $data->nilai_indikator_5 <= 39) {
                $standard_5 = '4';
                $kualifikasi_5 = '5/B';
            } else if ($data->nilai_indikator_5 >= 40) {
                $standard_5 = '5';
                $kualifikasi_5 = '5/SB';
            }
    
            $indikator = RefMotivasiKomitmen::get(['indikator_nama', 'indikator_nomor']);
            $nilai = [];
            foreach ($indikator as $value) {
                if ($value->indikator_nomor == 1) {
                    $nilai[] = [
                        'indikator' => $value->indikator_nama,
                        'ranking' => $value->indikator_nomor,
                        'skor' => $data->nilai_indikator_1,
                        'standard' => $standard_1 ?? '',
                        'kualifikasi' => $kualifikasi_1 ?? ''
                    ];
                } else if ($value->indikator_nomor == 2) {
                    $nilai[] = [
                        'indikator' => $value->indikator_nama,
                        'ranking' => $value->indikator_nomor,
                        'skor' => $data->nilai_indikator_2,
                        'standard' => $standard_2 ?? '',
                        'kualifikasi' => $kualifikasi_2 ?? ''
                    ];
                } else if ($value->indikator_nomor == 3) {
                    $nilai[] = [
                        'indikator' => $value->indikator_nama,
                        'ranking' => $value->indikator_nomor,
                        'skor' => $data->nilai_indikator_3,
                        'standard' => $standard_3 ?? '',
                        'kualifikasi' => $kualifikasi_3 ?? ''
                    ];
                } else if ($value->indikator_nomor == 4) {
                    $nilai[] = [
                        'indikator' => $value->indikator_nama,
                        'ranking' => $value->indikator_nomor,
                        'skor' => $data->nilai_indikator_4,
                        'standard' => $standard_4 ?? '',
                        'kualifikasi' => $kualifikasi_4 ?? ''
                    ];
                } else if ($value->indikator_nomor == 5) {
                    $nilai[] = [
                        'indikator' => $value->indikator_nama,
                        'ranking' => $value->indikator_nomor,
                        'skor' => $data->nilai_indikator_5,
                        'standard' => $standard_5 ?? '',
                        'kualifikasi' => $kualifikasi_5 ?? ''
                    ];
                }
            }
    
            $skor_total = $data->nilai_indikator_1 + $data->nilai_indikator_2 + $data->nilai_indikator_3 + $data->nilai_indikator_4 + $data->nilai_indikator_5;
    
            $priority = [
                '5/SB',
                '4/SB',
                '3/SB',
                '2/SB',
                '1/SB',
                '5/B',
                '4/B',
                '3/B',
                '2/B',
                '1/B',
                '5/C+',
                '4/C+',
                '3/C+',
                '2/C+',
                '1/C+',
                '5/C',
                '4/C',
                '3/C',
                '2/C',
                '1/C',
                '5/C-',
                '4/C-',
                '3/C-',
                '2/C-',
                '1/C-',
                '5/K',
                '4/K',
                '3/K',
                '2/K',
                '1/K',
                '5/SK',
                '4/SK',
                '3/SK',
                '2/SK',
                '1/SK'
            ];
    
            // menyortir data berdasarkan urutan kualifikasi
            usort($nilai, function ($a, $b) use ($priority) {
                $posA = array_search($a['kualifikasi'], $priority);
                $posB = array_search($b['kualifikasi'], $priority);
                // Jika tidak ditemukan, beri nilai besar agar berada di urutan terakhir
                $posA = $posA === false ? PHP_INT_MAX : $posA;
                $posB = $posB === false ? PHP_INT_MAX : $posB;
    
                return $posA <=> $posB;
            });
    
            // Ambil kualifikasi tertinggi pertama
            $top_kualifikasi = $nilai[0]['kualifikasi'];
    
            // Ambil semua data dengan kualifikasi tertinggi
            $top_data = $top_kualifikasi ? array_filter($nilai, function ($item) use ($top_kualifikasi) {
                return $item['kualifikasi'] === $top_kualifikasi;
            }) : [];
    
            $kualifikasi_tertinggi = $top_data[0]['kualifikasi'];
            $deskripsi = RefDeskripsiMotivasiKomitmen::where('kategori_penilaian', 'like', '%' . $kualifikasi_tertinggi . '%')->first();

            HasilMotivasiKomitmen::updateOrCreate(
                [
                    'event_id' => Auth::guard('peserta')->user()->event_id,
                    'peserta_id' => Auth::guard('peserta')->user()->id,
                    'ujian_id' => $data->id,
                ],
                [
                    'nilai' => $nilai,
                    'skor_total' => $skor_total,
                    'level' => $deskripsi->level,
                    'deskripsi' => $deskripsi->deskripsi,
                    'kualifikasi_total' => $this->_getKualifikasi($deskripsi->level)
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
        } catch (\Throwable $th) {
            // throw $th;
            session()->flash('toast', [
                'type' => 'error',
                'message' => 'Terjadi kesalahan'
            ]);
        }
    }

    private function _getKualifikasi($level)
    {
        switch ($level) {
            case '5':
                $kualifikasi = 'Sangat Baik';
                break;
            case '4':
                $kualifikasi = 'Baik';
                break;
            case '3':
                $kualifikasi = 'Cukup';
                break;
            case '2':
                $kualifikasi = 'Kurang';
                break;
            case '1':
                $kualifikasi = 'Sangat Kurang';
                break;
            default:
                $kualifikasi = '';
                break;
        }

        return $kualifikasi;
    }
}
