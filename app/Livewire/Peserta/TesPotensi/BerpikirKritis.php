<?php

namespace App\Livewire\Peserta\TesPotensi;

use App\Models\BerpikirKritis\HasilBerpikirKritis;
use App\Models\BerpikirKritis\RefAspekBerpikirKritis;
use App\Models\BerpikirKritis\RefIndikatorBerpikirKritis;
use App\Models\BerpikirKritis\SoalBerpikirKritis;
use App\Models\BerpikirKritis\UjianBerpikirKritis;
use App\Models\Settings;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.peserta.app', ['title' => 'Tes Kecerdasan Emosi'])]
class BerpikirKritis extends Component
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

        $data = UjianBerpikirKritis::select('id', 'soal_id', 'jawaban', 'created_at')
            ->where('peserta_id', Auth::guard('peserta')->user()->id)
            ->where('event_id', Auth::guard('peserta')->user()->event_id)
            ->where('is_finished', 'false')
            ->first();

        $this->nomor_soal = explode(',', $data->soal_id);
        $this->jawaban_user = explode(',', $data->jawaban);
        $this->soal = SoalBerpikirKritis::find($this->nomor_soal[$this->id_soal - 1]);
        $this->jml_soal = SoalBerpikirKritis::count();
        $this->id_ujian = $data->id;
        $this->timer = $data->created_at->timestamp;

        $durasi_tes = Settings::where('alat_tes_id', 4)->first(['waktu']);
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
            [2, 3, 'nilai_indikator_2'],
            [4, 5, 'nilai_indikator_3'],
            [6, 7, 'nilai_indikator_4'],
            [8, 9, 'nilai_indikator_5'],
            [10, 11, 'nilai_indikator_6'],
            [12, 12, 'nilai_indikator_7'],
            [13, 13, 'nilai_indikator_8'],
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
        $data = UjianBerpikirKritis::findOrFail($this->id_ujian);
        $indikator = RefIndikatorBerpikirKritis::get(['indikator_nama', 'indikator_nomor']);

        $skor = new HasilBerpikirKritis();
        $skor->event_id = Auth::guard('peserta')->user()->event_id;
        $skor->peserta_id = Auth::guard('peserta')->user()->id;
        $skor->ujian_id = $data->id;
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

        $skor->nilai = $nilai;

        $skor_total = $data->nilai_indikator_1 + $data->nilai_indikator_2 + $data->nilai_indikator_3 + $data->nilai_indikator_4 + $data->nilai_indikator_5 + $data->nilai_indikator_6 + $data->nilai_indikator_7 + $data->nilai_indikator_8;
        
        if (($skor_total == 0) || ($skor_total >= 13 && $skor_total <= 28)) {
            $level_total = '1';
            $kualifikasi_total = 'Sangat Kurang';
            $kategori_total = 'Rendah';
        } else if ($skor_total >= 39 && $skor_total <= 46) {
            $level_total = '2';
            $kualifikasi_total = 'Kurang';
            $kategori_total = 'Rendah';
        } else if ($skor_total >= 47 && $skor_total <= 48) {
            $level_total = '3-';
            $kualifikasi_total = 'Cukup';
            $kategori_total = 'Sedang';
        } else if ($skor_total >= 49 && $skor_total <= 50) {
            $level_total = '3';
            $kualifikasi_total = 'Cukup';
            $kategori_total = 'Sedang';
        } else if ($skor_total >= 51 && $skor_total <= 52) {
            $level_total = '3+';
            $kualifikasi_total = 'Cukup';
            $kategori_total = 'Sedang';
        } else if ($skor_total >= 53 && $skor_total <= 60) {
            $level_total = '4';
            $kualifikasi_total = 'Baik';
            $kategori_total = 'Tinggi';
        } else if ($skor_total >= 61 && $skor_total <= 65) {
            $level_total = '5';
            $kualifikasi_total = 'Sangat Baik';
            $kategori_total = 'Tinggi';
        }

        $skor->level_total = $level_total;
        $skor->kualifikasi_total = $kualifikasi_total;
        $skor->kategori_total = $kategori_total;
        $skor->skor_total = $skor_total;

        if ($level_total == '3-' || $level_total == '3' || $level_total == '3+') {
            $level_norma_umum = '3';
        } else {
            $level_norma_umum = $level_total;
        }

        $aspek = RefAspekBerpikirKritis::where('aspek_nomor', $level_norma_umum)->first();
        $indikator_nomor = explode(',', $aspek->indikator_nomor);
        $deskripsi_list = [];
        foreach ($indikator_nomor as $indikator) {
            $kualifikasi_deskripsi = RefIndikatorBerpikirKritis::where('indikator_nomor', $indikator)->value('kualifikasi_deskripsi');
            $deskripsi_data = collect($kualifikasi_deskripsi);

            $nilai_indikator = $data->{'nilai_indikator_' . $indikator} ?? null;
            if (is_null($nilai_indikator)) {
                continue;
            }

            if ($indikator == 1) {
                if ($nilai_indikator >= 1 && $nilai_indikator <= 3) {
                    $kategori = 'Rendah';
                } else if ($nilai_indikator == 4) {
                    $kategori = 'Sedang';
                } else if ($nilai_indikator == 5) {
                    $kategori = 'Tinggi';
                }
            } else if ($indikator == 2) {
                if ($nilai_indikator >= 1 && $nilai_indikator <= 5) {
                    $kategori = 'Rendah';
                } else if ($nilai_indikator >= 6 && $nilai_indikator <= 8) {
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
                if ($nilai_indikator >= 1 && $nilai_indikator <= 4) {
                    $kategori = 'Rendah';
                } else if ($nilai_indikator >= 5 && $nilai_indikator <= 8) {
                    $kategori = 'Sedang';
                } else if ($nilai_indikator >= 9 && $nilai_indikator <= 10) {
                    $kategori = 'Tinggi';
                }
            } else if ($indikator == 5) {
                if ($nilai_indikator >= 1 && $nilai_indikator <= 4) {
                    $kategori = 'Rendah';
                } else if ($nilai_indikator >= 5 && $nilai_indikator <= 8) {
                    $kategori = 'Sedang';
                } else if ($nilai_indikator >= 9 && $nilai_indikator <= 10) {
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
                if ($nilai_indikator == 1) {
                    $kategori = 'Rendah';
                } else if ($nilai_indikator >= 2 && $nilai_indikator <= 3) {
                    $kategori = 'Sedang';
                } else if ($nilai_indikator >= 4 && $nilai_indikator <= 5) {
                    $kategori = 'Tinggi';
                }
            } else if ($indikator == 8) {
                if ($nilai_indikator == 1) {
                    $kategori = 'Rendah';
                } else if ($nilai_indikator >= 2 && $nilai_indikator <= 4) {
                    $kategori = 'Sedang';
                } else if ($nilai_indikator >= 5) {
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

        $skor->uraian_potensi_1 = $deskripsi_list[0] ?? null;
        $skor->uraian_potensi_2 = $deskripsi_list[1] ?? null;
        $skor->save();

        // change status ujian to true (finish)
        $data->is_finished = true;
        $data->save();

        return $this->redirect(route('peserta.tes-potensi'), navigate: true);
    }
}
