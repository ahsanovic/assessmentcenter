<?php

namespace App\Traits;

use App\Models\BerpikirKritis\SoalBerpikirKritis;
use App\Models\BerpikirKritis\UjianBerpikirKritis;
use App\Models\Interpersonal\SoalInterpersonal;
use App\Models\Interpersonal\UjianInterpersonal;
use App\Models\KecerdasanEmosi\SoalKecerdasanEmosi;
use App\Models\KecerdasanEmosi\UjianKecerdasanEmosi;
use App\Models\KesadaranDiri\SoalKesadaranDiri;
use App\Models\KesadaranDiri\UjianKesadaranDiri;
use App\Models\MotivasiKomitmen\SoalMotivasiKomitmen;
use App\Models\MotivasiKomitmen\UjianMotivasiKomitmen;
use App\Models\PengembanganDiri\SoalPengembanganDiri;
use App\Models\PengembanganDiri\UjianPengembanganDiri;
use App\Models\ProblemSolving\SoalProblemSolving;
use App\Models\ProblemSolving\UjianProblemSolving;
use App\Models\Settings;
use App\Models\SettingWaktuTes;
use App\Services\Potensi\PjfPotensiService;

trait StartTestTrait
{
    public function startTest($nama_tes, $urutan_tes)
    {
        switch ($nama_tes) {
            case 'Kemampuan Interpersonal':
                $this->_startTesInterpersonal($urutan_tes);
                break;
            case 'Belajar Cepat dan Pengembangan Diri':
                $this->_startTesPengembanganDiri($urutan_tes);
                break;
            case 'Kecerdasan Emosi':
                $this->_startTesKecerdasanEmosi($urutan_tes);
                break;
            case 'Motivasi dan Komitmen':
                $this->_startTesMotivasiKomitmen($urutan_tes);
                break;
            case 'Berpikir Kritis dan Strategis':
                $this->_startTesBerpikirKritis($urutan_tes);
                break;
            case 'Problem Solving':
                $this->_startTesProblemSolving($urutan_tes);
                break;
            case 'Kesadaran Diri':
                $this->_startTesKesadaranDiri($urutan_tes);
                break;
        }
    }

    protected function _startTesInterpersonal($urutan_tes)
    {
        $ujian_selesai = UjianInterpersonal::where('event_id', activeEventId())
            ->where('peserta_id', activePesertaId())
            ->where('is_finished', 'true')
            ->count();

        if ($ujian_selesai > 0) {
            return $this->redirect(route('peserta.tes-potensi.interpersonal', ['id' => 1]), navigate: true);
        }

        $ujian_berlangsung = UjianInterpersonal::where('event_id', activeEventId())
            ->where('peserta_id', activePesertaId())
            ->where('is_finished', 'false')
            ->count();

        if ($ujian_berlangsung > 0) {
            return $this->redirect(route('peserta.tes-potensi.interpersonal', ['id' => 1]));
        } else {
            $soal = SoalInterpersonal::get(['id']);
            $jumlah_soal = $soal->count();
            $soal_id = $soal->implode('id', ',');
            $jawaban_kosong = implode(',', array_fill(0, $jumlah_soal, 0));

            if ($urutan_tes === 1) {
                $durasi_tes = SettingWaktuTes::where('is_active', 'true')->where('jenis_tes', 1)->first(['waktu']); // 1 = tes potensi
                $waktu_tes_berakhir = now()->addMinutes($durasi_tes->waktu);
            } else {
                $waktu_tes_berakhir = null;
            }

            $ujian = new UjianInterpersonal;
            $ujian->event_id = activeEventId();
            $ujian->peserta_id = activePesertaId();
            $ujian->soal_id = $soal_id;
            $ujian->jawaban = $jawaban_kosong;
            $ujian->nilai_indikator_ke = 0;
            $ujian->nilai_indikator_bt = 0;
            $ujian->nilai_indikator_as = 0;
            $ujian->nilai_indikator_de = 0;
            $ujian->nilai_indikator_smk = 0;
            $ujian->urutan_tes = $urutan_tes;
            $ujian->waktu_tes_berakhir = $waktu_tes_berakhir;
            $ujian->save();

            return $this->redirect(route('peserta.tes-potensi.interpersonal', ['id' => 1]));
        }
    }

    protected function _startTesPengembanganDiri($urutan_tes)
    {
        $ujian_selesai = UjianPengembanganDiri::where('event_id', activeEventId())
            ->where('peserta_id', activePesertaId())
            ->where('is_finished', 'true')
            ->count();

        if ($ujian_selesai > 0) {
            return $this->redirect(route('peserta.tes-potensi.pengembangan-diri', ['id' => 1]), navigate: true);
        }

        $ujian_berlangsung = UjianPengembanganDiri::where('event_id', activeEventId())
            ->where('peserta_id', activePesertaId())
            ->where('is_finished', 'false')
            ->count();

        if ($ujian_berlangsung > 0) {
            return $this->redirect(route('peserta.tes-potensi.pengembangan-diri', ['id' => 1]));
        } else {
            $soal = SoalPengembanganDiri::get(['id']);
            $jumlah_soal = $soal->count();
            $soal_id = $soal->implode('id', ',');
            $jawaban_kosong = implode(',', array_fill(0, $jumlah_soal, 0));

            if ($urutan_tes === 1) {
                $durasi_tes = SettingWaktuTes::where('is_active', 'true')->where('jenis_tes', 1)->first(['waktu']);
                $waktu_tes_berakhir = now()->addMinutes($durasi_tes->waktu);
            } else {
                $waktu_tes_berakhir = null;
            }

            $ujian = new UjianPengembanganDiri;
            $ujian->peserta_id = activePesertaId();
            $ujian->event_id = activeEventId();
            $ujian->soal_id = $soal_id;
            $ujian->jawaban = $jawaban_kosong;
            $ujian->nilai_indikator_mb = 0;
            $ujian->nilai_indikator_mit = 0;
            $ujian->nilai_indikator_pde = 0;
            $ujian->nilai_indikator_spd = 0;
            $ujian->nilai_indikator_ed = 0;
            $ujian->urutan_tes = $urutan_tes;
            $ujian->waktu_tes_berakhir = $waktu_tes_berakhir;
            $ujian->save();

            return $this->redirect(route('peserta.tes-potensi.pengembangan-diri', ['id' => 1]));
        }
    }

    protected function _startTesKecerdasanEmosi($urutan_tes)
    {
        $ujian_selesai = UjianKecerdasanEmosi::where('event_id', activeEventId())
            ->where('peserta_id', activePesertaId())
            ->where('is_finished', 'true')
            ->count();

        if ($ujian_selesai > 0) {
            return $this->redirect(route('peserta.tes-potensi.kecerdasan-emosi', ['id' => 1]), navigate: true);
        }

        $ujian_berlangsung = UjianKecerdasanEmosi::where('event_id', activeEventId())
            ->where('peserta_id', activePesertaId())
            ->where('is_finished', 'false')
            ->count();

        if ($ujian_berlangsung > 0) {
            return $this->redirect(route('peserta.tes-potensi.kecerdasan-emosi', ['id' => 1]));
        } else {
            $soal = SoalKecerdasanEmosi::get(['id']);
            $jumlah_soal = $soal->count();
            $soal_id = $soal->implode('id', ',');
            $jawaban_kosong = implode(',', array_fill(0, $jumlah_soal, 0));

            if ($urutan_tes === 1) {
                $durasi_tes = SettingWaktuTes::where('is_active', 'true')->where('jenis_tes', 1)->first(['waktu']);
                $waktu_tes_berakhir = now()->addMinutes($durasi_tes->waktu);
            } else {
                $waktu_tes_berakhir = null;
            }

            $ujian = new UjianKecerdasanEmosi;
            $ujian->peserta_id = activePesertaId();
            $ujian->event_id = activeEventId();
            $ujian->soal_id = $soal_id;
            $ujian->jawaban = $jawaban_kosong;
            $ujian->nilai_indikator_kd = 0;
            $ujian->nilai_indikator_mpd = 0;
            $ujian->nilai_indikator_ke = 0;
            $ujian->nilai_indikator_ks = 0;
            $ujian->urutan_tes = $urutan_tes;
            $ujian->waktu_tes_berakhir = $waktu_tes_berakhir;
            $ujian->save();

            return $this->redirect(route('peserta.tes-potensi.kecerdasan-emosi', ['id' => 1]));
        }
    }

    protected function _startTesMotivasiKomitmen($urutan_tes)
    {
        $ujian_selesai = UjianMotivasiKomitmen::where('event_id', activeEventId())
            ->where('peserta_id', activePesertaId())
            ->where('is_finished', 'true')
            ->count();

        if ($ujian_selesai > 0) {
            return $this->redirect(route('peserta.tes-potensi.motivasi-komitmen', ['id' => 1]), navigate: true);
        }

        $ujian_berlangsung = UjianMotivasiKomitmen::where('event_id', activeEventId())
            ->where('peserta_id', activePesertaId())
            ->where('is_finished', 'false')
            ->count();

        if ($ujian_berlangsung > 0) {
            return $this->redirect(route('peserta.tes-potensi.motivasi-komitmen', ['id' => 1]));
        } else {
            $soal = SoalMotivasiKomitmen::get(['id']);
            $jumlah_soal = $soal->count();
            $soal_id = $soal->implode('id', ',');
            $jawaban_kosong = implode(',', array_fill(0, $jumlah_soal, 0));

            if ($urutan_tes === 1) {
                $durasi_tes = SettingWaktuTes::where('is_active', 'true')->where('jenis_tes', 1)->first(['waktu']);
                $waktu_tes_berakhir = now()->addMinutes($durasi_tes->waktu);
            } else {
                $waktu_tes_berakhir = null;
            }

            $ujian = new UjianMotivasiKomitmen;
            $ujian->peserta_id = activePesertaId();
            $ujian->event_id = activeEventId();
            $ujian->soal_id = $soal_id;
            $ujian->jawaban = $jawaban_kosong;
            $ujian->nilai_indikator_1 = 0;
            $ujian->nilai_indikator_2 = 0;
            $ujian->nilai_indikator_3 = 0;
            $ujian->nilai_indikator_4 = 0;
            $ujian->nilai_indikator_5 = 0;
            $ujian->urutan_tes = $urutan_tes;
            $ujian->waktu_tes_berakhir = $waktu_tes_berakhir;
            $ujian->save();

            return $this->redirect(route('peserta.tes-potensi.motivasi-komitmen', ['id' => 1]));
        }
    }

    protected function _startTesBerpikirKritis($urutan_tes)
    {
        $ujian_selesai = UjianBerpikirKritis::where('event_id', activeEventId())
            ->where('peserta_id', activePesertaId())
            ->where('is_finished', 'true')
            ->count();

        if ($ujian_selesai > 0) {
            return $this->redirect(route('peserta.tes-potensi.berpikir-kritis', ['id' => 1]), navigate: true);
        }

        $ujian_berlangsung = UjianBerpikirKritis::where('event_id', activeEventId())
            ->where('peserta_id', activePesertaId())
            ->where('is_finished', 'false')
            ->count();

        if ($ujian_berlangsung > 0) {
            return $this->redirect(route('peserta.tes-potensi.berpikir-kritis', ['id' => 1]));
        } else {
            $soal = SoalBerpikirKritis::get(['id']);
            $jumlah_soal = $soal->count();
            $soal_id = $soal->implode('id', ',');
            $jawaban_kosong = implode(',', array_fill(0, $jumlah_soal, 0));

            if ($urutan_tes === 1) {
                $durasi_tes = SettingWaktuTes::where('is_active', 'true')->where('jenis_tes', 1)->first(['waktu']);
                $waktu_tes_berakhir = now()->addMinutes($durasi_tes->waktu);
            } else {
                $waktu_tes_berakhir = null;
            }

            $ujian = new UjianBerpikirKritis;
            $ujian->peserta_id = activePesertaId();
            $ujian->event_id = activeEventId();
            $ujian->soal_id = $soal_id;
            $ujian->jawaban = $jawaban_kosong;
            $ujian->nilai_indikator_1 = 0;
            $ujian->nilai_indikator_2 = 0;
            $ujian->nilai_indikator_3 = 0;
            $ujian->nilai_indikator_4 = 0;
            $ujian->nilai_indikator_5 = 0;
            $ujian->nilai_indikator_6 = 0;
            $ujian->nilai_indikator_7 = 0;
            $ujian->nilai_indikator_8 = 0;
            $ujian->urutan_tes = $urutan_tes;
            $ujian->waktu_tes_berakhir = $waktu_tes_berakhir;
            $ujian->save();

            return $this->redirect(route('peserta.tes-potensi.berpikir-kritis', ['id' => 1]));
        }
    }

    protected function _startTesProblemSolving($urutan_tes)
    {
        $ujian_selesai = UjianProblemSolving::where('event_id', activeEventId())
            ->where('peserta_id', activePesertaId())
            ->where('is_finished', 'true')
            ->count();

        if ($ujian_selesai > 0) {
            return $this->redirect(route('peserta.tes-potensi.problem-solving', ['id' => 1]), navigate: true);
        }

        $ujian_berlangsung = UjianProblemSolving::where('event_id', activeEventId())
            ->where('peserta_id', activePesertaId())
            ->where('is_finished', 'false')
            ->count();

        if ($ujian_berlangsung > 0) {
            return $this->redirect(route('peserta.tes-potensi.problem-solving', ['id' => 1]));
        } else {
            $soal = SoalProblemSolving::get(['id']);
            $jumlah_soal = $soal->count();
            $soal_id = $soal->implode('id', ',');
            $jawaban_kosong = implode(',', array_fill(0, $jumlah_soal, 0));

            if ($urutan_tes === 1) {
                $durasi_tes = SettingWaktuTes::where('is_active', 'true')->where('jenis_tes', 1)->first(['waktu']);
                $waktu_tes_berakhir = now()->addMinutes($durasi_tes->waktu);
            } else {
                $waktu_tes_berakhir = null;
            }

            $ujian = new UjianProblemSolving;
            $ujian->peserta_id = activePesertaId();
            $ujian->event_id = activeEventId();
            $ujian->soal_id = $soal_id;
            $ujian->jawaban = $jawaban_kosong;
            $ujian->nilai_indikator_1 = 0;
            $ujian->nilai_indikator_2 = 0;
            $ujian->nilai_indikator_3 = 0;
            $ujian->nilai_indikator_4 = 0;
            $ujian->nilai_indikator_5 = 0;
            $ujian->nilai_indikator_6 = 0;
            $ujian->nilai_indikator_7 = 0;
            $ujian->nilai_indikator_8 = 0;
            $ujian->urutan_tes = $urutan_tes;
            $ujian->waktu_tes_berakhir = $waktu_tes_berakhir;
            $ujian->save();

            return $this->redirect(route('peserta.tes-potensi.problem-solving', ['id' => 1]));
        }
    }

    protected function _startTesKesadaranDiri($urutan_tes)
    {
        $ujian_selesai = UjianKesadaranDiri::where('event_id', activeEventId())
            ->where('peserta_id', activePesertaId())
            ->where('is_finished', 'true')
            ->count();

        if ($ujian_selesai) {
            return $this->redirect(route('peserta.tes-potensi.kesadaran-diri', ['id' => 1]), navigate: true);
        }

        $ujian_berlangsung = UjianKesadaranDiri::where('event_id', activeEventId())
            ->where('peserta_id', activePesertaId())
            ->where('is_finished', 'false')
            ->count();

        if ($ujian_berlangsung) {
            return $this->redirect(route('peserta.tes-potensi.kesadaran-diri', ['id' => 1]));
        } else {
            $soal = SoalKesadaranDiri::get(['id']);
            $jumlah_soal = $soal->count();
            $soal_id = $soal->implode('id', ',');
            $jawaban_kosong = implode(',', array_fill(0, $jumlah_soal, 0));

            if ($urutan_tes === 1) {
                $durasi_tes = SettingWaktuTes::where('is_active', 'true')->where('jenis_tes', 1)->first(['waktu']);
                $waktu_tes_berakhir = now()->addMinutes($durasi_tes->waktu);
            } else {
                $waktu_tes_berakhir = null;
            }

            $ujian = new UjianKesadaranDiri;
            $ujian->peserta_id = activePesertaId();
            $ujian->event_id = activeEventId();
            $ujian->soal_id = $soal_id;
            $ujian->jawaban = $jawaban_kosong;
            $ujian->nilai_indikator_1 = 0;
            $ujian->nilai_indikator_2 = 0;
            $ujian->nilai_indikator_3 = 0;
            $ujian->urutan_tes = $urutan_tes;
            $ujian->waktu_tes_berakhir = $waktu_tes_berakhir;
            $ujian->save();

            return $this->redirect(route('peserta.tes-potensi.kesadaran-diri', ['id' => 1]));
        }
    }

    protected function ringkasanSubtesPotensi(): array
    {
        $peta = $this->petaSubtesPotensi();
        $settings = Settings::with('alatTes')->orderBy('urutan')->get();
        $items = [];

        foreach ($settings as $setting) {
            $nama = $setting->alatTes->alat_tes ?? '';
            if (! isset($peta[$nama])) {
                continue;
            }

            $ujian = $peta[$nama]::query()
                ->where('event_id', activeEventId())
                ->where('peserta_id', activePesertaId())
                ->first(['id', 'is_finished', 'jawaban']);

            $selesai = $ujian && $this->ujianDitandaiSelesai($ujian->is_finished);
            $kosong = 0;
            if ($ujian) {
                foreach (explode(',', (string) $ujian->jawaban) as $jawaban) {
                    if ($jawaban === '0' || $jawaban === '' || $jawaban === null) {
                        $kosong++;
                    }
                }
            }

            $items[] = [
                'urutan' => (int) $setting->urutan,
                'nama' => $nama,
                'status' => ! $ujian ? 'belum' : ($selesai ? 'selesai' : 'berlangsung'),
                'jawaban_kosong' => $kosong,
            ];
        }

        return $items;
    }

    protected function bukaSubtes(int $urutan): void
    {
        if ($this->tesPotensiSelesaiSemua()) {
            session()->flash('toast', [
                'type' => 'error',
                'message' => 'Semua subtes sudah selesai.',
            ]);

            return;
        }

        $setting = Settings::with('alatTes')->where('urutan', $urutan)->first();
        if (! $setting?->alatTes) {
            return;
        }

        $this->startTest($setting->alatTes->alat_tes, (int) $setting->urutan);
    }

    protected function subtesPotensiTerkunci($ujian): bool
    {
        return $this->ujianDitandaiSelesai($ujian->is_finished);
    }

    protected function tesPotensiSelesaiSemua(): bool
    {
        $status = getFinishedTes();

        return count(array_filter($status)) === count($status);
    }

    /**
     * Setelah hasil subtes tersimpan: hitung PJF bila sudah lengkap, lalu lanjut urutan.
     * Kegagalan menyimpan PJF tidak menghentikan perpindahan ke subtes berikutnya atau kuesioner.
     */
    protected function lanjutSetelahSubtesPotensi(mixed $urutanTes): mixed
    {
        try {
            app(PjfPotensiService::class)->simpanJikaSemuaSelesai(activeEventId(), activePesertaId());
        } catch (\Throwable $e) {
            report($e);
        }

        $currentSequenceTest = Settings::where('urutan', $urutanTes)->first(['urutan']);
        if ($currentSequenceTest && $currentSequenceTest->urutan !== 7) {
            $nextTest = Settings::with('alatTes')->where('urutan', $currentSequenceTest->urutan + 1)->first();
            $this->startTest($nextTest->alatTes->alat_tes, $nextTest->urutan);

            return null;
        }

        if ($currentSequenceTest && $currentSequenceTest->urutan == 7) {
            return $this->redirect(route('peserta.kuesioner'), navigate: true);
        }

        return $this->redirect(route('peserta.tes-potensi.home'), navigate: true);
    }

    protected function ujianDitandaiSelesai(mixed $nilai): bool
    {
        return in_array($nilai, ['true', true, 1, '1'], true);
    }

    protected function petaSubtesPotensi(): array
    {
        return [
            'Kemampuan Interpersonal' => UjianInterpersonal::class,
            'Kesadaran Diri' => UjianKesadaranDiri::class,
            'Berpikir Kritis dan Strategis' => UjianBerpikirKritis::class,
            'Problem Solving' => UjianProblemSolving::class,
            'Kecerdasan Emosi' => UjianKecerdasanEmosi::class,
            'Belajar Cepat dan Pengembangan Diri' => UjianPengembanganDiri::class,
            'Motivasi dan Komitmen' => UjianMotivasiKomitmen::class,
        ];
    }
}
