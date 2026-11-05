<?php

namespace App\Services\LiterasiDigital;

use App\Models\LiterasiDigital\SoalLiterasiDigital;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class LiterasiDigitalJawabanService
{
    public const METODE_TES_ID = 3;

    public const EXPORT_HEADERS = [
        'NIP',
        'Nama Peserta',
        'Jabatan',
        'Unit Kerja',
        'Pilar',
        'Soal',
        'Jawaban Peserta',
        'Kunci',
        'Status',
        'Skor',
    ];

    private const EXPORT_KEYS = [
        'nip',
        'nama',
        'jabatan',
        'unit_kerja',
        'pilar',
        'soal',
        'jawaban',
        'kunci',
        'status',
        'skor',
    ];

    /**
     * @param  iterable<int, \App\Models\LiterasiDigital\JawabanLiterasiDigital>  $jawaban
     * @return array<int, array<string, mixed>>
     */
    public function buildRows(iterable $jawaban): array
    {
        $items = collect($jawaban)
            ->filter(fn ($baris) => $baris->soal !== null)
            ->sort(function ($left, $right) {
                return [(int) $left->urutan, (int) $left->id] <=> [(int) $right->urutan, (int) $right->id];
            })
            ->values();

        $rows = [];

        foreach ($items as $index => $baris) {
            $soal = $baris->soal;
            $huruf = $this->normalizeLetter($baris->jawaban);
            $kunci = $this->normalizeLetter($soal->kunci_jawaban);
            $status = $this->evaluate($huruf, $kunci);
            $urutan = (int) $baris->urutan;

            $rows[] = [
                'nomor' => $urutan > 0 ? $urutan : $index + 1,
                'soal_id' => $soal->id,
                'pilar' => filled($baris->pilar?->nama) ? (string) $baris->pilar->nama : '-',
                'pertanyaan' => (string) $soal->soal,
                'jawaban_peserta' => $this->formatJawaban($soal, $huruf),
                'jawaban_peserta_letter' => $huruf,
                'kunci' => $kunci ? $this->formatJawaban($soal, $kunci) : null,
                'kunci_letter' => $kunci,
                'status' => $status,
                'status_label' => $this->statusLabel($status),
                'skor_label' => match ($status) {
                    'correct' => '1',
                    'wrong' => '0',
                    default => '-',
                },
            ];
        }

        return $rows;
    }

    /**
     * @param  iterable<int, \App\Models\LiterasiDigital\UjianLiterasiDigital>  $ujianList
     * @return array<int, array<string, string>>
     */
    public function exportRows(iterable $ujianList): array
    {
        $rows = [];

        foreach ($ujianList as $ujian) {
            $peserta = $ujian->peserta;
            if (! $peserta) {
                continue;
            }

            foreach ($this->buildRows($ujian->jawaban ?? []) as $answer) {
                $rows[] = [
                    'nip' => $this->text($peserta->nip),
                    'nama' => $this->text($peserta->nama),
                    'jabatan' => $this->text($peserta->jabatan),
                    'unit_kerja' => $this->text($peserta->unit_kerja),
                    'pilar' => $answer['pilar'],
                    'soal' => 'Soal '.$answer['nomor'].'. '.$answer['pertanyaan'],
                    'jawaban' => $answer['jawaban_peserta'],
                    'kunci' => $answer['kunci'] ?? '-',
                    'status' => $answer['status_label'],
                    'skor' => $answer['skor_label'],
                ];
            }
        }

        return $rows;
    }

    /**
     * @param  array<int, array<string, string>>  $rows
     */
    public function makeSpreadsheet(string $eventName, array $rows): Spreadsheet
    {
        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Jawaban Literasi Digital');

        $lastCol = $this->columnLetter(count(self::EXPORT_HEADERS) - 1);

        $sheet->mergeCells("A1:{$lastCol}1");
        $sheet->setCellValueExplicit('A1', $eventName !== '' ? $eventName : 'Event', DataType::TYPE_STRING);
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        foreach (self::EXPORT_HEADERS as $colIndex => $header) {
            $sheet->setCellValueExplicit($this->columnLetter($colIndex).'3', $header, DataType::TYPE_STRING);
        }

        $sheet->getStyle("A3:{$lastCol}3")->getFont()->setBold(true);
        $sheet->getStyle("A3:{$lastCol}3")->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFE2E8F0');

        $rowNumber = 4;

        foreach ($rows as $row) {
            foreach (self::EXPORT_KEYS as $colIndex => $key) {
                $sheet->setCellValueExplicit(
                    $this->columnLetter($colIndex).$rowNumber,
                    (string) ($row[$key] ?? '-'),
                    DataType::TYPE_STRING
                );
            }

            $sheet->getRowDimension($rowNumber)->setRowHeight(-1);
            $rowNumber++;
        }

        if ($rows !== []) {
            $lastDataRow = $rowNumber - 1;
            $sheet->getStyle("A4:{$lastCol}{$lastDataRow}")->getAlignment()
                ->setWrapText(true)
                ->setVertical(Alignment::VERTICAL_TOP);
        }

        $widths = [
            'A' => 20,
            'B' => 28,
            'C' => 24,
            'D' => 28,
            'E' => 32,
            'F' => 60,
            'G' => 40,
            'H' => 40,
            'I' => 18,
            'J' => 10,
        ];

        foreach ($widths as $column => $width) {
            $sheet->getColumnDimension($column)->setWidth($width);
        }

        $sheet->freezePane('A4');

        return $spreadsheet;
    }

    public function filename(string $eventName): string
    {
        $slug = Str::slug($eventName);
        if ($slug === '') {
            $slug = 'event';
        }

        return 'jawaban-literasi-digital-'.$slug.'-'.date('Y-m-d').'.xlsx';
    }

    public function formatJawaban(SoalLiterasiDigital $soal, ?string $letter): string
    {
        if ($letter === null) {
            return '(belum dijawab)';
        }

        $text = trim((string) ($soal->{'opsi_'.strtolower($letter)} ?? ''));

        return $text !== '' ? $letter.' — '.$text : $letter;
    }

    public function evaluate(?string $jawaban, ?string $kunci): string
    {
        if ($jawaban === null) {
            return 'unanswered';
        }

        if ($kunci === null) {
            return 'neutral';
        }

        return $jawaban === $kunci ? 'correct' : 'wrong';
    }

    private function normalizeLetter(mixed $value): ?string
    {
        $value = strtoupper(trim((string) $value));

        if ($value === '' || $value === '0') {
            return null;
        }

        return $value;
    }

    private function statusLabel(string $status): string
    {
        return match ($status) {
            'correct' => 'Benar',
            'wrong' => 'Salah',
            'unanswered' => 'Belum dijawab',
            default => 'Tidak ada kunci',
        };
    }

    private function text(mixed $value): string
    {
        $value = trim((string) ($value ?? ''));

        return $value === '' ? '-' : $value;
    }

    private function columnLetter(int $index): string
    {
        $letter = '';
        $index++;

        while ($index > 0) {
            $index--;
            $letter = chr(65 + ($index % 26)).$letter;
            $index = intdiv($index, 26);
        }

        return $letter;
    }
}
