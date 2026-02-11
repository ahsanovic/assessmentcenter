@props([
    'route',
    'params' => [],
    'query' => [],
    'text' => 'Download',
    'icon' => 'download',
    'color' => 'success',
    'disabled' => false,
])

<a href="{{ route($route, $params) }}?{{ http_build_query($query) }}" class="btn btn-sm btn-icon-text btn-{{ $color }} {{ $disabled ? 'disabled' : '' }}">
    <span wire:ignore>
        <i class="btn-icon-prepend" data-feather="{{ $icon }}"></i>
    </span>
    {{ $text }}
</a>