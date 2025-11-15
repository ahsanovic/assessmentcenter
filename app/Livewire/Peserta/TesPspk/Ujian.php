<?php

namespace App\Livewire\Peserta\TesPspk;

use App\Models\Pspk\HasilPspk;
use App\Models\Pspk\RefDescPspk;
use App\Models\Pspk\SoalPspk;
use App\Models\Pspk\UjianPspk;
use App\Models\RefAspekPspk;
use App\Traits\PelanggaranTrait;
use App\Traits\TimerTrait;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.peserta.app', ['title' => 'Tes PSPK'])]
class Ujian extends Component
{
    use TimerTrait, PelanggaranTrait;

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

        $data = UjianPspk::select('id', 'soal_id', 'jawaban', 'created_at')
            ->where('peserta_id', Auth::guard('peserta')->user()->id)
            ->where('event_id', Auth::guard('peserta')->user()->event_id)
            ->first();

        if (!$data) {
            session()->flash('toast', [
                'type' => 'error',
                'message' => 'Data ujian tidak ditemukan. Silakan mulai tes terlebih dahulu.'
            ]);
            return $this->redirect(route('peserta.tes-pspk.home'), navigate: true);
        }

        $this->nomor_soal = explode(',', $data->soal_id);
        $this->jawaban_user = explode(',', $data->jawaban);
        $this->soal = SoalPspk::find($this->nomor_soal[$this->id_soal - 1]);

        $metode_tes_id = Auth::guard('peserta')->user()->event->metode_tes_id;
        switch ($metode_tes_id) {
            case '5': // PSPK level 1
                $level_pspk = 1;
                break;
            case '6': // PSPK level 2
                $level_pspk = 2;
                break;
        }

        $total_soal_by_level = SoalPspk::where('level_pspk_id', $level_pspk)->count();
        $this->jml_soal = $total_soal_by_level;

        $this->id_ujian = $data->id;
        $this->timerTest('Pspk');

        for ($i = 0, $j = 0; $i < $this->jml_soal; $i++) {
            if ($this->jawaban_user[$i] == '0') {
                $j = $j + 1;
                $this->jawaban_kosong = $j;
            }
        }
    }

    public function render()
    {
        return view('livewire.peserta.tes-pspk.ujian', [
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
        $data = UjianPspk::where('peserta_id', Auth::guard('peserta')->user()->id)
            ->where('event_id', Auth::guard('peserta')->user()->event_id)
            ->where('is_finished', 'false')
            ->first();

        $soal_id = explode(',', $data->soal_id);

        // update jawaban
        $jawaban_user = explode(',', $data->jawaban);

        $jawaban_user[$index_array] = $this->jawaban_user[$index_array] ?? '0';
        $jawaban_user_str = implode(',', $jawaban_user);

        // Simpan jawaban user
        $data->jawaban = $jawaban_user_str;
        $data->save();

        // Perbarui Livewire state
        $this->jawaban_user = $jawaban_user;
        $this->jawaban_kosong = collect($this->jawaban_user)->filter(fn($j) => $j == '0')->count();

        $skor_aspek = $data->skor_aspek ?? [];
        $aspek_list = RefAspekPspk::pluck('kode_aspek')->toArray();
        foreach ($aspek_list as $a) {
            if (!isset($skor_aspek[$a])) {
                $skor_aspek[$a] = 0;
            }
        }

        // Hitung ulang total skor berdasarkan semua jawaban
        // tapi tetap update ke struktur skor_aspek lama agar tidak hilang
        $updated_skor = array_fill_keys($aspek_list, 0);

        if (auth()->guard('peserta')->user()->event->metode_tes_id == 5) { // pspk level 1
            foreach ($soal_id as $i => $sid) {
                $jawaban = $jawaban_user[$i] ?? null;
                $soal = SoalPspk::find($sid);
    
                if ($soal && $jawaban && $jawaban != '0') {
                    $aspek_kode = $soal->aspek->kode_aspek ?? 'Tidak Diketahui';
    
                    if (!isset($updated_skor[$aspek_kode])) {
                        $updated_skor[$aspek_kode] = 0;
                    }
    
                    $skor_opsi = match (strtoupper($jawaban)) {
                        'A' => $soal->poin_opsi_a ?? 0,
                        'B' => $soal->poin_opsi_b ?? 0,
                        'C' => $soal->poin_opsi_c ?? 0,
                        'D' => $soal->poin_opsi_d ?? 0,
                        'E' => $soal->poin_opsi_e ?? 0,
                        default => 0,
                    };
    
                    $updated_skor[$aspek_kode] += $skor_opsi;
                }
            }
        } else if (auth()->guard('peserta')->user()->event->metode_tes_id == 6) { // pspk level 2
            foreach ($soal_id as $i => $sid) {
                $jawaban = $jawaban_user[$i] ?? null;
                $soal = SoalPspk::find($sid);
        
                if ($soal && $jawaban && $jawaban != '0') {
                    $aspek_kode = $soal->aspek->kode_aspek ?? 'Tidak Diketahui';
                    if (!isset($updated_skor[$aspek_kode])) {
                        $updated_skor[$aspek_kode] = 0;
                    }
                    
                    $updated_skor[$aspek_kode] += ($soal->kunci_jawaban == $jawaban) ? 5 : 1;
                }
            }
        }


        // Gabungkan nilai baru ke dalam skor lama agar tidak overwrite
        foreach ($updated_skor as $key => $val) {
            $skor_aspek[$key] = $val; // update nilainya, tapi kuncinya tetap lengkap
        }

        // simpan skor per aspek
        $data->skor_aspek = $skor_aspek;
        $data->nilai_total = array_sum($updated_skor);
        $data->save();

        if ($nomor_soal < $this->jml_soal) {
            $this->redirect(route('peserta.tes-pspk.ujian', ['id' => $nomor_soal + 1]), true);
        } else if ($nomor_soal == $this->jml_soal) {
            $this->redirect(route('peserta.tes-pspk.ujian', ['id' => $nomor_soal]), true);
        }
    }

    public function navigate($id)
    {
        if ($id >= 1 && $id <= $this->jml_soal) {
            $this->id_soal = $id;
            $this->soal = SoalPspk::find($this->nomor_soal[$id - 1]);
            $this->redirect(route('peserta.tes-pspk.ujian', ['id' => $id]), true);
        }
    }

    public function finish()
    {
        try {
            $data = UjianPspk::findOrFail($this->id_ujian);

            if (auth()->guard('peserta')->user()->event->metode_tes_id == 5) { // pspk level 1
                // total nilai
                $total_nilai = [];
                foreach ($data->skor_aspek as $key => $val) {
                    if (!$val) {
                        $data->skor_aspek[$key] = 0;
                    }
                    $total_nilai[] = $this->_getLevelPerAspek($data->skor_aspek[$key]);
                }

                // jpm
                $jpm = (array_sum($total_nilai)) / (1 * 9) * 100;

                // kategori
                $kategori = $this->_getKategori($jpm);

                // deskripsi
                $deskripsi = [];
                foreach ($total_nilai as $key => $val) {
                    $kode_aspek = array_keys($data->skor_aspek)[$key];
                    $desc = RefDescPspk::where('level_pspk', 1)
                        ->where('aspek_id', RefAspekPspk::where('kode_aspek', $kode_aspek)->first()->id)
                        ->first();

                    if ($val == 0.5) {
                        $deskripsi[$kode_aspek] = $desc->deskripsi_min;
                    } else if ($val == 1) {
                        $deskripsi[$kode_aspek] = $desc->deskripsi;
                    } else if ($val == 1.5) {
                        $deskripsi[$kode_aspek] = $desc->deskripsi_plus;
                    }
                }
            } else if (auth()->guard('peserta')->user()->event->metode_tes_id == 6) { // pspk level 2
                // total nilai
                $total_nilai = [];
                foreach ($data->skor_aspek as $key => $val) {
                    if (!$val) {
                        $data->skor_aspek[$key] = 0;
                    }
                    $total_nilai[] = $this->_getLevelPerAspekLv2($data->skor_aspek[$key]);
                }

                // jpm
                $jpm = (array_sum($total_nilai)) / (2 * 9) * 100;

                // kategori
                $kategori = $this->_getKategori($jpm);

                // deskripsi
                $deskripsi = [];
                foreach ($total_nilai as $key => $val) {
                    $kode_aspek = array_keys($data->skor_aspek)[$key];
                    $desc = RefDescPspk::where('level_pspk', 2)
                        ->where('aspek_id', RefAspekPspk::where('kode_aspek', $kode_aspek)->first()->id)
                        ->first();

                    if ($val == 1) {
                        $deskripsi[$kode_aspek] = $desc->deskripsi_min;
                    } else if ($val == 1.5) {
                        $deskripsi[$kode_aspek] = $desc->deskripsi_min;
                    } else if ($val == 2) {
                        $deskripsi[$kode_aspek] = $desc->deskripsi;
                    } else if ($val == 2.5) {
                        $deskripsi[$kode_aspek] = $desc->deskripsi_plus;
                    }
                }
            }

            HasilPspk::updateOrCreate(
                [
                    'event_id' => Auth::guard('peserta')->user()->event_id,
                    'peserta_id' => Auth::guard('peserta')->user()->id,
                    'ujian_id' => $data->id,
                ],
                [
                    'nilai_total' => $data->nilai_total,
                    'nilai_capaian' => $total_nilai,
                    'jpm' => $jpm,
                    'kategori' => $kategori,
                    'deskripsi' => $deskripsi
                ]
            );

            // change status ujian to true (finish)
            $data->is_finished = true;
            $data->save();

            return $this->redirect(route('peserta.tes-pspk.hasil'));
        } catch (\Throwable $th) {
            // throw $th;
            session()->flash('toast', [
                'type' => 'error',
                'message' => 'Terjadi kesalahan'
            ]);
        }
    }

    private function _getLevelPerAspek($nilai)
    {
        if ($nilai >= 6 && $nilai <= 10) {
            $level = 0.5;
        } else if ($nilai >= 11 && $nilai <= 14) {
            $level = 1;
        } else if ($nilai >= 15 && $nilai <= 18) {
            $level = 1.5;
        }

        return $level;
    }

    private function _getLevelPerAspekLv2($nilai)
    {
        if ($nilai >= 6 && $nilai <= 11) {
            $level = 1;
        } else if ($nilai >= 12 && $nilai <= 17) {
            $level = 1.5;
        } else if ($nilai >= 18 && $nilai <= 23) {
            $level = 2;
        } else if ($nilai >= 24 && $nilai <= 30) {
            $level = 2.5;
        }

        return $level;
    }

    private function _getKategori($jpm)
    {
        if ($jpm >= 90) {
            $kategori = 'Optimal';
        } else if ($jpm < 90 && $jpm >= 78) {
            $kategori = 'Cukup Optimal';
        } else if ($jpm < 78) {
            $kategori = 'Kurang Optimal';
        }

        return $kategori;
    }
}
