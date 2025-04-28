<?php

namespace App\Livewire\Admin\Dashboard;

use App\Models\Assessor;
use App\Models\Event;
use App\Models\NilaiJpm;
use App\Models\Peserta;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.admin.app', ['title' => 'Dashboard'])]
class Index extends Component
{
    public $total_event;
    public $total_peserta;
    public $total_assessor;
    public $tes_hari_ini;
    public $total_event_tes_potensi_per_bulan;
    public $tahun;
    public $event;
    public $avg_skor;
    public $jpm = [];
    public $event_name;
    public $jumlah_peserta_per_tahun;

    public function mount()
    {
        $this->tahun ??= now()->year;
        $this->total_event = Event::count();
        $this->total_peserta = Peserta::count();
        $this->total_assessor = Assessor::count();
        $this->tes_hari_ini = Event::whereDate('tgl_mulai', now()->format('Y-m-d'))
            ->whereIsFinished('false')
            ->count();

        $this->updateChartEvent();
        $this->updateJumlahPesertaPerTahun();
        $this->updateRadarChart();
        $this->updateJpmRank();
    }

    public function updatedTahun()
    {
        $this->updateChartEvent();
        $this->updateJumlahPesertaPerTahun();
    }

    public function updateJumlahPesertaPerTahun()
    {
        $this->jumlah_peserta_per_tahun = Peserta::whereHas('event', function ($query) {
            $query->whereYear('tgl_mulai', $this->tahun)
                ->where('metode_tes_id', 2)
                ->whereIsActive('true');
        })
        ->get()
        ->unique('id')
        ->count();
    }

    public function resetFilterEvent()
    {
        $this->dispatch('reset-select2');
        $this->dispatch('update-radar-chart', data: []);
    }

    public function updateChartEvent()
    {
        $total_event_tes_potensi_per_bulan = Event::selectRaw('MONTH(tgl_mulai) as bulan, COUNT(*) as total')
            ->where('metode_tes_id', 2)
            ->whereYear('tgl_mulai', $this->tahun)
            ->groupBy(DB::raw('MONTH(tgl_mulai)'))
            ->orderBy(DB::raw('MONTH(tgl_mulai)'))
            ->pluck('total', 'bulan')
            ->toArray();

        $data_chart = [];
        for ($i = 1; $i <= 12; $i++) {
            $data_chart[] = $total_event_tes_potensi_per_bulan[$i] ?? 0;
        }

        $this->dispatch('update-chart', data: $data_chart);
    }

    public function updatedEvent()
    {
        $this->event_name = Event::find($this->event)?->nama_event;
        $this->updateRadarChart();
        $this->updateJpmRank();
    }

    public function updateJpmRank()
    {
        if (!$this->event) {
            $this->jpm = [];
            return;
        }

        $this->jpm = NilaiJpm::with('peserta')
            ->where('event_id', $this->event)
            ->orderByDesc('jpm')
            ->limit(5)
            ->get();
    }

    public function updateRadarChart()
    {
        if (!$this->event) {
            $this->avg_skor = [];
            $this->dispatch('update-radar-chart', data: []);
            return;
        }

        $avg_skor = [
            'Kemampuan Interpersonal' => DB::table('hasil_interpersonal')
                ->when($this->event, fn($q) => $q->where('event_id', $this->event))
                ->avg('skor_total'),

            'Kesadaran Diri' => DB::table('hasil_kesadaran_diri')
                ->when($this->event, fn($q) => $q->where('event_id', $this->event))
                ->avg('skor_total'),

            'Problem Solving' => DB::table('hasil_problem_solving')
                ->when($this->event, fn($q) => $q->where('event_id', $this->event))
                ->avg('skor_total'),

            'Berpikir Kritis dan Strategis' => DB::table('hasil_berpikir_kritis')
                ->when($this->event, fn($q) => $q->where('event_id', $this->event))
                ->avg('skor_total'),

            'Motivasi dan Komitmen' => DB::table('hasil_motivasi_komitmen')
                ->when($this->event, fn($q) => $q->where('event_id', $this->event))
                ->avg('skor_total'),

            'Kecerdasan Emosi' => DB::table('hasil_kecerdasan_emosi')
                ->when($this->event, fn($q) => $q->where('event_id', $this->event))
                ->avg('skor_total'),

            'Belajar Cepat dan Pengembangan Diri' => DB::table('hasil_pengembangan_diri')
                ->when($this->event, fn($q) => $q->where('event_id', $this->event))
                ->avg('skor_total'),
        ];

        $this->avg_skor = $avg_skor;
        $this->dispatch('update-radar-chart', data: array_values($avg_skor), eventName: $this->event ? Event::find($this->event)?->nama_event : '');
    }

    public function render()
    {
        $list_tahun = Event::selectRaw('YEAR(tgl_mulai) as tahun')
            ->distinct()
            ->orderByDesc('tahun')
            ->pluck('tahun');

        $list_event = Event::pluck('nama_event', 'id');

        return view('livewire.admin.dashboard.index', [
            'data_chart' => $this->total_event_tes_potensi_per_bulan,
            'list_tahun' => $list_tahun,
            'list_event' => $list_event,
            'avg_skor' => $this->avg_skor
        ]);
    }
}
