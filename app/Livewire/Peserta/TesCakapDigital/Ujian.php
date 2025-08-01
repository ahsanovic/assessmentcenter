<?php

namespace App\Livewire\Peserta\TesCakapDigital;

use App\Models\CakapDigital\HasilCakapDigital;
use App\Models\CakapDigital\SoalCakapDigital;
use App\Models\CakapDigital\UjianCakapDigital;
use App\Traits\PelanggaranTrait;
use App\Traits\TimerTrait;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.peserta.app', ['title' => 'Tes Cakap Digital'])]
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
    public $current_sequence;

    public function mount($id)
    {
        $this->id_soal = $id;

        $data = UjianCakapDigital::select('id', 'soal_id', 'jawaban', 'created_at')
            ->where('peserta_id', Auth::guard('peserta')->user()->id)
            ->where('event_id', Auth::guard('peserta')->user()->event_id)
            ->first();

        if (!$data) {
            session()->flash('toast', [
                'type' => 'error',
                'message' => 'Data ujian tidak ditemukan. Silakan mulai tes terlebih dahulu.'
            ]);
            return $this->redirect(route('peserta.tes-cakap-digital.home'), navigate: true);
        }

        $this->nomor_soal = explode(',', $data->soal_id);
        $this->jawaban_user = explode(',', $data->jawaban);
        $this->soal = SoalCakapDigital::find($this->nomor_soal[$this->id_soal - 1]);
        $this->jml_soal = SoalCakapDigital::count();
        $this->id_ujian = $data->id;
        $this->timerTest('Cakap Digital');

        for ($i = 0, $j = 0; $i < $this->jml_soal; $i++) {
            if ($this->jawaban_user[$i] == '0') {
                $j = $j + 1;
                $this->jawaban_kosong = $j;
            }
        }
    }

    public function render()
    {
        return view('livewire.peserta.tes-cakap-digital.ujian', [
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
        $data = UjianCakapDigital::where('peserta_id', Auth::guard('peserta')->user()->id)
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

        // Cek dan hitung skor literasi digital
        if ($nomor_soal >= 1 && $nomor_soal <= 60) {
            $total_skor = 0;
            for ($i = 1; $i <= 60; $i++) {
                $idx = $i - 1;
                $jawaban = $jawaban_user[$idx] ?? null;
                if ($jawaban && isset($soal_id[$idx])) {
                    $soal = SoalCakapDigital::find($soal_id[$idx]);
                    if ($soal && $soal->kunci_jawaban == $jawaban) {
                        $total_skor += 1;
                    }
                }
            }
            $data->nilai_literasi = $total_skor;
            $data->save();
        }

        // Cek dan hitung skor emerging skill
        if ($nomor_soal >= 61 && $nomor_soal <= 120) {
            $total_skor = 0;
            for ($i = 61; $i <= 120; $i++) {
                $idx = $i - 1;
                $jawaban = $jawaban_user[$idx] ?? null;
                if ($jawaban && isset($soal_id[$idx])) {
                    $soal = SoalCakapDigital::find($soal_id[$idx]);
                    if ($soal && $soal->kunci_jawaban == $jawaban) {
                        $total_skor += 1;
                    }
                }
            }
            $data->nilai_emerging = $total_skor;
            $data->save();
        }

        if ($nomor_soal < $this->jml_soal) {
            $this->redirect(route('peserta.tes-cakap-digital.ujian', ['id' => $nomor_soal + 1]), true);
        } else if ($nomor_soal == $this->jml_soal) {
            $this->redirect(route('peserta.tes-cakap-digital.ujian', ['id' => $nomor_soal]), true);
        }
    }

    public function navigate($id)
    {
        if ($id >= 1 && $id <= $this->jml_soal) {
            $this->id_soal = $id;
            $this->soal = SoalCakapDigital::find($this->nomor_soal[$id - 1]);
            $this->redirect(route('peserta.tes-cakap-digital.ujian', ['id' => $id]), true);
        }
    }

    public function finish()
    {
        try {
            $data = UjianCakapDigital::findOrFail($this->id_ujian);

            // scoring kategori
            $kategori_literasi = $this->_getKategori($data->nilai_literasi);
            $kategori_emerging = $this->_getKategori($data->nilai_emerging);

            HasilCakapDigital::updateOrCreate(
                [
                    'event_id' => Auth::guard('peserta')->user()->event_id,
                    'peserta_id' => Auth::guard('peserta')->user()->id,
                    'ujian_id' => $data->id,
                ],
                [
                    'nilai_literasi' => $data->nilai_literasi,
                    'kategori_literasi' => $kategori_literasi,
                    'nilai_emerging' => $data->nilai_emerging,
                    'kategori_emerging' => $kategori_emerging,
                ]
            );

            // change status ujian to true (finish)
            $data->is_finished = true;
            $data->save();

            return $this->redirect(route('peserta.tes-cakap-digital.hasil'), navigate: true);
        } catch (\Throwable $th) {
            //throw $th;
            session()->flash('toast', [
                'type' => 'error',
                'message' => 'Terjadi kesalahan'
            ]);
        }
    }

    private function _getKategori($nilai)
    {
        $kategori_map = [
            [0, 4, '1-'],
            [5, 8, '1'],
            [9, 12, '1+'],
            [13, 16, '2-'],
            [17, 20, '2'],
            [21, 24, '2+'],
            [25, 28, '3-'],
            [29, 32, '3'],
            [33, 36, '3+'],
            [37, 40, '4-'],
            [41, 44, '4'],
            [45, 48, '4+'],
            [49, 52, '5-'],
            [53, 56, '5'],
            [57, 60, '5+'],
        ];

        foreach ($kategori_map as [$min, $max, $label]) {
            if ($nilai >= $min && $nilai <= $max) {
                return $label;
            }
        }

        return null;
    }
}
