<?php

namespace App\Livewire\Admin\HasilResponden;

use App\Models\Event;
use App\Models\Kuesioner;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Rap2hpoutre\FastExcel\FastExcel;

#[Layout('components.layouts.admin.app', ['title' => 'Hasil Responden'])]
class Show extends Component
{
    use WithPagination;

    public $event;
    public $id_event;
    public $pertanyaan = [];

    #[Url(as: 'q')]
    public ?string $search =  '';

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->reset(['search']);
        $this->resetPage();
        $this->render();
    }

    public function mount($idEvent)
    {
        $this->id_event = $idEvent;
        $this->event = Event::with('peserta')->findOrFail($this->id_event);
        $this->pertanyaan = Kuesioner::pluck('deskripsi', 'id')->toArray();
    }

    public function downloadExcel()
    {
        $peserta = $this->event->peserta()->with('jawabanResponden')->get();
        $skorLabel = [
            1 => 'Sangat Tidak Setuju',
            2 => 'Tidak Setuju',
            3 => 'Netral',
            4 => 'Setuju',
            5 => 'Sangat Setuju',
        ];

        $exportData = [];
        $no = 1;

        foreach ($peserta as $item) {
            $jawaban = $item->jawabanResponden->first();
            $kuesioner_ids = explode(',', $jawaban->kuesioner_id ?? '');
            $skors = explode(',', $jawaban->skor ?? '');

            $jawabanText = [];
            foreach ($kuesioner_ids as $i => $id) {
                $pertanyaan = $this->pertanyaan[$id] ?? '-';
                $skor = $skors[$i] ?? '-';
                $skorText = $skorLabel[$skor] ?? '-';
                $jawabanText[] = "Pertanyaan: {$pertanyaan} | Skor: {$skorText}";
            }

            $exportData[] = [
                'No' => $no++,
                'Nama Peserta' => $item->nama,
                'Jawaban Responden' => implode("\n", $jawabanText),
                'Kritik & Saran' => $jawaban->jawaban_esai ?? '-',
            ];
        }

        $filename = 'jawaban-responden-' . str_replace(' ', '-', strtolower($this->event->name ?? 'event')) . '-' . date('Y-m-d') . '.xlsx';

        return (new FastExcel(collect($exportData)))->download($filename);
    }

    public function render()
    {
        $data = $this->event->peserta()
            ->with('jawabanResponden')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('nama', 'like', '%' . $this->search . '%');
                });
            })
            ->paginate(5);

        return view('livewire.admin.hasil-responden.show', [
            'data' => $data,
            'pertanyaan' => $this->pertanyaan
        ]);
    }
}
