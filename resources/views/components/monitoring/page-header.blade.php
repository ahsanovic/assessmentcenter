@props([
    'icon' => 'activity',
    'title',
    'description' => null,
    'color' => 'primary',
])

@php
    $colorClass = match ($color) {
        'success' => 'bg-success text-success',
        'warning' => 'bg-warning text-warning',
        'danger' => 'bg-danger text-danger',
        'info' => 'bg-info text-info',
        default => 'bg-primary text-primary',
    };
@endphp

<div {{ $attributes->merge(['class' => 'ac-page-header d-flex align-items-start justify-content-between flex-wrap gap-3 mb-3']) }}>
    <div class="d-flex align-items-center gap-3">
        <span class="ac-page-header__icon rounded-circle {{ $colorClass }} bg-opacity-10 d-inline-flex align-items-center justify-content-center"
              wire:ignore>
            <i data-feather="{{ $icon }}" style="width: 22px; height: 22px;"></i>
        </span>
        <div>
            <h5 class="mb-0 fw-bold">{{ $title }}</h5>
            @if ($description)
                <p class="text-muted mb-0 ac-page-header__desc">{{ $description }}</p>
            @endif
        </div>
    </div>
    @isset($actions)
        <div class="d-flex flex-wrap align-items-center gap-2">
            {{ $actions }}
        </div>
    @endisset
</div>
