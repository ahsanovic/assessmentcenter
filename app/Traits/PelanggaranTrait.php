<?php

namespace App\Traits;

use App\Events\Pelanggaran;
use App\Models\LogPelanggaran;
use Illuminate\Support\Facades\Auth;
use Ulid\Ulid;

trait PelanggaranTrait
{
    public function laporPelanggaran()
    {
        $this->peringatan++;

        LogPelanggaran::create([
            'id' => Ulid::generate(true),
            'peserta_id' => Auth::guard('peserta')->user()->id,
            'event_id' => Auth::guard('peserta')->user()->event_id,
            'keterangan' => "Peringatan ke-{$this->peringatan}: Meninggalkan tab"
        ]);

        // Kirim event broadcast ke admin
        broadcast(new Pelanggaran(Auth::user()->nama, $this->peringatan));

        // Emit ke frontend untuk toastr peserta
        $this->dispatch('toast', [
            'type' => 'warning',
            'message' => "Peringatan ke-{$this->peringatan}: Anda meninggalkan tab!"
        ]);
    }
}
