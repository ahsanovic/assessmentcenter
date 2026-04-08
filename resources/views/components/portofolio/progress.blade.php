@props([
    'progress',
])

@php
    $p = $progress;
    $isComplete = ($p['percent'] ?? 0) >= 100;
@endphp

<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm overflow-hidden border-start border-4 {{ $isComplete ? 'border-success' : 'border-primary' }}">
            <div class="card-body p-4">
                <div class="d-flex flex-wrap align-items-start justify-content-between gap-3 mb-3">
                    <div class="d-flex align-items-start gap-3">
                        <div class="rounded-3 p-2 flex-shrink-0 {{ $isComplete ? 'bg-success bg-opacity-10' : 'bg-primary bg-opacity-10' }}" wire:ignore>
                            <i data-feather="{{ $isComplete ? 'check-circle' : 'trending-up' }}" class="{{ $isComplete ? 'text-success' : 'text-primary' }}" style="width: 26px; height: 26px;"></i>
                        </div>
                        <div>
                            <h6 class="mb-1 fw-semibold text-dark">Progres pengisian portofolio</h6>
                            <p class="mb-0 small text-muted">
                                @if ($isComplete)
                                    Seluruh bagian wajib sudah terisi. Anda dapat meninjau ringkasan di halaman <a href="{{ route('peserta.portofolio') }}" class="text-primary" wire:navigate>portofolio</a>.
                                @else
                                    {{ $p['done'] }} dari {{ $p['total'] }} bagian wajib selesai — lengkapi bagian yang masih menunggu.@if (($p['metode_tes_id'] ?? 0) === 1) Pelatihan bersifat opsional.@endif
                                @endif
                            </p>
                        </div>
                    </div>
                    <div class="text-md-end ms-auto">
                        <div class="d-inline-flex flex-column align-items-end">
                            <span class="fs-2 fw-bold lh-1" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">
                                {{ $p['percent'] }}%
                            </span>
                            <span class="small text-muted mt-1">Kelengkapan bagian wajib</span>
                        </div>
                    </div>
                </div>

                <div class="progress rounded-pill mb-4" style="height: 12px; background: rgba(102, 126, 234, 0.12);">
                    <div
                        class="progress-bar border-0 rounded-pill"
                        role="progressbar"
                        style="width: {{ $p['percent'] }}%; background: linear-gradient(90deg, #667eea 0%, #764ba2 100%); min-width: {{ $p['percent'] > 0 ? '0.5rem' : '0' }};"
                        aria-valuenow="{{ $p['percent'] }}"
                        aria-valuemin="0"
                        aria-valuemax="100"
                    ></div>
                </div>

                <div class="row g-2">
                    @foreach ($p['sections'] as $section)
                        @php
                            $opsional = ! empty($section['optional']);
                        @endphp
                        <div class="col-12 col-sm-6 col-xl-4">
                            <a
                                href="{{ $section['route'] }}"
                                wire:navigate
                                class="d-block text-decoration-none rounded-3 p-3 h-100 border shadow-sm {{ $section['done'] ? 'bg-success bg-opacity-10 border-success border-opacity-25' : 'bg-light border-0' }}"
                                style="--bs-border-opacity: 0.35;"
                            >
                                <div class="d-flex align-items-start gap-2">
                                    <span wire:ignore class="flex-shrink-0 mt-0">
                                        <i data-feather="{{ $section['icon'] }}" class="text-{{ $section['color'] }}" style="width: 18px; height: 18px;"></i>
                                    </span>
                                    <div class="flex-grow-1 overflow-hidden">
                                        <div class="d-flex align-items-center justify-content-between gap-2 flex-wrap">
                                            <span class="fw-semibold small text-dark">{{ $section['label'] }}</span>
                                            @if ($opsional)
                                                @if ($section['done'])
                                                    <span class="badge rounded-pill bg-success bg-opacity-100 text-white small">Diisi</span>
                                                @else
                                                    <span class="badge rounded-pill bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 small">Opsional</span>
                                                @endif
                                            @else
                                                @if ($section['done'])
                                                    <span class="badge rounded-pill bg-success bg-opacity-100 text-white small">Selesai</span>
                                                @else
                                                    <span class="badge rounded-pill bg-white text-muted border small">Belum</span>
                                                @endif
                                            @endif
                                        </div>
                                        @if (! $section['done'])
                                            <p class="mb-0 mt-1 small text-muted">{{ $section['hint'] }}</p>
                                        @endif
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
