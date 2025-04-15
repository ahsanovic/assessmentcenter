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

    public function mount()
    {
        $this->kuesioner = Kuesioner::where('is_active', 't')->get();
    }

    public function render()
    {
        return view('livewire.peserta.kuesioner.index');
    }

    public function submit()
    {
        try {
            $kuesioner_id = [];
            $skor = [];

            foreach ($this->kuesioner as $item) {
                if ($item->is_esai === 'f') {
                    $kuesioner_id[] = $item->id;
                    $skor[] = $this->jawaban_responden[$item->id]['skor'] ?? 0;
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

            $this->redirect(route('peserta.tes-potensi'), true);
        } catch (\Throwable $th) {
            throw $th;
            session()->flash('toast', [
                'type' => 'error',
                'message' => 'Gagal mengirimkan kuesioner'
            ]);
        }
    }
}
