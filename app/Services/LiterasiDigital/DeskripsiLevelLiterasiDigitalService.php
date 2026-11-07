<?php

namespace App\Services\LiterasiDigital;

use App\Models\LiterasiDigital\RefDeskripsiLevelLiterasiDigital;
use App\Models\LiterasiDigital\RefDeskripsiLiterasiDigitalTerbitan;
use App\Models\LiterasiDigital\RefDeskripsiLiterasiDigitalTerbitanItem;
use App\Models\LiterasiDigital\RefDeskripsiLiterasiDigitalVersi;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class DeskripsiLevelLiterasiDigitalService
{
    public static function normalisasi(?string $teks): ?string
    {
        if ($teks === null) {
            return null;
        }

        $teks = str_replace(["\r\n", "\r"], "\n", trim($teks));

        return $teks === '' ? null : $teks;
    }

    /**
     * Salin draft yang sudah ada menjadi terbitan pertama.
     * Dipakai sekali saat snapshot mulai berlaku, supaya teks yang sudah diisi tidak hilang.
     */
    public function publikasikanDraftAwal(): ?int
    {
        return DB::transaction(function () {
            if (RefDeskripsiLiterasiDigitalTerbitan::query()->lockForUpdate()->exists()) {
                return null;
            }

            $draft = RefDeskripsiLevelLiterasiDigital::query()
                ->orderBy('id')
                ->lockForUpdate()
                ->get();

            $adaTeks = false;
            $versi = [];

            foreach ($draft as $baris) {
                $teks = self::normalisasi($baris->deskripsi);
                if ($baris->deskripsi !== $teks) {
                    $baris->deskripsi = $teks;
                    $baris->save();
                }

                $versi[$this->kunciSel((int) $baris->pilar_id, (int) $baris->level)] = $teks === null
                    ? null
                    : $this->versiId((int) $baris->pilar_id, (int) $baris->level, $teks);

                if ($teks !== null) {
                    $adaTeks = true;
                }
            }

            if (! $adaTeks) {
                return null;
            }

            return $this->buatTerbitan($draft, $versi);
        });
    }

    public function simpan(RefDeskripsiLevelLiterasiDigital $draft, ?string $teks): bool
    {
        $teks = self::normalisasi($teks);

        return DB::transaction(function () use ($draft, $teks) {
            $semua = RefDeskripsiLevelLiterasiDigital::query()
                ->orderBy('id')
                ->lockForUpdate()
                ->get();

            $baris = $semua->firstWhere('id', $draft->id);
            if (! $baris instanceof RefDeskripsiLevelLiterasiDigital) {
                throw new RuntimeException('Referensi deskripsi level tidak ditemukan.');
            }

            if ($baris->deskripsi === $teks) {
                return false;
            }

            $versiId = $teks === null
                ? null
                : $this->versiId((int) $baris->pilar_id, (int) $baris->level, $teks);

            $baris->deskripsi = $teks;
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
            ->whereNull('deskripsi_literasi_digital_terbitan_id')
            ->update([
                'deskripsi_literasi_digital_terbitan_id' => $terbitanId,
                'updated_at' => now(),
            ]);
    }

    /**
     * Teks deskripsi untuk satu pilar dan level pada event.
     * Event yang sudah terkunci memakai terbitannya. Event yang belum terkunci memakai terbitan terbaru.
     */
    public function teksUntuk(int $eventId, int $pilarId, int $level): ?string
    {
        $terbitanId = DB::table('event')
            ->where('id', $eventId)
            ->value('deskripsi_literasi_digital_terbitan_id');

        if ($terbitanId === null) {
            $terbitanId = RefDeskripsiLiterasiDigitalTerbitan::query()->orderByDesc('id')->value('id');
        }

        if ($terbitanId === null) {
            return null;
        }

        $item = RefDeskripsiLiterasiDigitalTerbitanItem::query()
            ->with('versi')
            ->where('terbitan_id', $terbitanId)
            ->where('pilar_id', $pilarId)
            ->where('level', $level)
            ->first();

        return $item?->versi?->deskripsi;
    }

    private function versiId(int $pilarId, int $level, string $teks): int
    {
        $ada = RefDeskripsiLiterasiDigitalVersi::query()
            ->where('pilar_id', $pilarId)
            ->where('level', $level)
            ->where('deskripsi', $teks)
            ->value('id');

        if ($ada !== null) {
            return (int) $ada;
        }

        $versi = RefDeskripsiLiterasiDigitalVersi::query()->create([
            'pilar_id' => $pilarId,
            'level' => $level,
            'deskripsi' => $teks,
        ]);

        return (int) $versi->id;
    }

    /**
     * @param  Collection<int, RefDeskripsiLevelLiterasiDigital>  $draft
     */
    private function terbitkan(Collection $draft, RefDeskripsiLevelLiterasiDigital $berubah, ?int $versiId): void
    {
        $terakhir = RefDeskripsiLiterasiDigitalTerbitan::query()
            ->orderByDesc('id')
            ->lockForUpdate()
            ->first();

        $petaLama = [];
        if ($terakhir !== null) {
            $petaLama = RefDeskripsiLiterasiDigitalTerbitanItem::query()
                ->where('terbitan_id', $terakhir->id)
                ->get()
                ->mapWithKeys(fn (RefDeskripsiLiterasiDigitalTerbitanItem $item) => [
                    $this->kunciSel((int) $item->pilar_id, (int) $item->level) => $item->versi_id,
                ])
                ->all();
        }

        $kunciBerubah = $this->kunciSel((int) $berubah->pilar_id, (int) $berubah->level);
        $versiSebelumnya = $petaLama[$kunciBerubah] ?? null;
        $petaBaru = [];
        foreach ($draft as $baris) {
            $kunci = $this->kunciSel((int) $baris->pilar_id, (int) $baris->level);
            if ((int) $baris->id === (int) $berubah->id) {
                $petaBaru[$kunci] = $versiId;

                continue;
            }

            if (array_key_exists($kunci, $petaLama)) {
                $petaBaru[$kunci] = $petaLama[$kunci];

                continue;
            }

            $petaBaru[$kunci] = $this->versiDariDraft($baris);
        }

        if ($terakhir !== null && $this->petaSama($petaLama, $petaBaru)) {
            return;
        }

        $this->buatTerbitan($draft, $petaBaru);

        if ($terakhir !== null && $versiSebelumnya !== null) {
            $this->sematkanEventYangSudahPunyaHasil((int) $terakhir->id);
        }
    }

    /**
     * @param  Collection<int, RefDeskripsiLevelLiterasiDigital>  $draft
     * @param  array<string, int|null>  $versiPerSel
     */
    private function buatTerbitan(Collection $draft, array $versiPerSel): int
    {
        $terbitan = RefDeskripsiLiterasiDigitalTerbitan::query()->create([]);
        $now = now();
        $item = [];

        foreach ($draft as $baris) {
            $kunci = $this->kunciSel((int) $baris->pilar_id, (int) $baris->level);
            $item[] = [
                'terbitan_id' => $terbitan->id,
                'pilar_id' => $baris->pilar_id,
                'level' => (int) $baris->level,
                'versi_id' => $versiPerSel[$kunci] ?? null,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        RefDeskripsiLiterasiDigitalTerbitanItem::query()->insert($item);

        return (int) $terbitan->id;
    }

    private function versiDariDraft(RefDeskripsiLevelLiterasiDigital $baris): ?int
    {
        $teks = self::normalisasi($baris->deskripsi);
        if ($teks === null) {
            return null;
        }

        return $this->versiId((int) $baris->pilar_id, (int) $baris->level, $teks);
    }

    private function sematkanEventYangSudahPunyaHasil(int $terbitanId): void
    {
        DB::table('event')
            ->whereNull('deskripsi_literasi_digital_terbitan_id')
            ->whereIn('id', function ($query) {
                $query->select('event_id')
                    ->from('hasil_literasi_digital')
                    ->groupBy('event_id');
            })
            ->update([
                'deskripsi_literasi_digital_terbitan_id' => $terbitanId,
                'updated_at' => now(),
            ]);
    }

    private function idTerbitanAktifBerisiTeks(): ?int
    {
        $terbitanId = RefDeskripsiLiterasiDigitalTerbitan::query()->orderByDesc('id')->value('id');
        if ($terbitanId === null) {
            return null;
        }

        $adaTeks = RefDeskripsiLiterasiDigitalTerbitanItem::query()
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
