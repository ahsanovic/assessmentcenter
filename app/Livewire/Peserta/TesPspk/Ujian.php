<?php

namespace App\Livewire\Peserta\TesPspk;

use App\Models\Pspk\HasilPspk;
use App\Models\Pspk\RefDescPspk;
use App\Models\Pspk\RefSaranPengembangan;
use App\Models\Pspk\SoalPspk;
use App\Models\Pspk\UjianPspk;
use App\Models\RefAspekPspk;
use App\Traits\PelanggaranTrait;
use App\Traits\TimerTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

#[Layout('components.layouts.peserta.app', ['title' => 'Tes PSPK'])]
class Ujian extends Component
{
    use PelanggaranTrait, TimerTrait;

    public $soal;

    public $jml_soal;

    public $id_soal;

    public $nomor_soal;

    public $jawaban_user = [];

    public $jawaban_kosong;

    public $id_ujian;

    public $timer;

    public $flagged = [];

    public int $levelPspk = 0;

    public int $jmlAnkas = 0;

    public bool $isLevel34 = false;

    public bool $lv34SjtEntered = false;

    #[On('updateFlagsFromBrowser')]
    public function updateFlagsFromBrowser($flags)
    {
        $this->flagged = $flags;
    }

    public function toggleFlag($nomor)
    {
        $this->refreshLv34SealFromDatabase();

        if ($this->isLevel34 && $this->jmlAnkas > 0 && (int) $nomor <= $this->jmlAnkas && $this->lv34SjtEntered) {
            return;
        }

        // Livewire hanya kirim nomor soal, JS akan update localStorage
        $this->dispatch('toggle-flag-in-browser', nomor: $nomor);

        // setelah JS update → JS akan kirim kembali flags terbaru
        $this->dispatch('request-flags-sync');
    }

    public function mount($id)
    {
        $this->dispatch('load-flags-from-browser');

        $data = UjianPspk::select('id', 'soal_id', 'jawaban', 'created_at', 'pspk_lv34_entered_sjt_at')
            ->where('peserta_id', Auth::guard('peserta')->user()->id)
            ->where('event_id', Auth::guard('peserta')->user()->event_id)
            ->first();

        if (! $data) {
            Session::flash('toast', [
                'type' => 'error',
                'message' => 'Data ujian tidak ditemukan. Silakan mulai tes terlebih dahulu.',
            ]);

            return $this->redirect(route('peserta.tes-pspk.home'), navigate: true);
        }

        $this->nomor_soal = explode(',', $data->soal_id);
        $this->jawaban_user = explode(',', $data->jawaban);

        $metode_tes_id = Auth::guard('peserta')->user()->event->metode_tes_id;
        switch ($metode_tes_id) {
            case '5': // PSPK level 1
                $level_pspk = 1;
                break;
            case '6': // PSPK level 2
                $level_pspk = 2;
                break;
            case '7': // PSPK level 3
                $level_pspk = 3;
                break;
            case '8': // PSPK level 4
                $level_pspk = 3; // level 3 dan 4 memiliki soal yang sama
                break;
            default:
                $level_pspk = null;
                break;
        }

        $this->levelPspk = $level_pspk ?? 0;
        $this->isLevel34 = in_array($this->levelPspk, [3, 4]);

        $total_soal_by_level = SoalPspk::where('level_pspk_id', $level_pspk)->count();
        $this->jml_soal = $total_soal_by_level;

        if ($this->isLevel34) {
            $soalMap = SoalPspk::whereIn('id', $this->nomor_soal)
                ->pluck('jenis_soal', 'id');

            $this->jmlAnkas = 0;
            foreach ($this->nomor_soal as $soalId) {
                if (((int) ($soalMap[$soalId] ?? 0)) === SoalPspk::JENIS_ANKAS) {
                    $this->jmlAnkas++;
                } else {
                    break;
                }
            }
        }

        $this->id_ujian = $data->id;
        $this->lv34SjtEntered = filled($data->pspk_lv34_entered_sjt_at);

        $requestedId = (int) $id;
        if ($requestedId < 1 || $requestedId > $this->jml_soal) {
            return $this->redirect(route('peserta.tes-pspk.ujian', ['id' => 1]), navigate: true);
        }

        $redirectGuard = $this->enforceLevel34PhaseUrl($requestedId);
        if ($redirectGuard !== null) {
            return $redirectGuard;
        }

        $this->id_soal = $requestedId;

        if (! isset($this->nomor_soal[$this->id_soal - 1])) {
            return $this->redirect(route('peserta.tes-pspk.home'), navigate: true);
        }

        $this->soal = SoalPspk::with('kasusLampiran')->find($this->nomor_soal[$this->id_soal - 1]);

        if ($this->isLevel34 && $this->lv34SjtEntered && $this->id_soal > $this->jmlAnkas) {
            Session::put(['pspk_lv34_last_sjt_nomor_'.$this->id_ujian => $this->id_soal]);
        }

        $this->timerTest('Pspk');

        for ($i = 0, $j = 0; $i < $this->jml_soal; $i++) {
            if ($this->jawaban_user[$i] == '0') {
                $j = $j + 1;
                $this->jawaban_kosong = $j;
            }
        }
    }

    /**
     * Sinkron LV3–4 dari DB pada setiap request Livewire tanpa reload penuh.
     */
    public function hydrate(): void
    {
        $this->refreshLv34SealFromDatabase();
    }

    private function refreshLv34SealFromDatabase(): void
    {
        if (blank($this->id_ujian) || ! $this->isLevel34 || $this->jmlAnkas <= 0) {
            return;
        }

        $row = UjianPspk::whereKey($this->id_ujian)->select('pspk_lv34_entered_sjt_at', 'jawaban')->first();
        if (! $row) {
            return;
        }

        $this->lv34SjtEntered = filled($row->pspk_lv34_entered_sjt_at);

        if (! $this->lv34SjtEntered) {
            return;
        }

        $fromDb = explode(',', $row->jawaban ?? '');
        for ($i = 0; $i < $this->jmlAnkas; $i++) {
            $this->jawaban_user[$i] = $fromDb[$i] ?? '0';
        }
    }

    /**
     * Sinkronkan ulang seluruh jawaban_user dari database.
     *
     * Dipakai saat navigasi soal agar pilihan radio yang belum disimpan
     * (perubahan wire:model yang ter-batch) tidak nyangkut di state dan
     * membuat tombol navigasi soal lain ikut berubah hijau.
     */
    private function refreshJawabanUserFromDatabase(): void
    {
        if (blank($this->id_ujian)) {
            return;
        }

        $row = UjianPspk::whereKey($this->id_ujian)->select('jawaban')->first();
        if (! $row) {
            return;
        }

        $this->jawaban_user = explode(',', $row->jawaban ?? '');

        $kosong = 0;
        foreach ($this->jawaban_user as $j) {
            if ($j == '0') {
                $kosong++;
            }
        }
        $this->jawaban_kosong = $kosong;
    }

    public function render()
    {
        $row = UjianPspk::whereKey($this->id_ujian)->select('jawaban')->first();
        $jawabanTersimpan = $row ? explode(',', $row->jawaban ?? '') : $this->jawaban_user;

        $isAnkasPhase = false;
        $allAnkasAnswered = true;
        $phaseJmlSoal = $this->jml_soal;
        $phaseKosong = 0;
        $phaseNomor = $this->id_soal;

        for ($i = 0; $i < $this->jml_soal; $i++) {
            if (($jawabanTersimpan[$i] ?? '0') == '0') {
                $phaseKosong++;
            }
        }

        if ($this->isLevel34 && $this->jmlAnkas > 0) {
            $isAnkasPhase = $this->id_soal <= $this->jmlAnkas;

            $ankasKosong = 0;
            for ($i = 0; $i < $this->jmlAnkas; $i++) {
                if (($jawabanTersimpan[$i] ?? '0') == '0') {
                    $ankasKosong++;
                    $allAnkasAnswered = false;
                }
            }

            if ($isAnkasPhase) {
                $phaseJmlSoal = $this->jmlAnkas;
                $phaseKosong = $ankasKosong;
                $phaseNomor = $this->id_soal;
            } else {
                $sjtKosong = 0;
                for ($i = $this->jmlAnkas; $i < $this->jml_soal; $i++) {
                    if (($jawabanTersimpan[$i] ?? '0') == '0') {
                        $sjtKosong++;
                    }
                }
                $phaseJmlSoal = $this->jml_soal - $this->jmlAnkas;
                $phaseKosong = $sjtKosong;
                $phaseNomor = $this->id_soal - $this->jmlAnkas;
            }
        }

        return view('livewire.peserta.tes-pspk.ujian', [
            'nomor_sekarang' => $this->id_soal,
            'jawaban' => $this->jawaban_user,
            'jawaban_tersimpan' => $jawabanTersimpan,
            'jawaban_kosong' => $phaseKosong,
            'jml_soal' => $phaseJmlSoal,
            'soal' => $this->soal,
            'isLevel34' => $this->isLevel34,
            'isAnkasPhase' => $isAnkasPhase,
            'jmlAnkas' => $this->jmlAnkas,
            'totalSoalAll' => $this->jml_soal,
            'allAnkasAnswered' => $allAnkasAnswered,
            'phaseNomor' => $phaseNomor,
        ]);
    }

    public function saveAndNext($nomor_soal)
    {
        $nomor_soal = (int) $nomor_soal;
        $this->refreshLv34SealFromDatabase();

        $data = UjianPspk::where('peserta_id', Auth::guard('peserta')->user()->id)
            ->where('event_id', Auth::guard('peserta')->user()->event_id)
            ->where('is_finished', 'false')
            ->first();

        if (! $data) {
            return $this->redirect(route('peserta.tes-pspk.home'), navigate: true);
        }

        if ($this->isLevel34 && $this->jmlAnkas > 0
            && $nomor_soal >= 1
            && $nomor_soal <= $this->jmlAnkas
            && $this->lv34SjtEntered) {
            Session::flash('toast', [
                'type' => 'info',
                'message' => 'Jawaban analisa kasus tidak dapat diubah setelah Anda melanjutkan ke bagian SJT.',
            ]);

            $target = max(
                $this->jmlAnkas + 1,
                min(
                    (int) Session::get('pspk_lv34_last_sjt_nomor_'.$data->id, $this->jmlAnkas + 1),
                    $this->jml_soal
                )
            );

            return $this->redirect(route('peserta.tes-pspk.ujian', ['id' => $target]), navigate: true);
        }

        $index_array = $nomor_soal - 1;

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
        $this->jawaban_kosong = collect($this->jawaban_user)->filter(fn ($j) => $j == '0')->count();

        $skor_aspek = $data->skor_aspek ?? [];
        $aspek_list = RefAspekPspk::pluck('kode_aspek')->toArray();
        foreach ($aspek_list as $a) {
            if (! isset($skor_aspek[$a])) {
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

                    if (! isset($updated_skor[$aspek_kode])) {
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
        } elseif (auth()->guard('peserta')->user()->event->metode_tes_id == 6) { // pspk level 2
            foreach ($soal_id as $i => $sid) {
                $jawaban = $jawaban_user[$i] ?? null;
                $soal = SoalPspk::find($sid);

                if ($soal && $jawaban && $jawaban != '0') {
                    $aspek_kode = $soal->aspek->kode_aspek ?? 'Tidak Diketahui';
                    if (! isset($updated_skor[$aspek_kode])) {
                        $updated_skor[$aspek_kode] = 0;
                    }

                    $updated_skor[$aspek_kode] += ($soal->kunci_jawaban == $jawaban) ? 5 : 1;
                }
            }
        } elseif (in_array((int) auth()->guard('peserta')->user()->event->metode_tes_id, [7, 8])) { // pspk level 3 dan 4
            foreach ($soal_id as $i => $sid) {
                $jawaban = $jawaban_user[$i] ?? null;
                $soal = SoalPspk::find($sid);

                if ($soal && $jawaban && $jawaban != '0') {
                    $aspek_kode = $soal->aspek->kode_aspek ?? 'Tidak Diketahui';
                    if (! isset($updated_skor[$aspek_kode])) {
                        $updated_skor[$aspek_kode] = 0;
                    }

                    // Untuk level 3/4, skor mengikuti bobot masing-masing opsi jawaban.
                    $skor_opsi = match (strtoupper($jawaban)) {
                        'A' => (int) ($soal->poin_opsi_a ?? 0),
                        'B' => (int) ($soal->poin_opsi_b ?? 0),
                        'C' => (int) ($soal->poin_opsi_c ?? 0),
                        'D' => (int) ($soal->poin_opsi_d ?? 0),
                        'E' => (int) ($soal->poin_opsi_e ?? 0),
                        default => 0,
                    };

                    $updated_skor[$aspek_kode] += $skor_opsi;
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

        if ($this->isLevel34 && $nomor_soal <= $this->jmlAnkas) {
            $targetId = $nomor_soal < $this->jmlAnkas ? $nomor_soal + 1 : $nomor_soal;
            $this->navigateAnkasInPlace($targetId);

            return;
        } elseif ($nomor_soal < $this->jml_soal) {
            $this->redirect(route('peserta.tes-pspk.ujian', ['id' => $nomor_soal + 1]), navigate: true);
        } else {
            $this->redirect(route('peserta.tes-pspk.ujian', ['id' => $nomor_soal]), navigate: true);
        }
    }

    public function navigate($id)
    {
        $id = (int) $id;
        $this->refreshLv34SealFromDatabase();

        if ($id < 1 || $id > $this->jml_soal) {
            return;
        }

        if ($redirect = $this->enforceLevel34PhaseUrl($id)) {
            return $redirect;
        }

        $this->refreshJawabanUserFromDatabase();

        $this->id_soal = $id;
        $this->soal = SoalPspk::with('kasusLampiran')->find($this->nomor_soal[$id - 1]);

        if ($this->isLevel34 && $id > $this->jmlAnkas && $this->lv34SjtEntered) {
            Session::put(['pspk_lv34_last_sjt_nomor_'.$this->id_ujian => $id]);
        }

        if ($this->isLevel34 && $id <= $this->jmlAnkas && ! $this->lv34SjtEntered) {
            $this->navigateAnkasInPlace($id);

            return;
        }

        $this->redirect(route('peserta.tes-pspk.ujian', ['id' => $id]), navigate: true);
    }

    private function navigateAnkasInPlace(int $targetId): void
    {
        $this->id_soal = $targetId;
        $this->soal = SoalPspk::with('kasusLampiran')->find($this->nomor_soal[$targetId - 1]);

        $url = route('peserta.tes-pspk.ujian', ['id' => $targetId]);
        $this->js('window.history.replaceState({}, \'\', '.json_encode($url).')');
    }

    public function lanjutKeSjt()
    {
        if (! $this->isLevel34 || $this->jmlAnkas === 0) {
            return;
        }

        for ($i = 0; $i < $this->jmlAnkas; $i++) {
            if ($this->jawaban_user[$i] == '0') {
                return;
            }
        }

        UjianPspk::where('id', $this->id_ujian)
            ->where('is_finished', 'false')
            ->update([
                'pspk_lv34_entered_sjt_at' => now(),
            ]);

        $this->lv34SjtEntered = true;

        Session::put([
            'pspk_lv34_last_sjt_nomor_'.$this->id_ujian => $this->jmlAnkas + 1,
        ]);

        $this->redirect(route('peserta.tes-pspk.ujian', ['id' => $this->jmlAnkas + 1]), navigate: true);
    }

    public function finish()
    {
        try {
            $data = UjianPspk::findOrFail($this->id_ujian);

            Session::forget([
                'pspk_lv34_last_sjt_nomor_'.$data->id,
            ]);

            if (auth()->guard('peserta')->user()->event->metode_tes_id == 5) { // pspk level 1
                // nilai capaian
                $total_nilai = [];
                foreach ($data->skor_aspek as $key => $val) {
                    if (! $val) {
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
                    } elseif ($val == 1) {
                        $deskripsi[$kode_aspek] = $desc->deskripsi;
                    } elseif ($val == 1.5) {
                        $deskripsi[$kode_aspek] = $desc->deskripsi_plus;
                    }
                }

                // saran pengembangan
                $saran_pengembangan = [];
                foreach ($total_nilai as $key => $val) {
                    $kode_aspek = array_keys($data->skor_aspek)[$key];
                    $saran = RefSaranPengembangan::where('level_pspk_id', 1)->first();

                    if (in_array($val, [1, 1.5])) {
                        $saran_pengembangan[$kode_aspek] = 'Dapat diberikan tantangan di level kompetensi yang lebih tinggi.';
                    } else {
                        $saran_pengembangan[$kode_aspek] = $saran->{$kode_aspek} ?? null;
                    }
                }
            } elseif (auth()->guard('peserta')->user()->event->metode_tes_id == 6) { // pspk level 2
                // nilai capaian
                $total_nilai = [];
                foreach ($data->skor_aspek as $key => $val) {
                    if (! $val) {
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
                    } elseif ($val == 1.5) {
                        $deskripsi[$kode_aspek] = $desc->deskripsi_min;
                    } elseif ($val == 2) {
                        $deskripsi[$kode_aspek] = $desc->deskripsi;
                    } elseif ($val == 2.5) {
                        $deskripsi[$kode_aspek] = $desc->deskripsi_plus;
                    }
                }

                // saran pengembangan
                $saran_pengembangan = [];
                foreach ($total_nilai as $key => $val) {
                    $kode_aspek = array_keys($data->skor_aspek)[$key];
                    $saran = RefSaranPengembangan::where('level_pspk_id', 2)->first();

                    if (in_array($val, [2, 2.5])) {
                        $saran_pengembangan[$kode_aspek] = 'Dapat diberikan tantangan di level kompetensi yang lebih tinggi.';
                    } else {
                        $saran_pengembangan[$kode_aspek] = $saran->{$kode_aspek} ?? null;
                    }
                }
            } elseif (in_array((int) auth()->guard('peserta')->user()->event->metode_tes_id, [7, 8])) { // pspk level 3 dan 4
                // nilai capaian
                $total_nilai = [];
                foreach ($data->skor_aspek as $key => $val) {
                    if (! $val) {
                        $data->skor_aspek[$key] = 0;
                    }
                    $total_nilai[] = $this->_getLevelPerAspekLv34($data->skor_aspek[$key]);
                }

                // jpm
                $jpm = $this->_countJpmLv34(array_sum($total_nilai));

                // kategori
                $kategori = $this->_getKategori($jpm);

                // dekskripsi
                $deskripsi = [];
                foreach ($total_nilai as $key => $val) {
                    $kode_aspek = array_keys($data->skor_aspek)[$key];
                    $desc = RefDescPspk::where('level_pspk', 3)
                        ->where('aspek_id', RefAspekPspk::where('kode_aspek', $kode_aspek)->first()->id)
                        ->first();

                    if ($val == 2) {
                        $deskripsi[$kode_aspek] = $desc->deskripsi_min;
                    } elseif ($val == 3) {
                        $deskripsi[$kode_aspek] = $desc->deskripsi;
                    } elseif ($val == 4) {
                        $deskripsi[$kode_aspek] = $desc->deskripsi_plus;
                    }
                }

                // saran pengembangan
                $saran_pengembangan = [];
                foreach ($total_nilai as $key => $val) {
                    $kode_aspek = array_keys($data->skor_aspek)[$key];
                    $saran = RefSaranPengembangan::where('level_pspk_id', (int) auth()->guard('peserta')->user()->event->metode_tes_id == 7 ? 3 : 4)
                        ->first();

                    if ($val == 4) {
                        $saran_pengembangan[$kode_aspek] = 'Dapat diberikan tantangan di level kompetensi yang lebih tinggi.';
                    } else {
                        $saran_pengembangan[$kode_aspek] = $saran->{$kode_aspek} ?? null;
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
                    'deskripsi' => $deskripsi,
                    'saran_pengembangan' => $saran_pengembangan,
                ]
            );

            // change status ujian to true (finish)
            $data->is_finished = true;
            $data->save();

            // Bersihkan localStorage via JS
            $this->dispatch('clear-flags-browser');

            return $this->redirect(route('peserta.tes-pspk.hasil'));
        } catch (\Throwable $th) {
            // throw $th;
            Session::flash('toast', [
                'type' => 'error',
                'message' => 'Terjadi kesalahan',
            ]);
        }
    }

    private function semuaAnkasTerjawab(): bool
    {
        if (! $this->isLevel34 || $this->jmlAnkas <= 0) {
            return true;
        }

        for ($i = 0; $i < $this->jmlAnkas; $i++) {
            if (($this->jawaban_user[$i] ?? '0') == '0') {
                return false;
            }
        }

        return true;
    }

    private function indeksPertamaAnkasBelumTerjawab(): int
    {
        for ($i = 0; $i < $this->jmlAnkas; $i++) {
            if (($this->jawaban_user[$i] ?? '0') == '0') {
                return $i + 1;
            }
        }

        return max(1, $this->jmlAnkas);
    }

    private function enforceLevel34PhaseUrl(int $requestedId)
    {
        $this->refreshLv34SealFromDatabase();

        if (! $this->isLevel34 || $this->jmlAnkas <= 0) {
            return null;
        }

        $lastNomorKey = 'pspk_lv34_last_sjt_nomor_'.$this->id_ujian;

        if ($requestedId > $this->jmlAnkas) {
            if (! $this->semuaAnkasTerjawab()) {
                Session::flash('toast', [
                    'type' => 'warning',
                    'message' => 'Selesaikan dulu tahap analisa kasus.',
                ]);

                return $this->redirect(route('peserta.tes-pspk.ujian', ['id' => $this->indeksPertamaAnkasBelumTerjawab()]), navigate: true);
            }

            if (! $this->lv34SjtEntered) {
                Session::flash('toast', [
                    'type' => 'warning',
                    'message' => 'Gunakan tombol Lanjut Tes Berikutnya untuk memulai bagian SJT.',
                ]);

                return $this->redirect(route('peserta.tes-pspk.ujian', ['id' => $this->jmlAnkas]), navigate: true);
            }

            return null;
        }

        if ($this->lv34SjtEntered) {
            $target = Session::get($lastNomorKey, $this->jmlAnkas + 1);
            $target = max($this->jmlAnkas + 1, min((int) $target, $this->jml_soal));

            Session::flash('toast', [
                'type' => 'info',
                'message' => 'Anda sudah melanjutkan ke tes SJT. Tidak dapat kembali ke soal analisa kasus.',
            ]);

            return $this->redirect(route('peserta.tes-pspk.ujian', ['id' => $target]), navigate: true);
        }

        return null;
    }

    private function _getLevelPerAspek($nilai)
    {
        if ($nilai >= 6 && $nilai <= 10) {
            $level = 0.5;
        } elseif ($nilai >= 11 && $nilai <= 14) {
            $level = 1;
        } elseif ($nilai >= 15 && $nilai <= 18) {
            $level = 1.5;
        }

        return $level;
    }

    private function _getLevelPerAspekLv2($nilai)
    {
        if ($nilai >= 6 && $nilai <= 11) {
            $level = 1;
        } elseif ($nilai >= 12 && $nilai <= 17) {
            $level = 1.5;
        } elseif ($nilai >= 18 && $nilai <= 23) {
            $level = 2;
        } elseif ($nilai >= 24 && $nilai <= 30) {
            $level = 2.5;
        }

        return $level;
    }

    private function _getLevelPerAspekLv34($nilai)
    {
        $level = match (true) {
            $nilai >= 6 && $nilai <= 9 => 2,
            $nilai >= 10 && $nilai <= 14 => 3,
            $nilai >= 15 && $nilai <= 18 => 4,
            default => 0,
        };

        return $level;
    }

    private function _getKategori($jpm)
    {
        if ($jpm >= 90) {
            $kategori = 'Optimal';
        } elseif ($jpm < 90 && $jpm >= 78) {
            $kategori = 'Cukup Optimal';
        } elseif ($jpm < 78) {
            $kategori = 'Kurang Optimal';
        }

        return $kategori;
    }

    private function _countJpmLv34(int $total_nilai_capaian): float
    {
        $metode_tes_id = auth()->guard('peserta')->user()->event->metode_tes_id;
        if ($metode_tes_id == 7) {
            return $total_nilai_capaian / (3 * 9) * 100;
        } elseif ($metode_tes_id == 8) {
            return $total_nilai_capaian / (4 * 9) * 100;
        }

        return 0;
    }
}
