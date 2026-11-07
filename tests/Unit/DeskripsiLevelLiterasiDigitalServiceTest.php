<?php

namespace Tests\Unit;

use App\Services\LiterasiDigital\DeskripsiLevelLiterasiDigitalService;
use Tests\TestCase;

class DeskripsiLevelLiterasiDigitalServiceTest extends TestCase
{
    public function test_normalisasi_menggabungkan_baris_dan_membuang_teks_kosong(): void
    {
        $this->assertNull(DeskripsiLevelLiterasiDigitalService::normalisasi(null));
        $this->assertNull(DeskripsiLevelLiterasiDigitalService::normalisasi("  \r\n"));
        $this->assertSame('Halo', DeskripsiLevelLiterasiDigitalService::normalisasi("  Halo\r\n"));
        $this->assertSame("a\nb", DeskripsiLevelLiterasiDigitalService::normalisasi("a\r\nb"));
    }
}
