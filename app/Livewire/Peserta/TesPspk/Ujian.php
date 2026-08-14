<?php

namespace App\Livewire\Peserta\TesPspk;

use App\Models\Pspk\SoalPspk;
use App\Models\Pspk\UjianPspk;
use App\Models\RefAspekPspk;
use App\Services\Pspk\PspkFinishUjianService;
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
            ->where('peserta_id', activePesertaId())
            ->where('event_id', activeEventId())
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

    public function saveAndNext($nomor_soal, $jawaban = null)
    {
        $nomor_soal = (int) $nomor_soal;
        $this->refreshLv34SealFromDatabase();

        $data = UjianPspk::where('peserta_id', activePesertaId())
            ->where('event_id', activeEventId())
            ->where('is_finished', 'false')
            ->first();

        if (! $data) {
            $this->skipRender();

            return null;
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
        $jawaban_user = explode(',', $data->jawaban);
        $jawaban_baru = $jawaban ?? ($this->jawaban_user[$index_array] ?? '0');
        $jawaban_baru = ($jawaban_baru === '' || $jawaban_baru === null) ? '0' : $jawaban_baru;

        $jawaban_user[$index_array] = $jawaban_baru;
        $data->jawaban = implode(',', $jawaban_user);
        $data->save();

        $this->jawaban_user = $jawaban_user;
        $this->jawaban_kosong = collect($this->jawaban_user)->filter(fn ($j) => $j == '0')->count();

        $this->recalculateScores($data, $soal_id, $jawaban_user);

        if ($this->isLevel34 && $nomor_soal <= $this->jmlAnkas) {
            $targetId = $nomor_soal < $this->jmlAnkas ? $nomor_soal + 1 : $nomor_soal;
            $this->skipRender();
            $payload = $this->soalPayload($targetId);

            if ($nomor_soal === $this->jmlAnkas) {
                $payload = array_merge($payload, $this->ankasTerakhirPromptPayload($jawaban_user));
            }

            return $payload;
        }

        $target = $nomor_soal < $this->jml_soal ? $nomor_soal + 1 : $nomor_soal;
        $this->skipRender();
        $payload = $this->soalPayload($target);

        if ($nomor_soal === $this->jml_soal) {
            $phaseStats = $this->computePhaseStats($jawaban_user, $target);
            $payload['prompt_soal_terakhir'] = true;
            $payload['semua_terjawab'] = (int) $phaseStats['phase_kosong'] === 0;
            $payload['jawaban_kosong'] = (int) $phaseStats['phase_kosong'];
            $payload['soal_belum_dijawab'] = $this->indeksPertamaBelumDijawabDalamFase($jawaban_user, $target);
        }

        return $payload;
    }

    public function navigate($id)
    {
        $id = (int) $id;
        $this->refreshLv34SealFromDatabase();

        if ($id < 1 || $id > $this->jml_soal) {
            $this->skipRender();

            return null;
        }

        if ($redirect = $this->enforceLevel34PhaseUrl($id)) {
            return $redirect;
        }

        $this->refreshJawabanUserFromDatabase();
        $this->skipRender();

        return $this->soalPayload($id);
    }

    private function soalPayload(int $id): array
    {
        $this->id_soal = $id;
        $this->soal = SoalPspk::with('kasusLampiran')->find($this->nomor_soal[$id - 1]);

        if ($this->isLevel34 && $id > $this->jmlAnkas && $this->lv34SjtEntered) {
            Session::put(['pspk_lv34_last_sjt_nomor_'.$this->id_ujian => $id]);
        }

        $jawaban = (string) ($this->jawaban_user[$id - 1] ?? '');
        $selected = ($jawaban !== '' && $jawaban !== '0') ? $jawaban : '';
        $stats = $this->computePhaseStats($this->jawaban_user, $id);

        return [
            'nomor' => $id,
            'phase_nomor' => $stats['phase_nomor'],
            'phase_jml_soal' => $stats['phase_jml_soal'],
            'jawaban_kosong' => $stats['phase_kosong'],
            'all_ankas_answered' => $stats['all_ankas_answered'],
            'is_ankas_phase' => $stats['is_ankas_phase'],
            'teks' => $this->soal?->soal,
            'opsi_a' => $this->soal?->opsi_a,
            'opsi_b' => $this->soal?->opsi_b,
            'opsi_c' => $this->soal?->opsi_c,
            'opsi_d' => $this->soal?->opsi_d,
            'opsi_e' => $this->soal?->opsi_e,
            'selected' => $selected,
            'jawaban_user' => array_values($this->jawaban_user),
            'url' => route('peserta.tes-pspk.ujian', ['id' => $id]),
        ];
    }

    private function computePhaseStats(array $jawabanUser, int $idSoal): array
    {
        $isAnkasPhase = false;
        $allAnkasAnswered = true;
        $phaseJmlSoal = $this->jml_soal;
        $phaseKosong = 0;
        $phaseNomor = $idSoal;

        if ($this->isLevel34 && $this->jmlAnkas > 0) {
            $isAnkasPhase = $idSoal <= $this->jmlAnkas;

            $ankasKosong = 0;
            for ($i = 0; $i < $this->jmlAnkas; $i++) {
                if (($jawabanUser[$i] ?? '0') == '0') {
                    $ankasKosong++;
                    $allAnkasAnswered = false;
                }
            }

            if ($isAnkasPhase) {
                $phaseJmlSoal = $this->jmlAnkas;
                $phaseKosong = $ankasKosong;
                $phaseNomor = $idSoal;
            } else {
                $sjtKosong = 0;
                for ($i = $this->jmlAnkas; $i < $this->jml_soal; $i++) {
                    if (($jawabanUser[$i] ?? '0') == '0') {
                        $sjtKosong++;
                    }
                }
                $phaseJmlSoal = $this->jml_soal - $this->jmlAnkas;
                $phaseKosong = $sjtKosong;
                $phaseNomor = $idSoal - $this->jmlAnkas;
            }
        } else {
            for ($i = 0; $i < $this->jml_soal; $i++) {
                if (($jawabanUser[$i] ?? '0') == '0') {
                    $phaseKosong++;
                }
            }
        }

        return [
            'is_ankas_phase' => $isAnkasPhase,
            'all_ankas_answered' => $allAnkasAnswered,
            'phase_jml_soal' => $phaseJmlSoal,
            'phase_kosong' => $phaseKosong,
            'phase_nomor' => $phaseNomor,
        ];
    }

    private function ankasTerakhirPromptPayload(array $jawabanUser): array
    {
        $ankasKosong = 0;
        for ($i = 0; $i < $this->jmlAnkas; $i++) {
            if (($jawabanUser[$i] ?? '0') == '0') {
                $ankasKosong++;
            }
        }

        return [
            'prompt_ankas_terakhir' => true,
            'semua_terjawab' => $ankasKosong === 0,
            'jawaban_kosong' => $ankasKosong,
            'soal_belum_dijawab' => $this->indeksPertamaAnkasBelumTerjawabFrom($jawabanUser),
        ];
    }

    private function indeksPertamaBelumDijawabDalamFase(array $jawabanUser, int $idSoal): int
    {
        if ($this->isLevel34 && $this->jmlAnkas > 0 && $idSoal > $this->jmlAnkas) {
            for ($i = $this->jmlAnkas; $i < $this->jml_soal; $i++) {
                if (($jawabanUser[$i] ?? '0') == '0') {
                    return $i + 1;
                }
            }

            return $this->jmlAnkas + 1;
        }

        foreach ($jawabanUser as $idx => $jawaban) {
            if ((string) $jawaban === '0' || $jawaban === '' || $jawaban === null) {
                return $idx + 1;
            }
        }

        return 1;
    }

    private function indeksPertamaAnkasBelumTerjawabFrom(array $jawabanUser): int
    {
        for ($i = 0; $i < $this->jmlAnkas; $i++) {
            if (($jawabanUser[$i] ?? '0') == '0') {
                return $i + 1;
            }
        }

        return max(1, $this->jmlAnkas);
    }

    private function recalculateScores(UjianPspk $data, array $soal_id, array $jawaban_user): void
    {
        $skor_aspek = $data->skor_aspek ?? [];
        $aspek_list = RefAspekPspk::pluck('kode_aspek')->toArray();
        foreach ($aspek_list as $a) {
            if (! isset($skor_aspek[$a])) {
                $skor_aspek[$a] = 0;
            }
        }

        $updated_skor = array_fill_keys($aspek_list, 0);
        $metode_tes_id = (int) auth()->guard('peserta')->user()->event->metode_tes_id;

        if ($metode_tes_id === 5) {
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
        } elseif ($metode_tes_id === 6) {
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
        } elseif (in_array($metode_tes_id, [7, 8], true)) {
            foreach ($soal_id as $i => $sid) {
                $jawaban = $jawaban_user[$i] ?? null;
                $soal = SoalPspk::find($sid);

                if ($soal && $jawaban && $jawaban != '0') {
                    $aspek_kode = $soal->aspek->kode_aspek ?? 'Tidak Diketahui';
                    if (! isset($updated_skor[$aspek_kode])) {
                        $updated_skor[$aspek_kode] = 0;
                    }

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

        foreach ($updated_skor as $key => $val) {
            $skor_aspek[$key] = $val;
        }

        $data->skor_aspek = $skor_aspek;
        $data->nilai_total = array_sum($updated_skor);
        $data->save();
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

            app(PspkFinishUjianService::class)->finish(
                $data,
                (int) auth()->guard('peserta')->user()->event->metode_tes_id
            );

            $this->dispatch('clear-flags-browser');

            return $this->redirect(route('peserta.tes-pspk.hasil'));
        } catch (\Throwable $th) {
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
        return $this->indeksPertamaAnkasBelumTerjawabFrom($this->jawaban_user);
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
}
