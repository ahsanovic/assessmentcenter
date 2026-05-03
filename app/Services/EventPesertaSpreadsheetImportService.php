<?php

namespace App\Services;

use App\Models\Peserta;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class EventPesertaSpreadsheetImportService
{
    /**
     * @return array{imported: int, errors: string[]}
     */
    public function import(int $eventId, string $absoluteFilePath): array
    {
        $spreadsheet = IOFactory::load($absoluteFilePath);
        $sheet = $spreadsheet->getActiveSheet();
        $highestRow = (int) $sheet->getHighestDataRow();

        $imported = 0;
        $errors = [];
        $importedNips = [];
        $importedNiks = [];

        for ($rowNum = 2; $rowNum <= $highestRow; $rowNum++) {
            $nama = $this->importCellPlainString($sheet, $rowNum, 2);

            if ($nama === '') {
                continue;
            }

            $jenisPesertaId = $this->normalizeJenisPesertaId($this->importCellPlainString($sheet, $rowNum, 3));

            $nip = $this->digitsFromExcelCell($sheet, $rowNum, 4);
            $nik = $this->digitsFromExcelCell($sheet, $rowNum, 5);
            $jabatan = $this->importCellPlainString($sheet, $rowNum, 6);
            $unitKerja = $this->importCellPlainString($sheet, $rowNum, 7);
            $instansi = $this->importCellPlainString($sheet, $rowNum, 8);
            $password = $this->importCellPlainString($sheet, $rowNum, 9);

            if (empty($nama)) {
                $errors[] = "Baris $rowNum: Nama harus diisi";

                continue;
            }

            if (! $jenisPesertaId) {
                $errors[] = "Baris $rowNum: Jenis peserta harus ASN atau Non ASN";

                continue;
            }

            if ($jenisPesertaId == 1 && (empty($nip) || strlen($nip) != 18)) {
                $errors[] = "Baris $rowNum: NIP harus 18 digit untuk ASN (pastikan kolom NIP berformat Teks di Excel agar digit tidak berubah)";

                continue;
            }

            if ($jenisPesertaId == 1) {
                $existingNip = Peserta::where('event_id', $eventId)
                    ->where('nip', $nip)
                    ->exists();

                if ($existingNip) {
                    $errors[] = "Baris $rowNum: NIP $nip sudah terdaftar di event ini";

                    continue;
                }

                if (in_array($nip, $importedNips, true)) {
                    $errors[] = "Baris $rowNum: NIP $nip duplikat dalam file import";

                    continue;
                }

                $importedNips[] = $nip;
            }

            if ($jenisPesertaId == 2 && (empty($nik) || strlen($nik) != 16)) {
                $errors[] = "Baris $rowNum: NIK harus 16 digit untuk Non ASN (pastikan kolom NIK berformat Teks di Excel agar digit tidak berubah)";

                continue;
            }

            if ($jenisPesertaId == 2) {
                $existingNik = Peserta::where('event_id', $eventId)
                    ->where('nik', $nik)
                    ->exists();

                if ($existingNik) {
                    $errors[] = "Baris $rowNum: NIK $nik sudah terdaftar di event ini";

                    continue;
                }

                if (in_array($nik, $importedNiks, true)) {
                    $errors[] = "Baris $rowNum: NIK $nik duplikat dalam file import";

                    continue;
                }

                $importedNiks[] = $nik;
            }

            if ($jenisPesertaId == 1 && empty($jabatan)) {
                $errors[] = "Baris $rowNum: Jabatan harus diisi untuk ASN";

                continue;
            }

            if (empty($unitKerja)) {
                $errors[] = "Baris $rowNum: Unit kerja harus diisi";

                continue;
            }

            if (empty($instansi)) {
                $errors[] = "Baris $rowNum: Instansi harus diisi";

                continue;
            }

            if (empty($password) || strlen($password) < 8) {
                $errors[] = "Baris $rowNum: Password harus minimal 8 karakter";

                continue;
            }

            $parsedNama = parse_nama_gelar($nama);

            $data = Peserta::create([
                'nama' => $parsedNama['nama'],
                'gelar_depan' => $parsedNama['gelar_depan'],
                'gelar_belakang' => $parsedNama['gelar_belakang'],
                'event_id' => $eventId,
                'jenis_peserta_id' => $jenisPesertaId,
                'nip' => $jenisPesertaId == 1 ? $nip : null,
                'nik' => $jenisPesertaId == 2 ? $nik : null,
                'jabatan' => $jenisPesertaId == 1 ? $jabatan : null,
                'unit_kerja' => $unitKerja,
                'instansi' => $instansi,
                'password' => bcrypt($password),
                'is_active' => 'true',
            ]);

            activity_log($data, 'create', 'peserta (import)');
            $imported++;
        }

        return [
            'imported' => $imported,
            'errors' => $errors,
        ];
    }

    /**
     * @return array<string, int>
     */
    public static function categorizeImportErrors(array $errors): array
    {
        $categories = [
            'duplikat_database' => 0,
            'duplikat_file' => 0,
            'format_salah' => 0,
            'data_kosong' => 0,
            'lainnya' => 0,
        ];

        foreach ($errors as $error) {
            if (strpos($error, 'sudah terdaftar di event') !== false) {
                $categories['duplikat_database']++;
            } elseif (strpos($error, 'duplikat dalam file') !== false) {
                $categories['duplikat_file']++;
            } elseif (strpos($error, 'harus') !== false && (strpos($error, 'digit') !== false || strpos($error, 'karakter') !== false)) {
                $categories['format_salah']++;
            } elseif (strpos($error, 'harus diisi') !== false) {
                $categories['data_kosong']++;
            } else {
                $categories['lainnya']++;
            }
        }

        return $categories;
    }

    private function importCellPlainString(Worksheet $sheet, int $row, int $colIndex): string
    {
        $coord = Coordinate::stringFromColumnIndex($colIndex).$row;
        $cell = $sheet->getCell($coord);
        $val = $cell->getValue();

        if ($val instanceof RichText) {
            return trim($val->getPlainText());
        }
        if ($val === null) {
            return '';
        }
        if (is_string($val)) {
            return trim($val);
        }
        if (is_int($val) || is_float($val)) {
            return trim((string) $val);
        }

        return trim((string) $cell->getFormattedValue());
    }

    private function digitsFromExcelCell(Worksheet $sheet, int $row, int $colIndex): string
    {
        $coord = Coordinate::stringFromColumnIndex($colIndex).$row;
        $cell = $sheet->getCell($coord);
        $val = $cell->getValue();

        if ($val instanceof RichText) {
            $val = $val->getPlainText();
        }

        if ($val === null || $val === '') {
            return '';
        }

        if (is_string($val)) {
            return preg_replace('/\D+/', '', $val);
        }

        if (is_int($val)) {
            return (string) max(0, $val);
        }

        if (is_float($val)) {
            $formatted = $cell->getFormattedValue();
            if (is_string($formatted)) {
                $digits = preg_replace('/\D+/', '', $formatted);
                if ($digits !== '') {
                    return $digits;
                }
            }

            return '';
        }

        return preg_replace('/\D+/', '', (string) $val);
    }

    private function normalizeJenisPesertaId(mixed $raw): ?int
    {
        if ($raw instanceof RichText) {
            $raw = $raw->getPlainText();
        }
        if ($raw === null || $raw === '') {
            return null;
        }
        if (is_numeric($raw) && (string) (int) $raw === (string) $raw) {
            $n = (int) $raw;
            if ($n === 1) {
                return 1;
            }
            if ($n === 2) {
                return 2;
            }
        }

        $s = strtolower(trim((string) $raw));
        $s = preg_replace('/[\s_]+/u', ' ', $s);
        $compact = str_replace(' ', '', $s);

        if ($compact === 'nonasn' || $s === 'non asn') {
            return 2;
        }
        if ($compact === 'asn') {
            return 1;
        }

        return null;
    }
}
