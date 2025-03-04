<?php

namespace App\Livewire\Admin\DataTes\TesSelesai;

use App\Models\Event;
use App\Models\Peserta;
use App\Models\RefAlatTes;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.admin.app', ['title' => 'Laporan Hasil Penilaian Potensi'])]
class ShowReport extends Component
{
    public $nip;
    public $peserta;

    public function mount($nip)
    {
        $this->nip = $nip;
        $this->peserta = Peserta::whereNip($this->nip)->first();
    }

    public function render()
    {
        $aspek_potensi = RefAlatTes::get(['alat_tes', 'definisi_aspek_potensi']);

        return view('livewire.admin.data-tes.tes-selesai.show-report', [
            'peserta' => $this->peserta,
            'aspek_potensi' => $aspek_potensi
        ]);
    }
}
