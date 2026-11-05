<?php

namespace Tests\Unit;

use App\Livewire\Admin\JawabanPeserta\LiterasiDigital\Index;
use App\Models\LiterasiDigital\JawabanLiterasiDigital;
use App\Models\LiterasiDigital\RefPilarLiterasiDigital;
use App\Models\LiterasiDigital\SoalLiterasiDigital;
use App\Models\LiterasiDigital\UjianLiterasiDigital;
use App\Models\Peserta;
use App\Services\LiterasiDigital\LiterasiDigitalJawabanService;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use ReflectionMethod;
use Tests\TestCase;

class LiterasiDigitalJawabanServiceTest extends TestCase
{
    public function test_menilai_jawaban_benar_salah_kosong_dan_tanpa_kunci(): void
    {
        $service = new LiterasiDigitalJawabanService;

        $benar = $this->jawaban(1, 1, 'a', 'A', 'Apa itu phishing?', ['a' => 'Penipuan']);
        $salah = $this->jawaban(2, 2, 'b', 'a', 'Soal dua', ['b' => 'Antivirus']);
        $kosong = $this->jawaban(3, 3, null, 'c', 'Soal tiga', ['c' => 'Backup']);
        $nol = $this->jawaban(4, 4, '0', 'd', 'Soal empat');
        $tanpaKunci = $this->jawaban(5, 5, 'e', '', 'Soal lima', ['e' => 'Firewall']);
        $tanpaSoal = $this->jawaban(6, 6, 'a', 'a', 'Hilang');
        $tanpaSoal->setRelation('soal', null);

        $rows = $service->buildRows([$salah, $tanpaSoal, $nol, $kosong, $tanpaKunci, $benar]);

        $this->assertCount(5, $rows);
        $this->assertSame([1, 2, 3, 4, 5], array_column($rows, 'nomor'));

        $this->assertSame('correct', $rows[0]['status']);
        $this->assertSame('Benar', $rows[0]['status_label']);
        $this->assertSame('1', $rows[0]['skor_label']);
        $this->assertSame('A — Penipuan', $rows[0]['jawaban_peserta']);
        $this->assertSame('A — Penipuan', $rows[0]['kunci']);
        $this->assertSame('Keamanan Digital', $rows[0]['pilar']);

        $this->assertSame('wrong', $rows[1]['status']);
        $this->assertSame('Salah', $rows[1]['status_label']);
        $this->assertSame('0', $rows[1]['skor_label']);
        $this->assertSame('B — Antivirus', $rows[1]['jawaban_peserta']);
        $this->assertSame('A — Opsi A', $rows[1]['kunci']);

        $this->assertSame('unanswered', $rows[2]['status']);
        $this->assertSame('Belum dijawab', $rows[2]['status_label']);
        $this->assertSame('-', $rows[2]['skor_label']);
        $this->assertSame('(belum dijawab)', $rows[2]['jawaban_peserta']);
        $this->assertSame('C — Backup', $rows[2]['kunci']);

        $this->assertSame('unanswered', $rows[3]['status']);
        $this->assertSame('(belum dijawab)', $rows[3]['jawaban_peserta']);

        $sepuluh = $this->jawaban(10, 10, 'a', 'a', 'Soal sepuluh');
        $sepuluh->urutan = '10';
        $dua = $this->jawaban(2, 20, 'a', 'a', 'Soal dua lagi');
        $dua->urutan = '2';
        $urutan = array_column($service->buildRows([$sepuluh, $dua]), 'nomor');
        $this->assertSame([2, 10], $urutan);

        $this->assertSame('neutral', $rows[4]['status']);
        $this->assertSame('Tidak ada kunci', $rows[4]['status_label']);
        $this->assertSame('E — Firewall', $rows[4]['jawaban_peserta']);
        $this->assertNull($rows[4]['kunci']);
        $this->assertSame('-', $rows[4]['skor_label']);
    }

    public function test_export_memakai_nama_lengkap_dan_mengabaikan_peserta_kosong(): void
    {
        $service = new LiterasiDigitalJawabanService;

        $peserta = new Peserta;
        $peserta->forceFill([
            'nip' => '001234',
            'gelar_depan' => 'Dr.',
            'nama' => 'budi',
            'gelar_belakang' => 'S.Kom',
            'jabatan' => 'analis kepegawaian',
            'unit_kerja' => 'biro umum',
        ]);

        $ujian = new UjianLiterasiDigital;
        $ujian->setRelation('peserta', $peserta);
        $ujian->setRelation('jawaban', collect([
            $this->jawaban(2, 9, 'b', 'a', 'Soal kedua', ['b' => 'Pilihan']),
            $this->jawaban(1, 8, 'a', 'a', '=CMD|', ['a' => 'Ya']),
        ]));

        $tanpaPeserta = new UjianLiterasiDigital;
        $tanpaPeserta->setRelation('peserta', null);
        $tanpaPeserta->setRelation('jawaban', collect([
            $this->jawaban(1, 7, 'a', 'a', 'Tidak ikut'),
        ]));

        $rows = $service->exportRows([$tanpaPeserta, $ujian]);

        $this->assertCount(2, $rows);
        $this->assertSame('001234', $rows[0]['nip']);
        $this->assertSame('Dr. BUDI, S.Kom', $rows[0]['nama']);
        $this->assertSame('Analis Kepegawaian', $rows[0]['jabatan']);
        $this->assertSame('BIRO UMUM', $rows[0]['unit_kerja']);
        $this->assertSame('Soal 1. =CMD|', $rows[0]['soal']);
        $this->assertSame('A — Ya', $rows[0]['jawaban']);
        $this->assertSame('Benar', $rows[0]['status']);
        $this->assertSame('1', $rows[0]['skor']);
        $this->assertSame('Soal 2. Soal kedua', $rows[1]['soal']);
        $this->assertSame('B — Pilihan', $rows[1]['jawaban']);
        $this->assertSame('Salah', $rows[1]['status']);
        $this->assertSame('0', $rows[1]['skor']);
    }

    public function test_spreadsheet_menulis_teks_bukan_rumus(): void
    {
        $service = new LiterasiDigitalJawabanService;
        $rows = [[
            'nip' => '001234',
            'nama' => 'BUDI',
            'jabatan' => 'Analis',
            'unit_kerja' => 'BIRO',
            'pilar' => 'Etika Digital',
            'soal' => 'Soal 1. =1+1',
            'jawaban' => 'A — Ya',
            'kunci' => 'A — Ya',
            'status' => 'Benar',
            'skor' => '1',
        ]];

        $spreadsheet = $service->makeSpreadsheet('Event Uji', $rows);
        $sheet = $spreadsheet->getActiveSheet();

        $this->assertSame('Jawaban Literasi Digital', $sheet->getTitle());
        $this->assertSame('Event Uji', $sheet->getCell('A1')->getValue());
        $this->assertSame('NIP', $sheet->getCell('A3')->getValue());
        $this->assertSame('Skor', $sheet->getCell('J3')->getValue());
        $this->assertSame('001234', $sheet->getCell('A4')->getValue());
        $this->assertSame(DataType::TYPE_STRING, $sheet->getCell('A4')->getDataType());
        $this->assertSame('Soal 1. =1+1', $sheet->getCell('F4')->getValue());
        $this->assertSame(DataType::TYPE_STRING, $sheet->getCell('F4')->getDataType());
        $this->assertSame('1', $sheet->getCell('J4')->getValue());
        $this->assertSame('jawaban-literasi-digital-event-uji-'.date('Y-m-d').'.xlsx', $service->filename('Event Uji'));
        $this->assertSame('jawaban-literasi-digital-event-'.date('Y-m-d').'.xlsx', $service->filename('***'));

        $path = storage_path('app/testing-literasi-jawaban.xlsx');
        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->setPreCalculateFormulas(false);
        $writer->save($path);

        try {
            $loaded = \PhpOffice\PhpSpreadsheet\IOFactory::load($path)->getActiveSheet();
            $this->assertSame('001234', $loaded->getCell('A4')->getValue());
            $this->assertSame('Soal 1. =1+1', $loaded->getCell('F4')->getValue());
        } finally {
            @unlink($path);
        }
    }

    public function test_filter_tanggal_hanya_menerima_format_hari_bulan_tahun(): void
    {
        $component = new Index;
        $method = new ReflectionMethod(Index::class, 'tanggalFilter');

        $component->tgl_mulai = '05-11-2026';
        $this->assertSame('2026-11-05', $method->invoke($component));

        $component->tgl_mulai = '32-13-2026';
        $this->assertNull($method->invoke($component));

        $component->tgl_mulai = 'bukan-tanggal';
        $this->assertNull($method->invoke($component));
    }

    /**
     * @param  array<string, string>  $opsi
     */
    private function jawaban(int $urutan, int $id, ?string $huruf, ?string $kunci, string $soalTeks, array $opsi = []): JawabanLiterasiDigital
    {
        $opsi = array_merge([
            'a' => 'Opsi A',
            'b' => 'Opsi B',
            'c' => 'Opsi C',
            'd' => 'Opsi D',
            'e' => 'Opsi E',
        ], $opsi);

        $soal = new SoalLiterasiDigital;
        $soal->forceFill([
            'id' => $id,
            'soal' => $soalTeks,
            'opsi_a' => $opsi['a'],
            'opsi_b' => $opsi['b'],
            'opsi_c' => $opsi['c'],
            'opsi_d' => $opsi['d'],
            'opsi_e' => $opsi['e'],
            'kunci_jawaban' => $kunci,
        ]);

        $pilar = new RefPilarLiterasiDigital;
        $pilar->forceFill([
            'nama' => 'Keamanan Digital',
            'kode' => 'keamanan',
        ]);

        $jawaban = new JawabanLiterasiDigital;
        $jawaban->forceFill([
            'id' => $id,
            'urutan' => $urutan,
            'jawaban' => $huruf,
        ]);
        $jawaban->setRelation('soal', $soal);
        $jawaban->setRelation('pilar', $pilar);

        return $jawaban;
    }
}
