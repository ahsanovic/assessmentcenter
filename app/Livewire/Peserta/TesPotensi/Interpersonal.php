<?php

namespace App\Livewire\Peserta\TesPotensi;

use App\Models\Interpersonal\HasilInterpersonal;
use App\Models\Interpersonal\RefInterpersonal;
use App\Models\Interpersonal\SoalInterpersonal;
use App\Models\Interpersonal\UjianInterpersonal;
use App\Models\Settings;
use App\Traits\PelanggaranTrait;
use App\Traits\StartTestTrait;
use App\Traits\TimerTrait;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

#[Layout('components.layouts.peserta.app', ['title' => 'Tes Interpersonal'])]
class Interpersonal extends Component
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

        $data = UjianInterpersonal::select('id', 'soal_id', 'jawaban', 'created_at')
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
        $this->soal = SoalInterpersonal::find($this->nomor_soal[$this->id_soal - 1]);
        $this->jml_soal = SoalInterpersonal::count();
        $this->id_ujian = $data->id;

        $first_sequence = Settings::with('alatTes')->where('urutan', 1)->first();
        $this->timerTest($first_sequence->alatTes->alat_tes);

        $current_sequence = Settings::with('alatTes')->where('alat_tes_id', 3)->first();
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
        return view('livewire..peserta.tes-potensi.interpersonal.ujian', [
            'nomor_sekarang' => $this->id_soal,
            'jawaban' => $this->jawaban_user,
            'jawaban_kosong' => $this->jawaban_kosong,
            'jml_soal' => $this->jml_soal,
            'soal' => $this->soal,
        ]);
    }

    public function saveAndNext($nomor_soal, $jawaban = null)
    {
        $index_array = $nomor_soal - 1;
        $data = UjianInterpersonal::where('peserta_id', Auth::guard('peserta')->user()->id)
            ->where('event_id', Auth::guard('peserta')->user()->event_id)
            ->where('is_finished', 'false')
            ->first();

        if (!$data) {
            $this->skipRender();
            return null;
        }

        $soal_id = explode(',', $data->soal_id);

        // update jawaban
        $jawaban_user = explode(',', $data->jawaban);

        $jawaban_baru = $jawaban ?? ($this->jawaban_user[$index_array] ?? '0');
        $jawaban_baru = ($jawaban_baru === '' || $jawaban_baru === null) ? '0' : $jawaban_baru;
        $jawaban_user[$index_array] = $jawaban_baru;

        $data->jawaban = implode(',', $jawaban_user);
        $data->save();

        // Perbarui Livewire state
        $this->jawaban_user = $jawaban_user;
        $this->jawaban_kosong = collect($this->jawaban_user)->filter(fn($j) => $j == '0')->count();

        $indikator_map = [
            [1, 7, 'nilai_indikator_ke'],
            [8, 17, 'nilai_indikator_bt'],
            [18, 27, 'nilai_indikator_as'],
            [28, 36, 'nilai_indikator_de'],
            [37, 45, 'nilai_indikator_smk'],
        ];

        foreach ($indikator_map as [$start, $end, $indikator]) {
            if ($nomor_soal >= $start && $nomor_soal <= $end) {
                $total_skor = 0;

                for ($i = $start; $i <= $end; $i++) {
                    $idx = $i - 1;
                    $jawaban = $jawaban_user[$idx] ?? null;

                    // Ambil poin dari soal terkait
                    if (isset($soal_id[$idx])) {
                        $poin_soal = SoalInterpersonal::find($soal_id[$idx]);
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
        $target = $nomor_soal < $this->jml_soal ? $nomor_soal + 1 : $nomor_soal;

        $this->skipRender();

        $payload = $this->soalPayload($target);

        if ((int) $nomor_soal === (int) $this->jml_soal) {
            $payload['prompt_soal_terakhir'] = true;
            $payload['semua_terjawab'] = (int) $this->jawaban_kosong === 0;
            $payload['jawaban_kosong'] = (int) $this->jawaban_kosong;
            $payload['soal_belum_dijawab'] = $this->indeksPertamaBelumDijawab();
        }

        return $payload;
    }

    private function indeksPertamaBelumDijawab(): int
    {
        foreach ($this->jawaban_user as $idx => $jawaban) {
            if ((string) $jawaban === '0' || $jawaban === '' || $jawaban === null) {
                return $idx + 1;
            }
        }

        return 1;
    }

    public function navigate($id)
    {
        if ($id < 1 || $id > $this->jml_soal) {
            $this->skipRender();
            return null;
        }

        $this->skipRender();

        return $this->soalPayload((int) $id);
    }

    private function soalPayload(int $id): array
    {
        $this->id_soal = $id;
        $this->soal = SoalInterpersonal::find($this->nomor_soal[$id - 1]);

        $jawaban = (string) ($this->jawaban_user[$id - 1] ?? '');
        $selected = ($jawaban !== '' && $jawaban !== '0') ? $jawaban : '';

        return [
            'nomor' => $id,
            'teks' => $this->soal?->soal,
            'opsi_a' => $this->soal?->opsi_a,
            'opsi_b' => $this->soal?->opsi_b,
            'opsi_c' => $this->soal?->opsi_c,
            'opsi_d' => $this->soal?->opsi_d,
            'opsi_e' => $this->soal?->opsi_e,
            'selected' => $selected,
            'jawaban_user' => array_values($this->jawaban_user),
            'jawaban_kosong' => (int) $this->jawaban_kosong,
            'url' => route('peserta.tes-potensi.interpersonal', ['id' => $id]),
        ];
    }

    public function finish()
    {
        try {
            $data = UjianInterpersonal::findOrFail($this->id_ujian);

            // indikator komunikasi efektif
            if ($data->nilai_indikator_ke >= 0 && $data->nilai_indikator_ke <= 3) {
                $standard_ke = '1';
                $kualifikasi_ke = 'SK';
            } else if ($data->nilai_indikator_ke >= 4 && $data->nilai_indikator_ke <= 7) {
                $standard_ke = '2';
                $kualifikasi_ke = 'K';
            } else if ($data->nilai_indikator_ke >= 8 && $data->nilai_indikator_ke <= 11) {
                $standard_ke = '3';
                $kualifikasi_ke = 'C';
            // } else if ($data->nilai_indikator_ke == 8) {
            //     $standard_ke = '3-';
            //     $kualifikasi_ke = 'C-';
            // } else if ($data->nilai_indikator_ke >= 9 && $data->nilai_indikator_ke <= 10) {
            //     $standard_ke = '3';
            //     $kualifikasi_ke = 'C';
            // } else if ($data->nilai_indikator_ke == 11) {
            //     $standard_ke = '3+';
            //     $kualifikasi_ke = 'C+';
            } else if ($data->nilai_indikator_ke >= 12 && $data->nilai_indikator_ke <= 13) {
                $standard_ke = '4';
                $kualifikasi_ke = 'B';
            } else if ($data->nilai_indikator_ke == 14) {
                $standard_ke = '5';
                $kualifikasi_ke = 'SB';
            }

            // indikator bersikap terbuka
            if ($data->nilai_indikator_bt >= 0 && $data->nilai_indikator_bt <= 8) {
                $standard_bt = '1';
                $kualifikasi_bt = 'SK';
            } else if ($data->nilai_indikator_bt >= 9 && $data->nilai_indikator_bt <= 13) {
                $standard_bt = '2';
                $kualifikasi_bt = 'K';
            } else if ($data->nilai_indikator_bt >= 14 && $data->nilai_indikator_bt <= 16) {
                $standard_bt = '3';
                $kualifikasi_bt = 'C';
            // } else if ($data->nilai_indikator_bt == 14) {
            //     $standard_bt = '3-';
            //     $kualifikasi_bt = 'C-';
            // } else if ($data->nilai_indikator_bt == 15) {
            //     $standard_bt = '3';
            //     $kualifikasi_bt = 'C';
            // } else if ($data->nilai_indikator_bt == 16) {
            //     $standard_bt = '3+';
            //     $kualifikasi_bt = 'C+';
            } else if ($data->nilai_indikator_bt >= 17 && $data->nilai_indikator_bt <= 19) {
                $standard_bt = '4';
                $kualifikasi_bt = 'B';
            } else if ($data->nilai_indikator_bt == 20) {
                $standard_bt = '5';
                $kualifikasi_bt = 'SB';
            }

            // indikator asertif
            if ($data->nilai_indikator_as >= 0 && $data->nilai_indikator_as <= 12) {
                $standard_as = '1';
                $kualifikasi_as = 'SK';
            } else if ($data->nilai_indikator_as >= 13 && $data->nilai_indikator_as <= 15) {
                $standard_as = '2';
                $kualifikasi_as = 'K';
            } else if ($data->nilai_indikator_as >= 16 && $data->nilai_indikator_as <= 18) {
                $standard_as = '3';
                $kualifikasi_as = 'C';
            // } else if ($data->nilai_indikator_as == 16) {
            //     $standard_as = '3-';
            //     $kualifikasi_as = 'C-';
            // } else if ($data->nilai_indikator_as == 17) {
            //     $standard_as = '3';
            //     $kualifikasi_as = 'C';
            // } else if ($data->nilai_indikator_as == 18) {
            //     $standard_as = '3+';
            //     $kualifikasi_as = 'C+';
            } else if ($data->nilai_indikator_as == 19) {
                $standard_as = '4';
                $kualifikasi_as = 'B';
            } else if ($data->nilai_indikator_as == 20) {
                $standard_as = '5';
                $kualifikasi_as = 'SB';
            }

            // indikator dukungan emosional
            if ($data->nilai_indikator_de >= 0 && $data->nilai_indikator_de <= 11) {
                $standard_de = '1';
                $kualifikasi_de = 'SK';
            } else if ($data->nilai_indikator_de >= 12 && $data->nilai_indikator_de <= 13) {
                $standard_de = '2';
                $kualifikasi_de = 'K';
            } else if ($data->nilai_indikator_de >= 14 && $data->nilai_indikator_de <= 16) {
                $standard_de = '3';
                $kualifikasi_de = 'C';
            // } else if ($data->nilai_indikator_de == 14) {
            //     $standard_de = '3-';
            //     $kualifikasi_de = 'C-';
            // } else if ($data->nilai_indikator_de == 15) {
            //     $standard_de = '3';
            //     $kualifikasi_de = 'C';
            // } else if ($data->nilai_indikator_de == 16) {
            //     $standard_de = '3+';
            //     $kualifikasi_de = 'C+';
            } else if ($data->nilai_indikator_de == 17) {
                $standard_de = '4';
                $kualifikasi_de = 'B';
            } else if ($data->nilai_indikator_de == 18) {
                $standard_de = '5';
                $kualifikasi_de = 'SB';
            }

            // indikator sikap menghadapi konflik
            if ($data->nilai_indikator_smk >= 0 && $data->nilai_indikator_smk <= 11) {
                $standard_smk = '1';
                $kualifikasi_smk = 'SK';
            } else if ($data->nilai_indikator_smk >= 12 && $data->nilai_indikator_smk <= 13) {
                $standard_smk = '2';
                $kualifikasi_smk = 'K';
            } else if ($data->nilai_indikator_smk >= 14 && $data->nilai_indikator_smk <= 16) {
                $standard_smk = '3';
                $kualifikasi_smk = 'C';
            // } else if ($data->nilai_indikator_smk == 14) {
            //     $standard_smk = '3-';
            //     $kualifikasi_smk = 'C-';
            // } else if ($data->nilai_indikator_smk == 15) {
            //     $standard_smk = '3';
            //     $kualifikasi_smk = 'C';
            // } else if ($data->nilai_indikator_smk == 16) {
            //     $standard_smk = '3+';
            //     $kualifikasi_smk = 'C+';
            } else if ($data->nilai_indikator_smk == 17) {
                $standard_smk = '4';
                $kualifikasi_smk = 'B';
            } else if ($data->nilai_indikator_smk == 18) {
                $standard_smk = '5';
                $kualifikasi_smk = 'SB';
            }

            $indikator = RefInterpersonal::get(['indikator_nama', 'indikator_nomor']);
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

            $skor_total = $data->nilai_indikator_ke + $data->nilai_indikator_bt + $data->nilai_indikator_as + $data->nilai_indikator_de + $data->nilai_indikator_smk;
            // $skor->skor_total = $skor_total;
            if ($skor_total >= 0 && $skor_total <= 55) {
                $level_total = '1';
                $kualifikasi_total = 'Sangat Kurang';
            } else if ($skor_total >= 56 && $skor_total <= 67) {
                $level_total = '2';
                $kualifikasi_total = 'Kurang';
            } else if ($skor_total >= 68 && $skor_total <= 78) {
                $level_total = '3';
                $kualifikasi_total = 'Cukup';
            // } else if ($skor_total == 68) {
            //     $level_total = '3-';
            //     $kualifikasi_total = 'Cukup';
            // } else if ($skor_total >= 69 && $skor_total <= 75) {
            //     $level_total = '3';
            //     $kualifikasi_total = 'Cukup';
            // } else if ($skor_total >= 76 && $skor_total <= 78) {
            //     $level_total = '3+';
            //     $kualifikasi_total = 'Cukup';
            } else if ($skor_total >= 79 && $skor_total <= 89) {
                $level_total = '4';
                $kualifikasi_total = 'Baik';
            } else if ($skor_total == 90) {
                $level_total = '5';
                $kualifikasi_total = 'Sangat Baik';
            }

            $skor = HasilInterpersonal::updateOrCreate(
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

            // Ambil 5 data setelah diurutkan, kemudian urutkan berdasar ranking (nomor indikator)
            $top_data = array_slice($nilai, 0, 5);
            usort($nilai, function ($a, $b) use ($priority) {
                $posA = array_search($a['kualifikasi'], $priority);
                $posB = array_search($b['kualifikasi'], $priority);
                return $posA - $posB;
            });

            // Ambil nilai indikator nama, indikator nomor, dan kualifikasi dari hasil
            $indikator_nomor = array_column($top_data, 'ranking');
            $kualifikasi_array = array_column($top_data, 'kualifikasi');

            $data_to_save = [];

            foreach ($indikator_nomor as $index => $nomor) {
                $data_kualifikasi = RefInterpersonal::whereIndikatorNomor($nomor)->first();

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

            // Bersihkan localStorage via JS
            $this->dispatch('clear-flags-browser');

            $current_sequence_test = Settings::where('urutan', $data->urutan_tes)->first(['urutan']);
            if ($current_sequence_test && $current_sequence_test->urutan !== 7) {
                $next_test = Settings::with('alatTes')->where('urutan', $current_sequence_test->urutan + 1)->first();
                // session(['current_test' => $next_test->alat_tes_id]);
                $this->startTest($next_test->alatTes->alat_tes, $next_test->urutan);
            } else if ($current_sequence_test && $current_sequence_test->urutan == 7) {
                return $this->redirect(route('peserta.kuesioner'), navigate: true);
            } else {
                return $this->redirect(route('peserta.tes-potensi.home'), navigate: true);
            }
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
