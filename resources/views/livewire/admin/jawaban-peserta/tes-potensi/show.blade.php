<div>
    <x-breadcrumb :breadcrumbs="[
        ['url' => route('admin.dashboard'), 'title' => 'Dashboard'],
        ['url' => route('admin.jawaban-peserta.tes-potensi'), 'title' => 'Analisis Jawaban — Tes Potensi'],
        ['url' => null, 'title' => 'Detail Event']
    ]" />

    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex flex-wrap align-items-start justify-content-between gap-3 mb-3">
                        <div>
                            <h5 class="card-title mb-2">{{ $event->nama_event }}</h5>
                            <div class="d-flex flex-wrap gap-2">
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle">Tes Potensi</span>
                                <span class="badge bg-light text-dark border">
                                    <i data-feather="calendar" style="width:12px;height:12px;" class="me-1"></i>
                                    {{ $event->tgl_mulai }}
                                </span>
                                @if ($event->metodeTes)
                                    <span class="badge bg-secondary-subtle text-secondary border">{{ $event->metodeTes->metode_tes ?? 'Potensi' }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="d-flex flex-wrap gap-2 align-items-center">
                            <span class="badge rounded-pill bg-light text-dark border px-3 py-2">
                                Subtes aktif: {{ $subtesLabel }}
                            </span>
                        </div>
                    </div>

                    <ul class="nav nav-tabs nav-tabs-line mb-3 flex-wrap" role="tablist">
                        @foreach ($subtestTabs as $tab)
                            <li class="nav-item" wire:key="tab-potensi-{{ $tab['key'] }}">
                                <button type="button"
                                    class="nav-link {{ $subtes === $tab['key'] ? 'active' : '' }}"
                                    wire:click="setSubtes('{{ $tab['key'] }}')">
                                    {{ $tab['label'] }}
                                </button>
                            </li>
                        @endforeach
                    </ul>

                    <div class="d-flex flex-wrap justify-content-end gap-2 mb-3">
                        <button type="button" wire:click="downloadExcelSubtes" wire:loading.attr="disabled"
                            class="btn btn-sm btn-outline-success btn-icon-text">
                            <i class="btn-icon-prepend" data-feather="download"></i>
                            <span wire:loading.remove wire:target="downloadExcelSubtes">Excel Subtes Ini</span>
                            <span wire:loading wire:target="downloadExcelSubtes">Mengunduh...</span>
                        </button>
                        <button type="button" wire:click="downloadExcelSemua" wire:loading.attr="disabled"
                            class="btn btn-sm btn-success btn-icon-text">
                            <i class="btn-icon-prepend" data-feather="download-cloud"></i>
                            <span wire:loading.remove wire:target="downloadExcelSemua">Excel Semua Subtes</span>
                            <span wire:loading wire:target="downloadExcelSemua">Mengunduh...</span>
                        </button>
                    </div>

                    <div wire:loading.delay.short wire:target="setSubtes" class="text-muted small mb-2">Memuat analisa subtes...</div>
                    <livewire:admin.jawaban-peserta.tes-potensi.analisa
                        :event-id="$id_event"
                        :subtes="$subtes"
                        :key="'analisa-potensi-'.$id_event.'-'.$subtes" />

                    <h6 class="fw-semibold mb-2">Jawaban per peserta</h6>

                    <x-monitoring.filter-panel>
                        <div class="row g-2 align-items-end">
                            <div class="col-md-6 col-lg-5">
                                <label class="form-label small text-muted mb-1">Cari Peserta</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white"><i data-feather="search" style="width:16px;height:16px;"></i></span>
                                    <input wire:model.live.debounce.300ms="search" class="form-control" placeholder="Nama atau NIP..." autocomplete="off">
                                </div>
                            </div>
                            <div class="col-md-3 col-lg-2">
                                <x-btn-reset :text="'Reset'" />
                            </div>
                        </div>
                    </x-monitoring.filter-panel>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle shadow-sm border rounded mb-0">
                            <thead class="table-light border-bottom">
                                <tr>
                                    <th class="text-center" style="width: 45px;">#</th>
                                    <th>NIP</th>
                                    <th>Nama Peserta</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Progres</th>
                                    <th class="text-center" style="width: 140px;">Detail</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($data as $index => $ujian)
                                    @php
                                        $peserta = $ujian->peserta;
                                        $progress = $progressByUjian[$ujian->id] ?? null;
                                    @endphp
                                    <tr wire:key="ujian-potensi-{{ $subtes }}-{{ $ujian->id }}">
                                        <td class="text-center text-secondary fw-bold">{{ $data->firstItem() + $index }}</td>
                                        <td class="text-nowrap font-monospace small">{{ $peserta?->nip ?? '-' }}</td>
                                        <td class="fw-medium">{{ $peserta?->nama ?? '-' }}</td>
                                        <td class="text-center">
                                            @if ($progress && $progress['is_finished'])
                                                <span class="badge bg-success-subtle text-success border">Selesai</span>
                                            @else
                                                <span class="badge bg-warning-subtle text-warning-emphasis border">Berlangsung</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if ($progress)
                                                <span class="badge bg-light text-dark border">
                                                    {{ $progress['terjawab'] }}/{{ $progress['total_soal'] }}
                                                </span>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <button type="button"
                                                class="btn btn-sm btn-outline-warning btn-icon rounded-circle border-0 shadow-sm"
                                                wire:click="bukaJawaban({{ $ujian->id }})"
                                                wire:loading.attr="disabled"
                                                wire:target="bukaJawaban"
                                                title="Lihat jawaban subtes">
                                                <i class="link-icon" data-feather="eye"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-5">
                                            <i class="link-icon" data-feather="users" style="font-size: 28px; opacity: 0.6;"></i>
                                            <div class="mt-2 fw-semibold">Tidak ada peserta untuk subtes ini.</div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if ($detailUjianId)
                        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(15, 23, 42, .45);" wire:click.self="tutupJawaban" wire:key="detail-potensi-{{ $subtes }}-{{ $detailUjianId }}">
                            <div class="modal-dialog modal-xl modal-dialog-scrollable">
                                <div class="modal-content">
                                    <div class="modal-header border-bottom">
                                        <div>
                                            <h5 class="modal-title mb-1">{{ $detailPeserta?->nama ?? '-' }}</h5>
                                            <div class="small text-muted">
                                                {{ $subtesLabel }} · NIP: <span class="font-monospace">{{ $detailPeserta?->nip ?? '-' }}</span>
                                            </div>
                                        </div>
                                        <button type="button" class="btn-close" wire:click="tutupJawaban" aria-label="Tutup"></button>
                                    </div>
                                    <div class="modal-body bg-body-tertiary">
                                        @if ($useCardLayout)
                                            @forelse ($detailRows as $row)
                                                <div class="card mb-3 border shadow-sm bg-white">
                                                    <div class="card-body">
                                                        <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-2">
                                                            <span class="badge bg-dark rounded-pill">Soal {{ $row['nomor'] }}</span>
                                                            @if ($row['poin'] !== null && $row['poin_max'] !== null)
                                                                <span class="badge bg-primary-subtle text-primary border">Poin: {{ $row['poin'] }}/{{ $row['poin_max'] }}</span>
                                                            @elseif ($row['status'] === 'unanswered')
                                                                <span class="badge bg-secondary">Belum dijawab</span>
                                                            @endif
                                                        </div>
                                                        <p class="small text-muted mb-1">{{ Str::limit($row['indikator'], 100) }}</p>
                                                        <p class="mb-3 fw-medium text-wrap">{{ $row['pertanyaan'] }}</p>
                                                        <div class="p-3 rounded border bg-light">
                                                            <div class="small text-muted text-uppercase fw-semibold mb-1">Jawaban</div>
                                                            <div class="fw-semibold">{{ $row['jawaban_peserta'] }}</div>
                                                        </div>
                                                        <div class="mt-2 small text-muted">Skor per opsi: {{ $row['skor_opsi'] }}</div>
                                                    </div>
                                                </div>
                                            @empty
                                                <div class="text-center text-muted py-4">Belum ada data jawaban.</div>
                                            @endforelse
                                        @else
                                            <div class="table-responsive">
                                                <table class="table table-sm table-bordered bg-white mb-0">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th class="text-center" style="width: 48px;">No</th>
                                                            <th>Indikator</th>
                                                            <th>Jawaban</th>
                                                            <th class="text-center" style="width: 72px;">Poin</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @forelse ($detailRows as $row)
                                                            <tr>
                                                                <td class="text-center">{{ $row['nomor'] }}</td>
                                                                <td class="small">{{ Str::limit($row['indikator'], 60) }}</td>
                                                                <td class="small">{{ $row['jawaban_peserta'] }}</td>
                                                                <td class="text-center">{{ $row['poin'] !== null ? $row['poin'] : '-' }}</td>
                                                            </tr>
                                                        @empty
                                                            <tr>
                                                                <td colspan="4" class="text-center text-muted py-3">Belum ada data.</td>
                                                            </tr>
                                                        @endforelse
                                                    </tbody>
                                                </table>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-sm btn-secondary" wire:click="tutupJawaban">Tutup</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <x-pagination :items="$data" />
    </div>
</div>
