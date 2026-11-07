<?php

namespace Tests\Unit;

use App\Services\LiterasiDigital\LaporanLiterasiDigitalService;
use Tests\TestCase;

class LaporanLiterasiDigitalServiceTest extends TestCase
{
    public function test_saran_dipecah_per_judul_dan_butir(): void
    {
        $service = app(LaporanLiterasiDigitalService::class);

        $pecahan = $service->pecahanMarkdown("## Jaga data\n\n1. simpan sandi\n2. kunci layar\n\n- laporkan insiden");

        $this->assertCount(4, $pecahan);
        $this->assertStringContainsString('<h2>Jaga data</h2>', $pecahan[0]);
        $this->assertStringContainsString('simpan sandi', $pecahan[1]);
        $this->assertStringContainsString('start="2"', $pecahan[2]);
        $this->assertStringContainsString('kunci layar', $pecahan[2]);
        $this->assertStringContainsString('laporkan insiden', $pecahan[3]);
        $this->assertStringNotContainsString('simpan sandi', $pecahan[2]);
    }

    public function test_deskripsi_panjang_dipecah_per_paragraf(): void
    {
        $service = app(LaporanLiterasiDigitalService::class);

        $pecahan = $service->pecahanTeks("Paragraf pertama.\n\nParagraf kedua.");

        $this->assertSame(['Paragraf pertama.', 'Paragraf kedua.'], $pecahan);
    }
}
