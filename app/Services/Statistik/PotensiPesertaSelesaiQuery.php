<?php

namespace App\Services\Statistik;

use App\Models\Event;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\Relation;

final class PotensiPesertaSelesaiQuery
{
    public static function forEvent(Event $event, ?string $search, ?string $tanggalTes): HasMany
    {
        $query = $event->peserta();

        self::applySearch($query, $search);
        self::applyTanggalTes($query, $tanggalTes);
        self::applySemuaSubtesSelesai($query);

        return $query;
    }

    public static function applySearch(Builder|Relation $query, ?string $search): void
    {
        if ($search === null || $search === '') {
            return;
        }

        $query->where(function ($q) use ($search) {
            $term = '%'.$search.'%';
            $q->where('nama', 'like', $term)
                ->orWhere('nip', 'like', $term)
                ->orWhere('nik', 'like', $term)
                ->orWhere('jabatan', 'like', $term)
                ->orWhere('unit_kerja', 'like', $term);
        });
    }

    public static function applyTanggalTes(Builder|Relation $query, ?string $tanggalTes): void
    {
        if ($tanggalTes === null || $tanggalTes === '') {
            return;
        }

        $query->whereDate('test_started_at', date('Y-m-d', strtotime($tanggalTes)));
    }

    public static function applySemuaSubtesSelesai(Builder|Relation $query): void
    {
        $subtes = [
            'ujianInterpersonal',
            'ujianKesadaranDiri',
            'ujianBerpikirKritis',
            'ujianProblemSolving',
            'ujianPengembanganDiri',
            'ujianKecerdasanEmosi',
            'ujianMotivasiKomitmen',
        ];

        foreach ($subtes as $relasi) {
            $query->whereHas($relasi, function ($q) {
                $q->where('is_finished', 'true');
            });
        }
    }
}
