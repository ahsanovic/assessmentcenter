<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Peserta;
use App\Models\Pspk\UjianPspk;
use App\Models\Pspk\UjianPspkLv34;

trait AuthorizesPesertaPspkSoalLampiran
{
    private function pesertaPunyaAksesSoal(Peserta $peserta, int $soalId): bool
    {
        $eventId = (int) $peserta->event_id;
        $pesertaId = (int) $peserta->id;

        $ujian = UjianPspk::query()
            ->where('peserta_id', $pesertaId)
            ->where('event_id', $eventId)
            ->first();

        if ($ujian !== null && $this->idSoalAdaDiDaftarCsv($ujian->soal_id, $soalId)) {
            return true;
        }

        $ada = UjianPspkLv34::query()
            ->where('peserta_id', $pesertaId)
            ->where('event_id', $eventId)
            ->whereNotNull('soal_id')
            ->get(['soal_id']);

        foreach ($ada as $row) {
            if ($this->idSoalAdaDiDaftarCsv($row->soal_id, $soalId)) {
                return true;
            }
        }

        return false;
    }

    private function idSoalAdaDiDaftarCsv(?string $csv, int $soalId): bool
    {
        if ($csv === null || $csv === '') {
            return false;
        }

        foreach (explode(',', $csv) as $part) {
            if ((int) trim($part) === $soalId) {
                return true;
            }
        }

        return false;
    }
}
