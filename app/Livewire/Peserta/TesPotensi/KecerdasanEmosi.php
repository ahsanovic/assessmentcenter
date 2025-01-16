<?php

namespace App\Livewire\Peserta\TesPotensi;

use App\Models\KecerdasanEmosi\HasilKecerdasanEmosi;
use App\Models\KecerdasanEmosi\RefKecerdasanEmosi;
use App\Models\KecerdasanEmosi\SoalKecerdasanEmosi;
use App\Models\KecerdasanEmosi\UjianKecerdasanEmosi;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.peserta.app', ['title' => 'Tes Kecerdasan Emosi'])]
class KecerdasanEmosi extends Component
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

        $data = UjianKecerdasanEmosi::select('id', 'soal_id', 'jawaban')
            ->where('peserta_id', Auth::guard('peserta')->user()->id)
            ->where('event_id', Auth::guard('peserta')->user()->event_id)
            ->where('is_finished', 'false')
            ->first();

        $this->nomor_soal = explode(',', $data->soal_id);
        $this->jawaban_user = explode(',', $data->jawaban);
        $this->soal = SoalKecerdasanEmosi::find($this->nomor_soal[$this->id_soal - 1]);
        $this->jml_soal = SoalKecerdasanEmosi::count();
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

        // update jawaban
        $jawaban_user = explode(',', $data->jawaban);
        $jawaban_user[$index_array] = $this->jawaban_user[$index_array] ?? '0';
        $jawaban_user = implode(',', $jawaban_user);

        UjianKecerdasanEmosi::where('peserta_id', Auth::guard('peserta')->user()->id)
            ->where('event_id', Auth::guard('peserta')->user()->event_id)
            ->where('is_finished', 'false')
            ->update(['jawaban' => $jawaban_user]);

        $poin = SoalKecerdasanEmosi::find($soal_id[$index_array]);
        $poin_a = $poin->poin_opsi_a;
        $poin_b = $poin->poin_opsi_b;
        $poin_c = $poin->poin_opsi_c;

        $indikator_map = [
            [1, 5, 'nilai_indikator_kd'],
            [6, 11, 'nilai_indikator_mpd'],
            [12, 17, 'nilai_indikator_ke'],
            [18, 23, 'nilai_indikator_ks'],
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
            $this->redirect(route('peserta.tes-potensi.kecerdasan-emosi', ['id' => $nomor_soal + 1]), true);
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
        $data = UjianKecerdasanEmosi::findOrFail($this->id_ujian);

        // indikator kesadaran diri
        if ($data->nilai_indikator_kd >= 1 && $data->nilai_indikator_kd <= 3) {
            $standard_kd = '1';
            $kualifikasi_kd = 'SK';
        } else if ($data->nilai_indikator_kd == 4) {
            $standard_kd = '2';
            $kualifikasi_kd = 'K';
        } else if ($data->nilai_indikator_kd == 5) {
            $standard_kd = '3-';
            $kualifikasi_kd = 'C-';
        } else if ($data->nilai_indikator_kd == 6) {
            $standard_kd = '3';
            $kualifikasi_kd = 'C';
        } else if ($data->nilai_indikator_kd == 7) {
            $standard_kd = '3+';
            $kualifikasi_kd = 'C+';
        } else if ($data->nilai_indikator_kd >= 8 && $data->nilai_indikator_kd <= 9) {
            $standard_kd = '4';
            $kualifikasi_kd = 'B';
        } else if ($data->nilai_indikator_kd >= 10) {
            $standard_kd = '5';
            $kualifikasi_kd = 'SB';
        }

        // indikator motivasi dan pengaturan diri
        if ($data->nilai_indikator_mpd >= 1 && $data->nilai_indikator_mpd <= 5) {
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
        } else if ($data->nilai_indikator_mpd >= 11 && $data->nilai_indikator_mpd <= 12) {
            $standard_mpd = '4';
            $kualifikasi_mpd = 'B';
        } else if ($data->nilai_indikator_mpd >= 13) {
            $standard_mpd = '5';
            $kualifikasi_mpd = 'SB';
        }

        // indikator kesadaran emosional
        if ($data->nilai_indikator_ke >= 1 && $data->nilai_indikator_ke <= 3) {
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
        } else if ($data->nilai_indikator_ke >= 10) {
            $standard_ke = '5';
            $kualifikasi_ke = 'SB';
        }

        // indikator ketrampilan sosial
        if ($data->nilai_indikator_ks >= 1 && $data->nilai_indikator_ks <= 3) {
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
        } else if ($data->nilai_indikator_ks >= 11) {
            $standard_ks = '5';
            $kualifikasi_ks = 'SB';
        }

        $indikator = RefKecerdasanEmosi::get(['indikator_nama', 'indikator_nomor']);

        $skor = new HasilKecerdasanEmosi();
        $skor->event_id = Auth::guard('peserta')->user()->event_id;
        $skor->peserta_id = Auth::guard('peserta')->user()->id;
        $skor->ujian_id = $data->id;
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

        $skor->nilai = $nilai;

        $skor_total = $data->nilai_indikator_kd + $data->nilai_indikator_mpd + $data->nilai_indikator_ke + $data->nilai_indikator_ks;
        $skor->skor_total = $skor_total;
        if ($skor_total >= 1 && $skor_total <= 23) {
            $level_total = '1';
            $kualifikasi_total = 'Sangat Kurang';
        } else if ($skor_total >= 24 && $skor_total <= 25) {
            $level_total = '2';
            $kualifikasi_total = 'Kurang';
        } else if ($skor_total >= 26 && $skor_total <= 27) {
            $level_total = '3-';
            $kualifikasi_total = 'Cukup-';
        } else if ($skor_total >= 28 && $skor_total <= 29) {
            $level_total = '3';
            $kualifikasi_total = 'Cukup';
        } else if ($skor_total >= 30 && $skor_total <= 31) {
            $level_total = '3+';
            $kualifikasi_total = 'Cukup+';
        } else if ($skor_total >= 32 && $skor_total <= 35) {
            $level_total = '4';
            $kualifikasi_total = 'Baik';
        } else if ($skor_total >= 36) {
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
