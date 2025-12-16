@props([
    'text' => 'Reset',
    'icon' => 'refresh-ccw',
    'class' => '',
])

<button wire:click="resetFilters" class="btn btn-sm btn-inverse-danger btn-icon-text {{ $class }}">
    <span wire:ignore><i class="btn-icon-prepend" data-feather="{{ $icon }}"></i>{{ $text }}</span>
</button>