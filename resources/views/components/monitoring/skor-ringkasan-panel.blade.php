@props([
    'ringkasan' => null,
])

@php
    $dto = $ringkasan;
@endphp

@if ($dto instanceof \App\Services\Statistik\EventSkorRingkasanDto)
    <div class="ac-skor-ringkasan mb-4" wire:loading.class="opacity-50 pe-none">
        <div class="card border-0 shadow-sm ac-skor-ringkasan__card">
            <div class="card-body">
                <div class="d-flex flex-wrap align-items-start justify-content-between gap-2 mb-3">
                    <div class="d-flex align-items-center gap-2">
                        <span class="ac-skor-ringkasan__icon rounded-circle d-inline-flex align-items-center justify-content-center">
                            <i data-feather="bar-chart-2" style="width: 18px; height: 18px;"></i>
                        </span>
                        <div>
                            <h6 class="mb-0 fw-semibold">Ringkasan {{ $dto->labelSkor }}</h6>
                            <small class="text-muted">
                                @if ($dto->filterAktif)
                                    Mengikuti filter pencarian &amp; tanggal tes yang aktif
                                @else
                                    Seluruh peserta pada event ini
                                @endif
                            </small>
                        </div>
                    </div>
                    @if ($dto->labelSkor === 'PJF' && $dto->totalPesertaPopulasi > 0)
                        <span class="badge rounded-pill bg-primary-subtle text-primary border border-primary-subtle">
                            {{ $dto->totalDenganSkor }} / {{ $dto->totalPesertaPopulasi }} dengan skor
                        </span>
                    @elseif ($dto->totalDenganSkor > 0)
                        <span class="badge rounded-pill bg-primary-subtle text-primary border border-primary-subtle">
                            {{ number_format($dto->totalDenganSkor) }} peserta
                        </span>
                    @endif
                </div>

                @if ($dto->peringatan)
                    <div class="alert alert-warning py-2 px-3 small mb-3 mb-md-4 d-flex align-items-start gap-2" role="alert">
                        <i data-feather="alert-circle" style="width: 16px; height: 16px; flex-shrink: 0; margin-top: 2px;"></i>
                        <span>{{ $dto->peringatan }}</span>
                    </div>
                @endif

                @if ($dto->kosong())
                    <p class="text-muted small mb-0">Belum ada data {{ $dto->labelSkor }} untuk ditampilkan.</p>
                @else
                    <div class="row g-3 mb-3 mb-md-4">
                        <div class="col-6 col-md-3">
                            <div class="ac-skor-ringkasan__kpi h-100">
                                <span class="ac-skor-ringkasan__kpi-label">Rata-rata</span>
                                <span class="ac-skor-ringkasan__kpi-value">{{ number_format((float) $dto->rata, 2) }}%</span>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="ac-skor-ringkasan__kpi h-100">
                                <span class="ac-skor-ringkasan__kpi-label">Terendah</span>
                                <span class="ac-skor-ringkasan__kpi-value text-danger">
                                    {{ $dto->terendah ? number_format($dto->terendah['nilai'], 2).'%' : '—' }}
                                </span>
                                @if ($dto->terendah)
                                    <span class="ac-skor-ringkasan__kpi-hint text-truncate d-block"
                                          title="{{ $dto->terendah['nama'] }}"
                                          data-bs-toggle="tooltip"
                                          data-bs-placement="bottom">
                                        {{ \Illuminate\Support\Str::limit($dto->terendah['nama'], 22) }}
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="ac-skor-ringkasan__kpi h-100">
                                <span class="ac-skor-ringkasan__kpi-label">Tertinggi</span>
                                <span class="ac-skor-ringkasan__kpi-value text-success">
                                    {{ $dto->tertinggi ? number_format($dto->tertinggi['nilai'], 2).'%' : '—' }}
                                </span>
                                @if ($dto->tertinggi)
                                    <span class="ac-skor-ringkasan__kpi-hint text-truncate d-block"
                                          title="{{ $dto->tertinggi['nama'] }}"
                                          data-bs-toggle="tooltip"
                                          data-bs-placement="bottom">
                                        {{ \Illuminate\Support\Str::limit($dto->tertinggi['nama'], 22) }}
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="ac-skor-ringkasan__kpi h-100"
                                 title="Selisih skor tertinggi dan terendah. Semakin kecil, sebaran skor peserta semakin rapat."
                                 data-bs-toggle="tooltip">
                                <span class="ac-skor-ringkasan__kpi-label">Selisih skor</span>
                                <span class="ac-skor-ringkasan__kpi-value">
                                    @if ($dto->terendah && $dto->tertinggi)
                                        {{ number_format($dto->tertinggi['nilai'] - $dto->terendah['nilai'], 2) }} poin
                                    @else
                                        —
                                    @endif
                                </span>
                                <span class="ac-skor-ringkasan__kpi-hint d-block">Tertinggi dikurangi terendah</span>
                            </div>
                        </div>
                    </div>

                    @if ($dto->distribusi !== [])
                        <div>
                            <h6 class="small fw-semibold text-secondary text-uppercase mb-2" style="letter-spacing: 0.04em;">
                                Distribusi kategori
                            </h6>
                            <ul class="list-unstyled mb-0 ac-skor-ringkasan__dist">
                                @foreach ($dto->distribusi as $item)
                                    @php
                                        $barColor = \App\Support\KategoriCapaian::barColor($item['kategori']);
                                        $badgeClass = \App\Support\KategoriCapaian::badgeClass($item['kategori']);
                                    @endphp
                                    <li class="ac-skor-ringkasan__dist-row mb-2">
                                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-1">
                                            <span class="badge border {{ $badgeClass }}">{{ $item['kategori'] }}</span>
                                            <span class="small text-muted">
                                                {{ number_format($item['jumlah']) }} peserta
                                                <span class="fw-semibold text-dark">({{ number_format($item['persen'], 1) }}%)</span>
                                            </span>
                                        </div>
                                        <div class="ac-skor-ringkasan__bar-track">
                                            <div class="ac-skor-ringkasan__bar-fill"
                                                 style="width: {{ min(100, max(0, $item['persen'])) }}%; background-color: {{ $barColor }};"></div>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>
@endif
