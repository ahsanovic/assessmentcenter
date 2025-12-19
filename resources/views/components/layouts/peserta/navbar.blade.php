<div class="horizontal-menu">
    <nav class="navbar top-navbar">
        <div class="container">
            <div class="navbar-content">
                <a href="{{ route('peserta.dashboard') }}" class="navbar-brand d-none d-lg-flex align-items-center" wire:navigate>
                    <img src="{{ asset('assets/images/small-logo.png') }}" alt="logo" class="w-30px h-30px me-2">
                    <span class="fw-bold" style="color: #01a7f7;">SIKMA</span>
                </a>					
                <ul class="navbar-nav">
                    <li class="theme-switcher-wrapper nav-item">
                        <input type="checkbox" value="" id="theme-switcher">
                        <label for="theme-switcher">
                            <div class="box">
                                <div class="ball"></div>
                                <div class="icons">
                                    <i class="feather icon-sun"></i>
                                    <i class="feather icon-moon"></i>
                                </div>
                            </div>
                        </label>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="profileDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            @if(auth()->guard('peserta')->user()->foto)
                                <img class="w-30px h-30px ms-1 rounded-circle" src="{{ asset('storage/' . auth()->guard('peserta')->user()->foto) }}" alt="profile">
                            @else
                                <div class="w-30px h-30px ms-1 rounded-circle bg-primary d-flex align-items-center justify-content-center text-white fw-bold">
                                    {{ strtoupper(substr(auth()->guard('peserta')->user()->nama ?? 'P', 0, 1)) }}
                                </div>
                            @endif
                        </a>
                        <div class="dropdown-menu p-0" aria-labelledby="profileDropdown">
                            <div class="d-flex flex-column align-items-center border-bottom px-5 py-3">
                                <div class="mb-3">
                                    @if(auth()->guard('peserta')->user()->foto)
                                        <img class="w-80px h-80px rounded-circle" src="{{ asset('storage/' . auth()->guard('peserta')->user()->foto) }}" alt="">
                                    @else
                                        <div class="w-80px h-80px rounded-circle bg-primary d-flex align-items-center justify-content-center text-white fw-bold" style="font-size: 2rem;">
                                            {{ strtoupper(substr(auth()->guard('peserta')->user()->nama ?? 'P', 0, 1)) }}
                                        </div>
                                    @endif
                                </div>
                                <div class="text-center">
                                    <p class="fs-16px fw-bolder mb-1">{{ auth()->guard('peserta')->user()->nama ?? 'Peserta' }}</p>
                                    <p class="fs-12px text-secondary mb-0">{{ auth()->guard('peserta')->user()->nip ?? '-' }}</p>
                                </div>
                            </div>
                            <ul class="list-unstyled p-1 mb-0">
                                <li class="dropdown-item py-2">
                                    <form action="{{ route('peserta.logout') }}" method="post" class="mb-0">
                                        @csrf
                                        <button type="submit" class="btn btn-sm w-100 text-start d-flex align-items-center">
                                            <i class="me-2 icon-md" data-feather="log-out"></i>
                                            <span>Logout</span>
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </li>
                </ul>

                <!-- navbar toggler for small devices -->
                <div data-toggle="horizontal-menu-toggle" class="navbar-toggler navbar-toggler-right d-lg-none align-self-center">
                    <span></span>
                    <span></span>
                    <span></span>
                    <span></span>
                </div>	
            </div>
        </div>
    </nav>
    <nav class="bottom-navbar">
        <div class="container">
            <ul class="nav page-navigation">
                @if (!session('exam_pin'))
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('peserta.dashboard') ? 'active' : '' }}" href="{{ route('peserta.dashboard') }}" wire:navigate>
                            <i class="link-icon" data-feather="home"></i>
                            <span class="menu-title">Dashboard</span>
                        </a>
                    </li>
                @endif
            </ul>
        </div>
    </nav>
</div>
