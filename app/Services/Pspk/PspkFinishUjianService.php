<?php

namespace App\Services\Pspk;

use App\Models\Pspk\HasilPspk;
use App\Models\Pspk\RefDescPspk;
use App\Models\Pspk\RefSaranPengembangan;
use App\Models\Pspk\SoalPspk;
use App\Models\Pspk\UjianPspk;
use App\Models\RefAspekPspk;
use Illuminate\Support\Facades\DB;

class PspkFinishUjianService
{
    public function isFinished(UjianPspk $ujian): bool
    {
        return $ujian->is_finished === true || $ujian->is_finished === 'true';
    }

    public function finish(UjianPspk $ujian, int $metodeTesId): void
    {
        if ($this->isFinished($ujian)) {
            throw new \RuntimeException('Ujian sudah selesai.');
        }

        DB::transaction(function () use ($ujian, $metodeTesId) {
            $ujian = UjianPspk::lockForUpdate()->findOrFail($ujian->id);

            if ($this->isFinished($ujian)) {
                return;
            }

            $this->recalculateScores($ujian, $metodeTesId);
            $this->saveHasil($ujian, $metodeTesId);

            $ujian->is_finished = true;
            $ujian->save();
        });
    }

    public function recalculateScores(UjianPspk $ujian, int $metodeTesId): void
    {
        $soalIds = explode(',', (string) $ujian->soal_id);
        $jawabanUser = explode(',', (string) $ujian->jawaban);

        $soalMap = SoalPspk::with('aspek')
            ->whereIn('id', $soalIds)
            ->get()
            ->keyBy('id');

        $skorAspek = $ujian->skor_aspek ?? [];
        $aspekList = RefAspekPspk::pluck('kode_aspek')->toArray();

        foreach ($aspekList as $aspek) {
            if (! isset($skorAspek[$aspek])) {
                $skorAspek[$aspek] = 0;
            }
        }

        $updatedSkor = array_fill_keys($aspekList, 0);

        if ($metodeTesId === 5) {
            foreach ($soalIds as $i => $sid) {
                $jawaban = $jawabanUser[$i] ?? null;
                $soal = $soalMap->get((int) $sid);

                if ($soal && $jawaban && $jawaban != '0') {
                    $aspekKode = $soal->aspek->kode_aspek ?? 'Tidak Diketahui';

                    if (! isset($updatedSkor[$aspekKode])) {
                        $updatedSkor[$aspekKode] = 0;
                    }

                    $skorOpsi = match (strtoupper($jawaban)) {
                        'A' => $soal->poin_opsi_a ?? 0,
                        'B' => $soal->poin_opsi_b ?? 0,
                        'C' => $soal->poin_opsi_c ?? 0,
                        'D' => $soal->poin_opsi_d ?? 0,
                        'E' => $soal->poin_opsi_e ?? 0,
                        default => 0,
                    };

                    $updatedSkor[$aspekKode] += $skorOpsi;
                }
            }
        } elseif ($metodeTesId === 6) {
            foreach ($soalIds as $i => $sid) {
                $jawaban = $jawabanUser[$i] ?? null;
                $soal = $soalMap->get((int) $sid);

                if ($soal && $jawaban && $jawaban != '0') {
                    $aspekKode = $soal->aspek->kode_aspek ?? 'Tidak Diketahui';

                    if (! isset($updatedSkor[$aspekKode])) {
                        $updatedSkor[$aspekKode] = 0;
                    }

                    $updatedSkor[$aspekKode] += ($soal->kunci_jawaban == $jawaban) ? 5 : 1;
                }
            }
        } elseif (in_array($metodeTesId, [7, 8], true)) {
            foreach ($soalIds as $i => $sid) {
                $jawaban = $jawabanUser[$i] ?? null;
                $soal = $soalMap->get((int) $sid);

                if ($soal && $jawaban && $jawaban != '0') {
                    $aspekKode = $soal->aspek->kode_aspek ?? 'Tidak Diketahui';

                    if (! isset($updatedSkor[$aspekKode])) {
                        $updatedSkor[$aspekKode] = 0;
                    }

                    $skorOpsi = match (strtoupper($jawaban)) {
                        'A' => (int) ($soal->poin_opsi_a ?? 0),
                        'B' => (int) ($soal->poin_opsi_b ?? 0),
                        'C' => (int) ($soal->poin_opsi_c ?? 0),
                        'D' => (int) ($soal->poin_opsi_d ?? 0),
                        'E' => (int) ($soal->poin_opsi_e ?? 0),
                        default => 0,
                    };

                    $updatedSkor[$aspekKode] += $skorOpsi;
                }
            }
        }

        foreach ($updatedSkor as $key => $val) {
            $skorAspek[$key] = $val;
        }

        $ujian->skor_aspek = $skorAspek;
        $ujian->nilai_total = array_sum($updatedSkor);
        $ujian->save();
    }

    private function saveHasil(UjianPspk $ujian, int $metodeTesId): void
    {
        $data = $ujian->fresh();

        if ($metodeTesId === 5) {
            $totalNilai = [];
            foreach ($data->skor_aspek as $key => $val) {
                if (! $val) {
                    $data->skor_aspek[$key] = 0;
                }
                $totalNilai[] = $this->getLevelPerAspekLv1($data->skor_aspek[$key]);
            }

            $jpm = (array_sum($totalNilai)) / (1 * 9) * 100;
            $kategori = $this->getKategori($jpm);
            $deskripsi = $this->buildDeskripsiLv1($data, $totalNilai);
            $saranPengembangan = $this->buildSaranLv1($data, $totalNilai);
        } elseif ($metodeTesId === 6) {
            $totalNilai = [];
            foreach ($data->skor_aspek as $key => $val) {
                if (! $val) {
                    $data->skor_aspek[$key] = 0;
                }
                $totalNilai[] = $this->getLevelPerAspekLv2($data->skor_aspek[$key]);
            }

            $jpm = (array_sum($totalNilai)) / (2 * 9) * 100;
            $kategori = $this->getKategori($jpm);
            $deskripsi = $this->buildDeskripsiLv2($data, $totalNilai);
            $saranPengembangan = $this->buildSaranLv2($data, $totalNilai);
        } elseif (in_array($metodeTesId, [7, 8], true)) {
            $totalNilai = [];
            foreach ($data->skor_aspek as $key => $val) {
                if (! $val) {
                    $data->skor_aspek[$key] = 0;
                }

                if ($metodeTesId === 7) {
                    $totalNilai[] = $this->getLevelPerAspekLv3($data->skor_aspek[$key]);
                } else {
                    $totalNilai[] = $this->getLevelPerAspekLv4($data->skor_aspek[$key]);
                }
            }

            $jpm = $this->countJpmLv34(array_sum($totalNilai), $metodeTesId);
            $kategori = $this->getKategori($jpm);
            $deskripsi = $this->buildDeskripsiLv34($data, $totalNilai);
            $saranPengembangan = $this->buildSaranLv34($data, $totalNilai, $metodeTesId);
        } else {
            throw new \RuntimeException('Metode tes PSPK tidak dikenali.');
        }

        HasilPspk::updateOrCreate(
            [
                'event_id' => $data->event_id,
                'peserta_id' => $data->peserta_id,
                'ujian_id' => $data->id,
            ],
            [
                'nilai_total' => $data->nilai_total,
                'nilai_capaian' => $totalNilai,
                'jpm' => $jpm,
                'kategori' => $kategori,
                'deskripsi' => $deskripsi,
                'saran_pengembangan' => $saranPengembangan,
            ]
        );
    }

    /**
     * @return array<string, string|null>
     */
    private function buildDeskripsiLv1(UjianPspk $data, array $totalNilai): array
    {
        $deskripsi = [];
        foreach ($totalNilai as $key => $val) {
            $kodeAspek = array_keys($data->skor_aspek)[$key];
            $desc = RefDescPspk::where('level_pspk', 1)
                ->where('aspek_id', RefAspekPspk::where('kode_aspek', $kodeAspek)->first()->id)
                ->first();

            if ($val == 0.5) {
                $deskripsi[$kodeAspek] = $desc->deskripsi_min;
            } elseif ($val == 1) {
                $deskripsi[$kodeAspek] = $desc->deskripsi;
            } elseif ($val == 1.5) {
                $deskripsi[$kodeAspek] = $desc->deskripsi_plus;
            }
        }

        return $deskripsi;
    }

    /**
     * @return array<string, string|null>
     */
    private function buildSaranLv1(UjianPspk $data, array $totalNilai): array
    {
        $saranPengembangan = [];
        foreach ($totalNilai as $key => $val) {
            $kodeAspek = array_keys($data->skor_aspek)[$key];
            $saran = RefSaranPengembangan::where('level_pspk_id', 1)->first();

            if (in_array($val, [1, 1.5])) {
                $saranPengembangan[$kodeAspek] = 'Dapat diberikan tantangan di level kompetensi yang lebih tinggi.';
            } else {
                $saranPengembangan[$kodeAspek] = $saran->{$kodeAspek} ?? null;
            }
        }

        return $saranPengembangan;
    }

    /**
     * @return array<string, string|null>
     */
    private function buildDeskripsiLv2(UjianPspk $data, array $totalNilai): array
    {
        $deskripsi = [];
        foreach ($totalNilai as $key => $val) {
            $kodeAspek = array_keys($data->skor_aspek)[$key];
            $desc = RefDescPspk::where('level_pspk', 2)
                ->where('aspek_id', RefAspekPspk::where('kode_aspek', $kodeAspek)->first()->id)
                ->first();

            if ($val == 1) {
                $deskripsi[$kodeAspek] = $desc->deskripsi_min;
            } elseif ($val == 1.5) {
                $deskripsi[$kodeAspek] = $desc->deskripsi_min;
            } elseif ($val == 2) {
                $deskripsi[$kodeAspek] = $desc->deskripsi;
            } elseif ($val == 2.5) {
                $deskripsi[$kodeAspek] = $desc->deskripsi_plus;
            }
        }

        return $deskripsi;
    }

    /**
     * @return array<string, string|null>
     */
    private function buildSaranLv2(UjianPspk $data, array $totalNilai): array
    {
        $saranPengembangan = [];
        foreach ($totalNilai as $key => $val) {
            $kodeAspek = array_keys($data->skor_aspek)[$key];
            $saran = RefSaranPengembangan::where('level_pspk_id', 2)->first();

            if (in_array($val, [2, 2.5])) {
                $saranPengembangan[$kodeAspek] = 'Dapat diberikan tantangan di level kompetensi yang lebih tinggi.';
            } else {
                $saranPengembangan[$kodeAspek] = $saran->{$kodeAspek} ?? null;
            }
        }

        return $saranPengembangan;
    }

    /**
     * @return array<string, string|null>
     */
    private function buildDeskripsiLv34(UjianPspk $data, array $totalNilai): array
    {
        $deskripsi = [];
        foreach ($totalNilai as $key => $val) {
            $kodeAspek = array_keys($data->skor_aspek)[$key];
            $desc = RefDescPspk::where('level_pspk', 3)
                ->where('aspek_id', RefAspekPspk::where('kode_aspek', $kodeAspek)->first()->id)
                ->first();

            if ($val == 2) {
                $deskripsi[$kodeAspek] = $desc->deskripsi_min;
            } elseif ($val == 3) {
                $deskripsi[$kodeAspek] = $desc->deskripsi;
            } elseif ($val == 4) {
                $deskripsi[$kodeAspek] = $desc->deskripsi_plus;
            }
        }

        return $deskripsi;
    }

    /**
     * @return array<string, string|null>
     */
    private function buildSaranLv34(UjianPspk $data, array $totalNilai, int $metodeTesId): array
    {
        $saranPengembangan = [];
        foreach ($totalNilai as $key => $val) {
            $kodeAspek = array_keys($data->skor_aspek)[$key];
            $saran = RefSaranPengembangan::where('level_pspk_id', $metodeTesId === 7 ? 3 : 4)
                ->first();

            if ($metodeTesId === 7) {
                if ($val == 3 || $val == 4) {
                    $saranPengembangan[$kodeAspek] = 'Dapat diberikan tantangan di level kompetensi yang lebih tinggi.';
                } else {
                    $saranPengembangan[$kodeAspek] = $saran->{$kodeAspek} ?? null;
                }
            } elseif ($metodeTesId === 8) {
                if ($val == 4) {
                    $saranPengembangan[$kodeAspek] = 'Dapat diberikan tantangan di level kompetensi yang lebih tinggi.';
                } else {
                    $saranPengembangan[$kodeAspek] = $saran->{$kodeAspek} ?? null;
                }
            }
        }

        return $saranPengembangan;
    }

    private function getLevelPerAspekLv1($nilai): float
    {
        if ($nilai >= 6 && $nilai <= 10) {
            return 0.5;
        } elseif ($nilai >= 11 && $nilai <= 14) {
            return 1;
        } elseif ($nilai >= 15 && $nilai <= 18) {
            return 1.5;
        }

        return 0;
    }

    private function getLevelPerAspekLv2($nilai): float
    {
        if ($nilai >= 6 && $nilai <= 11) {
            return 1;
        } elseif ($nilai >= 12 && $nilai <= 17) {
            return 1.5;
        } elseif ($nilai >= 18 && $nilai <= 23) {
            return 2;
        } elseif ($nilai >= 24 && $nilai <= 30) {
            return 2.5;
        }

        return 0;
    }

    private function getLevelPerAspekLv3($nilai): int
    {
        return match (true) {
            $nilai >= 0 && $nilai <= 6 => 2,
            $nilai >= 7 && $nilai <= 12 => 3,
            $nilai >= 13 && $nilai <= 18 => 4,
            default => 0,
        };
    }

    private function getLevelPerAspekLv4($nilai): int
    {
        return match (true) {
            $nilai >= 0 && $nilai <= 4 => 2,
            $nilai >= 5 && $nilai <= 9 => 3,
            $nilai >= 10 && $nilai <= 18 => 4,
            default => 0,
        };
    }

    private function getKategori($jpm): string
    {
        if ($jpm >= 90) {
            return 'Optimal';
        } elseif ($jpm < 90 && $jpm >= 78) {
            return 'Cukup Optimal';
        }

        return 'Kurang Optimal';
    }

    private function countJpmLv34(int $totalNilaiCapaian, int $metodeTesId): float
    {
        if ($metodeTesId === 7) {
            return $totalNilaiCapaian / (3 * 9) * 100;
        } elseif ($metodeTesId === 8) {
            return $totalNilaiCapaian / (4 * 9) * 100;
        }

        return 0;
    }
}
