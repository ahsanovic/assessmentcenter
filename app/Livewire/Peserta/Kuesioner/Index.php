<?php

namespace App\Livewire\Peserta\Kuesioner;

use App\Models\JawabanResponden;
use App\Models\Kuesioner;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.peserta.app', ['title' => 'Kuesioner'])]
class Index extends Component
{
    public $kuesioner;
    public $jawaban_responden = [];
    public $jawaban_esai = [];
    public $konfirmasi = false;

    public function mount()
    {
        $this->kuesioner = Kuesioner::where('is_active', 't')->get();
        $jawaban = JawabanResponden::where('event_id', Auth::user()->event_id)
            ->where('peserta_id', Auth::user()->id)
            ->first();

        if ($jawaban) {
            $kuesioner_id = explode(',', $jawaban->kuesioner_id);
            $skor = explode(',', $jawaban->skor);
            foreach ($this->kuesioner as $item) {
                if (in_array($item->id, $kuesioner_id)) {
                    $this->jawaban_responden[$item->id]['skor'] = $skor[array_search($item->id, $kuesioner_id)];
                }
            }

            if (!empty($jawaban->jawaban_esai)) {
                foreach ($this->kuesioner as $item) {
                    if ($item->is_esai === 't') {
                        $this->jawaban_responden[$item->id]['jawaban_esai'] = $jawaban->jawaban_esai;
                        break;
                    }
                }
            }
        }
    }

    public function render()
    {
        return view('livewire.peserta.kuesioner.index');
    }

    public function setRating($kuesionerId, $skor)
    {
        $this->jawaban_responden[$kuesionerId]['skor'] = (int) $skor;
        $this->resetErrorBag("jawaban_responden.{$kuesionerId}.skor");
    }

    public function submit()
    {
        $rules = [
            'konfirmasi' => 'accepted',
        ];
        $messages = [
            'konfirmasi.accepted' => 'Anda harus mengonfirmasi bahwa semua pertanyaan telah diisi dengan jujur.',
        ];

        foreach ($this->kuesioner as $item) {
            if ($item->is_esai === 'f') {
                $rules["jawaban_responden.{$item->id}.skor"] = 'required|integer|min:1|max:5';
                $messages["jawaban_responden.{$item->id}.skor.required"] = 'Penilaian bintang wajib diisi.';
                $messages["jawaban_responden.{$item->id}.skor.min"] = 'Penilaian bintang wajib diisi.';
                $messages["jawaban_responden.{$item->id}.skor.max"] = 'Penilaian maksimal 5 bintang.';
            } else {
                $rules["jawaban_responden.{$item->id}.jawaban_esai"] = 'required|string|min:1';
                $messages["jawaban_responden.{$item->id}.jawaban_esai.required"] = 'Jawaban esai wajib diisi.';
            }
        }

        $this->validate($rules, $messages);

        try {
            $kuesioner_id = [];
            $skor = [];

            foreach ($this->kuesioner as $item) {
                if ($item->is_esai === 'f') {
                    $kuesioner_id[] = $item->id;
                    $skor[] = $this->jawaban_responden[$item->id]['skor'];
                }
            }

            $jawaban_esai = null;
            foreach ($this->kuesioner as $item) {
                if ($item->is_esai === 't' && !empty($this->jawaban_responden[$item->id]['jawaban_esai'])) {
                    $jawaban_esai = $this->jawaban_responden[$item->id]['jawaban_esai'];
                    break;
                }
            }

            JawabanResponden::updateOrCreate(
                [
                    'event_id' => Auth::user()->event_id,
                    'peserta_id' => Auth::user()->id,
                ],
                [
                    'kuesioner_id' => implode(',', $kuesioner_id),
                    'skor' => implode(',', $skor),
                    'jawaban_esai' => $jawaban_esai,
                ]
            );

            session()->flash('toast', [
                'type' => 'success',
                'message' => 'berhasil mengirimkan kuesioner'
            ]);

            $this->redirect(route('peserta.tes-potensi.hasil-nilai'), true);
        } catch (\Throwable $th) {
            session()->flash('toast', [
                'type' => 'error',
                'message' => 'Gagal mengirimkan kuesioner'
            ]);
        }
    }
}
