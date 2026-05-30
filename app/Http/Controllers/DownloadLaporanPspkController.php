<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Peserta;
use App\Models\RefAspekPspk;
use App\Models\TtdLaporan;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\StreamedResponse;
use ZipStream\ZipStream;

class DownloadLaporanPspkController extends Controller
{
    public function createPdf($idEvent, $identifier)
    {
        $peserta = Peserta::where(function ($q) use ($identifier) {
            $q->where('nip', $identifier)
                ->orWhere('nik', $identifier);
        })
            ->whereHas('ujianPspk', function ($q) use ($idEvent) {
                $q->where('event_id', $idEvent)
                    ->where('is_finished', 'true');
            })
            ->firstOrFail();

        $aspek_potensi = RefAspekPspk::all();
        $tte = TtdLaporan::where('is_active', 't')->first();

        $data = Event::with([
            'peserta' => function ($query) use ($peserta) {
                $query->where('id', $peserta->id);
            },
            'nomorLaporan' => function ($query) use ($idEvent) {
                $query->where('event_id', $idEvent);
            },
            'hasilPspk' => function ($query) use ($peserta) {
                $query->where('peserta_id', $peserta->id);
            },
        ])->findOrFail($idEvent);

        $nomor_laporan = $this->resolveNomorLaporan($peserta, $data->nomorLaporan);

        $view = $this->resolvePdfView($data->metode_tes_id);

        $pdf = Pdf::loadView($view, $this->buildPdfPayload(
            $peserta,
            $data,
            $aspek_potensi,
            $tte,
            $nomor_laporan
        ))->setPaper('A4', 'portrait');

        return $pdf->stream('report-pspk-'.$peserta->nip ?: $peserta->nik.'-'.strtoupper($peserta->nama).'.pdf');
    }

    public function downloadAll($idEvent)
    {
        $tanggal = request()->query('tanggalTes');

        $aspek_potensi = RefAspekPspk::all();
        $tte = TtdLaporan::where('is_active', 't')->first();
        $event = Event::findOrFail($idEvent);
        $view = $this->resolvePdfView($event->metode_tes_id);

        $all_peserta = Peserta::with('event')
            ->where('event_id', $idEvent)
            ->when($tanggal, function ($query) use ($tanggal) {
                $query->whereDate('test_started_at', $tanggal);
            })
            ->whereHas('ujianPspk', function ($query) {
                $query->where('is_finished', 'true');
            })
            ->get();

        $response = new StreamedResponse(
            function () use ($all_peserta, $aspek_potensi, $tte, $idEvent, $view) {
                if (ob_get_level()) {
                    ob_end_clean();
                }

                ini_set('memory_limit', '1024M');
                ini_set('max_execution_time', '600');

                $zip = new ZipStream(
                    outputName: 'laporan-semua-peserta-tes-pspk.zip',
                    sendHttpHeaders: true
                );

                foreach ($all_peserta as $peserta) {
                    $data = Event::with([
                        'peserta' => function ($query) use ($peserta) {
                            $query->where('id', $peserta->id);
                        },
                        'nomorLaporan' => function ($query) use ($idEvent) {
                            $query->where('event_id', $idEvent);
                        },
                        'hasilPspk' => function ($query) use ($peserta) {
                            $query->where('peserta_id', $peserta->id);
                        },
                    ])->find($idEvent);

                    if (! $data || $data->hasilPspk->isEmpty()) {
                        continue;
                    }

                    $nomor_laporan = $this->resolveNomorLaporan($peserta, $data->nomorLaporan);

                    // generate PDF
                    $pdf = Pdf::loadView($view, $this->buildPdfPayload(
                        $peserta,
                        $data,
                        $aspek_potensi,
                        $tte,
                        $nomor_laporan
                    ))->setPaper('A4', 'portrait');

                    // nama file di dalam zip
                    $identifier = $peserta->nip ?: $peserta->nik;
                    $safeName = preg_replace('/[^A-Za-z0-9_\-]/', '_', strtoupper($peserta->nama));
                    $filename = $identifier.'-'.$safeName.'.pdf';

                    // masukkan langsung ke stream
                    $zip->addFile($filename, $pdf->output());
                }

                $zip->finish(); // kirim zip ke browser
            }
        );

        return $response;
    }

    private function resolvePdfView(int $metodeTesId): string
    {
        return match ($metodeTesId) {
            5, 6 => 'livewire.admin.data-tes.tes-pspk.tes-selesai.download-pdf',
            7, 8 => 'livewire.admin.data-tes.tes-pspk.tes-selesai.download-pdf-lv34',
            default => 'livewire.admin.data-tes.tes-pspk.tes-selesai.download-pdf',
        };
    }

    private function buildPdfPayload(
        Peserta $peserta,
        Event $data,
        $aspek_potensi,
        $tte,
        ?string $nomor_laporan
    ): array {
        $hasil = $data->hasilPspk->first();

        return [
            'peserta' => $peserta,
            'data' => $data,
            'aspek_potensi' => $aspek_potensi,
            'saran_pengembangan' => $hasil?->saran_pengembangan ?? [],
            'deskripsi' => $hasil?->deskripsi ?? [],
            'jpm' => $hasil?->jpm ?? 0,
            'kategori' => $hasil?->kategori ?? '',
            'tte' => $tte,
            'nomor_laporan' => $nomor_laporan,
        ];
    }

    private function resolveNomorLaporan(Peserta $peserta, Collection $nomorLaporans): ?string
    {
        $items = $nomorLaporans->filter(fn ($nl) => filled($nl->nomor));
        if ($items->isEmpty()) {
            return null;
        }

        $tesDay = $peserta->test_started_at
            ? Carbon::parse($peserta->test_started_at)->startOfDay()
            : null;

        foreach ($items as $nl) {
            $rawTgl = $nl->getRawOriginal('tanggal');
            if ($tesDay === null || ! $rawTgl) {
                continue;
            }
            if (Carbon::parse($rawTgl)->startOfDay()->equalTo($tesDay)) {
                return $nl->nomor;
            }
        }

        if ($items->count() === 1) {
            return $items->first()->nomor;
        }

        return $items->sort(function ($a, $b) {
            $ta = $a->getRawOriginal('tanggal') ?? '';
            $tb = $b->getRawOriginal('tanggal') ?? '';
            if ($ta !== $tb) {
                return strcmp($tb, $ta);
            }

            return (string) $b->getKey() <=> (string) $a->getKey();
        })->first()?->nomor;
    }
}
