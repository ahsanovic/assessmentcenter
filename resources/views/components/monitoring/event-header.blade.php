@props([
    'nama',
    'namaGrup' => null,
])

<div class="ac-event-header">
    <div class="ac-event-header__icon" wire:ignore>
        <i data-feather="calendar"></i>
    </div>
    <div class="ac-event-header__content min-w-0">
        <div class="ac-event-header__label">Event</div>
        <div class="ac-event-header__name">{{ $nama }}</div>
        @if (filled($namaGrup))
            <x-monitoring.event-group-hint :nama="$namaGrup" />
        @endif
    </div>
</div>
