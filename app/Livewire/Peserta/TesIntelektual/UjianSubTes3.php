<?php

namespace App\Livewire\Peserta\TesIntelektual;

use App\Models\Intelektual\HasilIntelektual;
use App\Models\Intelektual\RefIntelektual;
use App\Models\Intelektual\SoalIntelektual;
use App\Models\Intelektual\UjianIntelektualSubTes3;
use App\Traits\PelanggaranTrait;
use App\Traits\TimerTrait;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.peserta.app', ['title' => 'Sub Tes 3'])]
class UjianSubTes3 extends Component
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

        $data = UjianIntelektualSubTes3::select('id', 'soal_id', 'jawaban', 'created_at')
            ->where('peserta_id', Auth::guard('peserta')->user()->id)
            ->where('event_id', Auth::guard('peserta')->user()->event_id)
            ->first();

        if (!$data) {
            session()->flash('toast', [
                'type' => 'error',
                'message' => 'Data ujian tidak ditemukan. Silakan mulai tes terlebih dahulu.'
            ]);
            return $this->redirect(route('peserta.tes-intelektual.home'), navigate: true);
        }

        $this->nomor_soal = explode(',', $data->soal_id);
        $this->jawaban_user = explode(',', $data->jawaban);
        $this->soal = SoalIntelektual::find($this->nomor_soal[$this->id_soal - 1]);
        $this->jml_soal = SoalIntelektual::where('sub_tes', 3)->count();
        $this->id_ujian = $data->id;
        $this->timerTest('Intelektual Sub Tes 3');

        for ($i = 0, $j = 0; $i < $this->jml_soal; $i++) {
            if ($this->jawaban_user[$i] == '0') {
                $j = $j + 1;
                $this->jawaban_kosong = $j;
            }
        }
    }

    public function render()
    {
        return view('livewire.peserta.tes-intelektual.subtes-3.ujian', [
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
        $data = UjianIntelektualSubTes3::where('peserta_id', Auth::guard('peserta')->user()->id)
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

        // hitung nilai
        $total_skor = 0;
        for ($i = 1; $i <= $this->jml_soal; $i++) {
            $idx = $i - 1;
            $jawaban = $jawaban_user[$idx] ?? null;
            if ($jawaban && isset($soal_id[$idx])) {
                $soal = SoalIntelektual::find($soal_id[$idx]);
                if ($soal && $soal->kunci_jawaban == $jawaban) {
                    $total_skor += 1;
                }
            }
        }
        $data->nilai = $total_skor;
        $data->save();

        if ($nomor_soal < $this->jml_soal) {
            $this->redirect(route('peserta.tes-intelektual.subtes3', ['id' => $nomor_soal + 1]), true);
        } else if ($nomor_soal == $this->jml_soal) {
            $this->redirect(route('peserta.tes-intelektual.subtes3', ['id' => $nomor_soal]), true);
        }
    }

    public function navigate($id)
    {
        if ($id >= 1 && $id <= $this->jml_soal) {
            $this->id_soal = $id;
            $this->soal = SoalIntelektual::find($this->nomor_soal[$id - 1]);
            $this->redirect(route('peserta.tes-intelektual.subtes3', ['id' => $id]), true);
        }
    }

    public function finish()
    {
        try {
            $data = UjianIntelektualSubTes3::findOrFail($this->id_ujian);

            // hitung nilai total
            $nilai_subtes_1 = HasilIntelektual::where('event_id', Auth::guard('peserta')->user()->event_id)
                ->where('peserta_id', Auth::guard('peserta')->user()->id)
                ->value('nilai_subtes_1') ?? 0;

            $nilai_subtes_2 = HasilIntelektual::where('event_id', Auth::guard('peserta')->user()->event_id)
                ->where('peserta_id', Auth::guard('peserta')->user()->id)
                ->value('nilai_subtes_2') ?? 0;

            $nilai_subtes_3 = $data->nilai;

            $nilai_total = $nilai_subtes_1 + $nilai_subtes_2 + $nilai_subtes_3;

            // hitung level dan kategori
            $kategori_level = $this->_getKategoriLevel($nilai_total);

            // get deskripsi potensi
            $deskripsi_potensi_subtes_1 = $this->_getDeskripsiPotensi(1, $kategori_level['level']);
            $deskripsi_potensi_subtes_2 = $this->_getDeskripsiPotensi(2, $kategori_level['level']);
            $deskripsi_potensi_subtes_3 = $this->_getDeskripsiPotensi(3, $kategori_level['level']);

            HasilIntelektual::updateOrCreate(
                [
                    'event_id' => Auth::guard('peserta')->user()->event_id,
                    'peserta_id' => Auth::guard('peserta')->user()->id,
                ],
                [
                    'nilai_subtes_3' => $data->nilai,
                    'nilai_total' => $nilai_total,
                    'kategori' => $kategori_level['kategori'],
                    'level' => $kategori_level['level'],
                    'uraian_potensi_subtes_1' => $deskripsi_potensi_subtes_1,
                    'uraian_potensi_subtes_2' => $deskripsi_potensi_subtes_2,
                    'uraian_potensi_subtes_3' => $deskripsi_potensi_subtes_3,
                ]
            );

            // change status ujian to true (finish)
            $data->is_finished = true;
            $data->save();

            return $this->redirect(route('peserta.tes-intelektual.home'));
        } catch (\Throwable $th) {
            //throw $th;
            session()->flash('toast', [
                'type' => 'error',
                'message' => 'Terjadi kesalahan'
            ]);
        }
    }

    private function _getKategoriLevel($nilai)
    {
        $level_map = [
            [0, 6, 'Sangat Kurang', 1],
            [7, 13, 'Kurang', 2],
            [14, 26, 'Cukup', 3],
            [27, 32, 'Baik', 4],
            [33, 39, 'Sangat Baik', 5],
        ];

        foreach ($level_map as [$min, $max, $kategori, $level]) {
            if ($nilai >= $min && $nilai <= $max) {
                return ['kategori' => $kategori, 'level' => $level];
            }
        }
    }

    private function _getDeskripsiPotensi($subtes, $level)
    {
        $ref = RefIntelektual::where('sub_tes', $subtes)->first();
        if ($level == 1) {
            $deskripsi = collect($ref->kualifikasi)->firstWhere('kualifikasi', 'Sangat Kurang');
            $uraian_potensi = $deskripsi['uraian_potensi'] ?? null;
            return $uraian_potensi;
        } else if ($level == 2) {
            $deskripsi = collect($ref->kualifikasi)->firstWhere('kualifikasi', 'Kurang');
            $uraian_potensi = $deskripsi['uraian_potensi'] ?? null;
            return $uraian_potensi;
        } else if ($level == 3) {
            $deskripsi = collect($ref->kualifikasi)->firstWhere('kualifikasi', 'Cukup');
            $uraian_potensi = $deskripsi['uraian_potensi'] ?? null;
            return $uraian_potensi;
        } else if ($level == 4) {
            $deskripsi = collect($ref->kualifikasi)->firstWhere('kualifikasi', 'Baik');
            $uraian_potensi = $deskripsi['uraian_potensi'] ?? null;
            return $uraian_potensi;
        } else if ($level == 5) {
            $deskripsi = collect($ref->kualifikasi)->firstWhere('kualifikasi', 'Sangat Baik');
            $uraian_potensi = $deskripsi['uraian_potensi'] ?? null;
            return $uraian_potensi;
        } else {
            return null;
        }
    }
}
