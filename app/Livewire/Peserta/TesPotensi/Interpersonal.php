<?php

namespace App\Livewire\Peserta\TesPotensi;

use App\Models\Interpersonal\HasilInterpersonal;
use App\Models\Interpersonal\RefInterpersonal;
use App\Models\Interpersonal\SoalInterpersonal;
use App\Models\Interpersonal\UjianInterpersonal;
use App\Models\Settings;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.peserta.app', ['title' => 'Tes Interpersonal'])]
class Interpersonal extends Component
{
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
        // $count_peserta = UjianInterpersonal::where('peserta_id', Auth::guard('peserta')->user()->id)
        //     ->where('event_id', Auth::guard('peserta')->user()->event_id)
        //     ->where('is_finished', 'false')
        //     ->count();

        // if ($this->id_soal < 1 || $this->id_soal > $this->jml_soal || $count_peserta < 1) {
        //     return redirect('tes-potensi/interpersonal/1');
        // }

        $data = UjianInterpersonal::select('id', 'soal_id', 'jawaban', 'created_at')
            ->where('peserta_id', Auth::guard('peserta')->user()->id)
            ->where('event_id', Auth::guard('peserta')->user()->event_id)
            ->where('is_finished', 'false')
            ->first();

        $this->nomor_soal = explode(',', $data->soal_id);
        $this->jawaban_user = explode(',', $data->jawaban);
        $this->soal = SoalInterpersonal::find($this->nomor_soal[$this->id_soal - 1]);
        $this->jml_soal = SoalInterpersonal::count();
        $this->id_ujian = $data->id;
        $this->timer = $data->created_at->timestamp;

        $durasi_tes = Settings::where('alat_tes_id', 3)->first(['waktu']);
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
        return view('livewire..peserta.tes-potensi.interpersonal.ujian', [
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
        $data = UjianInterpersonal::where('peserta_id', Auth::guard('peserta')->user()->id)
            ->where('event_id', Auth::guard('peserta')->user()->event_id)
            ->where('is_finished', 'false')
            ->first();

        $soal_id = explode(',', $data->soal_id);

        // update jawaban
        $jawaban_user = explode(',', $data->jawaban);
        $jawaban_user[$index_array] = $this->jawaban_user[$index_array] ?? '0';
        $jawaban_user = implode(',', $jawaban_user);

        UjianInterpersonal::where('peserta_id', Auth::guard('peserta')->user()->id)
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

        $poin = SoalInterpersonal::find($soal_id[$index_array]);
        $poin_a = $poin->poin_opsi_a;
        $poin_b = $poin->poin_opsi_b;
        $poin_c = $poin->poin_opsi_c;

        $indikator_map = [
            [1, 7, 'nilai_indikator_ke'],
            [8, 17, 'nilai_indikator_bt'],
            [18, 27, 'nilai_indikator_as'],
            [28, 36, 'nilai_indikator_de'],
            [37, 45, 'nilai_indikator_smk'],
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
            $this->redirect(route('peserta.tes-potensi.interpersonal', ['id' => $nomor_soal + 1]), true);
        }
    }

    public function navigate($id)
    {
        if ($id >= 1 && $id <= $this->jml_soal) {
            $this->id_soal = $id;
            $this->soal = SoalInterpersonal::find($this->nomor_soal[$id - 1]);
            $this->redirect(route('peserta.tes-potensi.interpersonal', ['id' => $id]), true);
        }
    }

    public function finish()
    {
        $data = UjianInterpersonal::findOrFail($this->id_ujian);
        // indikator komunikasi efektif
        if ($data->nilai_indikator_ke >= 1 && $data->nilai_indikator_ke <= 3) {
            $standard_ke = '1';
            $kualifikasi_ke = 'SK';
        } else if ($data->nilai_indikator_ke >= 4 && $data->nilai_indikator_ke <= 7) {
            $standard_ke = '2';
            $kualifikasi_ke = 'K';
        } else if ($data->nilai_indikator_ke == 8) {
            $standard_ke = '3-';
            $kualifikasi_ke = 'C-';
        } else if ($data->nilai_indikator_ke >= 9 && $data->nilai_indikator_ke <= 10) {
            $standard_ke = '3';
            $kualifikasi_ke = 'C';
        } else if ($data->nilai_indikator_ke == 11) {
            $standard_ke = '3+';
            $kualifikasi_ke = 'C+';
        } else if ($data->nilai_indikator_ke >= 12 && $data->nilai_indikator_ke <= 13) {
            $standard_ke = '4';
            $kualifikasi_ke = 'B';
        } else if ($data->nilai_indikator_ke >= 14) {
            $standard_ke = '5';
            $kualifikasi_ke = 'SB';
        }

        // indikator bersikap terbuka
        if ($data->nilai_indikator_bt >= 1 && $data->nilai_indikator_bt <= 8) {
            $standard_bt = '1';
            $kualifikasi_bt = 'SK';
        } else if ($data->nilai_indikator_bt >= 9 && $data->nilai_indikator_bt <= 13) {
            $standard_bt = '2';
            $kualifikasi_bt = 'K';
        } else if ($data->nilai_indikator_bt == 14) {
            $standard_bt = '3-';
            $kualifikasi_bt = 'C-';
        } else if ($data->nilai_indikator_bt == 15) {
            $standard_bt = '3';
            $kualifikasi_bt = 'C';
        } else if ($data->nilai_indikator_bt == 16) {
            $standard_bt = '3+';
            $kualifikasi_bt = 'C+';
        } else if ($data->nilai_indikator_bt >= 17 && $data->nilai_indikator_bt <= 20) {
            $standard_bt = '4';
            $kualifikasi_bt = 'B';
        } else if ($data->nilai_indikator_bt >= 21) {
            $standard_bt = '5';
            $kualifikasi_bt = 'SB';
        }

        // indikator asertif
        if ($data->nilai_indikator_as >= 1 && $data->nilai_indikator_as <= 12) {
            $standard_as = '1';
            $kualifikasi_as = 'SK';
        } else if ($data->nilai_indikator_as >= 13 && $data->nilai_indikator_as <= 15) {
            $standard_as = '2';
            $kualifikasi_as = 'K';
        } else if ($data->nilai_indikator_as == 16) {
            $standard_as = '3-';
            $kualifikasi_as = 'C-';
        } else if ($data->nilai_indikator_as >= 17 && $data->nilai_indikator_as <= 18) {
            $standard_as = '3';
            $kualifikasi_as = 'C';
        } else if ($data->nilai_indikator_as == 19) {
            $standard_as = '3+';
            $kualifikasi_as = 'C+';
        } else if ($data->nilai_indikator_as >= 20 && $data->nilai_indikator_as <= 21) {
            $standard_as = '4';
            $kualifikasi_as = 'B';
        } else if ($data->nilai_indikator_as >= 22) {
            $standard_as = '5';
            $kualifikasi_as = 'SB';
        }

        // indikator dukungan emosional
        if ($data->nilai_indikator_de >= 1 && $data->nilai_indikator_de <= 11) {
            $standard_de = '1';
            $kualifikasi_de = 'SK';
        } else if ($data->nilai_indikator_de >= 12 && $data->nilai_indikator_de <= 13) {
            $standard_de = '2';
            $kualifikasi_de = 'K';
        } else if ($data->nilai_indikator_de == 14) {
            $standard_de = '3-';
            $kualifikasi_de = 'C-';
        } else if ($data->nilai_indikator_de >= 15 && $data->nilai_indikator_de <= 16) {
            $standard_de = '3';
            $kualifikasi_de = 'C';
        } else if ($data->nilai_indikator_de == 17) {
            $standard_de = '3+';
            $kualifikasi_de = 'C+';
        } else if ($data->nilai_indikator_de >= 18 && $data->nilai_indikator_de <= 19) {
            $standard_de = '4';
            $kualifikasi_de = 'B';
        } else if ($data->nilai_indikator_de >= 20) {
            $standard_de = '5';
            $kualifikasi_de = 'SB';
        }

        // indikator sikap menghadapi konflik
        if ($data->nilai_indikator_smk >= 1 && $data->nilai_indikator_smk <= 11) {
            $standard_smk = '1';
            $kualifikasi_smk = 'SK';
        } else if ($data->nilai_indikator_smk >= 12 && $data->nilai_indikator_smk <= 13) {
            $standard_smk = '2';
            $kualifikasi_smk = 'K';
        } else if ($data->nilai_indikator_smk == 14) {
            $standard_smk = '3-';
            $kualifikasi_smk = 'C-';
        } else if ($data->nilai_indikator_smk >= 15 && $data->nilai_indikator_smk <= 16) {
            $standard_smk = '3';
            $kualifikasi_smk = 'C';
        } else if ($data->nilai_indikator_smk == 17) {
            $standard_smk = '3+';
            $kualifikasi_smk = 'C+';
        } else if ($data->nilai_indikator_smk >= 18 && $data->nilai_indikator_smk <= 19) {
            $standard_smk = '4';
            $kualifikasi_smk = 'B';
        } else if ($data->nilai_indikator_smk >= 20) {
            $standard_smk = '5';
            $kualifikasi_smk = 'SB';
        }

        $indikator = RefInterpersonal::get(['indikator_nama', 'indikator_nomor']);

        $skor = new HasilInterpersonal();
        $skor->event_id = Auth::guard('peserta')->user()->event_id;
        $skor->peserta_id = Auth::guard('peserta')->user()->id;
        $skor->ujian_id = $data->id;
        $nilai = [];
        foreach ($indikator as $value) {
            if ($value->indikator_nomor == 1) {
                $nilai[] = [
                    'indikator' => $value->indikator_nama,
                    'ranking' => $value->indikator_nomor,
                    'skor' => $data->nilai_indikator_ke,
                    'standard' => $standard_ke ?? '',
                    'kualifikasi' => $kualifikasi_ke ?? ''
                ];
            } else if ($value->indikator_nomor == 2) {
                $nilai[] = [
                    'indikator' => $value->indikator_nama,
                    'ranking' => $value->indikator_nomor,
                    'skor' => $data->nilai_indikator_bt,
                    'standard' => $standard_bt ?? '',
                    'kualifikasi' => $kualifikasi_bt ?? ''
                ];
            } else if ($value->indikator_nomor == 3) {
                $nilai[] = [
                    'indikator' => $value->indikator_nama,
                    'ranking' => $value->indikator_nomor,
                    'skor' => $data->nilai_indikator_as,
                    'standard' => $standard_as ?? '',
                    'kualifikasi' => $kualifikasi_as ?? ''
                ];
            } else if ($value->indikator_nomor == 4) {
                $nilai[] = [
                    'indikator' => $value->indikator_nama,
                    'ranking' => $value->indikator_nomor,
                    'skor' => $data->nilai_indikator_de,
                    'standard' => $standard_de ?? '',
                    'kualifikasi' => $kualifikasi_de ?? ''
                ];
            } else if ($value->indikator_nomor == 5) {
                $nilai[] = [
                    'indikator' => $value->indikator_nama,
                    'ranking' => $value->indikator_nomor,
                    'skor' => $data->nilai_indikator_smk,
                    'standard' => $standard_smk ?? '',
                    'kualifikasi' => $kualifikasi_smk ?? ''
                ];
            }
        }

        $skor->nilai = $nilai;

        $skor_total = $data->nilai_indikator_ke + $data->nilai_indikator_bt + $data->nilai_indikator_as + $data->nilai_indikator_de + $data->nilai_indikator_smk;
        $skor->skor_total = $skor_total;
        if ($skor_total <= 55) {
            $level_total = '1';
            $kualifikasi_total = 'Sangat Kurang';
        } else if ($skor_total >= 56 && $skor_total <= 67) {
            $level_total = '2';
            $kualifikasi_total = 'Kurang';
        } else if ($skor_total == 68) {
            $level_total = '3-';
            $kualifikasi_total = 'Cukup';
        } else if ($skor_total >= 69 && $skor_total <= 75) {
            $level_total = '3';
            $kualifikasi_total = 'Cukup';
        } else if ($skor_total >= 76 && $skor_total <= 78) {
            $level_total = '3+';
            $kualifikasi_total = 'Cukup';
        } else if ($skor_total >= 79 && $skor_total <= 89) {
            $level_total = '4';
            $kualifikasi_total = 'Baik';
        } else if ($skor_total >= 90) {
            $level_total = '5';
            $kualifikasi_total = 'Sangat Baik';
        }

        $skor->level_total = $level_total;
        $skor->kualifikasi_total = $kualifikasi_total;

        $priority = ['SB', 'B', 'C+', 'C', 'C-', 'K', 'SK'];

        // menyortir data berdasarkan urutan kualifikasi
        usort($nilai, function ($a, $b) use ($priority) {
            $posA = array_search($a['kualifikasi'], $priority);
            $posB = array_search($b['kualifikasi'], $priority);
            return $posA - $posB;
        });

        // Ambil kualifikasi tertinggi pertama
        $top_kualifikasi = $nilai[0]['kualifikasi'];

        // Ambil semua data dengan kualifikasi tertinggi
        $top_data = array_filter($nilai, function($item) use ($top_kualifikasi) {
            return $item['kualifikasi'] === $top_kualifikasi;
        });

        // Jika jumlah data kurang dari 2, ambil tambahan data dari kualifikasi berikutnya
        if (count($top_data) < 2) {
            $next_kualifikasi = $nilai[count($top_data)]['kualifikasi'];
            $next_data = array_filter($nilai, function($item) use ($next_kualifikasi) {
                return $item['kualifikasi'] === $next_kualifikasi;
            });
            $top_data = array_merge($top_data, array_slice($next_data, 0, 2 - count($top_data)));
        }

        // Ambil nilai indikator nama, indikator nomor, dan kualifikasi dari hasil
        $indikator_nama = array_column($top_data, 'indikator');
        $indikator_nomor = array_column($top_data, 'ranking');
        $kualifikasi_array = array_column($top_data, 'kualifikasi');

        // cari uraian potensi berdasar indikator dengan kualifikasi tertinggi pertama dan kedua
        $data_kualifikasi_1 = RefInterpersonal::whereIndikatorNomor($indikator_nomor[0])->first();
        $kualifikasi_1 = $data_kualifikasi_1->kualifikasi;
        $data_kualifikasi_2 = RefInterpersonal::whereIndikatorNomor($indikator_nomor[1])->first();
        $kualifikasi_2 = $data_kualifikasi_2->kualifikasi;

        $first_qualification = $this->_getKualifikasi($kualifikasi_array[0]);
        $second_qualification = $this->_getKualifikasi($kualifikasi_array[1]);
        $uraian_potensi_1 = collect($kualifikasi_1)->firstWhere('kualifikasi', $first_qualification);
        $uraian_potensi_2 = collect($kualifikasi_2)->firstWhere('kualifikasi', $second_qualification);

        $skor->indikator_potensi_1 = $indikator_nama[0];
        $skor->uraian_potensi_1 = $uraian_potensi_1['uraian_potensi'];
        $skor->indikator_potensi_2 = $indikator_nama[1];
        $skor->uraian_potensi_2 = $uraian_potensi_2['uraian_potensi'];
        $skor->save();

        // change status ujian to true (finish)
        $data->is_finished = true;
        $data->save();

        return $this->redirect(route('peserta.tes-potensi'), navigate: true);
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
            case 'C':
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
