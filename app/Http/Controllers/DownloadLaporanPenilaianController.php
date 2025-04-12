<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Peserta;
use App\Models\Settings;
use Barryvdh\DomPDF\Facade\Pdf;

class DownloadLaporanPenilaianController extends Controller
{
    public function createPdf($idEvent, $nip)
    {
        $peserta = Peserta::where('nip', $nip)->firstOrFail();
        $aspek_potensi = Settings::with('alatTes')->orderBy('urutan')->get();

        $data = Event::with([
                'peserta' => function ($query) use ($peserta) {
                    $query->where('id', $peserta->id);
                }, 
                'hasilInterpersonal',
                'hasilKesadaranDiri',
                'hasilBerpikirKritis',
                'hasilProblemSolving',
                'hasilPengembanganDiri',
                'hasilKecerdasanEmosi',
                'hasilMotivasiKomitmen',
                'ujianInterpersonal' => function ($query) {
                    $query->select('id', 'event_id', 'created_at');
                }, 
            ])
            ->where('id', $idEvent)
            ->whereHas('ujianInterpersonal', function ($query) {
                $query->where('is_finished', 'true');
            })
            ->whereHas('ujianKesadaranDiri', function ($query) {
                $query->where('is_finished', 'true');
            })
            ->whereHas('ujianBerpikirKritis', function ($query) {
                $query->where('is_finished', 'true');
            })
            ->whereHas('ujianPengembanganDiri', function ($query) {
                $query->where('is_finished', 'true');
            })
            ->whereHas('ujianProblemSolving', function ($query) {
                $query->where('is_finished', 'true');
            })
            ->whereHas('ujianKecerdasanEmosi', function ($query) {
                $query->where('is_finished', 'true');
            })
            ->whereHas('ujianMotivasiKomitmen', function ($query) {
                $query->where('is_finished', 'true');
            })
            ->whereHas('peserta', function ($query) use ($peserta) {
                $query->where('id', $peserta->id);
            })
            ->firstOrFail();

        $pdf = Pdf::loadView('livewire.admin.data-tes.tes-selesai.download-pdf', [
            'peserta' => $peserta,
            'aspek_potensi' => $aspek_potensi,
            'data' => $data,
        ])->setPaper('A4', 'portrait');

        // return $pdf->download('report-' . $peserta->nip . '-' . $peserta->nama . '.pdf');
        return $pdf->stream('report-' . $peserta->nip . '-' . $peserta->nama . '.pdf');
    }
}
