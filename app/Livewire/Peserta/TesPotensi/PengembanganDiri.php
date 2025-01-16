<?php

namespace App\Livewire\Peserta\TesPotensi;

use App\Models\PengembanganDiri\HasilPengembanganDiri;
use App\Models\PengembanganDiri\RefPengembanganDiri;
use App\Models\PengembanganDiri\SoalPengembanganDiri;
use App\Models\PengembanganDiri\UjianPengembanganDiri;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.peserta.app', ['title' => 'Tes Belajar Cepat dan Pengembangan Diri'])]
class PengembanganDiri extends Component
{
    public $soal;
    public $jml_soal;
    public $id_soal;
    public $nomor_soal;
    public $jawaban_user = [];
    public $jawaban_kosong;
    public $id_ujian;

    public function mount($id)
    {
        $this->id_soal = $id;
        // $count_peserta = UjianPengembanganDiri::where('peserta_id', Auth::guard('peserta')->user()->id)
        //     ->where('event_id', Auth::guard('peserta')->user()->event_id)
        //     ->where('is_finished', 'false')
        //     ->count();

        // if ($this->id_soal < 1 || $this->id_soal > $this->jml_soal || $count_peserta < 1) {
        //     return redirect('tes-potensi/pengembangan-diri/1');
        // }

        $data = UjianPengembanganDiri::select('id', 'soal_id', 'jawaban')
            ->where('peserta_id', Auth::guard('peserta')->user()->id)
            ->where('event_id', Auth::guard('peserta')->user()->event_id)
            ->where('is_finished', 'false')
            ->first();

        $this->nomor_soal = explode(',', $data->soal_id);
        $this->jawaban_user = explode(',', $data->jawaban);
        $this->soal = SoalPengembanganDiri::find($this->nomor_soal[$this->id_soal - 1]);
        $this->jml_soal = SoalPengembanganDiri::count();
        $this->id_ujian = $data->id;

        for ($i = 0, $j = 0; $i < $this->jml_soal; $i++) {
            if ($this->jawaban_user[$i] == '0') {
                $j = $j + 1;
                $this->jawaban_kosong = $j;
            }
        }
    }

    public function render()
    {
        return view('livewire..peserta.tes-potensi.pengembangan-diri.ujian', [
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
        $data = UjianPengembanganDiri::where('peserta_id', Auth::guard('peserta')->user()->id)
            ->where('event_id', Auth::guard('peserta')->user()->event_id)
            ->where('is_finished', 'false')
            ->first();

        $soal_id = explode(',', $data->soal_id);

        // update jawaban
        $jawaban_user = explode(',', $data->jawaban);
        $jawaban_user[$index_array] = $this->jawaban_user[$index_array] ?? '0';
        $jawaban_user = implode(',', $jawaban_user);

        UjianPengembanganDiri::where('peserta_id', Auth::guard('peserta')->user()->id)
            ->where('event_id', Auth::guard('peserta')->user()->event_id)
            ->where('is_finished', 'false')
            ->update(['jawaban' => $jawaban_user]);

        $poin = SoalPengembanganDiri::find($soal_id[$index_array]);
        $poin_a = $poin->poin_opsi_a;
        $poin_b = $poin->poin_opsi_b;

        $indikator_map = [
            [1, 13, 'nilai_indikator_mb'],
            [14, 24, 'nilai_indikator_mit'],
            [25, 32, 'nilai_indikator_pde'],
            [33, 41, 'nilai_indikator_spd'],
            [42, 52, 'nilai_indikator_ed'],
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
            $this->redirect(route('peserta.tes-potensi.pengembangan-diri', ['id' => $nomor_soal + 1]), true);
        }
    }

    public function navigate($id)
    {
        if ($id >= 1 && $id <= $this->jml_soal) {
            $this->id_soal = $id;
            $this->soal = SoalPengembanganDiri::find($this->nomor_soal[$id - 1]);
            $this->redirect(route('peserta.tes-potensi.pengembangan-diri', ['id' => $id]), true);
        }
    }

    public function finish()
    {
        $data = UjianPengembanganDiri::findOrFail($this->id_ujian);
        // indikator motivasi belajar
        if ($data->nilai_indikator_mb >= 1 && $data->nilai_indikator_mb <= 4) {
            $standard_mb = 1;
            $kualifikasi_mb = 'SK';
        } else if ($data->nilai_indikator_mb >= 5 && $data->nilai_indikator_mb <= 6) {
            $standard_mb = 2;
            $kualifikasi_mb = 'K';
        } else if ($data->nilai_indikator_mb >= 7 && $data->nilai_indikator_mb <= 8) {
            $standard_mb = 3;
            $kualifikasi_mb = 'C';
        } else if ($data->nilai_indikator_mb >= 9 && $data->nilai_indikator_mb <= 10) {
            $standard_mb = 4;
            $kualifikasi_mb = 'B';
        } else if ($data->nilai_indikator_mb >= 11) {
            $standard_mb = 5;
            $kualifikasi_mb = 'SB';
        }

        // indikator mencari informasi tepat/akurat
        if ($data->nilai_indikator_mit >= 1 && $data->nilai_indikator_mit <= 14) {
            $standard_mit = 1;
            $kualifikasi_mit = 'SK';
        } else if ($data->nilai_indikator_mit >= 15 && $data->nilai_indikator_mit <= 16) {
            $standard_mit = 2;
            $kualifikasi_mit = 'K';
        } else if ($data->nilai_indikator_mit >= 17 && $data->nilai_indikator_mit <= 18) {
            $standard_mit = 3;
            $kualifikasi_mit = 'C';
        } else if ($data->nilai_indikator_mit == 19) {
            $standard_mit = 4;
            $kualifikasi_mit = 'B';
        } else if ($data->nilai_indikator_mit >= 20) {
            $standard_mit = 5;
            $kualifikasi_mit = 'SB';
        }

        // indikator pengembangan diri efektif
        if ($data->nilai_indikator_pde >= 1 && $data->nilai_indikator_pde <= 17) {
            $standard_pde = 1;
            $kualifikasi_pde = 'SK';
        } else if ($data->nilai_indikator_pde >= 18 && $data->nilai_indikator_pde <= 19) {
            $standard_pde = 2;
            $kualifikasi_pde = 'K';
        } else if ($data->nilai_indikator_pde >= 20 && $data->nilai_indikator_pde <= 21) {
            $standard_pde = 3;
            $kualifikasi_pde = 'C';
        } else if ($data->nilai_indikator_pde == 22) {
            $standard_pde = 4;
            $kualifikasi_pde = 'B';
        } else if ($data->nilai_indikator_pde >= 23) {
            $standard_pde = 5;
            $kualifikasi_pde = 'SB';
        }

        // indikator strategis pengembangan diri
        if ($data->nilai_indikator_spd >= 1 && $data->nilai_indikator_spd <= 29) {
            $standard_spd = 1;
            $kualifikasi_spd = 'SK';
        } else if ($data->nilai_indikator_spd >= 30 && $data->nilai_indikator_spd <= 31) {
            $standard_spd = 2;
            $kualifikasi_spd = 'K';
        } else if ($data->nilai_indikator_spd == 32) {
            $standard_spd = 3;
            $kualifikasi_spd = 'C';
        } else if ($data->nilai_indikator_spd >= 33 && $data->nilai_indikator_spd <= 34) {
            $standard_spd = 4;
            $kualifikasi_spd = 'B';
        } else if ($data->nilai_indikator_spd >= 35) {
            $standard_spd = 5;
            $kualifikasi_spd = 'SB';
        }

        // indikator evaluasi diri dan hasil kerja
        if ($data->nilai_indikator_ed >= 1 && $data->nilai_indikator_ed <= 46) {
            $standard_ed = 1;
            $kualifikasi_ed = 'SK';
        } else if ($data->nilai_indikator_ed >= 47 && $data->nilai_indikator_ed <= 48) {
            $standard_ed = 2;
            $kualifikasi_ed = 'K';
        } else if ($data->nilai_indikator_ed >= 49 && $data->nilai_indikator_ed <= 50) {
            $standard_ed = 3;
            $kualifikasi_ed = 'C';
        } else if ($data->nilai_indikator_ed >= 51 && $data->nilai_indikator_ed <= 52) {
            $standard_ed = 4;
            $kualifikasi_ed = 'B';
        } else if ($data->nilai_indikator_ed >= 53) {
            $standard_ed = 5;
            $kualifikasi_ed = 'SB';
        }

        $indikator = RefPengembanganDiri::get(['indikator_nama', 'indikator_nomor']);

        $skor = new HasilPengembanganDiri();
        $skor->event_id = Auth::guard('peserta')->user()->event_id;
        $skor->peserta_id = Auth::guard('peserta')->user()->id;
        $skor->ujian_id = $data->id;
        $nilai = [];
        foreach ($indikator as $value) {
            if ($value->indikator_nomor == 1) {
                $nilai[] = [
                    'indikator' => $value->indikator_nama,
                    'ranking' => $value->indikator_nomor,
                    'skor' => $data->nilai_indikator_mb,
                    'standard' => $standard_mb ?? '',
                    'kualifikasi' => $kualifikasi_mb ?? ''
                ];
            } else if ($value->indikator_nomor == 2) {
                $nilai[] = [
                    'indikator' => $value->indikator_nama,
                    'ranking' => $value->indikator_nomor,
                    'skor' => $data->nilai_indikator_mit,
                    'standard' => $standard_mit ?? '',
                    'kualifikasi' => $kualifikasi_mit ?? ''
                ];
            } else if ($value->indikator_nomor == 3) {
                $nilai[] = [
                    'indikator' => $value->indikator_nama,
                    'ranking' => $value->indikator_nomor,
                    'skor' => $data->nilai_indikator_pde,
                    'standard' => $standard_pde ?? '',
                    'kualifikasi' => $kualifikasi_pde ?? ''
                ];
            } else if ($value->indikator_nomor == 4) {
                $nilai[] = [
                    'indikator' => $value->indikator_nama,
                    'ranking' => $value->indikator_nomor,
                    'skor' => $data->nilai_indikator_spd,
                    'standard' => $standard_spd ?? '',
                    'kualifikasi' => $kualifikasi_spd ?? ''
                ];
            } else if ($value->indikator_nomor == 5) {
                $nilai[] = [
                    'indikator' => $value->indikator_nama,
                    'ranking' => $value->indikator_nomor,
                    'skor' => $data->nilai_indikator_ed,
                    'standard' => $standard_ed ?? '',
                    'kualifikasi' => $kualifikasi_ed ?? ''
                ];
            }
        }

        $skor->nilai = $nilai;

        $skor_total = $data->nilai_indikator_mb + $data->nilai_indikator_mit + $data->nilai_indikator_pde + $data->nilai_indikator_spd + $data->nilai_indikator_ed;
        $skor->skor_total = $skor_total;
        if ($skor_total <= 120) {
            $level_total = 1;
            $kualifikasi_total = 'Sangat Kurang';
        } else if ($skor_total >= 121 && $skor_total <= 124) {
            $level_total = 2;
            $kualifikasi_total = 'Kurang';
        } else if ($skor_total >= 125 && $skor_total <= 127) {
            $level_total = 3;
            $kualifikasi_total = 'Cukup';
        } else if ($skor_total >= 128 && $skor_total <= 131) {
            $level_total = 4;
            $kualifikasi_total = 'Baik';
        } else if ($skor_total >= 132) {
            $level_total = 5;
            $kualifikasi_total = 'Sangat Baik';
        }

        $skor->level_total = $level_total;
        $skor->kualifikasi_total = $kualifikasi_total;

        $priority = ['SB', 'B', 'C', 'K', 'SK'];

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

        // Ambil hanya nilai 'indikator' dari hasil
        $indikators = array_column($top_data, 'indikator');

        $skor->indikator_potensi_1 = $indikators[0];
        $skor->indikator_potensi_2 = $indikators[1];
        $skor->save();

        // change status ujian to true (finish)
        $data->is_finished = true;
        $data->save();

        return $this->redirect(route('peserta.tes-potensi'), navigate: true);
    }
}
