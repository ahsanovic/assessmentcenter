<?php

namespace App\Livewire\Admin\Dashboard;

use App\Models\Assessor;
use App\Models\Event;
use App\Models\LogPelanggaran;
use App\Models\NilaiJpm;
use App\Models\Peserta;
use App\Models\RefMetodeTes;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.admin.app', ['title' => 'Dashboard'])]
class Index extends Component
{
    public $total_event;

    public $total_peserta;

    public $total_assessor;

    public $tes_hari_ini;

    public $event_aktif;

    public $event_selesai;

    public $total_pelanggaran;

    public $peserta_aktif;

    public $tahun;

    public $event;

    public $event_name;

    public $ringkasan_metode = [];

    public $penyelesaian_instrumen = [];

    public $rata_skor_instrumen = [];

    public $event_chart_series = [];

    public $jpm_potensi_chart = [];

    public $distribusi_kategori_capaian = [];

    public function mount()
    {
        $this->tahun ??= now()->year;
        $this->loadRingkasanUtama();
        $this->updateChartEvent();
        $this->updateDistribusiMetode();
        $this->updateStatistikInstrumen();
        $this->updatePotensiPspkCharts();
    }

    public function updatedTahun()
    {
        $this->updateChartEvent();
        $this->updateDistribusiMetode();
        $this->updateStatistikInstrumen();
        $this->updatePotensiPspkCharts();
    }

    public function updatedEvent()
    {
        $this->event_name = Event::find($this->event)?->nama_event;
        $this->updateStatistikInstrumen();
        $this->updatePotensiPspkCharts();
    }

    public function resetFilterEvent()
    {
        $this->event = null;
        $this->event_name = null;
        $this->dispatch('reset-select2');
        $this->updateStatistikInstrumen();
        $this->updatePotensiPspkCharts();
    }

    protected function loadRingkasanUtama(): void
    {
        $this->total_event = Event::count();
        $this->total_peserta = Peserta::count();
        $this->total_assessor = Assessor::count();
        $this->tes_hari_ini = Event::whereDate('tgl_mulai', now()->format('Y-m-d'))
            ->whereIsFinished('false')
            ->count();
        $this->event_aktif = Event::whereIsFinished('false')->count();
        $this->event_selesai = Event::whereIsFinished('true')->count();
        $this->total_pelanggaran = LogPelanggaran::count();
        $this->peserta_aktif = Peserta::where('is_active', 'true')->count();
    }

    /**
     * @return array<int, int>
     */
    protected function eventIdsForScope(): array
    {
        if ($this->event) {
            return [(int) $this->event];
        }

        return Event::whereYear('tgl_mulai', $this->tahun)->pluck('id')->map(fn ($id) => (int) $id)->all();
    }

    /**
     * @return array<int, int>
     */
    protected function potensiEventIdsForScope(): array
    {
        $ids = $this->eventIdsForScope();
        if ($ids === []) {
            return [];
        }

        return Event::query()
            ->whereIn('id', $ids)
            ->whereIn('metode_tes_id', [1, 2])
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();
    }

    /**
     * @return array<int, int>
     */
    protected function pspkEventIdsForScope(): array
    {
        $ids = $this->eventIdsForScope();
        if ($ids === []) {
            return [];
        }

        return Event::query()
            ->whereIn('id', $ids)
            ->whereIn('metode_tes_id', [5, 6, 7, 8])
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();
    }

    protected function normalizeKategoriCapaianTier(?string $kategori): ?string
    {
        if ($kategori === null || trim($kategori) === '') {
            return null;
        }

        $k = strtolower(trim($kategori));

        if (str_contains($k, 'kurang') || $k === 'rendah') {
            return 'Kurang Optimal';
        }

        if (str_contains($k, 'cukup') || $k === 'menengah') {
            return 'Cukup Optimal';
        }

        if (str_contains($k, 'optimal') || $k === 'tinggi') {
            return 'Optimal';
        }

        return null;
    }

    /**
     * @return array{Optimal: int, Cukup Optimal: int, Kurang Optimal: int}
     */
    protected function emptyKategoriCapaianBuckets(): array
    {
        return [
            'Optimal' => 0,
            'Cukup Optimal' => 0,
            'Kurang Optimal' => 0,
        ];
    }

    public function updatePotensiPspkCharts(): void
    {
        $this->updateJpmPotensiChart();
        $this->updateDistribusiKategoriCapaian();
    }

    public function updateJpmPotensiChart(): void
    {
        $eventIds = $this->potensiEventIdsForScope();

        $labels = [];
        $values = [];
        $rataKeseluruhan = null;

        if ($eventIds !== []) {
            $rows = NilaiJpm::query()
                ->join('event', 'event.id', '=', 'nilai_jpm.event_id')
                ->whereIn('nilai_jpm.event_id', $eventIds)
                ->select(
                    'nilai_jpm.event_id',
                    'event.nama_event',
                    DB::raw('ROUND(AVG(nilai_jpm.jpm), 2) as rata_jpm'),
                    DB::raw('MAX(event.tgl_mulai) as tgl_event')
                )
                ->groupBy('nilai_jpm.event_id', 'event.nama_event')
                ->orderByDesc('tgl_event')
                ->limit(10)
                ->get();

            $labels = $rows->map(fn ($row) => Str::limit($row->nama_event, 45))->all();
            $values = $rows->map(fn ($row) => (float) $row->rata_jpm)->all();

            $avg = NilaiJpm::query()->whereIn('event_id', $eventIds)->avg('jpm');
            $rataKeseluruhan = $avg !== null ? round((float) $avg, 2) : null;
        }

        $this->jpm_potensi_chart = [
            'labels' => $labels,
            'values' => $values,
            'rata_keseluruhan' => $rataKeseluruhan,
        ];

        $this->dispatch(
            'update-jpm-potensi-chart',
            labels: $labels,
            values: $values,
            rataKeseluruhan: $rataKeseluruhan,
        );
    }

    public function updateDistribusiKategoriCapaian(): void
    {
        $tierLabels = ['Optimal', 'Cukup Optimal', 'Kurang Optimal'];
        $potensiBuckets = $this->emptyKategoriCapaianBuckets();
        $pspkBuckets = $this->emptyKategoriCapaianBuckets();

        $potensiEventIds = $this->potensiEventIdsForScope();
        if ($potensiEventIds !== []) {
            NilaiJpm::query()
                ->whereIn('event_id', $potensiEventIds)
                ->pluck('kategori')
                ->each(function ($kategori) use (&$potensiBuckets) {
                    $tier = $this->normalizeKategoriCapaianTier($kategori);
                    if ($tier !== null) {
                        $potensiBuckets[$tier]++;
                    }
                });
        }

        $pspkEventIds = $this->pspkEventIdsForScope();
        if ($pspkEventIds !== []) {
            DB::table('hasil_pspk')
                ->whereIn('event_id', $pspkEventIds)
                ->pluck('kategori')
                ->each(function ($kategori) use (&$pspkBuckets) {
                    $tier = $this->normalizeKategoriCapaianTier($kategori);
                    if ($tier !== null) {
                        $pspkBuckets[$tier]++;
                    }
                });
        }

        $this->distribusi_kategori_capaian = [
            'labels' => $tierLabels,
            'potensi' => array_values($potensiBuckets),
            'pspk' => array_values($pspkBuckets),
        ];

        $this->dispatch(
            'update-kategori-capaian-chart',
            labels: $tierLabels,
            potensi: array_values($potensiBuckets),
            pspk: array_values($pspkBuckets),
        );
    }

    public function updateChartEvent(): void
    {
        $metodeGroups = [
            'Assessment Center' => [1],
            'Tes Potensi' => [2],
            'Cakap Digital' => [3],
            'Kompetensi Teknis' => [4],
            'PSPK' => [5, 6, 7, 8],
        ];

        $series = [];
        foreach ($metodeGroups as $label => $metodeIds) {
            $perBulan = Event::selectRaw('MONTH(tgl_mulai) as bulan, COUNT(*) as total')
                ->whereIn('metode_tes_id', $metodeIds)
                ->whereYear('tgl_mulai', $this->tahun)
                ->groupBy(DB::raw('MONTH(tgl_mulai)'))
                ->orderBy(DB::raw('MONTH(tgl_mulai)'))
                ->pluck('total', 'bulan')
                ->toArray();

            $data = [];
            for ($i = 1; $i <= 12; $i++) {
                $data[] = (int) ($perBulan[$i] ?? 0);
            }

            $series[] = [
                'name' => $label,
                'data' => $data,
            ];
        }

        $this->event_chart_series = $series;
        $this->dispatch('update-event-chart', series: $series);
    }

    public function updateDistribusiMetode(): void
    {
        $distribusi = Event::query()
            ->select('metode_tes_id', DB::raw('COUNT(*) as total'))
            ->whereYear('tgl_mulai', $this->tahun)
            ->groupBy('metode_tes_id')
            ->pluck('total', 'metode_tes_id');

        $metodeLabels = RefMetodeTes::pluck('metode_tes', 'id');

        $labels = [];
        $values = [];
        foreach ($distribusi as $metodeId => $total) {
            $labels[] = $metodeLabels[$metodeId] ?? "Metode #{$metodeId}";
            $values[] = (int) $total;
        }

        $this->ringkasan_metode = [
            'labels' => $labels,
            'values' => $values,
        ];

        $this->dispatch('update-metode-chart', labels: $labels, values: $values);
    }

    public function updateStatistikInstrumen(): void
    {
        $eventIds = $this->eventIdsForScope();

        $countIn = function (string $table) use ($eventIds): int {
            if ($eventIds === []) {
                return 0;
            }

            return (int) DB::table($table)->whereIn('event_id', $eventIds)->count();
        };

        $avgIn = function (string $table, string $column) use ($eventIds): ?float {
            if ($eventIds === []) {
                return null;
            }

            $avg = DB::table($table)->whereIn('event_id', $eventIds)->avg($column);

            return $avg !== null ? round((float) $avg, 2) : null;
        };

        $penyelesaian = [
            'Tes Intelektual' => $countIn('hasil_intelektual'),
            'Kemampuan Interpersonal' => $countIn('hasil_interpersonal'),
            'Kesadaran Diri' => $countIn('hasil_kesadaran_diri'),
            'Problem Solving' => $countIn('hasil_problem_solving'),
            'Berpikir Kritis & Strategis' => $countIn('hasil_berpikir_kritis'),
            'Motivasi & Komitmen' => $countIn('hasil_motivasi_komitmen'),
            'Kecerdasan Emosi' => $countIn('hasil_kecerdasan_emosi'),
            'Pengembangan Diri' => $countIn('hasil_pengembangan_diri'),
            'Cakap Digital' => $countIn('hasil_cakap_digital'),
            'PSPK' => $countIn('hasil_pspk'),
        ];

        if (Schema::hasTable('hasil_kompetensi_teknis')) {
            $penyelesaian['Kompetensi Teknis'] = $countIn('hasil_kompetensi_teknis');
        } elseif (Schema::hasTable('ujian_kompetensi_teknis')) {
            $penyelesaian['Kompetensi Teknis'] = $eventIds === []
                ? 0
                : (int) DB::table('ujian_kompetensi_teknis')
                    ->whereIn('event_id', $eventIds)
                    ->where('is_finished', 'true')
                    ->count();
        }

        $rataSkor = [
            'Tes Intelektual' => $avgIn('hasil_intelektual', 'nilai_total'),
            'Kemampuan Interpersonal' => $avgIn('hasil_interpersonal', 'skor_total'),
            'Kesadaran Diri' => $avgIn('hasil_kesadaran_diri', 'skor_total'),
            'Problem Solving' => $avgIn('hasil_problem_solving', 'skor_total'),
            'Berpikir Kritis & Strategis' => $avgIn('hasil_berpikir_kritis', 'skor_total'),
            'Motivasi & Komitmen' => $avgIn('hasil_motivasi_komitmen', 'skor_total'),
            'Kecerdasan Emosi' => $avgIn('hasil_kecerdasan_emosi', 'skor_total'),
            'Pengembangan Diri' => $avgIn('hasil_pengembangan_diri', 'skor_total'),
            'PSPK' => $avgIn('hasil_pspk', 'jpm'),
        ];

        if ($eventIds !== []) {
            $cakap = DB::table('hasil_cakap_digital')
                ->whereIn('event_id', $eventIds)
                ->selectRaw('AVG((COALESCE(jpm_literasi, 0) + COALESCE(jpm_emerging, 0)) / 2) as rata')
                ->value('rata');
            $rataSkor['Cakap Digital'] = $cakap !== null ? round((float) $cakap, 2) : null;
        } else {
            $rataSkor['Cakap Digital'] = null;
        }

        if (Schema::hasTable('hasil_kompetensi_teknis')) {
            $rataSkor['Kompetensi Teknis'] = $avgIn('hasil_kompetensi_teknis', 'nilai_total');
        }

        $this->penyelesaian_instrumen = $penyelesaian;
        $this->rata_skor_instrumen = $rataSkor;

        $this->dispatch(
            'update-instrument-charts',
            penyelesaianLabels: array_keys($penyelesaian),
            penyelesaianValues: array_values($penyelesaian),
            skorLabels: array_keys(array_filter($rataSkor, fn ($v) => $v !== null)),
            skorValues: array_values(array_filter($rataSkor, fn ($v) => $v !== null)),
            scopeLabel: $this->event_name ?: "Tahun {$this->tahun}",
        );
    }

    public function render()
    {
        $list_tahun = Event::selectRaw('YEAR(tgl_mulai) as tahun')
            ->distinct()
            ->orderByDesc('tahun')
            ->pluck('tahun');

        if ($list_tahun->isEmpty()) {
            $list_tahun = collect([now()->year]);
        }

        $list_event = Event::query()
            ->orderByDesc('tgl_mulai')
            ->pluck('nama_event', 'id');

        $event_terbaru = Event::with('metodeTes')
            ->orderByDesc('tgl_mulai')
            ->limit(8)
            ->get();

        $peserta_per_metode = Event::query()
            ->join('peserta', 'peserta.event_id', '=', 'event.id')
            ->where('peserta.is_active', 'true')
            ->select('event.metode_tes_id', DB::raw('COUNT(peserta.id) as total'))
            ->groupBy('event.metode_tes_id')
            ->pluck('total', 'metode_tes_id');

        $metodeLabels = RefMetodeTes::pluck('metode_tes', 'id');

        $rataSkorFiltered = array_filter($this->rata_skor_instrumen, fn ($v) => $v !== null);

        $dashboard_chart_config = [
            'eventSeries' => $this->event_chart_series,
            'metodeLabels' => $this->ringkasan_metode['labels'] ?? [],
            'metodeValues' => $this->ringkasan_metode['values'] ?? [],
            'penyelesaianLabels' => array_keys($this->penyelesaian_instrumen),
            'penyelesaianValues' => array_values($this->penyelesaian_instrumen),
            'skorLabels' => array_keys($rataSkorFiltered),
            'skorValues' => array_values($rataSkorFiltered),
            'jpmPotensiLabels' => $this->jpm_potensi_chart['labels'] ?? [],
            'jpmPotensiValues' => $this->jpm_potensi_chart['values'] ?? [],
            'kategoriCapaianLabels' => $this->distribusi_kategori_capaian['labels'] ?? ['Optimal', 'Cukup Optimal', 'Kurang Optimal'],
            'kategoriCapaianPotensi' => $this->distribusi_kategori_capaian['potensi'] ?? [0, 0, 0],
            'kategoriCapaianPspk' => $this->distribusi_kategori_capaian['pspk'] ?? [0, 0, 0],
            'filterEvent' => $this->event,
        ];

        return view('livewire.admin.dashboard.index', [
            'list_tahun' => $list_tahun,
            'list_event' => $list_event,
            'event_terbaru' => $event_terbaru,
            'peserta_per_metode' => $peserta_per_metode,
            'metode_labels' => $metodeLabels,
            'dashboard_chart_config' => $dashboard_chart_config,
        ]);
    }
}
