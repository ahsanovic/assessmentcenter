<div data-admin-dashboard data-dashboard-config='@json($dashboard_chart_config)'>
    <div class="mb-4">
        <h4 class="mb-1">Dashboard</h4>
        <p class="text-secondary mb-0">Ringkasan statistik keseluruhan Assessment Center</p>
    </div>

    {{-- Statistik utama --}}
    <div class="row">
        <div class="col-md-3 col-sm-6">
            <a href="{{ route('admin.event') }}" wire:navigate class="text-decoration-none text-body">
                <div class="card mb-3">
                    <div class="card-body">
                        <h6 class="card-title text-secondary mb-2">Total Event</h6>
                        <h2 class="mb-0 fw-bold">{{ number_format($total_event) }}</h2>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card mb-3">
                <div class="card-body">
                    <h6 class="card-title text-secondary mb-2">Total Peserta</h6>
                    <h2 class="mb-0 fw-bold">{{ number_format($total_peserta) }}</h2>
                    <small class="text-muted">{{ number_format($peserta_aktif) }} aktif</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <a href="{{ route('admin.assessor') }}" wire:navigate class="text-decoration-none text-body">
                <div class="card mb-3">
                    <div class="card-body">
                        <h6 class="card-title text-secondary mb-2">Total Assessor</h6>
                        <h2 class="mb-0 fw-bold">{{ number_format($total_assessor) }}</h2>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-3 col-sm-6">
            <a href="{{ route('admin.tes-berlangsung') }}" wire:navigate class="text-decoration-none text-body">
                <div class="card mb-3">
                    <div class="card-body">
                        <h6 class="card-title text-secondary mb-2">Event Mulai Hari Ini</h6>
                        <h2 class="mb-0 fw-bold">{{ number_format($tes_hari_ini) }}</h2>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-3 col-sm-6">
            <div class="card mb-3 border-start border-4 border-primary">
                <div class="card-body py-3">
                    <h6 class="card-title text-secondary mb-1">Event Berlangsung</h6>
                    <h3 class="mb-0">{{ number_format($event_aktif) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card mb-3 border-start border-4 border-success">
                <div class="card-body py-3">
                    <h6 class="card-title text-secondary mb-1">Event Selesai</h6>
                    <h3 class="mb-0">{{ number_format($event_selesai) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card mb-3 border-start border-4 border-danger">
                <div class="card-body py-3">
                    <h6 class="card-title text-secondary mb-1">Total Pelanggaran Tes</h6>
                    <p class="text-muted mb-1" style="font-size: 11px;">Semua instrumen (potensi, PSPK, cakap digital, dll.)</p>
                    <h3 class="mb-0">{{ number_format($total_pelanggaran) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card mb-3 border-start border-4 border-warning">
                <div class="card-body py-3">
                    <h6 class="card-title text-secondary mb-1">Peserta Aktif</h6>
                    <h3 class="mb-0">{{ number_format($peserta_aktif) }}</h3>
                </div>
            </div>
        </div>
    </div>

    {{-- Grafik event per metode --}}
    <div class="row">
        <div class="col-lg-8 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2">
                        <div>
                            <h6 class="card-title mb-0">Grafik Event per Metode Tes</h6>
                            <p class="text-secondary mb-0 small">Jumlah event yang dimulai per bulan — semua jenis tes</p>
                        </div>
                        <select wire:model.live="tahun" id="tahun" class="form-select w-auto">
                            @foreach ($list_tahun as $t)
                                <option value="{{ $t }}">{{ $t }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div id="chart-event" style="min-height: 360px;"></div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 grid-margin stretch-card">
            <div class="card h-100">
                <div class="card-body">
                    <h6 class="card-title mb-1">Distribusi Event</h6>
                    <p class="text-secondary small mb-3">Per metode tes — tahun {{ $tahun }}</p>
                    <div id="chart-metode" style="min-height: 320px;"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Statistik instrumen --}}
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-3">
                        <div>
                            <h6 class="card-title mb-1">Statistik Instrumen Tes</h6>
                            <p class="text-secondary small mb-0">
                                Penyelesaian dan rata-rata skor seluruh alat tes
                                @if ($event_name)
                                    — <strong>{{ $event_name }}</strong>
                                @else
                                    — tahun <strong>{{ $tahun }}</strong> (semua event)
                                @endif
                            </p>
                        </div>
                        <div class="d-flex flex-wrap align-items-center gap-2">
                            <div wire:ignore>
                                <select id="dashboard-filter-event" class="form-select" style="min-width: 280px">
                                    <option value="">Semua event (filter tahun)</option>
                                    @foreach ($list_event as $key => $nama)
                                        <option value="{{ $key }}">{{ $nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="button" wire:click="resetFilterEvent" class="btn btn-sm btn-inverse-danger ac-btn-reset">Reset</button>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6">
                            <p class="text-muted small fw-medium mb-2">Jumlah peserta menyelesaikan tes</p>
                            <div id="chart-penyelesaian" style="min-height: 420px;"></div>
                        </div>
                        <div class="col-lg-6">
                            <p class="text-muted small fw-medium mb-2">Rata-rata skor / nilai</p>
                            <div id="chart-skor" style="min-height: 420px;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- JPM & kategori capaian tes potensi / PSPK --}}
    <div class="row">
        <div class="col-lg-6 grid-margin stretch-card">
            <div class="card h-100">
                <div class="card-body">
                    <h6 class="card-title mb-1">Rata-rata JPM (Person Job Fit) — Tes Potensi</h6>
                    <p class="text-secondary small mb-2">
                        Rata-rata per event (maks. 10 event terbaru)
                        @if ($event_name)
                            — <strong>{{ $event_name }}</strong>
                        @else
                            — tahun <strong>{{ $tahun }}</strong>
                        @endif
                        @if (! empty($jpm_potensi_chart['rata_keseluruhan']))
                            <span class="d-block mt-1">Rata-rata keseluruhan: <strong>{{ $jpm_potensi_chart['rata_keseluruhan'] }}%</strong></span>
                        @endif
                    </p>
                    <div id="chart-jpm-potensi" style="min-height: 380px;"></div>
                </div>
            </div>
        </div>
        <div class="col-lg-6 grid-margin stretch-card">
            <div class="card h-100">
                <div class="card-body">
                    <h6 class="card-title mb-1">Distribusi Kategori Capaian</h6>
                    <p class="text-secondary small mb-2">
                        Optimal, Cukup Optimal, Kurang Optimal — Tes Potensi (JPM) &amp; PSPK
                        @if ($event_name)
                            — <strong>{{ $event_name }}</strong>
                        @else
                            — tahun <strong>{{ $tahun }}</strong>
                        @endif
                    </p>
                    <p class="text-muted mb-2" style="font-size: 11px;">
                        Tes potensi: kategori JPM (Tinggi / Menengah / Rendah) dipetakan ke tingkat capaian yang sama.
                    </p>
                    <div id="chart-kategori-capaian" style="min-height: 380px;"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Ringkasan tambahan --}}
    <div class="row">
        <div class="col-lg-5 grid-margin stretch-card">
            <div class="card h-100">
                <div class="card-body">
                    <h6 class="card-title mb-3">Peserta Aktif per Metode Tes</h6>
                    <ul class="list-group list-group-flush">
                        @forelse ($peserta_per_metode as $metodeId => $jumlah)
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <span>{{ $metode_labels[$metodeId] ?? "Metode #{$metodeId}" }}</span>
                                <span class="badge bg-primary rounded-pill">{{ number_format($jumlah) }}</span>
                            </li>
                        @empty
                            <li class="list-group-item text-secondary px-0">Belum ada data peserta.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-lg-7 grid-margin stretch-card">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="card-title mb-0">Event Terbaru</h6>
                        <a href="{{ route('admin.event') }}" wire:navigate class="btn btn-sm btn-primary">Lihat semua</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Nama Event</th>
                                    <th>Metode Tes</th>
                                    <th>Tanggal</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($event_terbaru as $ev)
                                    <tr>
                                        <td class="text-wrap">{{ $ev->nama_event }}</td>
                                        <td>{{ $ev->metodeTes?->metode_tes ?? '-' }}</td>
                                        <td>{{ $ev->tgl_mulai }}</td>
                                        <td>
                                            @if ($ev->is_finished == 'true')
                                                <span class="badge bg-success">Selesai</span>
                                            @else
                                                <span class="badge bg-warning text-dark">Berlangsung</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-secondary text-center">Belum ada event.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
