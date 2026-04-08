<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Peserta;
use App\Models\RefPertanyaanPengalaman;
use App\Models\RefPertanyaanPenilaian;
use App\Models\RwKarir;
use App\Models\RwPelatihan;
use App\Models\RwPendidikan;
use Barryvdh\DomPDF\Facade\Pdf;
use Symfony\Component\HttpFoundation\StreamedResponse;
use ZipStream\ZipStream;

class DownloadPortofolioController extends Controller
{
    public function downloadPdf($idEvent, $pesertaId)
    {
        $event = Event::findOrFail($idEvent);
        if ((int) $event->metode_tes_id !== 1) {
            abort(404);
        }

        $peserta = Peserta::with(['agama', 'golPangkat', 'jenisPeserta'])
            ->where('event_id', $idEvent)
            ->where('id', $pesertaId)
            ->firstOrFail();

        $data = $this->buildViewData($peserta, $event);

        $pdf = Pdf::loadView('pdf.portofolio-peserta', $data)
            ->setPaper('A4', 'portrait');

        $filename = $this->pdfFilename($peserta);

        return $pdf->download($filename);
    }

    public function downloadZip($idEvent)
    {
        $event = Event::findOrFail($idEvent);
        if ((int) $event->metode_tes_id !== 1) {
            abort(404);
        }

        $pesertaList = Peserta::where('event_id', $idEvent)->orderBy('id')->get();

        $response = new StreamedResponse(function () use ($pesertaList, $event) {
            if (ob_get_level()) {
                ob_end_clean();
            }

            ini_set('memory_limit', '1024M');
            ini_set('max_execution_time', '600');

            $zip = new ZipStream(
                outputName: 'portofolio-event-'.$event->nama_event.'.zip',
                sendHttpHeaders: true
            );

            foreach ($pesertaList as $peserta) {
                $peserta->load(['agama', 'golPangkat', 'jenisPeserta']);
                $data = $this->buildViewData($peserta, $event);
                $pdf = Pdf::loadView('pdf.portofolio-peserta', $data)
                    ->setPaper('A4', 'portrait');

                $zip->addFile($this->pdfFilename($peserta), $pdf->output());
            }

            $zip->finish();
        });

        return $response;
    }

    private function pdfFilename(Peserta $peserta): string
    {
        $nip = $peserta->nip ?: $peserta->nik;
        $namaSafe = preg_replace('/[^A-Za-z0-9_\-]/', '_', strtoupper($peserta->getOriginal('nama') ?? ''));

        return $nip.'-'.$namaSafe.'.pdf';
    }

    private function buildViewData(Peserta $peserta, Event $event): array
    {
        $pendidikan = RwPendidikan::wherePesertaEvent($peserta->id, $event->id)
            ->with('jenjangPendidikan')
            ->orderByDesc('thn_lulus')
            ->get();

        $pelatihan = RwPelatihan::wherePesertaEvent($peserta->id, $event->id)
            ->orderByRaw('YEAR(tgl_selesai) IS NULL, YEAR(tgl_selesai) DESC')
            ->get();

        $karir = RwKarir::wherePesertaEvent($peserta->id, $event->id)
            ->orderByDesc('tahun_selesai')
            ->get();

        $pertanyaan = RefPertanyaanPengalaman::with(['jawaban' => function ($query) use ($peserta, $event) {
            $query->where('peserta_id', $peserta->id)
                ->where('event_id', $event->id);
        }])
            ->orderBy('urutan', 'asc')
            ->get();

        $penilaian = RefPertanyaanPenilaian::with(['jawaban' => function ($query) use ($peserta, $event) {
            $query->where('peserta_id', $peserta->id)
                ->where('event_id', $event->id);
        }])
            ->orderBy('urutan', 'asc')
            ->get();

        $fotoDataUri = $this->resolveFotoDataUri($peserta);

        return [
            'peserta' => $peserta,
            'event' => $event,
            'pendidikan' => $pendidikan,
            'pelatihan' => $pelatihan,
            'karir' => $karir,
            'pertanyaan' => $pertanyaan,
            'penilaian' => $penilaian,
            'fotoDataUri' => $fotoDataUri,
        ];
    }

    private function resolveFotoDataUri(Peserta $peserta): ?string
    {
        if (empty($peserta->foto)) {
            return null;
        }

        $path = storage_path('app/public/'.$peserta->foto);
        if (! is_file($path)) {
            return null;
        }

        $mime = mime_content_type($path) ?: 'image/jpeg';
        $raw = @file_get_contents($path);
        if ($raw === false) {
            return null;
        }

        return 'data:'.$mime.';base64,'.base64_encode($raw);
    }
}
