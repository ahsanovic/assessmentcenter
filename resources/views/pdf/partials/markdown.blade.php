@props([
    'html' => null,
    'fallback' => null,
    'empty' => '-',
])

@if (!empty($html))
    <div class="md">{!! $html !!}</div>
@elseif (!empty(trim((string) $fallback)))
    <div class="md">{!! nl2br(e($fallback)) !!}</div>
@else
    {{ $empty }}
@endif
