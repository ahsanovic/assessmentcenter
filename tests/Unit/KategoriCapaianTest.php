<?php

namespace Tests\Unit;

use App\Support\KategoriCapaian;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class KategoriCapaianTest extends TestCase
{
    #[DataProvider('tierProvider')]
    public function test_normalize_tier(string $input, ?string $expected): void
    {
        $this->assertSame($expected, KategoriCapaian::normalizeTier($input));
    }

    public static function tierProvider(): array
    {
        return [
            ['Tinggi', 'Optimal'],
            ['Menengah', 'Cukup Optimal'],
            ['Rendah', 'Kurang Optimal'],
            ['Optimal', 'Optimal'],
            ['Cukup Optimal', 'Cukup Optimal'],
            ['Kurang Optimal', 'Kurang Optimal'],
            ['', null],
            ['Lainnya', null],
        ];
    }
}
