@props([
    'route',
    'params' => [],
    'icon' => 'download',
    'tooltip' => 'Download Pdf',
    'color' => 'success',
    'target' => '',
    'navigate' => false,
    'disabled' => false,
])

@php
    $classes = "btn btn-sm btn-outline-{$color} btn-icon rounded-circle border-0 shadow-sm";
@endphp

<a 
    @if(! $disabled)
        href="{{ route($route, $params) }}"
    @endif
    class="{{ $classes }} {{ $disabled ? 'disabled opacity-50' : '' }}"
    target="{{ $target }}"
    data-bs-toggle="tooltip"
    data-bs-placement="top"
    title="{{ $tooltip }}"
    style="transition: background 0.2s; {{ $disabled ? 'pointer-events:none;' : '' }}"
    @if($navigate && ! $disabled) wire:navigate @endif
>
    <span wire:ignore>
        <i class="link-icon" data-feather="{{ $icon }}"></i>
    </span>
</a>