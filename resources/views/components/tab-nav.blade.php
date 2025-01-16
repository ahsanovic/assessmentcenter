<div class="col-4 col-md-2 pe-0">
    <div class="nav nav-tabs nav-tabs-vertical" id="v-tab" role="tablist" aria-orientation="vertical">
        @foreach ($nav as $item)
            <a class="nav-link {{ $item['active'] }}" data-bs-toggle="pill" href="{{ $item['url'] }}" role="tab"
                wire:navigate>{{ $item['title'] }}</a>
        @endforeach
    </div>
</div>