<?php

namespace App\Livewire\Admin\HasilResponden;

use App\Models\Event;
use App\Models\JawabanResponden;
use App\Models\Kuesioner;
use App\Models\RefMetodeTes;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Rap2hpoutre\FastExcel\FastExcel;

#[Layout('components.layouts.admin.app', ['title' => 'Hasil Responden'])]
class Index extends Component
{
    use WithPagination;

    public $selected_id;

    #[Url(as: 'q')]
    public ?string $search = '';
    public string $downloadPeriod = '1m';

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $totalQuery = JawabanResponden::query()
            ->join('event', 'event.id', '=', 'jawaban_responden.event_id')
            ->where('event.metode_tes_id', Event::METODE_KUESIONER_RESPONDEN);

        if ($this->search !== '') {
            $totalQuery->where('event.nama_event', 'like', '%' . $this->search . '%');
        }

        $totalResponden = $totalQuery->count();

        $metodeKuesionerLabel = RefMetodeTes::query()
            ->whereKey(Event::METODE_KUESIONER_RESPONDEN)
            ->value('metode_tes')
            ?: 'Lainnya';

        $data = Event::query()
            ->with(['metodeTes'])
            ->withCount('jawabanResponden')
            ->where('metode_tes_id', Event::METODE_KUESIONER_RESPONDEN)
            ->whereHas('jawabanResponden')
            ->when($this->search, function ($query) {
                $query->where('nama_event', 'like', '%' . $this->search . '%');
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $downloadSince = $this->resolveDownloadStartDate();
        $downloadPreviewCount = $this->jawabanExportBaseQuery($downloadSince)->count();
        $downloadSinceLabel = $downloadSince->locale('id')->translatedFormat('d F Y');

        return view('livewire.admin.hasil-responden.index', compact(
            'data',
            'totalResponden',
            'metodeKuesionerLabel',
            'downloadPreviewCount',
            'downloadSinceLabel'
        ));
    }

    public function downloadKuesioner()
    {
        $periodLabels = [
            '1m' => '1-bulan',
            '3m' => '3-bulan',
            '6m' => '6-bulan',
            '1y' => '1-tahun',
        ];

        $startDate = $this->resolveDownloadStartDate();
        if ($this->jawabanExportBaseQuery($startDate)->doesntExist()) {
            $this->addError('downloadKuesioner', 'Tidak ada jawaban kuesioner pada periode yang dipilih.');

            return null;
        }

        $pertanyaan = Kuesioner::pluck('deskripsi', 'id')->toArray();
        $skorLabel = [
            1 => 'Sangat Tidak Setuju',
            2 => 'Tidak Setuju',
            3 => 'Netral',
            4 => 'Setuju',
            5 => 'Sangat Setuju',
        ];

        $rows = $this->jawabanExportBaseQuery($startDate)
            ->orderByDesc('jawaban_responden.created_at')
            ->get([
                'jawaban_responden.kuesioner_id',
                'jawaban_responden.skor',
                'jawaban_responden.jawaban_esai',
                'jawaban_responden.created_at',
                'event.nama_event',
                'peserta.nama as nama_peserta',
            ]);

        $exportData = $rows->values()->map(function ($item, $index) use ($pertanyaan, $skorLabel) {
            $kuesionerIds = array_filter(explode(',', (string) $item->kuesioner_id));
            $skors = explode(',', (string) $item->skor);
            $detailJawaban = [];

            foreach ($kuesionerIds as $i => $id) {
                $pertanyaanText = $pertanyaan[$id] ?? '-';
                $skor = isset($skors[$i]) ? (int) $skors[$i] : 0;
                $detailJawaban[] = 'Pertanyaan: ' . $pertanyaanText . ' | Skor: ' . ($skorLabel[$skor] ?? '-');
            }

            return [
                'No' => $index + 1,
                'Nama Event' => $item->nama_event ?? '-',
                'Nama Peserta' => $item->nama_peserta ?? '-',
                'Jawaban Kuesioner' => implode("\n", $detailJawaban),
                'Kritik & Saran' => $item->jawaban_esai ?? '-',
                'Waktu Isi' => Carbon::parse($item->created_at)->format('d-m-Y H:i'),
            ];
        });

        $periodLabel = $periodLabels[$this->downloadPeriod] ?? '1-bulan';
        $filename = 'kuesioner-responden-' . $periodLabel . '-' . now()->format('Y-m-d') . '.xlsx';

        return (new FastExcel($exportData))->download($filename);
    }

    private function jawabanExportBaseQuery(?Carbon $from = null): Builder
    {
        $from ??= $this->resolveDownloadStartDate();

        return JawabanResponden::query()
            ->join('event', 'event.id', '=', 'jawaban_responden.event_id')
            ->leftJoin('peserta', 'peserta.id', '=', 'jawaban_responden.peserta_id')
            ->where('event.metode_tes_id', Event::METODE_KUESIONER_RESPONDEN)
            ->where('jawaban_responden.created_at', '>=', $from);
    }

    private function resolveDownloadStartDate(): Carbon
    {
        return match ($this->downloadPeriod) {
            '3m' => now()->subMonthsNoOverflow(3)->startOfDay(),
            '6m' => now()->subMonthsNoOverflow(6)->startOfDay(),
            '1y' => now()->subYear()->startOfDay(),
            default => now()->subMonthNoOverflow()->startOfDay(),
        };
    }

    public function resetFilters()
    {
        $this->reset(['search', 'downloadPeriod']);
        $this->downloadPeriod = '1m';
        $this->resetPage();
        $this->render();
    }
}
