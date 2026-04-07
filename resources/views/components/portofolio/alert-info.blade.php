@props([
    'title' => '',
    'description' => '',
    'icon' => '',
    'color' => 'info',
])

<!-- Alert Info -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm border-start border-4 border-{{ $color }}">
            <div class="card-body p-4">
                <div class="d-flex align-items-start">
                    <div class="me-3" wire:ignore>
                        <i class="text-{{ $color }}" data-feather="{{ $icon }}" style="width: 24px; height: 24px;"></i>
                    </div>
                    <div>
                        <h6 class="mb-2 text-{{ $color }}">{{ $title }}</h6>
                        <p class="mb-0 text-muted">{{ $description }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>