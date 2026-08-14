<?php

namespace App\Services\Rekap;

use App\Models\Event;
use App\Models\EventGroup;
use App\Models\Peserta;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Symfony\Component\HttpFoundation\StreamedResponse;

class RekapGabunganExportService
{
    public function __construct(
        private readonly EventRekapRowBuilder $rowBuilder,
        private readonly RekapPesertaQueryService $queryService,
    ) {}

    public function downloadFromEvent(int $eventId, ?string $tanggalTes = null): StreamedResponse|Response
    {
        $event = Event::with(['eventGroup', 'metodeTes'])->findOrFail($eventId);

        if (! $event->event_group_id) {
            abort(404, 'Event ini tidak terhubung ke grup assessment. Gabungkan event ke grup terlebih dahulu.');
        }

        $group = EventGroup::findOrFail($event->event_group_id);
        $events = Event::with('metodeTes')
            ->where('event_group_id', $group->id)
            ->orderBy('metode_tes_id')
            ->get();

        $spreadsheet = $this->buildSpreadsheet($group, $events, $tanggalTes);
        $filename = 'rekap-gabungan-' . Str::slug($group->nama) . '.xlsx';

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function buildSpreadsheet(EventGroup $group, Collection $events, ?string $tanggalTes = null): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();
        $summaryRows = $this->buildSummaryRows($events, $tanggalTes);

        $summarySheet = $spreadsheet->getActiveSheet();
        $summarySheet->setTitle($this->sanitizeSheetTitle('Rekap Gabungan'));
        $this->writeSheetFromRows($summarySheet, $summaryRows);

        foreach ($events as $event) {
            $pesertaList = $this->queryService->getFinishedPesertaForEvent($event, $tanggalTes);
            $rows = $this->buildDetailRowsForEvent($event, $pesertaList);

            if ($rows->isEmpty()) {
                continue;
            }

            $sheet = $spreadsheet->createSheet();
            $sheet->setTitle($this->sanitizeSheetTitle($event->metodeTes->metode_tes ?? 'Tes ' . $event->id));
            $this->writeSheetFromRows($sheet, $rows);
        }

        return $spreadsheet;
    }

    private function buildSummaryRows(Collection $events, ?string $tanggalTes): Collection
    {
        $merged = [];

        foreach ($events as $event) {
            $pesertaList = $this->queryService->getFinishedPesertaForEvent($event, $tanggalTes);
            $prefix = $this->metodePrefix($event->metode_tes_id);

            foreach ($pesertaList as $peserta) {
                $key = $this->participantKey($peserta);

                if (! isset($merged[$key])) {
                    $merged[$key] = [
                        'Nama Peserta' => $peserta->nama,
                        'NIP/NIK' => $peserta->nip ?: $peserta->nik,
                        'Jabatan' => $peserta->jabatan ?? '',
                        'Instansi' => $peserta->instansi ?? '',
                        'Unit Kerja' => $peserta->unit_kerja ?? '',
                    ];
                }

                $snippet = $this->rowBuilder->buildSummarySnippet($peserta, $event->metode_tes_id);
                foreach ($snippet as $column => $value) {
                    $merged[$key][$prefix . ' - ' . $column] = $value;
                }
            }
        }

        return collect(array_values($merged));
    }

    private function buildDetailRowsForEvent(Event $event, Collection $pesertaList): Collection
    {
        return match ((int) $event->metode_tes_id) {
            2 => $this->rowBuilder->buildPotensiRows($pesertaList),
            3 => $this->rowBuilder->buildCakapDigitalRows($pesertaList),
            4 => $this->rowBuilder->buildKompetensiTeknisRows($pesertaList),
            5, 6, 7, 8 => $this->rowBuilder->buildPspkRows($pesertaList),
            default => collect(),
        };
    }

    private function writeSheetFromRows(Worksheet $sheet, Collection $rows): void
    {
        if ($rows->isEmpty()) {
            $sheet->setCellValue('A1', 'Tidak ada data');

            return;
        }

        $headers = array_keys($rows->first());
        $colIndex = 1;
        foreach ($headers as $header) {
            $colLetter = Coordinate::stringFromColumnIndex($colIndex);
            $sheet->setCellValue($colLetter . '1', $header);
            $colIndex++;
        }

        $rowNum = 2;
        foreach ($rows as $row) {
            $colIndex = 1;
            foreach ($headers as $header) {
                $colLetter = Coordinate::stringFromColumnIndex($colIndex);
                $sheet->setCellValue($colLetter . $rowNum, $row[$header] ?? '');
                $colIndex++;
            }
            $rowNum++;
        }
    }

    private function participantKey(Peserta $peserta): string
    {
        return $peserta->nip ?: $peserta->nik;
    }

    private function metodePrefix(int $metodeTesId): string
    {
        return match ($metodeTesId) {
            2 => 'Potensi',
            3 => 'Cakap Digital',
            4 => 'Kompetensi Teknis',
            5 => 'PSPK L1',
            6 => 'PSPK L2',
            7 => 'PSPK L3',
            8 => 'PSPK L4',
            default => 'Tes',
        };
    }

    private function sanitizeSheetTitle(string $title): string
    {
        $title = preg_replace('/[\\\\\\/*?:\\[\\]]/', '', $title) ?? 'Sheet';
        $title = trim($title);

        if ($title === '') {
            $title = 'Sheet';
        }

        return mb_substr($title, 0, 31);
    }
}
