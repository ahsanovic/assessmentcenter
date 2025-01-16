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

                <li class="nav-item">
                    <a href="{{ route('assessor.dashboard') }}" wire:navigate class="nav-link">
                        <i class="link-icon" data-feather="box"></i>
                        <span class="link-title">Dashboard</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('assessor.event') }}" class="nav-link" wire:navigate>
                        <i class="link-icon" data-feather="info"></i>
                        <span class="link-title">Event</span>
                    </a>
                </li>

                <li class="nav-item nav-category"></li>
                <li class="nav-item">
                    <form action="{{ route('assessor.logout') }}" method="post">
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
