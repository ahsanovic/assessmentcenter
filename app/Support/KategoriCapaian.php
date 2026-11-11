<?php

namespace App\Support;

final class KategoriCapaian
{
    /**
     * Selaraskan label kategori potensi (Tinggi/Menengah/Rendah) dengan tier PSPK/LD.
     */
    public static function normalizeTier(?string $kategori): ?string
    {
        if ($kategori === null || trim($kategori) === '') {
            return null;
        }

        $k = strtolower(trim($kategori));

        if (str_contains($k, 'kurang') || $k === 'rendah') {
            return 'Kurang Optimal';
        }

        if (str_contains($k, 'cukup') || $k === 'menengah') {
            return 'Cukup Optimal';
        }

        if (str_contains($k, 'optimal') || $k === 'tinggi') {
            return 'Optimal';
        }

        return null;
    }

    /**
     * @return array{Optimal: int, Cukup Optimal: int, Kurang Optimal: int}
     */
    public static function emptyTierBuckets(): array
    {
        return [
            'Optimal' => 0,
            'Cukup Optimal' => 0,
            'Kurang Optimal' => 0,
        ];
    }

    /**
     * Warna badge/bar untuk label kategori (asli atau tier).
     */
    public static function badgeClass(?string $kategori): string
    {
        $tier = self::normalizeTier($kategori);
        if ($tier === null) {
            $k = strtolower(trim((string) $kategori));
            if (str_contains($k, 'tinggi') || (str_contains($k, 'optimal') && ! str_contains($k, 'kurang') && ! str_contains($k, 'cukup'))) {
                $tier = 'Optimal';
            } elseif (str_contains($k, 'menengah') || str_contains($k, 'cukup')) {
                $tier = 'Cukup Optimal';
            } elseif (str_contains($k, 'rendah') || str_contains($k, 'kurang')) {
                $tier = 'Kurang Optimal';
            }
        }

        return match ($tier) {
            'Optimal' => 'bg-success-subtle text-success-emphasis border-success-subtle',
            'Cukup Optimal' => 'bg-warning-subtle text-warning-emphasis border-warning-subtle',
            'Kurang Optimal' => 'bg-danger-subtle text-danger-emphasis border-danger-subtle',
            default => 'bg-secondary-subtle text-secondary border-secondary-subtle',
        };
    }

    public static function barColor(?string $kategori): string
    {
        $tier = self::normalizeTier($kategori);
        if ($tier === null) {
            return 'var(--ac-primary, #3b82f6)';
        }

        return match ($tier) {
            'Optimal' => 'var(--ac-success, #059669)',
            'Cukup Optimal' => 'var(--ac-warning, #d97706)',
            'Kurang Optimal' => 'var(--ac-danger, #dc2626)',
            default => 'var(--ac-primary, #3b82f6)',
        };
    }
}
