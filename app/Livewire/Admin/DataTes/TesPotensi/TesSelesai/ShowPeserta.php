<?php

namespace App\Livewire\Admin\DataTes\TesPotensi\TesSelesai;

use App\Models\Event;
use App\Services\Statistik\EventSkorRingkasanService;
use App\Services\Statistik\PotensiPesertaSelesaiQuery;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.admin.app', ['title' => 'Hasil Tes'])]
class ShowPeserta extends Component
{
    use WithPagination;

    public $event;

    public $id_event;

    public $selected_id;

    public $tanggal_tes;

    #[Url(as: 'q')]
    public ?string $search = '';

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->reset(['search', 'tanggal_tes']);
        $this->resetPage();
        $this->render();
    }

    public function mount($idEvent)
    {
        $this->id_event = $idEvent;
        $this->event = Event::with(['peserta', 'eventGroup'])->findOrFail($this->id_event);
    }

    public function render()
    {
        $eventId = (int) $this->id_event;
        $hasilTabel = [
            'hasil_interpersonal',
            'hasil_kesadaran_diri',
            'hasil_berpikir_kritis',
            'hasil_problem_solving',
            'hasil_pengembangan_diri',
            'hasil_kecerdasan_emosi',
            'hasil_motivasi_komitmen',
        ];
        $waktuSelesaiParts = array_map(
            fn (string $tabel) => "COALESCE((SELECT MAX(created_at) FROM {$tabel} WHERE peserta_id = peserta.id AND event_id = {$eventId}), '1970-01-01 00:00:00')",
            $hasilTabel
        );
        $waktuSelesaiSubquery = 'GREATEST('.implode(', ', $waktuSelesaiParts).') as waktu_selesai';

        $data = PotensiPesertaSelesaiQuery::forEvent($this->event, $this->search, $this->tanggal_tes)
            ->leftJoin('nilai_jpm', function ($join) use ($eventId) {
                $join->on('nilai_jpm.peserta_id', '=', 'peserta.id')
                    ->where('nilai_jpm.event_id', '=', $eventId);
            })
            ->select('peserta.*')
            ->addSelect([
                'nilai_jpm.jpm as skor_pjf',
                'nilai_jpm.kategori as kategori_pjf',
            ])
            ->selectRaw('peserta.test_started_at as waktu_mulai')
            ->selectRaw($waktuSelesaiSubquery)
            ->paginate(10);

        $ringkasanSkor = app(EventSkorRingkasanService::class)
            ->ringkasanPotensi($this->event, $this->search, $this->tanggal_tes);

        return view('livewire.admin.data-tes.tes-potensi.tes-selesai.show-peserta', [
            'data' => $data,
            'ringkasanSkor' => $ringkasanSkor,
        ]);
    }
}
