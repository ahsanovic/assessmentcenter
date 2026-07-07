<?php

namespace App\Services\Dokumen;

use App\Models\AbsensiEvent;
use App\Models\Event;
use Barryvdh\DomPDF\Facade\Pdf;
use Barryvdh\DomPDF\PDF as DomPdf;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class AbsensiEventPdfService
{
    public function generateFromModel(AbsensiEvent $absensi): DomPdf
    {
        $absensi->refresh();

        $event = Event::query()->findOrFail($absensi->event_id);

        $peserta = $this->resolvePeserta(
            $event,
            $absensi->peserta_dari,
            $absensi->peserta_sampai
        );

        return $this->generate($event, $peserta, [
            'judul' => $absensi->judul,
            'tanggal' => $this->formatTanggalDisplay(
                $this->detectHari($absensi->tanggal),
                $absensi->tanggal
            ),
            'sesi' => $absensi->sesi,
            'waktu' => $this->formatWaktuDisplay(
                $absensi->waktu_mulai,
                $absensi->waktu_selesai,
                $absensi->zona_waktu
            ),
            'tempat' => $absensi->tempat,
            'extraRows' => (int) ($absensi->baris_tambahan ?? 0),
        ]);
    }

    public function resolvePeserta(Event $event, ?int $dari = null, ?int $sampai = null): Collection
    {
        $all = $event->peserta()
            ->select([
                'id',
                'event_id',
                'nama',
                'gelar_depan',
                'gelar_belakang',
                'unit_kerja',
            ])
            ->orderBy('id')
            ->get();

        if (! $dari && ! $sampai) {
            return $all;
        }

        $dari = max(1, $dari ?? 1);
        $sampai = min($all->count(), $sampai ?? $all->count());

        if ($dari > $sampai || $all->isEmpty()) {
            return collect();
        }

        return $all->slice($dari - 1, $sampai - $dari + 1)->values();
    }

    public function generate(Event $event, Collection $peserta, array $payload): DomPdf
    {
        $event->setRelation('peserta', $peserta);

        return Pdf::loadView('pdf.absensi-event', [
            'event' => $event,
            'judul' => $payload['judul'],
            'tanggal' => $payload['tanggal'],
            'sesi' => $payload['sesi'] ?? null,
            'waktu' => $payload['waktu'],
            'tempat' => $payload['tempat'],
            'extraRows' => (int) ($payload['extraRows'] ?? 0),
        ])->setPaper('A4', 'portrait');
    }

    public function filename(Event $event, ?AbsensiEvent $absensi = null): string
    {
        $safeEventName = preg_replace('/[^A-Za-z0-9_\-]/', '_', strtoupper($event->nama_event));
        $suffix = '';

        if ($absensi?->sesi) {
            $suffix .= '_sesi'.$absensi->sesi;
        }

        if ($absensi?->peserta_dari && $absensi?->peserta_sampai) {
            $suffix .= '_no'.$absensi->peserta_dari.'-'.$absensi->peserta_sampai;
        }

        return 'absensi_'.$safeEventName.$suffix.'.pdf';
    }

    public function formatTanggalDisplay(?string $hari, string $tanggal): string
    {
        $carbon = Carbon::createFromFormat('d-m-Y', $tanggal);
        $bulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];

        $formatted = $carbon->day.' '.($bulan[(int) $carbon->month] ?? '').' '.$carbon->year;

        if (filled($hari)) {
            return $hari.', '.$formatted;
        }

        return $formatted;
    }

    public function formatWaktuDisplay(string $waktuMulai, ?string $waktuSelesai, string $zonaWaktu): string
    {
        if (filled($waktuSelesai)) {
            return $waktuMulai.' - '.$waktuSelesai.' '.$zonaWaktu;
        }

        return $waktuMulai.' '.$zonaWaktu.' - Selesai';
    }

    protected function detectHari(string $tanggal): ?string
    {
        $hariId = [
            'Sunday' => 'Minggu',
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu',
        ];

        try {
            $carbon = Carbon::createFromFormat('d-m-Y', $tanggal);
        } catch (\Throwable) {
            return null;
        }

        return $hariId[$carbon->format('l')] ?? null;
    }
}
