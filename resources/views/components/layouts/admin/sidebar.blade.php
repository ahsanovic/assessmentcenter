<div>
    @php
        $tesTypeSegments = ['intelektual', 'cakap-digital', 'kompetensi-teknis', 'pspk'];

        if (! function_exists('adminRouteMatches')) {
            function adminRouteMatches(?string $current, string $routeName, array $excludeSegmentsAfter = []): bool
            {
                if ($current === null) {
                    return false;
                }

                if ($current === $routeName) {
                    return true;
                }

                $prefix = $routeName.'.';
                if (! str_starts_with($current, $prefix)) {
                    return false;
                }

                if ($excludeSegmentsAfter === []) {
                    return true;
                }

                $remainder = substr($current, strlen($prefix));
                $firstSegment = explode('.', $remainder, 2)[0];

                return ! in_array($firstSegment, $excludeSegmentsAfter, true);
            }
        }

        if (! function_exists('isActiveRoute')) {
            function isActiveRoute(string $routeName, bool $exact = true, array $excludeSegmentsAfter = []): bool
            {
                $current = \Route::currentRouteName();

                if ($exact) {
                    return $current === $routeName;
                }

                return adminRouteMatches($current, $routeName, $excludeSegmentsAfter);
            }
        }

        // Daftar id dari collapsible menu beserta route yang mereka cover
        $collapseMenus = [
            'referensi' => [
                'routes' => [
                    'admin.alat-tes',
                    'admin.metode-tes',
                    'admin.pertanyaan-pengalaman',
                    'admin.pertanyaan-penilaian',
                    'admin.kuesioner',
                    'admin.pegawai',
                ]
            ],
            'data-tes-intelektual' => [
                'routes' => [
                    'admin.tes-berlangsung.intelektual',
                    'admin.tes-selesai.intelektual',
                ]
            ],
            'data-tes-potensi' => [
                'routes' => [
                    ['name' => 'admin.tes-berlangsung', 'exclude' => $tesTypeSegments],
                    ['name' => 'admin.tes-selesai', 'exclude' => $tesTypeSegments],
                    'admin.hasil-responden',
                    'admin.pelanggaran-tes',
                ]
            ],
            'data-tes-cakap-digital' => [
                'routes' => [
                    'admin.tes-berlangsung.cakap-digital',
                    'admin.tes-selesai.cakap-digital',
                    'admin.pelanggaran-tes-cakap-digital',
                ]
            ],
            'data-tes-kompetensi-teknis' => [
                'routes' => [
                    'admin.tes-berlangsung.kompetensi-teknis',
                    'admin.tes-selesai.kompetensi-teknis',
                    'admin.pelanggaran-tes-kompetensi-teknis',
                ]
            ],
            'data-tes-pspk' => [
                'routes' => [
                    'admin.tes-berlangsung.pspk',
                    'admin.tes-selesai.pspk',
                    'admin.pelanggaran-tes-pspk',
                ]
            ],
            'intelektual' => [
                'routes' => [
                    'admin.ref-intelektual',
                    'admin.model-soal-intelektual',
                    'admin.soal-intelektual-subtes1',
                    'admin.soal-intelektual-subtes2',
                    'admin.soal-intelektual-subtes3',
                ]
            ],
            'pengembangan-diri' => [
                'routes' => [
                    'admin.ref-pengembangan-diri',
                    'admin.soal-pengembangan-diri',
                ]
            ],
            'interpersonal' => [
                'routes' => [
                    'admin.ref-interpersonal',
                    'admin.soal-interpersonal',
                ]
            ],
            'kecerdasan-emosi' => [
                'routes' => [
                    'admin.ref-kecerdasan-emosi',
                    'admin.soal-kecerdasan-emosi',
                ]
            ],
            'motivasi-komitmen' => [
                'routes' => [
                    'admin.ref-motivasi-komitmen',
                    'admin.soal-motivasi-komitmen',
                ]
            ],
            'berpikir-kritis' => [
                'routes' => [
                    'admin.ref-aspek-berpikir-kritis',
                    'admin.ref-indikator-berpikir-kritis',
                    'admin.soal-berpikir-kritis',
                ]
            ],
            'problem-solving' => [
                'routes' => [
                    'admin.ref-aspek-problem-solving',
                    'admin.ref-indikator-problem-solving',
                    'admin.soal-problem-solving',
                ]
            ],
            'kesadaran-diri' => [
                'routes' => [
                    'admin.ref-kesadaran-diri',
                    'admin.soal-kesadaran-diri',
                ]
            ],
            'cakap-digital' => [
                'routes' => [
                    'admin.soal-cakap-digital',
                ]
            ],
            'kompetensi-teknis' => [
                'routes' => [
                    'admin.soal-kompetensi-teknis',
                ]
            ],
            'pspk-lv1' => [
                'routes' => [
                    'admin.soal-pspk',
                    'admin.ref-pspk',
                    'admin.ref-saran-pengembangan',
                    'admin.pspk-kasus-lampiran',
                ]
            ],
            'jawaban-peserta' => [
                'routes' => [
                    'admin.jawaban-peserta.pspk',
                ]
            ],
            'dokumen' => [
                'routes' => [
                    'admin.dokumen.berita-acara',
                    'admin.dokumen.absensi',
                ]
            ],
        ];

        $activeCollapse = [];
        $currentRoute = \Route::currentRouteName();

        foreach ($collapseMenus as $id => $menu) {
            if (! isset($menu['routes'])) {
                continue;
            }

            foreach ($menu['routes'] as $route) {
                $routeName = is_array($route) ? $route['name'] : $route;
                $exclude = is_array($route) ? ($route['exclude'] ?? []) : [];

                if (adminRouteMatches($currentRoute, $routeName, $exclude)) {
                    $activeCollapse[$id] = true;
                    break;
                }
            }
        }
    @endphp
    <nav class="sidebar" wire:key="admin-sidebar-{{ $currentRoute ?? 'none' }}">
        <div class="sidebar-header">
            <a href="#" class="sidebar-brand">
                SIKMA
            </a>
            <div class="sidebar-toggler">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>
        <div class="sidebar-body">
            <ul class="nav" id="sidebarNav" data-server-managed="true">

                <li class="nav-item">
                    <a href="{{ route('admin.dashboard') }}" wire:navigate class="nav-link {{ isActiveRoute('admin.dashboard') ? 'active' : '' }}">
                        <i class="link-icon" data-feather="home"></i>
                        <span class="link-title">Dashboard</span>
                    </a>
                </li>
                <li class="nav-item nav-category">Referensi</li>
                <li class="nav-item">
                    <a class="nav-link {{ isset($activeCollapse['referensi']) ? '' : 'collapsed' }}" data-bs-toggle="collapse" href="#referensi" role="button"
                        aria-expanded="{{ isset($activeCollapse['referensi']) ? 'true' : 'false' }}" aria-controls="referensi">
                        <i class="link-icon" data-feather="database"></i>
                        <span class="link-title">Data Referensi</span>
                        <i class="link-arrow" data-feather="chevron-down"></i>
                    </a>
                    <div class="collapse{{ isset($activeCollapse['referensi']) ? ' show' : '' }}" data-bs-parent="#sidebarNav" id="referensi">
                        <ul class="nav sub-menu">
                            <li class="nav-item">
                                <a href="{{ route('admin.alat-tes') }}" wire:navigate
                                    class="nav-link {{ isActiveRoute('admin.alat-tes') ? 'active' : '' }}">Alat Tes</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.metode-tes') }}" wire:navigate
                                    class="nav-link {{ isActiveRoute('admin.metode-tes') ? 'active' : '' }}">Metode Tes</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.pertanyaan-pengalaman') }}" wire:navigate class="nav-link {{ isActiveRoute('admin.pertanyaan-pengalaman') ? 'active' : '' }}">
                                    Pertanyaan Pengalaman
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.pertanyaan-penilaian') }}" wire:navigate class="nav-link {{ isActiveRoute('admin.pertanyaan-penilaian') ? 'active' : '' }}">
                                    Pertanyaan Penilaian
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.kuesioner') }}" wire:navigate class="nav-link {{ isActiveRoute('admin.kuesioner') ? 'active' : '' }}">
                                    Pertanyaan Kuesioner
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.pegawai') }}" wire:navigate class="nav-link {{ isActiveRoute('admin.pegawai') ? 'active' : '' }}">
                                    Data Pegawai
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="nav-item nav-category">Setting</li>
                <li class="nav-item">
                    <a href="{{ route('admin.settings.urutan') }}" wire:navigate class="nav-link {{ isActiveRoute('admin.settings.urutan') ? 'active' : '' }}">
                        <i class="link-icon" data-feather="layers"></i>
                        <span class="link-title">Urutan Tes Potensi</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.settings.waktu') }}" wire:navigate class="nav-link {{ isActiveRoute('admin.settings.waktu') ? 'active' : '' }}">
                        <i class="link-icon" data-feather="clock"></i>
                        <span class="link-title">Waktu Tes</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.ttd-laporan') }}" wire:navigate class="nav-link {{ isActiveRoute('admin.ttd-laporan') ? 'active' : '' }}">
                        <i class="link-icon" data-feather="edit-3"></i>
                        <span class="link-title">Ttd Laporan Penilaian</span>
                    </a>
                </li>

                <li class="nav-item nav-category">Event</li>
                <li class="nav-item">
                    <a href="{{ route('admin.event') }}" wire:navigate class="nav-link {{ isActiveRoute('admin.event', false) ? 'active' : '' }}">
                        <i class="link-icon" data-feather="calendar"></i>
                        <span class="link-title">Data Event</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.assessor') }}" wire:navigate class="nav-link {{ isActiveRoute('admin.assessor') ? 'active' : '' }}">
                        <i class="link-icon" data-feather="user-check"></i>
                        <span class="link-title">Data Assessor</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.distribusi-peserta') }}" wire:navigate class="nav-link {{ isActiveRoute('admin.distribusi-peserta', false) ? 'active' : '' }}">
                        <i class="link-icon" data-feather="shuffle"></i>
                        <span class="link-title">Distribusi Peserta</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.nomor-laporan') }}" wire:navigate class="nav-link {{ isActiveRoute('admin.nomor-laporan') ? 'active' : '' }}">
                        <i class="link-icon" data-feather="file-text"></i>
                        <span class="link-title">Nomor Laporan Penilaian</span>
                    </a>
                </li>

                <li class="nav-item nav-category">Dokumen</li>
                <li class="nav-item">
                    <a class="nav-link {{ isset($activeCollapse['dokumen']) ? '' : 'collapsed' }}" data-bs-toggle="collapse" href="#dokumen" role="button"
                        aria-expanded="{{ isset($activeCollapse['dokumen']) ? 'true' : 'false' }}" aria-controls="dokumen">
                        <i class="link-icon" data-feather="file"></i>
                        <span class="link-title">Dokumen</span>
                        <i class="link-arrow" data-feather="chevron-down"></i>
                    </a>
                    <div class="collapse{{ isset($activeCollapse['dokumen']) ? ' show' : '' }}" data-bs-parent="#sidebarNav" id="dokumen">
                        <ul class="nav sub-menu">
                            <li class="nav-item">
                                <a href="{{ route('admin.dokumen.berita-acara') }}" wire:navigate
                                    class="nav-link {{ isActiveRoute('admin.dokumen.berita-acara', false) ? 'active' : '' }}">Berita Acara</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.dokumen.absensi') }}" wire:navigate
                                    class="nav-link {{ isActiveRoute('admin.dokumen.absensi', false) ? 'active' : '' }}">Absensi</a>
                            </li>
                        </ul>
                    </div>
                </li>

                <li class="nav-item nav-category">Pelaksanaan Tes</li>
                <li class="nav-item">
                    <a class="nav-link {{ isset($activeCollapse['data-tes-intelektual']) ? '' : 'collapsed' }}" data-bs-toggle="collapse" href="#data-tes-intelektual" role="button"
                        aria-expanded="{{ isset($activeCollapse['data-tes-intelektual']) ? 'true' : 'false' }}" aria-controls="data-tes-intelektual">
                        <i class="link-icon" data-feather="cpu"></i>
                        <span class="link-title">Tes Intelektual</span>
                        <i class="link-arrow" data-feather="chevron-down"></i>
                    </a>
                    <div class="collapse{{ isset($activeCollapse['data-tes-intelektual']) ? ' show' : '' }}" data-bs-parent="#sidebarNav" id="data-tes-intelektual">
                        <ul class="nav sub-menu">
                            <li class="nav-item">
                                <a href="{{ route('admin.tes-berlangsung.intelektual') }}" wire:navigate
                                    class="nav-link {{ isActiveRoute('admin.tes-berlangsung.intelektual', false) ? 'active' : '' }}">Tes Berlangsung</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.tes-selesai.intelektual') }}" wire:navigate
                                    class="nav-link {{ isActiveRoute('admin.tes-selesai.intelektual', false) ? 'active' : '' }}">Tes Selesai</a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ isset($activeCollapse['data-tes-potensi']) ? '' : 'collapsed' }}" data-bs-toggle="collapse" href="#data-tes-potensi" role="button"
                        aria-expanded="{{ isset($activeCollapse['data-tes-potensi']) ? 'true' : 'false' }}" aria-controls="data-tes-potensi">
                        <i class="link-icon" data-feather="bar-chart-2"></i>
                        <span class="link-title">Tes Potensi</span>
                        <i class="link-arrow" data-feather="chevron-down"></i>
                    </a>
                    <div class="collapse{{ isset($activeCollapse['data-tes-potensi']) ? ' show' : '' }}" data-bs-parent="#sidebarNav" id="data-tes-potensi">
                        <ul class="nav sub-menu">
                            <li class="nav-item">
                                <a href="{{ route('admin.tes-berlangsung') }}" wire:navigate
                                    class="nav-link {{ isActiveRoute('admin.tes-berlangsung', false, $tesTypeSegments) ? 'active' : '' }}">Tes Berlangsung</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.tes-selesai') }}" wire:navigate
                                    class="nav-link {{ isActiveRoute('admin.tes-selesai', false, $tesTypeSegments) ? 'active' : '' }}">Tes Selesai</a>
                            </li>
                            <li class="nav-item">
                            <a href="{{ route('admin.hasil-responden') }}" wire:navigate
                                    class="nav-link {{ isActiveRoute('admin.hasil-responden', false) ? 'active' : '' }}">Hasil Responden</a>
                            </li>
                            <li class="nav-item">
                            <a href="{{ route('admin.pelanggaran-tes') }}" wire:navigate
                                    class="nav-link {{ isActiveRoute('admin.pelanggaran-tes', false) ? 'active' : '' }}">Pelanggaran Tes</a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ isset($activeCollapse['data-tes-cakap-digital']) ? '' : 'collapsed' }}" data-bs-toggle="collapse" href="#data-tes-cakap-digital" role="button"
                        aria-expanded="{{ isset($activeCollapse['data-tes-cakap-digital']) ? 'true' : 'false' }}" aria-controls="data-tes-cakap-digital">
                        <i class="link-icon" data-feather="smartphone"></i>
                        <span class="link-title">Tes Cakap Digital</span>
                        <i class="link-arrow" data-feather="chevron-down"></i>
                    </a>
                    <div class="collapse{{ isset($activeCollapse['data-tes-cakap-digital']) ? ' show' : '' }}" data-bs-parent="#sidebarNav" id="data-tes-cakap-digital">
                        <ul class="nav sub-menu">
                            <li class="nav-item">
                                <a href="{{ route('admin.tes-berlangsung.cakap-digital') }}" wire:navigate
                                    class="nav-link {{ isActiveRoute('admin.tes-berlangsung.cakap-digital', false) ? 'active' : '' }}">Tes Berlangsung</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.tes-selesai.cakap-digital') }}" wire:navigate
                                    class="nav-link {{ isActiveRoute('admin.tes-selesai.cakap-digital', false) ? 'active' : '' }}">Tes Selesai</a>
                            </li>
                            <li class="nav-item">
                            <a href="{{ route('admin.pelanggaran-tes-cakap-digital') }}" wire:navigate
                                    class="nav-link {{ isActiveRoute('admin.pelanggaran-tes-cakap-digital', false) ? 'active' : '' }}">Pelanggaran Tes</a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ isset($activeCollapse['data-tes-kompetensi-teknis']) ? '' : 'collapsed' }}" data-bs-toggle="collapse" href="#data-tes-kompetensi-teknis" role="button"
                        aria-expanded="{{ isset($activeCollapse['data-tes-kompetensi-teknis']) ? 'true' : 'false' }}" aria-controls="data-tes-kompetensi-teknis">
                        <i class="link-icon" data-feather="tool"></i>
                        <span class="link-title">Tes Kompetensi Teknis</span>
                        <i class="link-arrow" data-feather="chevron-down"></i>
                    </a>
                    <div class="collapse{{ isset($activeCollapse['data-tes-kompetensi-teknis']) ? ' show' : '' }}" data-bs-parent="#sidebarNav" id="data-tes-kompetensi-teknis">
                        <ul class="nav sub-menu">
                            <li class="nav-item">
                                <a href="{{ route('admin.tes-berlangsung.kompetensi-teknis') }}" wire:navigate
                                    class="nav-link {{ isActiveRoute('admin.tes-berlangsung.kompetensi-teknis', false) ? 'active' : '' }}">Tes Berlangsung</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.tes-selesai.kompetensi-teknis') }}" wire:navigate
                                    class="nav-link {{ isActiveRoute('admin.tes-selesai.kompetensi-teknis', false) ? 'active' : '' }}">Tes Selesai</a>
                            </li>
                            <li class="nav-item">
                            <a href="{{ route('admin.pelanggaran-tes-kompetensi-teknis') }}" wire:navigate
                                    class="nav-link {{ isActiveRoute('admin.pelanggaran-tes-kompetensi-teknis', false) ? 'active' : '' }}">Pelanggaran Tes</a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ isset($activeCollapse['data-tes-pspk']) ? '' : 'collapsed' }}" data-bs-toggle="collapse" href="#data-tes-pspk" role="button"
                        aria-expanded="{{ isset($activeCollapse['data-tes-pspk']) ? 'true' : 'false' }}" aria-controls="data-tes-pspk">
                        <i class="link-icon" data-feather="book-open"></i>
                        <span class="link-title">Tes PSPK</span>
                        <i class="link-arrow" data-feather="chevron-down"></i>
                    </a>
                    <div class="collapse{{ isset($activeCollapse['data-tes-pspk']) ? ' show' : '' }}" data-bs-parent="#sidebarNav" id="data-tes-pspk">
                        <ul class="nav sub-menu">
                            <li class="nav-item">
                                <a href="{{ route('admin.tes-berlangsung.pspk') }}" wire:navigate
                                    class="nav-link {{ isActiveRoute('admin.tes-berlangsung.pspk', false) ? 'active' : '' }}">Tes Berlangsung</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.tes-selesai.pspk') }}" wire:navigate
                                    class="nav-link {{ isActiveRoute('admin.tes-selesai.pspk', false) ? 'active' : '' }}">Tes Selesai</a>
                            </li>
                            <li class="nav-item">
                            <a href="{{ route('admin.pelanggaran-tes-pspk') }}" wire:navigate
                                    class="nav-link {{ isActiveRoute('admin.pelanggaran-tes-pspk', false) ? 'active' : '' }}">Pelanggaran Tes</a>
                            </li>
                        </ul>
                    </div>
                </li>

                <li class="nav-item nav-category">Jawaban Peserta</li>
                <li class="nav-item">
                    <a class="nav-link {{ isset($activeCollapse['jawaban-peserta']) ? '' : 'collapsed' }}" data-bs-toggle="collapse" href="#jawaban-peserta" role="button"
                        aria-expanded="{{ isset($activeCollapse['jawaban-peserta']) ? 'true' : 'false' }}" aria-controls="jawaban-peserta">
                        <i class="link-icon" data-feather="file-text"></i>
                        <span class="link-title">Jawaban Peserta</span>
                        <i class="link-arrow" data-feather="chevron-down"></i>
                    </a>
                    <div class="collapse{{ isset($activeCollapse['jawaban-peserta']) ? ' show' : '' }}" data-bs-parent="#sidebarNav" id="jawaban-peserta">
                        <ul class="nav sub-menu">
                            <li class="nav-item">
                                <a href="{{ route('admin.jawaban-peserta.pspk') }}" wire:navigate
                                    class="nav-link {{ isActiveRoute('admin.jawaban-peserta.pspk', false) ? 'active' : '' }}">Tes PSPK</a>
                            </li>
                        </ul>
                    </div>
                </li>

                <li class="nav-item nav-category">Instrumen Tes</li>
                <li class="nav-item">
                    <a class="nav-link {{ isset($activeCollapse['intelektual']) ? '' : 'collapsed' }}" data-bs-toggle="collapse" href="#intelektual" role="button"
                        aria-expanded="{{ isset($activeCollapse['intelektual']) ? 'true' : 'false' }}" aria-controls="intelektual">
                        <i class="link-icon" data-feather="cpu"></i>
                        <span class="link-title">Intelektual</span>
                        <i class="link-arrow" data-feather="chevron-down"></i>
                    </a>
                    <div class="collapse{{ isset($activeCollapse['intelektual']) ? ' show' : '' }}" data-bs-parent="#sidebarNav" id="intelektual">
                        <ul class="nav sub-menu">
                            <li class="nav-item">
                                <a href="{{ route('admin.ref-intelektual') }}" wire:navigate
                                    class="nav-link {{ isActiveRoute('admin.ref-intelektual', false) ? 'active' : '' }}">Data Referensi</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.model-soal-intelektual') }}" wire:navigate
                                    class="nav-link {{ isActiveRoute('admin.model-soal-intelektual', false) ? 'active' : '' }}">Model Soal</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.soal-intelektual-subtes1') }}" wire:navigate
                                    class="nav-link {{ isActiveRoute('admin.soal-intelektual-subtes1', false) ? 'active' : '' }}">Soal Sub Tes 1</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.soal-intelektual-subtes2') }}" wire:navigate
                                    class="nav-link {{ isActiveRoute('admin.soal-intelektual-subtes2', false) ? 'active' : '' }}">Soal Sub Tes 2</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.soal-intelektual-subtes3') }}" wire:navigate
                                    class="nav-link {{ isActiveRoute('admin.soal-intelektual-subtes3', false) ? 'active' : '' }}">Soal Sub Tes 3</a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ isset($activeCollapse['pengembangan-diri']) ? '' : 'collapsed' }}" data-bs-toggle="collapse" href="#pengembangan-diri" role="button"
                        aria-expanded="{{ isset($activeCollapse['pengembangan-diri']) ? 'true' : 'false' }}" aria-controls="pengembangan-diri">
                        <i class="link-icon" data-feather="user-plus"></i>
                        <span class="link-title">Pengembangan Diri</span>
                        <i class="link-arrow" data-feather="chevron-down"></i>
                    </a>
                    <div class="collapse{{ isset($activeCollapse['pengembangan-diri']) ? ' show' : '' }}" data-bs-parent="#sidebarNav" id="pengembangan-diri">
                        <ul class="nav sub-menu">
                            <li class="nav-item">
                                <a href="{{ route('admin.ref-pengembangan-diri') }}" wire:navigate
                                    class="nav-link {{ isActiveRoute('admin.ref-pengembangan-diri', false) ? 'active' : '' }}">Data Referensi</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.soal-pengembangan-diri') }}" wire:navigate
                                    class="nav-link {{ isActiveRoute('admin.soal-pengembangan-diri', false) ? 'active' : '' }}">Soal</a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ isset($activeCollapse['interpersonal']) ? '' : 'collapsed' }}" data-bs-toggle="collapse" href="#interpersonal" role="button"
                        aria-expanded="{{ isset($activeCollapse['interpersonal']) ? 'true' : 'false' }}" aria-controls="interpersonal">
                        <i class="link-icon" data-feather="message-circle"></i>
                        <span class="link-title">Interpersonal</span>
                        <i class="link-arrow" data-feather="chevron-down"></i>
                    </a>
                    <div class="collapse{{ isset($activeCollapse['interpersonal']) ? ' show' : '' }}" data-bs-parent="#sidebarNav" id="interpersonal">
                        <ul class="nav sub-menu">
                            <li class="nav-item">
                                <a href="{{ route('admin.ref-interpersonal') }}" wire:navigate class="nav-link {{ isActiveRoute('admin.ref-interpersonal', false) ? 'active' : '' }}">Data
                                    Referensi</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.soal-interpersonal') }}" wire:navigate
                                    class="nav-link {{ isActiveRoute('admin.soal-interpersonal', false) ? 'active' : '' }}">Soal</a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ isset($activeCollapse['kecerdasan-emosi']) ? '' : 'collapsed' }}" data-bs-toggle="collapse" href="#kecerdasan-emosi" role="button"
                        aria-expanded="{{ isset($activeCollapse['kecerdasan-emosi']) ? 'true' : 'false' }}" aria-controls="kecerdasan-emosi">
                        <i class="link-icon" data-feather="heart"></i>
                        <span class="link-title">Kecerdasan Emosi</span>
                        <i class="link-arrow" data-feather="chevron-down"></i>
                    </a>
                    <div class="collapse{{ isset($activeCollapse['kecerdasan-emosi']) ? ' show' : '' }}" data-bs-parent="#sidebarNav" id="kecerdasan-emosi">
                        <ul class="nav sub-menu">
                            <li class="nav-item">
                                <a href="{{ route('admin.ref-kecerdasan-emosi') }}" wire:navigate
                                    class="nav-link {{ isActiveRoute('admin.ref-kecerdasan-emosi', false) ? 'active' : '' }}">Data Referensi</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.soal-kecerdasan-emosi') }}" wire:navigate
                                    class="nav-link {{ isActiveRoute('admin.soal-kecerdasan-emosi', false) ? 'active' : '' }}">Soal</a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ isset($activeCollapse['motivasi-komitmen']) ? '' : 'collapsed' }}" data-bs-toggle="collapse" href="#motivasi-komitmen" role="button"
                        aria-expanded="{{ isset($activeCollapse['motivasi-komitmen']) ? 'true' : 'false' }}" aria-controls="motivasi-komitmen">
                        <i class="link-icon" data-feather="target"></i>
                        <span class="link-title">Motivasi dan Komitmen</span>
                        <i class="link-arrow" data-feather="chevron-down"></i>
                    </a>
                    <div class="collapse{{ isset($activeCollapse['motivasi-komitmen']) ? ' show' : '' }}" data-bs-parent="#sidebarNav" id="motivasi-komitmen">
                        <ul class="nav sub-menu">
                            <li class="nav-item">
                                <a href="{{ route('admin.ref-motivasi-komitmen') }}" wire:navigate
                                    class="nav-link {{ isActiveRoute('admin.ref-motivasi-komitmen', false) ? 'active' : '' }}">Data Referensi</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.soal-motivasi-komitmen') }}" wire:navigate
                                    class="nav-link {{ isActiveRoute('admin.soal-motivasi-komitmen', false) ? 'active' : '' }}">Soal</a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ isset($activeCollapse['berpikir-kritis']) ? '' : 'collapsed' }}" data-bs-toggle="collapse" href="#berpikir-kritis" role="button"
                        aria-expanded="{{ isset($activeCollapse['berpikir-kritis']) ? 'true' : 'false' }}" aria-controls="berpikir-kritis">
                        <i class="link-icon" data-feather="help-circle"></i>
                        <span class="link-title">Berpikir Kritis</span>
                        <i class="link-arrow" data-feather="chevron-down"></i>
                    </a>
                    <div class="collapse{{ isset($activeCollapse['berpikir-kritis']) ? ' show' : '' }}" data-bs-parent="#sidebarNav" id="berpikir-kritis">
                        <ul class="nav sub-menu">
                            <li class="nav-item">
                                <a href="{{ route('admin.ref-aspek-berpikir-kritis') }}" wire:navigate
                                    class="nav-link {{ isActiveRoute('admin.ref-aspek-berpikir-kritis', false) ? 'active' : '' }}">Data Referensi Aspek</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.ref-indikator-berpikir-kritis') }}" wire:navigate
                                    class="nav-link {{ isActiveRoute('admin.ref-indikator-berpikir-kritis', false) ? 'active' : '' }}">Data Referensi Indikator</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.soal-berpikir-kritis') }}" wire:navigate
                                    class="nav-link {{ isActiveRoute('admin.soal-berpikir-kritis', false) ? 'active' : '' }}">Soal</a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ isset($activeCollapse['problem-solving']) ? '' : 'collapsed' }}" data-bs-toggle="collapse" href="#problem-solving" role="button"
                        aria-expanded="{{ isset($activeCollapse['problem-solving']) ? 'true' : 'false' }}" aria-controls="problem-solving">
                        <i class="link-icon" data-feather="git-merge"></i>
                        <span class="link-title">Problem Solving</span>
                        <i class="link-arrow" data-feather="chevron-down"></i>
                    </a>
                    <div class="collapse{{ isset($activeCollapse['problem-solving']) ? ' show' : '' }}" data-bs-parent="#sidebarNav" id="problem-solving">
                        <ul class="nav sub-menu">
                            <li class="nav-item">
                                <a href="{{ route('admin.ref-aspek-problem-solving') }}" wire:navigate
                                    class="nav-link {{ isActiveRoute('admin.ref-aspek-problem-solving', false) ? 'active' : '' }}">Data Referensi Aspek</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.ref-indikator-problem-solving') }}" wire:navigate
                                    class="nav-link {{ isActiveRoute('admin.ref-indikator-problem-solving', false) ? 'active' : '' }}">Data Referensi Indikator</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.soal-problem-solving') }}" wire:navigate
                                    class="nav-link {{ isActiveRoute('admin.soal-problem-solving', false) ? 'active' : '' }}">Soal</a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ isset($activeCollapse['kesadaran-diri']) ? '' : 'collapsed' }}" data-bs-toggle="collapse" href="#kesadaran-diri" role="button"
                        aria-expanded="{{ isset($activeCollapse['kesadaran-diri']) ? 'true' : 'false' }}" aria-controls="kesadaran-diri">
                        <i class="link-icon" data-feather="eye"></i>
                        <span class="link-title">Kesadaran Diri</span>
                        <i class="link-arrow" data-feather="chevron-down"></i>
                    </a>
                    <div class="collapse{{ isset($activeCollapse['kesadaran-diri']) ? ' show' : '' }}" data-bs-parent="#sidebarNav" id="kesadaran-diri">
                        <ul class="nav sub-menu">
                            <li class="nav-item">
                                <a href="{{ route('admin.ref-kesadaran-diri') }}" wire:navigate
                                    class="nav-link {{ isActiveRoute('admin.ref-kesadaran-diri', false) ? 'active' : '' }}">Data Referensi</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.soal-kesadaran-diri') }}" wire:navigate
                                    class="nav-link {{ isActiveRoute('admin.soal-kesadaran-diri', false) ? 'active' : '' }}">Soal</a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ isset($activeCollapse['cakap-digital']) ? '' : 'collapsed' }}" data-bs-toggle="collapse" href="#cakap-digital" role="button"
                        aria-expanded="{{ isset($activeCollapse['cakap-digital']) ? 'true' : 'false' }}" aria-controls="cakap-digital">
                        <i class="link-icon" data-feather="wifi"></i>
                        <span class="link-title">Cakap Digital</span>
                        <i class="link-arrow" data-feather="chevron-down"></i>
                    </a>
                    <div class="collapse{{ isset($activeCollapse['cakap-digital']) ? ' show' : '' }}" data-bs-parent="#sidebarNav" id="cakap-digital">
                        <ul class="nav sub-menu">
                            <li class="nav-item">
                                <a href="{{ route('admin.soal-cakap-digital') }}" wire:navigate
                                    class="nav-link {{ isActiveRoute('admin.soal-cakap-digital', false) ? 'active' : '' }}">Soal</a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ isset($activeCollapse['kompetensi-teknis']) ? '' : 'collapsed' }}" data-bs-toggle="collapse" href="#kompetensi-teknis" role="button"
                        aria-expanded="{{ isset($activeCollapse['kompetensi-teknis']) ? 'true' : 'false' }}" aria-controls="kompetensi-teknis">
                        <i class="link-icon" data-feather="terminal"></i>
                        <span class="link-title">Kompetensi Teknis</span>
                        <i class="link-arrow" data-feather="chevron-down"></i>
                    </a>
                    <div class="collapse{{ isset($activeCollapse['kompetensi-teknis']) ? ' show' : '' }}" data-bs-parent="#sidebarNav" id="kompetensi-teknis">
                        <ul class="nav sub-menu">
                            <li class="nav-item">
                                <a href="{{ route('admin.soal-kompetensi-teknis') }}" wire:navigate
                                    class="nav-link {{ isActiveRoute('admin.soal-kompetensi-teknis', false) ? 'active' : '' }}">Soal</a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ isset($activeCollapse['pspk-lv1']) ? '' : 'collapsed' }}" data-bs-toggle="collapse" href="#pspk-lv1" role="button"
                        aria-expanded="{{ isset($activeCollapse['pspk-lv1']) ? 'true' : 'false' }}" aria-controls="pspk-lv1">
                        <i class="link-icon" data-feather="file-text"></i>
                        <span class="link-title">PSPK</span>
                        <i class="link-arrow" data-feather="chevron-down"></i>
                    </a>
                    <div class="collapse{{ isset($activeCollapse['pspk-lv1']) ? ' show' : '' }}" data-bs-parent="#sidebarNav" id="pspk-lv1">
                        <ul class="nav sub-menu">
                            <li class="nav-item">
                                <a href="{{ route('admin.soal-pspk') }}" wire:navigate
                                    class="nav-link {{ isActiveRoute('admin.soal-pspk', false) ? 'active' : '' }}">Soal</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.pspk-kasus-lampiran') }}" wire:navigate
                                    class="nav-link {{ isActiveRoute('admin.pspk-kasus-lampiran', false) ? 'active' : '' }}">Paket analisa kasus (PDF)</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.ref-pspk') }}" wire:navigate
                                    class="nav-link {{ isActiveRoute('admin.ref-pspk', false) ? 'active' : '' }}">Referensi Deskripsi</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.ref-saran-pengembangan') }}" wire:navigate
                                    class="nav-link {{ isActiveRoute('admin.ref-saran-pengembangan', false) ? 'active' : '' }}">Ref Saran Pengembangan</a>
                            </li>
                        </ul>
                    </div>
                </li>

                @if (auth()->guard('admin')->user()->role === 'admin')
                <li class="nav-item nav-category">Akses dan Jejak</li>
                <li class="nav-item">
                    <a href="{{ route('admin.user') }}" wire:navigate class="nav-link {{ isActiveRoute('admin.user') ? 'active' : '' }}">
                        <i class="link-icon" data-feather="users"></i>
                        <span class="link-title">User</span>
                    </a>
                </li>
                @if (auth()->user()->role === 'admin')
                    <li class="nav-item">
                        <a href="{{ route('admin.log-activity') }}" wire:navigate class="nav-link {{ isActiveRoute('admin.log-activity') ? 'active' : '' }}">
                            <i class="link-icon" data-feather="activity"></i>
                            <span class="link-title">Aktifitas Log</span>
                        </a>
                    </li>
                @endif
                @endif


                <li class="nav-item nav-category"></li>
                <li class="nav-item">
                    <form action="{{ route('admin.logout') }}" method="post">
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
    <!-- partial -->
</div>
