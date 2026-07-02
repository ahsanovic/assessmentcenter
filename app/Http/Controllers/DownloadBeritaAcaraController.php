<?php

namespace App\Http\Controllers;

use App\Models\BeritaAcara;
use App\Services\Dokumen\BeritaAcaraPdfService;

class DownloadBeritaAcaraController extends Controller
{
    public function download(int $id, BeritaAcaraPdfService $service)
    {
        $beritaAcara = BeritaAcara::with(['event.metodeTes', 'adminPegawai', 'testerPegawai'])->findOrFail($id);

        $pdf = $service->generateFromModel($beritaAcara);

        return $pdf->stream($service->filename($beritaAcara->judul, $beritaAcara->event));
    }
}
