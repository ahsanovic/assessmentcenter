<?php

namespace App\Http\Controllers;

use App\Models\RefPegawai;
use App\Services\Pegawai\PegawaiQrCodeService;

class DownloadPegawaiQrCodeController extends Controller
{
    public function show(int $id)
    {
        $pegawai = RefPegawai::findOrFail($id);

        if (! $pegawai->qrcode_path) {
            abort(404);
        }

        return PegawaiQrCodeService::imageResponse($pegawai->qrcode_path);
    }
}
