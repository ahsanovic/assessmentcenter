<div>
    <nav class="sidebar">
        <div class="sidebar-header">
            <p class="sidebar-brand">
                BKD <span>AC</span>
            </p>
            <div class="sidebar-toggler">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>
        <div class="sidebar-body">
            <ul class="nav" id="sidebarNav">

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
                <li class="nav-item nav-category">Menu Tes</li>
                <li class="nav-item" style="{{ session('exam_pin') ? 'pointer-events: none; opacity: 0.6;' : '' }}">
                    <a href="{{ route('peserta.tes-potensi') }}" class="nav-link" wire:navigate>
                        <i class="link-icon" data-feather="info"></i>
                        <span class="link-title">Tes Potensi</span>
                    </a>
                </li>
                
                <li class="nav-item nav-category"></li>
                <li class="nav-item" style="{{ session('exam_pin') ? 'pointer-events: none; opacity: 0.6;' : '' }}">
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
