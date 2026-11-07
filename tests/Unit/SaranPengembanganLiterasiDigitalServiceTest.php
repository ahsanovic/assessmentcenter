<?php

namespace Tests\Unit;

use App\Services\LiterasiDigital\SaranPengembanganLiterasiDigitalService;
use Tests\TestCase;

class SaranPengembanganLiterasiDigitalServiceTest extends TestCase
{
    public function test_normalisasi_dan_ringkas_menjaga_struktur_markdown(): void
    {
        $this->assertNull(SaranPengembanganLiterasiDigitalService::normalisasi(null));
        $this->assertNull(SaranPengembanganLiterasiDigitalService::normalisasi("  \r\n"));
        $this->assertSame('Halo', SaranPengembanganLiterasiDigitalService::normalisasi("  Halo\r\n"));
        $this->assertSame("a\nb", SaranPengembanganLiterasiDigitalService::normalisasi("a\r\nb"));

        $ringkas = SaranPengembanganLiterasiDigitalService::ringkas("## Etika\n\nLakukan hal berikut:\n- **jaga** data\n- baca [panduan](https://example.test)");

        $this->assertStringContainsString('Etika', $ringkas);
        $this->assertStringContainsString('jaga data', $ringkas);
        $this->assertStringContainsString('panduan', $ringkas);
        $this->assertStringNotContainsString('##', $ringkas);
        $this->assertStringNotContainsString('**', $ringkas);
        $this->assertStringNotContainsString('https://', $ringkas);
    }
}
