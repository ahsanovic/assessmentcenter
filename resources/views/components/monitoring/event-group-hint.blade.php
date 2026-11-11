@props([
    'nama',
    'compact' => false,
])

<div
    {{ $attributes->merge(['class' => 'ac-event-group-hint'.($compact ? ' ac-event-group-hint--compact' : '')]) }}
    data-bs-toggle="tooltip"
    data-bs-placement="top"
    title="{{ $nama }}"
>
    <span class="ac-event-group-hint__icon" wire:ignore><i data-feather="layers"></i></span>
    <span class="ac-event-group-hint__text">{{ $nama }}</span>
</div>
