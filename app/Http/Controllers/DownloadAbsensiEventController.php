<?php

namespace App\Http\Controllers;

use App\Models\AbsensiEvent;
use App\Services\Dokumen\AbsensiEventPdfService;

class DownloadAbsensiEventController extends Controller
{
    public function download(int $id, AbsensiEventPdfService $service)
    {
        $absensi = AbsensiEvent::with('event')->findOrFail($id);

        $pdf = $service->generateFromModel($absensi);

        $absensi = $absensi->fresh('event');

        return $pdf->stream($service->filename($absensi->event, $absensi))
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate');
    }
}
