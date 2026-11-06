<?php

namespace App\Livewire\Admin\JawabanPeserta\TesPotensi;

use App\Models\Event;
use App\Services\TesPotensi\TesPotensiJawabanService;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use PhpOffice\PhpSpreadsheet\IOFactory;

#[Layout('components.layouts.admin.app', ['title' => 'Jawaban Peserta — Tes Potensi'])]
class Show extends Component
{
    use WithPagination;

    public Event $event;

    public int $id_event;

    #[Url(as: 'subtes')]
    public string $subtes = 'interpersonal';

    #[Url(as: 'q')]
    public ?string $search = '';

    public ?int $detailUjianId = null;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedSubtes(): void
    {
        if (! in_array($this->subtes, app(TesPotensiJawabanService::class)->subtestKeys(), true)) {
            $this->subtes = 'interpersonal';
        }

        $this->detailUjianId = null;
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset(['search']);
        $this->resetPage();
    }

    public function mount(int $idEvent): void
    {
        $this->id_event = $idEvent;
        $this->event = Event::with('metodeTes')->findOrFail($idEvent);

        $service = app(TesPotensiJawabanService::class);

        if (! $service->eventHasPotensiAnswers($this->event)) {
            abort(404);
        }

        if (! in_array($this->subtes, $service->subtestKeys(), true)) {
            $this->subtes = 'interpersonal';
        }
    }

    public function setSubtes(string $key): void
    {
        $service = app(TesPotensiJawabanService::class);
        if (! in_array($key, $service->subtestKeys(), true)) {
            return;
        }

        $this->subtes = $key;
        $this->detailUjianId = null;
        $this->resetPage();
    }

    public function bukaJawaban(int $ujianId): void
    {
        $this->detailUjianId = $ujianId;
    }

    public function tutupJawaban(): void
    {
        $this->detailUjianId = null;
    }

    public function downloadExcelSubtes()
    {
        return $this->streamSpreadsheet(false);
    }

    public function downloadExcelSemua()
    {
        return $this->streamSpreadsheet(true);
    }

    private function streamSpreadsheet(bool $allSubtests)
    {
        $service = app(TesPotensiJawabanService::class);
        $eventName = (string) ($this->event->nama_event ?? 'Event');

        if ($allSubtests) {
            $spreadsheet = $service->makeSpreadsheetForEvent($eventName, $this->id_event);
            if ($spreadsheet->getSheetCount() === 1 && $spreadsheet->getSheet(0)->getTitle() === 'Kosong') {
                $this->dispatch('toast', [
                    'type' => 'error',
                    'message' => 'Tidak ada data jawaban untuk diunduh.',
                ]);

                return null;
            }
            $filename = $service->filename($eventName);
        } else {
            $participantRows = $service->exportParticipantRows($this->id_event, $this->subtes);
            $analysisRows = $service->exportItemAnalysisRows($this->id_event, $this->subtes);

            if ($participantRows === [] && $analysisRows === []) {
                $this->dispatch('toast', [
                    'type' => 'error',
                    'message' => 'Tidak ada data jawaban untuk subtes ini.',
                ]);

                return null;
            }

            $spreadsheet = $service->makeSpreadsheetForSubtest($eventName, $this->id_event, $this->subtes);
            $filename = $service->filename($eventName, $this->subtes);
        }

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->setPreCalculateFormulas(false);

        $path = storage_path('app/'.Str::uuid().'.xlsx');

        try {
            $writer->save($path);
        } catch (\Throwable $e) {
            @unlink($path);
            throw $e;
        } finally {
            $spreadsheet->disconnectWorksheets();
            unset($spreadsheet, $writer);
        }

        return response()->streamDownload(function () use ($path) {
            try {
                $handle = fopen($path, 'rb');
                if ($handle !== false) {
                    fpassthru($handle);
                    fclose($handle);
                }
            } finally {
                @unlink($path);
            }
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function render()
    {
        $service = app(TesPotensiJawabanService::class);
        $def = $service->definition($this->subtes);

        /** @var class-string<\Illuminate\Database\Eloquent\Model> $ujianModel */
        $ujianModel = $def['ujian_model'];

        $data = $ujianModel::query()
            ->with('peserta:id,nip,nama')
            ->where('event_id', $this->id_event)
            ->whereHas('peserta')
            ->when(filled($this->search), function ($query) {
                $term = '%'.trim((string) $this->search).'%';
                $query->whereHas('peserta', function ($peserta) use ($term) {
                    $peserta->where(function ($inner) use ($term) {
                        $inner->where('nama', 'like', $term)
                            ->orWhere('nip', 'like', $term);
                    });
                });
            })
            ->orderBy('id')
            ->paginate(10);

        $progressByUjian = $service->buildParticipantProgressRows($data->getCollection(), $this->subtes);

        $detailRows = [];
        $detailPeserta = null;
        if ($this->detailUjianId) {
            $detailUjian = $ujianModel::query()
                ->with('peserta:id,nip,nama')
                ->where('event_id', $this->id_event)
                ->whereKey($this->detailUjianId)
                ->first();

            if ($detailUjian) {
                $detailPeserta = $detailUjian->peserta;
                $detailRows = $service->buildDetailRowsForUjian(
                    $detailUjian,
                    $this->subtes,
                    $service->loadSoalMap($this->subtes)
                );
            } else {
                $this->detailUjianId = null;
            }
        }

        $subtestTabs = collect($service->subtestDefinitions())
            ->map(fn ($item, $key) => ['key' => $key, 'label' => $item['label']])
            ->values()
            ->all();

        $useCardLayout = in_array($this->subtes, ['berpikir-kritis', 'problem-solving'], true);

        return view('livewire.admin.jawaban-peserta.tes-potensi.show', [
            'data' => $data,
            'progressByUjian' => $progressByUjian,
            'detailRows' => $detailRows,
            'detailPeserta' => $detailPeserta,
            'subtestTabs' => $subtestTabs,
            'subtesLabel' => $def['label'],
            'useCardLayout' => $useCardLayout,
        ]);
    }
}
