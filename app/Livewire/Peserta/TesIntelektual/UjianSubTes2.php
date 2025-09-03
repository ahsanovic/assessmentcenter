<?php

namespace App\Livewire\Peserta\TesIntelektual;

use App\Models\Intelektual\HasilIntelektual;
use App\Models\Intelektual\SoalIntelektual;
use App\Models\Intelektual\UjianIntelektualSubTes2;
use App\Traits\PelanggaranTrait;
use App\Traits\TimerTrait;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.peserta.app', ['title' => 'Sub Tes 2'])]
class UjianSubTes2 extends Component
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

        $data = UjianIntelektualSubTes2::select('id', 'soal_id', 'jawaban', 'created_at')
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
        $this->jml_soal = SoalIntelektual::where('sub_tes', 2)->count();
        $this->id_ujian = $data->id;
        $this->timerTest('Intelektual Sub Tes 2');

        for ($i = 0, $j = 0; $i < $this->jml_soal; $i++) {
            if ($this->jawaban_user[$i] == '0') {
                $j = $j + 1;
                $this->jawaban_kosong = $j;
            }
        }
    }

    public function render()
    {
        return view('livewire.peserta.tes-intelektual.subtes-2.ujian', [
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
        $data = UjianIntelektualSubTes2::where('peserta_id', Auth::guard('peserta')->user()->id)
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
            $this->redirect(route('peserta.tes-intelektual.subtes2', ['id' => $nomor_soal + 1]), true);
        } else if ($nomor_soal == $this->jml_soal) {
            $this->redirect(route('peserta.tes-intelektual.subtes2', ['id' => $nomor_soal]), true);
        }
    }

    public function navigate($id)
    {
        if ($id >= 1 && $id <= $this->jml_soal) {
            $this->id_soal = $id;
            $this->soal = SoalIntelektual::find($this->nomor_soal[$id - 1]);
            $this->redirect(route('peserta.tes-intelektual.subtes2', ['id' => $id]), true);
        }
    }

    public function finish()
    {
        try {
            $data = UjianIntelektualSubTes2::findOrFail($this->id_ujian);

            HasilIntelektual::updateOrCreate(
                [
                    'event_id' => Auth::guard('peserta')->user()->event_id,
                    'peserta_id' => Auth::guard('peserta')->user()->id,
                ],
                [
                    'nilai_subtes_2' => $data->nilai,
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
}
