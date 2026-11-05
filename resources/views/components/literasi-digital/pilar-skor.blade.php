@props([
    'skor',
    'level',
])

@php
    $level = (int) $level;
    $badgeClass = match ($level) {
        1 => 'bg-secondary-subtle text-secondary border-secondary-subtle',
        2 => 'bg-info-subtle text-info border-info-subtle',
        3 => 'bg-primary-subtle text-primary border-primary-subtle',
        4 => 'bg-warning-subtle text-dark border-warning-subtle',
        default => 'bg-success-subtle text-success border-success-subtle',
    };
@endphp

<div class="d-inline-flex flex-column align-items-center gap-1">
    <span class="fw-semibold lh-1">{{ $skor }}</span>
    <span class="badge rounded-pill border {{ $badgeClass }} px-2 py-1 fw-medium" style="font-size: 0.7rem;">
        Level {{ $level }}
    </span>
</div>
