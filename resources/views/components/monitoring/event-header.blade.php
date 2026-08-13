@props(['nama'])

<div class="ac-event-header">
    <div class="ac-event-header__icon" wire:ignore>
        <i data-feather="calendar"></i>
    </div>
    <div>
        <div class="ac-event-header__label">Event</div>
        <div class="ac-event-header__name">{{ $nama }}</div>
    </div>
</div>
