<?php

namespace App\Livewire\Admin\HasilResponden;

use App\Models\Event;
use App\Models\JawabanResponden;
use App\Models\Kuesioner;
use Illuminate\Database\Eloquent\Builder;
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
        $this->event = Event::findOrFail($this->id_event);

        if ((int) $this->event->metode_tes_id !== Event::METODE_KUESIONER_RESPONDEN) {
            abort(404);
        }

        $this->pertanyaan = Kuesioner::pluck('deskripsi', 'id')->toArray();
    }

    public function downloadExcel()
    {
        $peserta = $this->jawabanRespondenQuery()->get();
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
            $kuesioner_ids = explode(',', $item->kuesioner_id ?? '');
            $skors = explode(',', $item->skor ?? '');

            $jawabanText = [];
            foreach ($kuesioner_ids as $i => $id) {
                $pertanyaan = $this->pertanyaan[$id] ?? '-';
                $skor = $skors[$i] ?? '-';
                $skorText = $skorLabel[$skor] ?? '-';
                $jawabanText[] = "Pertanyaan: {$pertanyaan} | Skor: {$skorText}";
            }

            $exportData[] = [
                'No' => $no++,
                'Nama Peserta' => $item->peserta->nama ?? '-',
                'Jawaban Responden' => implode("\n", $jawabanText),
                'Kritik & Saran' => $item->jawaban_esai ?? '-',
            ];
        }

        $filename = 'jawaban-responden-' . str_replace(' ', '-', strtolower($this->event->nama_event ?? 'event')) . '-' . date('Y-m-d') . '.xlsx';

        return (new FastExcel(collect($exportData)))->download($filename);
    }

    public function render()
    {
        $data = $this->jawabanRespondenQuery()->paginate(10);

        return view('livewire.admin.hasil-responden.show', [
            'data' => $data,
            'pertanyaan' => $this->pertanyaan
        ]);
    }

    /**
     * Daftar diisi dari jawaban event ini, bukan dari peserta.event_id.
     * Saat peserta login lewat event lain dalam grup yang sama, jawaban tetap
     * merekam event tes yang dikerjakan, tetapi peserta_id bisa menunjuk baris partisipasi lain.
     */
    private function jawabanRespondenQuery(): Builder
    {
        return JawabanResponden::query()
            ->where('event_id', $this->id_event)
            ->with('peserta')
            ->when($this->search, function (Builder $query) {
                $query->whereHas('peserta', function (Builder $peserta) {
                    $peserta->where('nama', 'like', '%'.$this->search.'%');
                });
            })
            ->orderBy('id');
    }
}
