@props(['answered' => 0, 'total' => 0])

@php
    $total = max(0, (int) $total);
    $answered = max(0, min((int) $answered, $total > 0 ? $total : (int) $answered));
    $pct = $total > 0 ? min(100, (int) round(($answered / $total) * 100)) : 0;
    $tone = $pct >= 100 ? 'success' : ($pct >= 50 ? 'primary' : 'warning');
@endphp

<div class="ac-progress-answer" style="min-width: 100px;">
    <div class="d-flex justify-content-between align-items-center small mb-1 gap-2">
        <span class="text-muted text-nowrap">{{ $answered }} / {{ $total }}</span>
        <span class="fw-semibold text-{{ $tone }}">{{ $pct }}%</span>
    </div>
    <div class="progress" style="height: 6px; border-radius: 4px; background: #f1f5f9;">
        <div class="progress-bar bg-{{ $tone }}" role="progressbar"
             style="width: {{ $pct }}%; border-radius: 4px;"
             aria-valuenow="{{ $pct }}" aria-valuemin="0" aria-valuemax="100"></div>
    </div>
</div>
