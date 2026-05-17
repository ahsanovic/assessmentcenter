<?php

namespace App\Http\Controllers;

use App\Models\Pspk\PspkKasusLampiran;
use App\Services\Pspk\StorePspkKasusPdf;
use Symfony\Component\HttpFoundation\Response;

/**
 * PDF paket analisa kasus: hanya untuk admin yang sudah login (route group auth:admin).
 */
class DownloadPspkKasusLampiranAdminController extends Controller
{
    public function __invoke(PspkKasusLampiran $kasus): Response
    {
        $path = $kasus->lampiran_pdf_path;
        if ($path === null || $path === '') {
            abort(404);
        }

        return StorePspkKasusPdf::pdfResponse($path, 'paket-kasus-'.$kasus->id.'.pdf');
    }
}
