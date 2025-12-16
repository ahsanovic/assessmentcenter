@props([
    'url' => '#',
    'text' => 'Tambah',
    'icon' => 'edit',
    'class' => '',
])

<a href="{{ $url }}" wire:navigate wire:ignore class="btn btn-sm btn-outline-primary btn-icon-text {{ $class }}">
    <i class="btn-icon-prepend" data-feather="{{ $icon }}"></i>
    {{ $text }}
</a>

