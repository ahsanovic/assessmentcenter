<div>
    <!-- partial:partials/_navbar.html -->
    <nav class="navbar">
        <div class="navbar-content">

            <div class="logo-mini-wrapper">
                <img src="{{ asset('assets/images/logo-mini-light.png') }}" class="logo-mini logo-mini-light"
                    alt="logo">
                <img src="{{ asset('assets/images/logo-mini-dark.png') }}" class="logo-mini logo-mini-dark" alt="logo">
            </div>

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
                    <a class="nav-link dropdown-toggle" href="#" id="profileDropdown" role="button"
                        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <img class="w-30px h-30px ms-1 rounded-circle" src="{{ asset('storage/' . auth()->user()->foto) }}"
                            alt="profile">
                    </a>
                    <div class="dropdown-menu p-0" aria-labelledby="profileDropdown">
                        <div class="d-flex flex-column align-items-center border-bottom px-5 py-3">
                            <div class="mb-3">
                                <img class="w-80px h-80px rounded-circle" src="{{ asset('storage/' . auth()->user()->foto) }}"
                                    alt="">
                            </div>
                            <div class="text-center">
                                <p class="fs-16px fw-bolder">{{ auth()->user()->nama }}</p>
                                <p class="fs-12px text-secondary">{{ auth()->user()->nip }}</p>
                            </div>
                        </div>
                        <ul class="list-unstyled p-1">
                            <li class="dropdown-item py-2">
                                <form action="{{ route('peserta.logout') }}" method="post">
                                    @csrf
                                    <button type="submit" class="btn btn-sm w-100 text-start">
                                        <i class="me-2 icon-md" data-feather="log-out"></i>
                                        <span>Logout</span>
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </li>
            </ul>

            <a href="#" class="sidebar-toggler">
                <i data-feather="menu"></i>
            </a>

        </div>
    </nav>
    <!-- partial -->
</div>
