@props([
    'title' => 'Filter & Pencarian',
    'hint' => 'Saring data sesuai kebutuhan Anda.',
    'badge' => null,
])

<div {{ $attributes->merge(['class' => 'card mt-4 mb-4 bg-light-subtle ac-filter-panel']) }}>
    <div class="card-body">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
            <div class="d-flex align-items-center gap-2">
                <span class="rounded-circle bg-danger bg-opacity-10 text-danger d-inline-flex align-items-center justify-content-center"
                      style="width: 36px; height: 36px;">
                    <i data-feather="filter" style="width: 18px; height: 18px;"></i>
                </span>
                <div>
                    <h6 class="mb-0 fw-semibold">{{ $title }}</h6>
                    @if ($hint)
                        <small class="text-muted">{{ $hint }}</small>
                    @endif
                </div>
            </div>
            @if ($badge !== null)
                <span class="badge rounded-pill bg-secondary-subtle text-secondary border">
                    {{ $badge }}
                </span>
            @endif
        </div>
        {{ $slot }}
    </div>
</div>
