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
        // Jangan render ulang halaman ujian. Request ini sering berjalan
        // berbarengan dengan simpan jawaban pada komponen yang sama.
        $this->skipRender();

        $peserta = Auth::guard('peserta')->user();

        if (! $peserta) {
            return;
        }

        $total_pelanggaran = LogPelanggaran::where('peserta_id', $peserta->id)
            ->where('event_id', $peserta->event_id)
            ->count();

        $peringatan = $total_pelanggaran + 1;

        LogPelanggaran::create([
            'id' => Ulid::generate(true),
            'peserta_id' => $peserta->id,
            'event_id' => $peserta->event_id,
            'keterangan' => "Peringatan ke-{$peringatan}: Meninggalkan tab",
        ]);

        try {
            broadcast(new Pelanggaran($peserta->nama, $peringatan));
        } catch (\Throwable $e) {
            report($e);
        }

        $this->dispatch('toast', [
            'type' => 'warning',
            'message' => "Peringatan ke-{$peringatan}: Anda meninggalkan tab!",
        ]);
    }
}
