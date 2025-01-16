<?php

namespace App\Livewire\Peserta\Portofolio;

use App\Models\JawabanPenilaian;
use App\Models\RefPertanyaanPenilaian;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.peserta.app', ['title' => 'Portofolio'])]
class Penilaian extends Component
{
    public $jawaban = [];
    public $pertanyaan;
    public $value;

    public function mount()
    {
        $this->pertanyaan = RefPertanyaanPenilaian::orderBy('urutan', 'asc')->get();

        // Ambil data jawaban pengguna saat ini
        $jawabanData = JawabanPenilaian::wherePesertaEvent(
            Auth::guard('peserta')->user()->id,
            Auth::guard('peserta')->user()->event_id
        )
            ->get();

        // Isi jawaban ke properti $this->jawaban sesuai pertanyaan
        $this->jawaban = $this->pertanyaan->map(function ($pertanyaan) use ($jawabanData) {
            $jawaban = $jawabanData->firstWhere('pertanyaan_id', $pertanyaan->id);
            return $jawaban ? $jawaban->jawaban : '';
        })->toArray();
    }

    public function render()
    {
        return view('livewire.peserta.portofolio._partials.penilaian.index', [
            'pertanyaan' => $this->pertanyaan,
        ]);
    }

    public function save()
    {
        foreach ($this->jawaban as $value) {
            $trimmed_value = trim($value);
            if ($trimmed_value == '<p><br></p>' || $trimmed_value == '') {
                $this->dispatch('toast', [
                    'type' => 'error',
                    'message' => 'jawaban wajib diisi semua'
                ]);
                return;
            }
        }

        try {
            foreach ($this->jawaban as $index => $content) {
                if (!isset($this->pertanyaan[$index])) continue;
                $pertanyaan_id = $this->pertanyaan[$index]->id;

                // Update atau simpan jawaban peserta
                JawabanPenilaian::updateOrCreate(
                    [
                        'event_id' => Auth::guard('peserta')->user()->event_id,
                        'peserta_id' => Auth::guard('peserta')->user()->id,
                        'pertanyaan_id' => $pertanyaan_id,
                    ],
                    [
                        'jawaban' => $content,
                    ]
                );
            }

            $this->dispatch('toast', [
                'type' => 'success',
                'message' => 'berhasil update data'
            ]);
        } catch (\Throwable $th) {
            // throw $th;
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'gagal update data'
            ]);
        }
    }
}
