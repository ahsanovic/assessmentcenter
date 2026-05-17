<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\AuthorizesPesertaPspkSoalLampiran;
use App\Models\Peserta;
use App\Models\Pspk\SoalPspk;
use Illuminate\Support\Facades\Auth;

class ShowPspkLampiranPdfViewerController extends Controller
{
    use AuthorizesPesertaPspkSoalLampiran;

    public function __invoke(SoalPspk $soal)
    {
        if (! $soal->perluPaketKasusPdf()) {
            abort(404);
        }

        if ($soal->kasusLampiran === null || blank($soal->kasusLampiran->lampiran_pdf_path)) {
            abort(404);
        }

        /** @var Peserta $peserta */
        $peserta = Auth::guard('peserta')->user();

        if (! $this->pesertaPunyaAksesSoal($peserta, (int) $soal->id)) {
            abort(403);
        }

        return response()->view('peserta.tes-pspk.lampiran-pdf-viewer', [
            'pdfFetchUrl' => route('peserta.tes-pspk.lampiran-pdf', ['soal' => $soal->id]),
        ]);
    }
}
