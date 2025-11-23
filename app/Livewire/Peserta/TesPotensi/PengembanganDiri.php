<?php

namespace App\Livewire\Peserta\TesPotensi;

use App\Models\PengembanganDiri\HasilPengembanganDiri;
use App\Models\PengembanganDiri\RefPengembanganDiri;
use App\Models\PengembanganDiri\SoalPengembanganDiri;
use App\Models\PengembanganDiri\UjianPengembanganDiri;
use App\Models\Settings;
use App\Traits\PelanggaranTrait;
use App\Traits\StartTestTrait;
use App\Traits\TimerTrait;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

#[Layout('components.layouts.peserta.app', ['title' => 'Tes Belajar Cepat dan Pengembangan Diri'])]
class PengembanganDiri extends Component
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
    public $flagged = [];

    #[On('updateFlagsFromBrowser')]
    public function updateFlagsFromBrowser($flags)
    {
        $this->flagged = $flags;
    }

    public function toggleFlag($nomor)
    {
        // Livewire hanya kirim nomor soal, JS akan update localStorage
        $this->dispatch('toggle-flag-in-browser', nomor: $nomor);

        // setelah JS update → JS akan kirim kembali flags terbaru
        $this->dispatch('request-flags-sync');
    }

    public function mount($id)
    {
        $this->dispatch('load-flags-from-browser');
        $this->id_soal = $id;
        // $count_peserta = UjianPengembanganDiri::where('peserta_id', Auth::guard('peserta')->user()->id)
        //     ->where('event_id', Auth::guard('peserta')->user()->event_id)
        //     ->where('is_finished', 'false')
        //     ->count();

        // if ($this->id_soal < 1 || $this->id_soal > $this->jml_soal || $count_peserta < 1) {
        //     return redirect('tes-potensi/pengembangan-diri/1');
        // }

        $data = UjianPengembanganDiri::select('id', 'soal_id', 'jawaban', 'created_at')
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
        $this->soal = SoalPengembanganDiri::find($this->nomor_soal[$this->id_soal - 1]);
        $this->jml_soal = SoalPengembanganDiri::count();
        $this->id_ujian = $data->id;

        $first_sequence = Settings::with('alatTes')->where('urutan', 1)->first();
        $this->timerTest($first_sequence->alatTes->alat_tes);

        $current_sequence = Settings::with('alatTes')->where('alat_tes_id', 5)->first();
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
            [1, 13, 'nilai_indikator_mb'],
            [14, 24, 'nilai_indikator_mit'],
            [25, 32, 'nilai_indikator_pde'],
            [33, 41, 'nilai_indikator_spd'],
            [42, 52, 'nilai_indikator_ed'],
        ];

        foreach ($indikator_map as [$start, $end, $indikator]) {
            if ($nomor_soal >= $start && $nomor_soal <= $end) {
                $total_skor = 0;

                for ($i = $start; $i <= $end; $i++) {
                    $idx = $i - 1;
                    $jawaban = $jawaban_user[$idx] ?? null;

                    // Ambil poin dari soal terkait
                    if (isset($soal_id[$idx])) {
                        $poin_soal = SoalPengembanganDiri::find($soal_id[$idx]);
                        if (!$poin_soal) continue;

                        switch ($jawaban) {
                            case 'A':
                                $total_skor += $poin_soal->poin_opsi_a;
                                break;
                            case 'B':
                                $total_skor += $poin_soal->poin_opsi_b;
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

        // Hapus flag soal jika ada
        if (isset($this->flagged[$nomor_soal])) {
            unset($this->flagged[$nomor_soal]);
    
            // Hapus juga dari localStorage (via JS)
            $this->dispatch('toggle-flag-in-browser', nomor: $nomor_soal);
        }

        if ($nomor_soal < $this->jml_soal) {
            $this->redirect(route('peserta.tes-potensi.pengembangan-diri', ['id' => $nomor_soal + 1]), true);
        } else if ($nomor_soal == $this->jml_soal) {
            $this->redirect(route('peserta.tes-potensi.pengembangan-diri', ['id' => $nomor_soal]), true);
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
        try {
            $data = UjianPengembanganDiri::findOrFail($this->id_ujian);

            // indikator motivasi belajar
            if ($data->nilai_indikator_mb >= 0 && $data->nilai_indikator_mb <= 4) {
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
            } else if ($data->nilai_indikator_mb >= 11 && $data->nilai_indikator_mb <= 13) {
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
            } else if ($data->nilai_indikator_mit >= 20 && $data->nilai_indikator_mit <= 22) {
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
            } else if ($data->nilai_indikator_pde >= 23 && $data->nilai_indikator_pde <= 24) {
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
            } else if ($data->nilai_indikator_spd >= 35 && $data->nilai_indikator_spd <= 36) {
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
            } else if ($data->nilai_indikator_ed >= 53 && $data->nilai_indikator_ed <= 55) {
                $standard_ed = 5;
                $kualifikasi_ed = 'SB';
            }

            $indikator_list = RefPengembanganDiri::get(['indikator_nama', 'indikator_nomor']);

            $nilai = [];
            foreach ($indikator_list as $value) {
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

            $skor_total = $data->nilai_indikator_mb + $data->nilai_indikator_mit + $data->nilai_indikator_pde + $data->nilai_indikator_spd + $data->nilai_indikator_ed;
            if ($skor_total >= 1 && $skor_total <= 119) {
                $level_total = 1;
                $kualifikasi_total = 'Sangat Kurang';
            } else if ($skor_total >= 120 && $skor_total <= 124) {
                $level_total = 2;
                $kualifikasi_total = 'Kurang';
            } else if ($skor_total >= 125 && $skor_total <= 127) {
                $level_total = 3;
                $kualifikasi_total = 'Cukup';
            } else if ($skor_total >= 128 && $skor_total <= 131) {
                $level_total = 4;
                $kualifikasi_total = 'Baik';
            } else if ($skor_total >= 132 && $skor_total <= 150) {
                $level_total = 5;
                $kualifikasi_total = 'Sangat Baik';
            }

            $skor = HasilPengembanganDiri::updateOrCreate(
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

            $priority = ['SB', 'B', 'C', 'K', 'SK'];
            usort($nilai, function ($a, $b) use ($priority) {
                $posA = array_search($a['kualifikasi'], $priority);
                $posB = array_search($b['kualifikasi'], $priority);
                return $posA - $posB;
            });

            // Ambil 5 data setelah diurutkan, kemudian urutkan berdasar ranking (nomor indikator)
            $top_data = array_slice($nilai, 0, 5);
            usort($top_data, function ($a, $b) {
                return $a['ranking'] - $b['ranking'];
            });

            // Ambil nilai indikator nama, indikator nomor, dan kualifikasi dari hasil
            $indikator_nomor = array_column($top_data, 'ranking');
            $kualifikasi_array = array_column($top_data, 'kualifikasi');

            $data_to_save = [];

            foreach ($indikator_nomor as $index => $nomor) {
                $data_kualifikasi = RefPengembanganDiri::whereIndikatorNomor($nomor)->first();

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

            $data->is_finished = true;
            $data->save();

            // Bersihkan localStorage via JS
            $this->dispatch('clear-flags-browser');

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
            case 'C':
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
