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
        $total_pelanggaran = LogPelanggaran::where('peserta_id', Auth::guard('peserta')->user()->id)
            ->where('event_id', Auth::guard('peserta')->user()->event_id)
            ->count();

        $peringatan = $total_pelanggaran + 1;

        LogPelanggaran::create([
            'id' => Ulid::generate(true),
            'peserta_id' => Auth::guard('peserta')->user()->id,
            'event_id' => Auth::guard('peserta')->user()->event_id,
            'keterangan' => "Peringatan ke-{$peringatan}: Meninggalkan tab"
        ]);

        // Kirim event broadcast ke admin
        broadcast(new Pelanggaran(Auth::user()->nama, $peringatan));

        // Emit ke frontend untuk toastr peserta
        $this->dispatch('toast', [
            'type' => 'warning',
            'message' => "Peringatan ke-{$peringatan}: Anda meninggalkan tab!"
        ]);
    }
}
