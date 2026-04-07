<div class="col-4 col-md-2 pe-0">
    <div class="nav nav-tabs nav-tabs-vertical" id="v-tab" role="tablist" aria-orientation="vertical" wire:ignore>
        @foreach ($nav as $item)
            <a
                class="nav-link d-flex align-items-center {{ $item['active'] }}"
                data-bs-toggle="pill"
                href="{{ $item['url'] }}"
                role="tab"
                wire:navigate
            >
                <span wire:ignore>
                    <i data-feather="{{ $item['icon'] ?? 'circle' }}" class="text-{{ $item['color'] ?? 'secondary' }} me-2" style="width: 20px; height: 20px;"></i> 
                    {{ $item['title'] }}
                </span>
            </a>
        @endforeach
    </div>
</div>