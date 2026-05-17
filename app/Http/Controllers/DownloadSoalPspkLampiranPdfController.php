<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\AuthorizesPesertaPspkSoalLampiran;
use App\Models\Peserta;
use App\Models\Pspk\SoalPspk;
use App\Services\Pspk\StorePspkKasusPdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class DownloadSoalPspkLampiranPdfController extends Controller
{
    use AuthorizesPesertaPspkSoalLampiran;

    /**
     * Hanya boleh di-fetch dari viewer aplikasi (bukan viewer PDF bawaan browser di tab baru),
     * agar menghambat unduh langsung dari URL biasa dan pemakaian iframe native.
     */
    public function __invoke(Request $request, SoalPspk $soal): Response
    {
        if ($request->header('X-Pspk-Inline-Pdf') !== '1') {
            abort(403);
        }

        if (! $soal->perluPaketKasusPdf()) {
            abort(404);
        }

        $kasus = $soal->kasusLampiran;
        if ($kasus === null) {
            abort(404);
        }

        $path = $kasus->lampiran_pdf_path;
        if ($path === null || $path === '') {
            abort(404);
        }

        /** @var Peserta $peserta */
        $peserta = Auth::guard('peserta')->user();

        if (! $this->pesertaPunyaAksesSoal($peserta, (int) $soal->id)) {
            abort(403);
        }

        return StorePspkKasusPdf::inlineFileResponse($path);
    }
}
