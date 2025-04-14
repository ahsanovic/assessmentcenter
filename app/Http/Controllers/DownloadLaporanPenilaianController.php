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
            'capaian_level_interpersonal' => $this->_capaianLevel($data->hasilInterpersonal[0]->level_total),
            'capaian_level_kecerdasan_emosi' => $this->_capaianLevel($data->hasilKecerdasanEmosi[0]->level_total),
            'capaian_level_pengembangan_diri' => $this->_capaianLevel($data->hasilPengembanganDiri[0]->level_total),
            'capaian_level_problem_solving' => $this->_capaianLevel($data->hasilProblemSolving[0]->level_total),
            'capaian_level_motivasi_komitmen' => $this->_capaianLevel($data->hasilMotivasiKomitmen[0]->level_total),
            'capaian_level_berpikir_kritis' => $this->_capaianLevel($data->hasilBerpikirKritis[0]->level_total),
            'capaian_level_kesadaran_diri' => $this->_capaianLevel($data->hasilKesadaranDiri[0]->level_total),
        ])->setPaper('A4', 'portrait');

        // return $pdf->download('report-' . $peserta->nip . '-' . $peserta->nama . '.pdf');
        return $pdf->stream('report-' . $peserta->nip . '-' . $peserta->nama . '.pdf');
    }

    private function _capaianLevel($level_total)
    {
        switch ($level_total) {
            case '5+':
                $capaian_level = '5.5';
                break;
            case '5':
                $capaian_level =  '5';
                break;
            case '5-':
                $capaian_level =  '4.5';
                break;
            case '4+':
                $capaian_level =  '4.33';
                break;
            case '4':
                $capaian_level =  '4';
                break;
            case '4-':
                $capaian_level =  '3.67';
                break;
            case '3+':
                $capaian_level =  '3.5';
                break;
            case '3':
                $capaian_level =  '3';
                break;
            case '3-':
                $capaian_level =  '2.5';
                break;
            case '2+':
                $capaian_level =  '2.33';
                break;
            case '2':
                $capaian_level =  '2';
                break;
            case '2-':
                $capaian_level =  '1.67';
                break;
            case '1+':
                $capaian_level =  '1.33';
                break;
            case '1':
                $capaian_level =  '1';
                break;
            case '1-':
                $capaian_level =  '0.67';
                break;
            default:
                $capaian_level =  '0';
                break;
        }

        return $capaian_level;
    }
}
