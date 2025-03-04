<?php

namespace App\Livewire\Peserta\TesPotensi;

use App\Models\KesadaranDiri\HasilKesadaranDiri;
use App\Models\KesadaranDiri\RefKesadaranDiri;
use App\Models\KesadaranDiri\SoalKesadaranDiri;
use App\Models\KesadaranDiri\UjianKesadaranDiri;
use App\Models\Settings;
use App\Models\SettingWaktuTes;
use App\Traits\StartTestTrait;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.peserta.app', ['title' => 'Tes Kesadaran Diri'])]
class KesadaranDiri extends Component
{
    use StartTestTrait;

    public $soal;
    public $jml_soal;
    public $id_soal;
    public $nomor_soal;
    public $jawaban_user = [];
    public $jawaban_kosong;
    public $id_ujian;
    public $timer;
    public $durasi_tes;

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
        $this->timer = $data->created_at->timestamp;

        $durasi_tes = SettingWaktuTes::whereIsActive('true')->first(['waktu']);
        $this->durasi_tes = $durasi_tes->waktu;

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
            'soal' => $this->soal
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

        // update jawaban
        $jawaban_user = explode(',', $data->jawaban);
        $jawaban_user[$index_array] = $this->jawaban_user[$index_array] ?? '0';
        $jawaban_user = implode(',', $jawaban_user);

        UjianKesadaranDiri::where('peserta_id', Auth::guard('peserta')->user()->id)
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

        $poin = SoalKesadaranDiri::find($soal_id[$index_array]);
        $poin_a = $poin->poin_opsi_a;
        $poin_b = $poin->poin_opsi_b;
        $poin_c = $poin->poin_opsi_c;

        $indikator_map = [
            [1, 22, 'nilai_indikator_1'],
            [23, 46, 'nilai_indikator_2'],
            [47, 65, 'nilai_indikator_3']
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
                }
                $data->update([$indikator => $skor]);
                break;
            }
        }

        if ($nomor_soal < $this->jml_soal) {
            $this->redirect(route('peserta.tes-potensi.kesadaran-diri', ['id' => $nomor_soal + 1]), true);
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
            if ($data->nilai_indikator_1 <= 49) {
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
            } else if ($data->nilai_indikator_1 >= 62 && $data->nilai_indikator_1 <= 67) {
                $kualifikasi_1 = 'B';
                $kategori_kualifikasi_1 = 'Baik';
                $kategori_1 = 'Tinggi';
            } else if ($data->nilai_indikator_1 > 67) {
                $kualifikasi_1 = 'SB';
                $kategori_kualifikasi_1 = 'Sangat Baik';
                $kategori_1 = 'Tinggi';
            }
    
            // indikator 2
            if ($data->nilai_indikator_2 <= 50) {
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
            } else if ($data->nilai_indikator_2 >= 67 && $data->nilai_indikator_2 <= 74) {
                $kualifikasi_2 = 'B';
                $kategori_kualifikasi_2 = 'Baik';
                $kategori_2 = 'Tinggi';
            } else if ($data->nilai_indikator_2 > 74) {
                $kualifikasi_2 = 'SB';
                $kategori_kualifikasi_2 = 'Sangat Baik';
                $kategori_2 = 'Tinggi';
            }
    
            // indikator 3
            if ($data->nilai_indikator_3 <= 41) {
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
            } else if ($data->nilai_indikator_3 > 56) {
                $kategori_3 = 'SB';
                $kategori_kualifikasi_3 = 'Sangat Baik';
                $kualifikasi_3 = 'Tinggi';
            }
    
            $indikator = RefKesadaranDiri::get(['indikator_nama', 'indikator_nomor']);
    
            // $skor = new HasilKesadaranDiri();
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
    
            // $skor->nilai = $nilai;
    
            $skor_total = $data->nilai_indikator_1 + $data->nilai_indikator_2 + $data->nilai_indikator_3;
            
            if (($skor_total == 0 || $skor_total <= 143)) {
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
            } else if ($skor_total > 194) {
                $level_total = '5';
                $kualifikasi_total = 'Sangat Baik';
                $kategori_total = 'SB';
            }
    
            // $skor->level_total = $level_total;
            // $skor->kualifikasi_total = $kualifikasi_total;
            // $skor->kategori_total = $kategori_total;
            // $skor->skor_total = $skor_total;
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
    
            // Ambil kategori tertinggi pertama
            $top_kategori = $nilai[0]['kategori'];
    
            // Ambil semua data dengan kategori tertinggi
            $top_data = array_filter($nilai, function($item) use ($top_kategori) {
                return $item['kategori'] === $top_kategori;
            });
    
            // Jika jumlah data kurang dari 2, ambil tambahan data dari kategori berikutnya
            if (count($top_data) < 2) {
                $next_kategori = $nilai[count($top_data)]['kategori'];
                $next_data = array_filter($nilai, function($item) use ($next_kategori) {
                    return $item['kategori'] === $next_kategori;
                });
                $top_data = array_merge($top_data, array_slice($next_data, 0, 2 - count($top_data)));
            }
    
            // Ambil nilai indikator nama, indikator nomor, dan kategori dari hasil
            $indikator_nomor = array_column($top_data, 'no_indikator');
            $kategori_array = array_column($top_data, 'kategori');
    
            // cari uraian potensi berdasar indikator dengan kategori tertinggi pertama dan kedua
            $data_kategori_1 = RefKesadaranDiri::whereIndikatorNomor($indikator_nomor[0])->first();
            $kategori_1 = $data_kategori_1->kualifikasi;
            $data_kategori_2 = RefKesadaranDiri::whereIndikatorNomor($indikator_nomor[1])->first();
            $kategori_2 = $data_kategori_2->kualifikasi;
    
            $first_qualification = $this->_getKualifikasi($kategori_array[0]);
            $second_qualification = $this->_getKualifikasi($kategori_array[1]);
            $uraian_potensi_1 = collect($kategori_1)->firstWhere('kualifikasi', $first_qualification);
            $uraian_potensi_2 = collect($kategori_2)->firstWhere('kualifikasi', $second_qualification);
    
            // $skor->uraian_potensi_1 = $uraian_potensi_1['uraian_potensi'];
            // $skor->uraian_potensi_2 = $uraian_potensi_2['uraian_potensi'];
            // $skor->save();
            $skor->update([
                'uraian_potensi_1' => $uraian_potensi_1['uraian_potensi'] ?? '',
                'uraian_potensi_2' => $uraian_potensi_2['uraian_potensi'] ?? '',
            ]);
    
            // change status ujian to true (finish)
            $data->is_finished = true;
            $data->save();

            $current_sequence_test = Settings::where('alat_tes_id', session('current_test'))->first(['urutan']);
            if ($current_sequence_test) {
                if ($current_sequence_test->urutan !== 7) {
                    $next_test = Settings::with('alatTes')->where('urutan', $current_sequence_test->urutan + 1)->first();
                    session(['current_test' => $next_test->alat_tes_id]);
                    $this->startTest($next_test->alatTes->alat_tes);
                } else {
                    return $this->redirect(route('peserta.tes-potensi.home'), navigate: true);
                }
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
        }

        return $kualifikasi;
    }
}
