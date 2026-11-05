<?php

namespace App\Livewire\Admin\JawabanPeserta\LiterasiDigital;

use App\Models\Event;
use App\Models\LiterasiDigital\UjianLiterasiDigital;
use App\Services\LiterasiDigital\LiterasiDigitalJawabanService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use PhpOffice\PhpSpreadsheet\IOFactory;

#[Layout('components.layouts.admin.app', ['title' => 'Jawaban Peserta — Tes Literasi Digital'])]
class Show extends Component
{
    use WithPagination;

    public Event $event;

    public int $id_event;

    #[Url(as: 'q')]
    public ?string $search = '';

    public function updatedSearch(): void
    {
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

        if ((int) $this->event->metode_tes_id !== LiterasiDigitalJawabanService::METODE_TES_ID) {
            abort(404);
        }
    }

    public function downloadExcel()
    {
        $service = app(LiterasiDigitalJawabanService::class);
        $rows = $service->exportRows($this->ujianQuery()->get());

        if ($rows === []) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Tidak ada data jawaban untuk diunduh.',
            ]);

            return null;
        }

        $spreadsheet = $service->makeSpreadsheet((string) ($this->event->nama_event ?? 'Event'), $rows);
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->setPreCalculateFormulas(false);

        $filename = $service->filename((string) ($this->event->nama_event ?? 'Event'));
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
        $service = app(LiterasiDigitalJawabanService::class);
        $data = $this->ujianQuery()->paginate(10);

        $jawabanByUjian = [];
        foreach ($data as $ujian) {
            $jawabanByUjian[$ujian->id] = $service->buildRows($ujian->jawaban);
        }

        return view('livewire.admin.jawaban-peserta.literasi-digital.show', [
            'data' => $data,
            'jawabanByUjian' => $jawabanByUjian,
        ]);
    }

    private function ujianQuery(): Builder
    {
        return UjianLiterasiDigital::query()
            ->with([
                'peserta',
                'jawaban' => function ($query) {
                    $query->orderBy('urutan')->orderBy('id')->with(['soal', 'pilar']);
                },
            ])
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
            ->orderBy('id');
    }
}
