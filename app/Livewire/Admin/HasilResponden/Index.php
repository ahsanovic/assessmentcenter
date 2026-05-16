<?php

namespace App\Livewire\Admin\HasilResponden;

use App\Models\Event;
use App\Models\JawabanResponden;
use App\Models\Kuesioner;
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

    /** Format d-m-Y; diisi dari flatpickr range */
    public ?string $downloadDari = null;

    /** Format d-m-Y */
    public ?string $downloadSampai = null;

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedDownloadDari(): void
    {
        $this->resetValidation('downloadKuesioner');
    }

    public function updatedDownloadSampai(): void
    {
        $this->resetValidation('downloadKuesioner');
    }

    public function render()
    {
        $totalQuery = JawabanResponden::query()
            ->join('event', 'event.id', '=', 'jawaban_responden.event_id')
            ->where('event.metode_tes_id', Event::METODE_KUESIONER_RESPONDEN);

        if ($this->search !== '') {
            $totalQuery->where('event.nama_event', 'like', '%'.$this->search.'%');
        }

        $totalResponden = $totalQuery->count();

        $data = Event::query()
            ->with(['metodeTes'])
            ->withCount('jawabanResponden')
            ->where('metode_tes_id', Event::METODE_KUESIONER_RESPONDEN)
            ->whereHas('jawabanResponden')
            ->when($this->search, function ($query) {
                $query->where('nama_event', 'like', '%'.$this->search.'%');
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $range = $this->resolveExportDateRange();
        $downloadPreviewCount = $range
            ? $this->jawabanExportBaseQuery()->count()
            : 0;
        $downloadReady = $range !== null && $downloadPreviewCount > 0;
        $activeDownloadPreset = $this->detectActiveDownloadPreset($range);
        $downloadStatusMessage = null;
        $downloadRangeLabel = null;
        if ($range) {
            [$from, $to] = $range;
            $downloadRangeLabel = $from->locale('id')->translatedFormat('d M Y')
                .' s.d. '
                .$to->locale('id')->translatedFormat('d M Y');
            $downloadStatusMessage = $downloadPreviewCount > 0
                ? number_format($downloadPreviewCount, 0, ',', '.').' jawaban siap diunduh'
                : 'Tidak ada data pada rentang ini';
        } else {
            $downloadStatusMessage = 'Pilih rentang tanggal untuk melihat data unduhan';
        }

        return view('livewire.admin.hasil-responden.index', compact(
            'data',
            'totalResponden',
            'downloadPreviewCount',
            'downloadRangeLabel',
            'downloadReady',
            'downloadStatusMessage',
            'activeDownloadPreset'
        ));
    }

    public function downloadKuesioner()
    {
        if (! $this->resolveExportDateRange()) {
            $this->addError('downloadKuesioner', 'Pilih rentang tanggal unduhan terlebih dahulu.');

            return null;
        }

        if ($this->jawabanExportBaseQuery()->doesntExist()) {
            $this->addError('downloadKuesioner', 'Tidak ada jawaban kuesioner pada rentang tanggal yang dipilih.');

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

        $rows = $this->jawabanExportBaseQuery()
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
                $detailJawaban[] = 'Pertanyaan: '.$pertanyaanText.' | Skor: '.($skorLabel[$skor] ?? '-');
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

        [$fromExport, $toExport] = $this->resolveExportDateRange();
        $filename = 'kuesioner-responden-dari-'
            .$fromExport->format('Y-m-d')
            .'-sampai-'
            .$toExport->format('Y-m-d')
            .'.xlsx';

        return (new FastExcel($exportData))->download($filename);
    }

    public function applyDownloadRangePreset(string $preset): void
    {
        $today = now();

        [$from, $to] = match ($preset) {
            'today' => [$today->copy()->startOfDay(), $today->copy()->endOfDay()],
            '7d' => [$today->copy()->subDays(6)->startOfDay(), $today->copy()->endOfDay()],
            '30d' => [$today->copy()->subDays(29)->startOfDay(), $today->copy()->endOfDay()],
            'month' => [$today->copy()->startOfMonth()->startOfDay(), $today->copy()->endOfDay()],
            default => [null, null],
        };

        if (! $from || ! $to) {
            return;
        }

        $this->downloadDari = $from->format('d-m-Y');
        $this->downloadSampai = $to->format('d-m-Y');
        $this->resetValidation('downloadKuesioner');
        $this->dispatch('flatpickr-download-range-set', from: $this->downloadDari, to: $this->downloadSampai);
    }

    private function jawabanExportBaseQuery(): Builder
    {
        $q = JawabanResponden::query()
            ->join('event', 'event.id', '=', 'jawaban_responden.event_id')
            ->leftJoin('peserta', 'peserta.id', '=', 'jawaban_responden.peserta_id')
            ->where('event.metode_tes_id', Event::METODE_KUESIONER_RESPONDEN);

        $range = $this->resolveExportDateRange();
        if ($range) {
            [$from, $to] = $range;
            $q->whereBetween('jawaban_responden.created_at', [$from, $to]);
        } else {
            $q->whereRaw('0 = 1');
        }

        return $q;
    }

    /**
     * @return array{0: \Carbon\Carbon, 1: \Carbon\Carbon}|null
     */
    private function resolveExportDateRange(): ?array
    {
        if (blank($this->downloadDari) || blank($this->downloadSampai)) {
            return null;
        }

        try {
            $from = Carbon::createFromFormat('d-m-Y', $this->downloadDari)->startOfDay();
            $to = Carbon::createFromFormat('d-m-Y', $this->downloadSampai)->endOfDay();
        } catch (\Throwable) {
            return null;
        }

        if ($from->greaterThan($to)) {
            return [$to->copy()->startOfDay(), $from->copy()->endOfDay()];
        }

        return [$from, $to];
    }

    /**
     * @param  array{0: \Carbon\Carbon, 1: \Carbon\Carbon}|null  $range
     */
    private function detectActiveDownloadPreset(?array $range): ?string
    {
        if (! $range) {
            return null;
        }

        [$from, $to] = $range;
        $today = now();
        $presets = [
            'today' => [$today->copy()->startOfDay(), $today->copy()->endOfDay()],
            '7d' => [$today->copy()->subDays(6)->startOfDay(), $today->copy()->endOfDay()],
            '30d' => [$today->copy()->subDays(29)->startOfDay(), $today->copy()->endOfDay()],
            'month' => [$today->copy()->startOfMonth()->startOfDay(), $today->copy()->endOfDay()],
        ];

        foreach ($presets as $key => [$presetFrom, $presetTo]) {
            if ($from->equalTo($presetFrom) && $to->equalTo($presetTo)) {
                return $key;
            }
        }

        return null;
    }

    public function resetFilters()
    {
        $this->reset(['search', 'downloadDari', 'downloadSampai']);
        $this->resetValidation('downloadKuesioner');
        $this->resetPage();
        $this->dispatch('flatpickr-download-range-clear');
    }
}
