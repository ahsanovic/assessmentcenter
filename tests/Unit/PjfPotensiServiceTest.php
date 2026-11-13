<?php

namespace Tests\Unit;

use App\Models\Intelektual\HasilIntelektual;
use App\Models\Interpersonal\HasilInterpersonal;
use App\Models\Peserta;
use App\Services\Potensi\PjfPotensiService;
use Tests\TestCase;

class PjfPotensiServiceTest extends TestCase
{
    public function test_kategori_tetap_terisi_jika_rasio_melebihi_satu(): void
    {
        $this->assertSame('Tinggi', getKategori(1));
        $this->assertSame('Tinggi', getKategori(1.01));
        $this->assertSame('Tinggi', getKategori(0.8));
        $this->assertSame('Menengah', getKategori(0.6));
        $this->assertSame('Menengah', getKategori(0.79));
        $this->assertSame('Rendah', getKategori(0.59));
    }

    public function test_pjf_tidak_disimpan_jika_level_intelektual_kosong(): void
    {
        $peserta = new Peserta;
        $peserta->id = 1;

        $intelektual = new HasilIntelektual;
        $intelektual->level = null;
        $peserta->setRelation('hasilIntelektual', $intelektual);

        foreach ([
            'hasilInterpersonal',
            'hasilKecerdasanEmosi',
            'hasilPengembanganDiri',
            'hasilProblemSolving',
            'hasilMotivasiKomitmen',
            'hasilBerpikirKritis',
            'hasilKesadaranDiri',
        ] as $relasi) {
            $hasil = new HasilInterpersonal;
            $hasil->level_total = '4';
            $peserta->setRelation($relasi, $hasil);
        }

        $this->assertNull((new PjfPotensiService)->simpanDariHasil(1, $peserta));
    }

    public function test_pjf_tidak_disimpan_jika_salah_satu_level_potensi_kosong(): void
    {
        $peserta = new Peserta;
        $peserta->id = 1;

        $intelektual = new HasilIntelektual;
        $intelektual->level = '4';
        $peserta->setRelation('hasilIntelektual', $intelektual);

        $kosong = new HasilInterpersonal;
        $kosong->level_total = '';
        $peserta->setRelation('hasilInterpersonal', $kosong);

        $this->assertNull((new PjfPotensiService)->simpanDariHasil(1, $peserta));
    }
}
