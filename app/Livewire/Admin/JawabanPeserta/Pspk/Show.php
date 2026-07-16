<?php

namespace App\Livewire\Admin\JawabanPeserta\Pspk;

use App\Models\Event;
use App\Models\Pspk\SoalPspk;
use App\Models\Pspk\UjianPspk;
use App\Services\PspkJawabanService;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

#[Layout('components.layouts.admin.app', ['title' => 'Jawaban Peserta — Tes PSPK'])]
class Show extends Component
{
    use WithPagination;

    public Event $event;

    public int $id_event;

    public int $levelPspk;

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

        if (! in_array((int) $this->event->metode_tes_id, PspkJawabanService::PSPK_METODE_IDS, true)) {
            abort(404);
        }

        $level = app(PspkJawabanService::class)->levelFromMetode((int) $this->event->metode_tes_id);
        if (! $level) {
            abort(404);
        }

        $this->levelPspk = $level;
    }

    public function downloadExcel()
    {
        $service = app(PspkJawabanService::class);

        $ujianList = UjianPspk::query()
            ->with('peserta')
            ->where('event_id', $this->id_event)
            ->whereHas('peserta')
            ->when($this->search, function ($query) {
                $query->whereHas('peserta', function ($q) {
                    $q->where('nama', 'like', '%'.$this->search.'%')
                        ->orWhere('nip', 'like', '%'.$this->search.'%');
                });
            })
            ->orderBy('id')
            ->get();

        if ($ujianList->isEmpty()) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Tidak ada data jawaban untuk diunduh.',
            ]);

            return null;
        }

        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Jawaban PSPK');

        $headers = ['NIP', 'Nama Peserta', 'Jabatan', 'Unit Kerja', 'Soal', 'Jawaban Peserta', 'Kunci', 'Skor per Opsi'];
        $lastCol = 'H';

        $sheet->mergeCells("A1:{$lastCol}1");
        $sheet->setCellValue('A1', $this->event->nama_event);
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        foreach ($headers as $colIndex => $header) {
            $colLetter = chr(ord('A') + $colIndex);
            $sheet->setCellValue($colLetter.'3', $header);
        }

        $sheet->getStyle("A3:{$lastCol}3")->getFont()->setBold(true);
        $sheet->getStyle("A3:{$lastCol}3")->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFE2E8F0');

        $allSoalIds = $ujianList
            ->flatMap(fn ($ujian) => explode(',', (string) $ujian->soal_id))
            ->filter()
            ->unique()
            ->values()
            ->all();

        $soalMap = SoalPspk::with('aspek')
            ->whereIn('id', $allSoalIds)
            ->get()
            ->keyBy('id');

        $row = 4;

        foreach ($ujianList as $ujian) {
            $peserta = $ujian->peserta;
            $rows = $service->buildRowsForUjian(
                (string) $ujian->soal_id,
                (string) $ujian->jawaban,
                $this->levelPspk,
                $soalMap
            );

            foreach ($rows as $answerRow) {
                $nip = (string) ($peserta->nip ?? '-');
                $nama = (string) ($peserta->nama ?? '-');
                $jabatan = (string) ($peserta->jabatan ?? '-');
                $unitKerja = (string) ($peserta->unit_kerja ?? '-');
                $soalText = 'Soal '.$answerRow['nomor'].'. '.$answerRow['pertanyaan'];
                $jawaban = $answerRow['jawaban_peserta'];
                $kunci = $answerRow['kunci'] ?? '-';
                $skorOpsi = $answerRow['skor_opsi'] ?? '-';

                $sheet->setCellValueExplicit('A'.$row, $nip, DataType::TYPE_STRING);
                $sheet->setCellValueExplicit('B'.$row, $nama, DataType::TYPE_STRING);
                $sheet->setCellValueExplicit('C'.$row, $jabatan, DataType::TYPE_STRING);
                $sheet->setCellValueExplicit('D'.$row, $unitKerja, DataType::TYPE_STRING);
                $sheet->setCellValueExplicit('E'.$row, $soalText, DataType::TYPE_STRING);
                $sheet->setCellValueExplicit('F'.$row, $jawaban, DataType::TYPE_STRING);
                $sheet->setCellValueExplicit('G'.$row, $kunci, DataType::TYPE_STRING);
                $sheet->setCellValueExplicit('H'.$row, $skorOpsi, DataType::TYPE_STRING);

                $row++;
            }
        }

        foreach (range('A', $lastCol) as $col) {
            $sheet->getStyle($col.':'.$col)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $filename = 'jawaban-pspk-'.Str::slug($this->event->nama_event ?? 'event').'-'.date('Y-m-d').'.xlsx';
        $path = storage_path('app/'.Str::uuid().'.xlsx');
        $writer->save($path);

        return response()->streamDownload(function () use ($path) {
            echo file_get_contents($path);
            @unlink($path);
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function render()
    {
        $service = app(PspkJawabanService::class);

        $data = UjianPspk::query()
            ->with('peserta')
            ->where('event_id', $this->id_event)
            ->whereHas('peserta')
            ->when($this->search, function ($query) {
                $query->whereHas('peserta', function ($q) {
                    $q->where('nama', 'like', '%'.$this->search.'%')
                        ->orWhere('nip', 'like', '%'.$this->search.'%');
                });
            })
            ->orderBy('id')
            ->paginate(10);

        $ujianOnPage = $data->getCollection();
        $jawabanByUjian = $service->buildRowsForAllUjian($ujianOnPage, $this->levelPspk);

        $levelLabel = match ($this->levelPspk) {
            1 => 'Level 1',
            2 => 'Level 2',
            3 => 'Level 3',
            4 => 'Level 4',
            default => 'PSPK',
        };

        return view('livewire.admin.jawaban-peserta.pspk.show', [
            'data' => $data,
            'jawabanByUjian' => $jawabanByUjian,
            'levelLabel' => $levelLabel,
            'isLevel34' => in_array($this->levelPspk, [3, 4], true),
        ]);
    }
}
