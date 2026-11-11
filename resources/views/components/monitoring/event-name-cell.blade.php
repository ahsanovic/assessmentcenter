@props([
    'nama',
    'namaGrup' => null,
])

<div {{ $attributes->merge(['class' => 'ac-event-name-cell']) }}>
    <div class="ac-event-name-cell__title">{{ $nama }}</div>
    @if (filled($namaGrup))
        <x-monitoring.event-group-hint :nama="$namaGrup" :compact="true" />
    @endif
</div>
