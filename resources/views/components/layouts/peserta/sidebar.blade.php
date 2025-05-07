<div>
    <nav class="sidebar">
        <div class="sidebar-header">
            <p class="sidebar-brand">
                SI-PRIMA
            </p>
            <div class="sidebar-toggler">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>
        <div class="sidebar-body">
            <ul class="nav" id="sidebarNav">

                @if (!session('exam_pin'))
                <li class="nav-item" style="{{ session('exam_pin') ? 'pointer-events: none; opacity: 0.6;' : '' }}">
                    <a href="{{ route('peserta.dashboard') }}" class="nav-link" wire:navigate>
                        <i class="link-icon" data-feather="box"></i>
                        <span class="link-title">Dashboard</span>
                    </a>
                </li>
                <li class="nav-item" style="{{ session('exam_pin') ? 'pointer-events: none; opacity: 0.6;' : '' }}">
                    <a href="{{ route('peserta.portofolio') }}" class="nav-link" wire:navigate>
                        <i class="link-icon" data-feather="info"></i>
                        <span class="link-title">Portofolio</span>
                    </a>
                </li>
                @endif
                <li class="nav-item nav-category">Menu Tes</li>
                <li class="nav-item">
                    @php
                        $event_id = auth()->guard('peserta')->user()->event_id;
                        $peserta_id = auth()->guard('peserta')->user()->id;
                        $data = getFinishedTes($event_id, $peserta_id);
                        $test_started = collect($data)->contains(true);
                    @endphp
                    <a href="{{ route('peserta.tes-potensi') }}" class="nav-link" wire:navigate style="{{ $test_started ? 'pointer-events: none;' : ''}}">
                        <i class="link-icon" data-feather="info"></i>
                        <span class="link-title">Tes Potensi</span>
                    </a>
                </li>
                
                <li class="nav-item nav-category">Logout</li>
                <li class="nav-item">
                    <form action="{{ route('peserta.logout') }}" method="post">
                        @csrf
                        <button type="submit" class="btn btn-sm ">
                            <i class="icon-md" data-feather="log-out"></i>
                            <span class="link-title">Logout</span>
                        </button>
                    </form>
                </li>

            </ul>
        </div>
    </nav>
</div>
