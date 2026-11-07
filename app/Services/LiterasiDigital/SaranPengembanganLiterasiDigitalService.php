<?php

namespace App\Services\LiterasiDigital;

use App\Models\LiterasiDigital\RefSaranLiterasiDigitalTerbitan;
use App\Models\LiterasiDigital\RefSaranLiterasiDigitalTerbitanItem;
use App\Models\LiterasiDigital\RefSaranLiterasiDigitalVersi;
use App\Models\LiterasiDigital\RefSaranPengembanganLiterasiDigital;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class SaranPengembanganLiterasiDigitalService
{
    public static function normalisasi(?string $teks): ?string
    {
        if ($teks === null) {
            return null;
        }

        $teks = str_replace(["\r\n", "\r"], "\n", trim($teks));

        return $teks === '' ? null : $teks;
    }

    public static function ringkas(?string $saran): string
    {
        $teks = self::normalisasi($saran) ?? '';
        $teks = preg_replace('/^#{1,6}\s+/m', '', $teks) ?? $teks;
        $teks = str_replace(['**', '__', '`'], '', $teks);
        $teks = preg_replace('/^\s*[-*+]\s+/m', '', $teks) ?? $teks;
        $teks = preg_replace('/\[(.*?)\]\((.*?)\)/', '$1', $teks) ?? $teks;
        $teks = preg_replace('/\s+/', ' ', $teks) ?? $teks;

        return trim($teks);
    }

    public function simpan(RefSaranPengembanganLiterasiDigital $draft, ?string $teks): bool
    {
        $teks = self::normalisasi($teks);

        return DB::transaction(function () use ($draft, $teks) {
            $semua = RefSaranPengembanganLiterasiDigital::query()
                ->orderBy('id')
                ->lockForUpdate()
                ->get();

            $baris = $semua->firstWhere('id', $draft->id);
            if (! $baris instanceof RefSaranPengembanganLiterasiDigital) {
                throw new RuntimeException('Referensi saran pengembangan tidak ditemukan.');
            }

            if ($baris->saran === $teks) {
                return false;
            }

            $versiId = $teks === null
                ? null
                : $this->versiId((int) $baris->pilar_id, (int) $baris->level, $teks);

            $baris->saran = $teks;
            $baris->save();

            $this->terbitkan($semua, $baris, $versiId);

            return true;
        });
    }

    /**
     * Kunci satu event yang baru punya hasil ke terbitan berisi teks yang sedang berlaku.
     * Event lain tidak dipindahkan dari sini.
     */
    public function kunciUntukEvent(int $eventId): void
    {
        if (! DB::table('hasil_literasi_digital')->where('event_id', $eventId)->exists()) {
            return;
        }

        $terbitanId = $this->idTerbitanAktifBerisiTeks();
        if ($terbitanId === null) {
            return;
        }

        DB::table('event')
            ->where('id', $eventId)
            ->whereNull('saran_literasi_digital_terbitan_id')
            ->update([
                'saran_literasi_digital_terbitan_id' => $terbitanId,
                'updated_at' => now(),
            ]);
    }

    /**
     * Teks saran untuk satu pilar dan level pada event.
     * Event yang sudah terkunci memakai terbitannya. Event yang belum terkunci memakai terbitan terbaru.
     */
    public function teksUntuk(int $eventId, int $pilarId, int $level): ?string
    {
        $terbitanId = DB::table('event')
            ->where('id', $eventId)
            ->value('saran_literasi_digital_terbitan_id');

        if ($terbitanId === null) {
            $terbitanId = RefSaranLiterasiDigitalTerbitan::query()->orderByDesc('id')->value('id');
        }

        if ($terbitanId === null) {
            return null;
        }

        $item = RefSaranLiterasiDigitalTerbitanItem::query()
            ->with('versi')
            ->where('terbitan_id', $terbitanId)
            ->where('pilar_id', $pilarId)
            ->where('level', $level)
            ->first();

        return $item?->versi?->saran;
    }

    private function versiId(int $pilarId, int $level, string $teks): int
    {
        $ada = RefSaranLiterasiDigitalVersi::query()
            ->where('pilar_id', $pilarId)
            ->where('level', $level)
            ->where('saran', $teks)
            ->value('id');

        if ($ada !== null) {
            return (int) $ada;
        }

        $versi = RefSaranLiterasiDigitalVersi::query()->create([
            'pilar_id' => $pilarId,
            'level' => $level,
            'saran' => $teks,
        ]);

        return (int) $versi->id;
    }

    /**
     * @param  Collection<int, RefSaranPengembanganLiterasiDigital>  $draft
     */
    private function terbitkan(Collection $draft, RefSaranPengembanganLiterasiDigital $berubah, ?int $versiId): void
    {
        $terakhir = RefSaranLiterasiDigitalTerbitan::query()
            ->orderByDesc('id')
            ->lockForUpdate()
            ->first();

        $petaLama = [];
        if ($terakhir !== null) {
            $petaLama = RefSaranLiterasiDigitalTerbitanItem::query()
                ->where('terbitan_id', $terakhir->id)
                ->get()
                ->mapWithKeys(fn (RefSaranLiterasiDigitalTerbitanItem $item) => [
                    $this->kunciSel((int) $item->pilar_id, (int) $item->level) => $item->versi_id,
                ])
                ->all();
        }

        $kunciBerubah = $this->kunciSel((int) $berubah->pilar_id, (int) $berubah->level);
        $versiSebelumnya = $petaLama[$kunciBerubah] ?? null;
        $petaBaru = [];
        foreach ($draft as $baris) {
            $kunci = $this->kunciSel((int) $baris->pilar_id, (int) $baris->level);
            $petaBaru[$kunci] = (int) $baris->id === (int) $berubah->id
                ? $versiId
                : ($petaLama[$kunci] ?? null);
        }

        if ($terakhir !== null && $this->petaSama($petaLama, $petaBaru)) {
            return;
        }

        $terbitan = RefSaranLiterasiDigitalTerbitan::query()->create([]);
        $now = now();
        $item = [];

        foreach ($draft as $baris) {
            $kunci = $this->kunciSel((int) $baris->pilar_id, (int) $baris->level);
            $item[] = [
                'terbitan_id' => $terbitan->id,
                'pilar_id' => $baris->pilar_id,
                'level' => (int) $baris->level,
                'versi_id' => $petaBaru[$kunci],
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        RefSaranLiterasiDigitalTerbitanItem::query()->insert($item);

        if ($terakhir !== null && $versiSebelumnya !== null) {
            $this->sematkanEventYangSudahPunyaHasil((int) $terakhir->id);
        }
    }

    private function sematkanEventYangSudahPunyaHasil(int $terbitanId): void
    {
        DB::table('event')
            ->whereNull('saran_literasi_digital_terbitan_id')
            ->whereIn('id', function ($query) {
                $query->select('event_id')
                    ->from('hasil_literasi_digital')
                    ->groupBy('event_id');
            })
            ->update([
                'saran_literasi_digital_terbitan_id' => $terbitanId,
                'updated_at' => now(),
            ]);
    }

    private function idTerbitanAktifBerisiTeks(): ?int
    {
        $terbitanId = RefSaranLiterasiDigitalTerbitan::query()->orderByDesc('id')->value('id');
        if ($terbitanId === null) {
            return null;
        }

        $adaTeks = RefSaranLiterasiDigitalTerbitanItem::query()
            ->where('terbitan_id', $terbitanId)
            ->whereNotNull('versi_id')
            ->exists();

        return $adaTeks ? (int) $terbitanId : null;
    }

    /**
     * @param  array<string, int|null>  $lama
     * @param  array<string, int|null>  $baru
     */
    private function petaSama(array $lama, array $baru): bool
    {
        if (count($lama) !== count($baru)) {
            return false;
        }

        foreach ($baru as $kunci => $versiId) {
            if (! array_key_exists($kunci, $lama)) {
                return false;
            }

            if (! $this->versiSama($lama[$kunci], $versiId)) {
                return false;
            }
        }

        return true;
    }

    private function versiSama(mixed $kiri, mixed $kanan): bool
    {
        if ($kiri === null || $kanan === null) {
            return $kiri === null && $kanan === null;
        }

        return (int) $kiri === (int) $kanan;
    }

    private function kunciSel(int $pilarId, int $level): string
    {
        return $pilarId.'-'.$level;
    }
}
