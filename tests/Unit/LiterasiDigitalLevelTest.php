<?php

namespace Tests\Unit;

use App\Services\LiterasiDigital\LiterasiDigitalUjianService;
use Tests\TestCase;

class LiterasiDigitalLevelTest extends TestCase
{
    public function test_rentang_level_menutup_skor_nol_sampai_lima_belas_tanpa_tumpang_tindih(): void
    {
        $terpakai = [];

        foreach (LiterasiDigitalUjianService::RENTANG_LEVEL as $level => [$bawah, $atas]) {
            $this->assertGreaterThanOrEqual($bawah, $atas);
            $this->assertSame($bawah.'–'.$atas, LiterasiDigitalUjianService::labelRentang((int) $level));

            for ($skor = $bawah; $skor <= $atas; $skor++) {
                $this->assertArrayNotHasKey($skor, $terpakai);
                $terpakai[$skor] = $level;
                $this->assertSame((int) $level, LiterasiDigitalUjianService::levelDariSkor($skor));
            }
        }

        $this->assertSame(range(0, 15), array_keys($terpakai));
        $this->assertSame(5, LiterasiDigitalUjianService::levelDariSkor(16));
        $this->assertSame(1, LiterasiDigitalUjianService::levelDariSkor(-1));
        $this->assertSame('-', LiterasiDigitalUjianService::labelRentang(0));
    }
}
