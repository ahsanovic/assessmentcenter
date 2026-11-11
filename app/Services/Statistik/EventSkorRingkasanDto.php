<?php

namespace App\Services\Statistik;

final class EventSkorRingkasanDto
{
    /**
     * @param  array<int, array{kategori: string, jumlah: int, persen: float}>  $distribusi
     * @param  array{nilai: float, nama: string}|null  $terendah
     * @param  array{nilai: float, nama: string}|null  $tertinggi
     */
    public function __construct(
        public readonly string $labelSkor,
        public readonly int $totalPesertaPopulasi,
        public readonly int $totalDenganSkor,
        public readonly ?float $rata,
        public readonly ?array $terendah,
        public readonly ?array $tertinggi,
        public readonly array $distribusi,
        public readonly bool $filterAktif,
        public readonly ?string $peringatan = null,
    ) {}

    public function kosong(): bool
    {
        return $this->totalDenganSkor === 0;
    }
}
