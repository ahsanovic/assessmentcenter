<div>
    <!-- partial:partials/_sidebar.html -->
    <nav class="sidebar">
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
            <ul class="nav" id="sidebarNav">

                <li class="nav-item">
                    <a href="{{ route('admin.dashboard') }}" wire:navigate class="nav-link">
                        <i class="link-icon" data-feather="box"></i>
                        <span class="link-title">Dashboard</span>
                    </a>
                </li>
                <li class="nav-item nav-category">Referensi</li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="collapse" href="#referensi" role="button"
                        aria-expanded="false" aria-controls="referensi">
                        <i class="link-icon" data-feather="box"></i>
                        <span class="link-title">Data Referensi</span>
                        <i class="link-arrow" data-feather="chevron-down"></i>
                    </a>
                    <div class="collapse" data-bs-parent="#sidebarNav" id="referensi">
                        <ul class="nav sub-menu">
                            <li class="nav-item">
                                <a href="{{ route('admin.alat-tes') }}" wire:navigate
                                    class="nav-link">Alat Tes</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.metode-tes') }}" wire:navigate
                                    class="nav-link">Metode Tes</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.pertanyaan-pengalaman') }}" wire:navigate class="nav-link">
                                    Pertanyaan Pengalaman
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.pertanyaan-penilaian') }}" wire:navigate class="nav-link">
                                    Pertanyaan Penilaian
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.kuesioner') }}" wire:navigate class="nav-link">
                                    Pertanyaan Kuesioner
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="nav-item nav-category">Setting</li>
                <li class="nav-item">
                    <a href="{{ route('admin.settings.urutan') }}" wire:navigate class="nav-link">
                        <i class="link-icon" data-feather="message-square"></i>
                        <span class="link-title">Urutan Tes Potensi</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.settings.waktu') }}" wire:navigate class="nav-link">
                        <i class="link-icon" data-feather="message-square"></i>
                        <span class="link-title">Waktu Tes</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.ttd-laporan') }}" wire:navigate class="nav-link">
                        <i class="link-icon" data-feather="message-square"></i>
                        <span class="link-title">Ttd Laporan Penilaian</span>
                    </a>
                </li>

                <li class="nav-item nav-category">Event</li>
                <li class="nav-item">
                    <a href="{{ route('admin.event') }}" wire:navigate class="nav-link">
                        <i class="link-icon" data-feather="message-square"></i>
                        <span class="link-title">Data Event</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.peserta') }}" wire:navigate class="nav-link">
                        <i class="link-icon" data-feather="message-square"></i>
                        <span class="link-title">Data Peserta</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.assessor') }}" wire:navigate class="nav-link">
                        <i class="link-icon" data-feather="message-square"></i>
                        <span class="link-title">Data Assessor</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.distribusi-peserta') }}" wire:navigate class="nav-link">
                        <i class="link-icon" data-feather="message-square"></i>
                        <span class="link-title">Distribusi Peserta</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.nomor-laporan') }}" wire:navigate class="nav-link">
                        <i class="link-icon" data-feather="message-square"></i>
                        <span class="link-title">Nomor Laporan Penilaian</span>
                    </a>
                </li>
                
                <li class="nav-item nav-category">Pelaksanaan Tes</li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="collapse" href="#data-tes-intelektual" role="button"
                        aria-expanded="false" aria-controls="data-tes-intelektual">
                        <i class="link-icon" data-feather="box"></i>
                        <span class="link-title">Tes Intelektual</span>
                        <i class="link-arrow" data-feather="chevron-down"></i>
                    </a>
                    <div class="collapse" data-bs-parent="#sidebarNav" id="data-tes-intelektual">
                        <ul class="nav sub-menu">
                            <li class="nav-item">
                                <a href="{{ route('admin.tes-berlangsung.intelektual') }}" wire:navigate
                                    class="nav-link">Tes Berlangsung</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.tes-selesai.intelektual') }}" wire:navigate
                                    class="nav-link">Tes Selesai</a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="collapse" href="#data-tes-potensi" role="button"
                        aria-expanded="false" aria-controls="data-tes-potensi">
                        <i class="link-icon" data-feather="box"></i>
                        <span class="link-title">Tes Potensi</span>
                        <i class="link-arrow" data-feather="chevron-down"></i>
                    </a>
                    <div class="collapse" data-bs-parent="#sidebarNav" id="data-tes-potensi">
                        <ul class="nav sub-menu">
                            <li class="nav-item">
                                <a href="{{ route('admin.tes-berlangsung') }}" wire:navigate
                                    class="nav-link">Tes Berlangsung</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.tes-selesai') }}" wire:navigate
                                    class="nav-link">Tes Selesai</a>
                            </li>
                            <li class="nav-item">
                            <a href="{{ route('admin.hasil-responden') }}" wire:navigate
                                    class="nav-link">Hasil Responden</a>
                            </li>
                            <li class="nav-item">
                            <a href="{{ route('admin.pelanggaran-tes') }}" wire:navigate
                                    class="nav-link">Pelanggaran Tes</a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="collapse" href="#data-tes-cakap-digital" role="button"
                        aria-expanded="false" aria-controls="data-tes-cakap-digital">
                        <i class="link-icon" data-feather="box"></i>
                        <span class="link-title">Tes Cakap Digital</span>
                        <i class="link-arrow" data-feather="chevron-down"></i>
                    </a>
                    <div class="collapse" data-bs-parent="#sidebarNav" id="data-tes-cakap-digital">
                        <ul class="nav sub-menu">
                            <li class="nav-item">
                                <a href="{{ route('admin.tes-berlangsung.cakap-digital') }}" wire:navigate
                                    class="nav-link">Tes Berlangsung</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.tes-selesai.cakap-digital') }}" wire:navigate
                                    class="nav-link">Tes Selesai</a>
                            </li>
                            <li class="nav-item">
                            <a href="{{ route('admin.pelanggaran-tes-cakap-digital') }}" wire:navigate
                                    class="nav-link">Pelanggaran Tes</a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="collapse" href="#data-tes-kompetensi-teknis" role="button"
                        aria-expanded="false" aria-controls="data-tes-kompetensi-teknis">
                        <i class="link-icon" data-feather="box"></i>
                        <span class="link-title">Tes Kompetensi Teknis</span>
                        <i class="link-arrow" data-feather="chevron-down"></i>
                    </a>
                    <div class="collapse" data-bs-parent="#sidebarNav" id="data-tes-kompetensi-teknis">
                        <ul class="nav sub-menu">
                            <li class="nav-item">
                                <a href="{{ route('admin.tes-berlangsung.kompetensi-teknis') }}" wire:navigate
                                    class="nav-link">Tes Berlangsung</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.tes-selesai.kompetensi-teknis') }}" wire:navigate
                                    class="nav-link">Tes Selesai</a>
                            </li>
                            <li class="nav-item">
                            <a href="{{ route('admin.pelanggaran-tes-kompetensi-teknis') }}" wire:navigate
                                    class="nav-link">Pelanggaran Tes</a>
                            </li>
                        </ul>
                    </div>
                </li>

                <li class="nav-item nav-category">Instrumen Tes</li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="collapse" href="#intelektual" role="button"
                        aria-expanded="false" aria-controls="intelektual">
                        <i class="link-icon" data-feather="box"></i>
                        <span class="link-title">Intelektual</span>
                        <i class="link-arrow" data-feather="chevron-down"></i>
                    </a>
                    <div class="collapse" data-bs-parent="#sidebarNav" id="intelektual">
                        <ul class="nav sub-menu">
                            <li class="nav-item">
                                <a href="{{ route('admin.ref-intelektual') }}" wire:navigate
                                    class="nav-link">Data Referensi</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.model-soal-intelektual') }}" wire:navigate
                                    class="nav-link">Model Soal</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.soal-intelektual-subtes1') }}" wire:navigate
                                    class="nav-link">Soal Sub Tes 1</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.soal-intelektual-subtes2') }}" wire:navigate
                                    class="nav-link">Soal Sub Tes 2</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.soal-intelektual-subtes3') }}" wire:navigate
                                    class="nav-link">Soal Sub Tes 3</a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="collapse" href="#pengembangan-diri" role="button"
                        aria-expanded="false" aria-controls="pengembangan-diri">
                        <i class="link-icon" data-feather="box"></i>
                        <span class="link-title">Pengembangan Diri</span>
                        <i class="link-arrow" data-feather="chevron-down"></i>
                    </a>
                    <div class="collapse" data-bs-parent="#sidebarNav" id="pengembangan-diri">
                        <ul class="nav sub-menu">
                            <li class="nav-item">
                                <a href="{{ route('admin.ref-pengembangan-diri') }}" wire:navigate
                                    class="nav-link">Data Referensi</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.soal-pengembangan-diri') }}" wire:navigate
                                    class="nav-link">Soal</a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="collapse" href="#interpersonal" role="button"
                        aria-expanded="false" aria-controls="interpersonal">
                        <i class="link-icon" data-feather="box"></i>
                        <span class="link-title">Interpersonal</span>
                        <i class="link-arrow" data-feather="chevron-down"></i>
                    </a>
                    <div class="collapse" data-bs-parent="#sidebarNav" id="interpersonal">
                        <ul class="nav sub-menu">
                            <li class="nav-item">
                                <a href="{{ route('admin.ref-interpersonal') }}" wire:navigate class="nav-link">Data
                                    Referensi</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.soal-interpersonal') }}" wire:navigate
                                    class="nav-link">Soal</a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="collapse" href="#kecerdasan-emosi" role="button"
                        aria-expanded="false" aria-controls="kecerdasan-emosi">
                        <i class="link-icon" data-feather="box"></i>
                        <span class="link-title">Kecerdasan Emosi</span>
                        <i class="link-arrow" data-feather="chevron-down"></i>
                    </a>
                    <div class="collapse" data-bs-parent="#sidebarNav" id="kecerdasan-emosi">
                        <ul class="nav sub-menu">
                            <li class="nav-item">
                                <a href="{{ route('admin.ref-kecerdasan-emosi') }}" wire:navigate
                                    class="nav-link">Data Referensi</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.soal-kecerdasan-emosi') }}" wire:navigate
                                    class="nav-link">Soal</a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="collapse" href="#motivasi-komitmen" role="button"
                        aria-expanded="false" aria-controls="motivasi-komitmen">
                        <i class="link-icon" data-feather="box"></i>
                        <span class="link-title">Motivasi dan Komitmen</span>
                        <i class="link-arrow" data-feather="chevron-down"></i>
                    </a>
                    <div class="collapse" data-bs-parent="#sidebarNav" id="motivasi-komitmen">
                        <ul class="nav sub-menu">
                            <li class="nav-item">
                                <a href="{{ route('admin.ref-motivasi-komitmen') }}" wire:navigate
                                    class="nav-link">Data Referensi</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.soal-motivasi-komitmen') }}" wire:navigate
                                    class="nav-link">Soal</a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="collapse" href="#berpikir-kritis" role="button"
                        aria-expanded="false" aria-controls="berpikir-kritis">
                        <i class="link-icon" data-feather="box"></i>
                        <span class="link-title">Berpikir Kritis</span>
                        <i class="link-arrow" data-feather="chevron-down"></i>
                    </a>
                    <div class="collapse" data-bs-parent="#sidebarNav" id="berpikir-kritis">
                        <ul class="nav sub-menu">
                            <li class="nav-item">
                                <a href="{{ route('admin.ref-aspek-berpikir-kritis') }}" wire:navigate
                                    class="nav-link">Data Referensi Aspek</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.ref-indikator-berpikir-kritis') }}" wire:navigate
                                    class="nav-link">Data Referensi Indikator</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.soal-berpikir-kritis') }}" wire:navigate
                                    class="nav-link">Soal</a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="collapse" href="#problem-solving" role="button"
                        aria-expanded="false" aria-controls="problem-solving">
                        <i class="link-icon" data-feather="box"></i>
                        <span class="link-title">Problem Solving</span>
                        <i class="link-arrow" data-feather="chevron-down"></i>
                    </a>
                    <div class="collapse" data-bs-parent="#sidebarNav" id="problem-solving">
                        <ul class="nav sub-menu">
                            <li class="nav-item">
                                <a href="{{ route('admin.ref-aspek-problem-solving') }}" wire:navigate
                                    class="nav-link">Data Referensi Aspek</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.ref-indikator-problem-solving') }}" wire:navigate
                                    class="nav-link">Data Referensi Indikator</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.soal-problem-solving') }}" wire:navigate
                                    class="nav-link">Soal</a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="collapse" href="#kesadaran-diri" role="button"
                        aria-expanded="false" aria-controls="kesadaran-diri">
                        <i class="link-icon" data-feather="box"></i>
                        <span class="link-title">Kesadaran Diri</span>
                        <i class="link-arrow" data-feather="chevron-down"></i>
                    </a>
                    <div class="collapse" data-bs-parent="#sidebarNav" id="kesadaran-diri">
                        <ul class="nav sub-menu">
                            <li class="nav-item">
                                <a href="{{ route('admin.ref-kesadaran-diri') }}" wire:navigate
                                    class="nav-link">Data Referensi</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.soal-kesadaran-diri') }}" wire:navigate
                                    class="nav-link">Soal</a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="collapse" href="#cakap-digital" role="button"
                        aria-expanded="false" aria-controls="cakap-digital">
                        <i class="link-icon" data-feather="box"></i>
                        <span class="link-title">Cakap Digital</span>
                        <i class="link-arrow" data-feather="chevron-down"></i>
                    </a>
                    <div class="collapse" data-bs-parent="#sidebarNav" id="cakap-digital">
                        <ul class="nav sub-menu">
                            <li class="nav-item">
                                <a href="{{ route('admin.soal-cakap-digital') }}" wire:navigate
                                    class="nav-link">Soal</a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="collapse" href="#kompetensi-teknis" role="button"
                        aria-expanded="false" aria-controls="kompetensi-teknis">
                        <i class="link-icon" data-feather="box"></i>
                        <span class="link-title">Kompetensi Teknis</span>
                        <i class="link-arrow" data-feather="chevron-down"></i>
                    </a>
                    <div class="collapse" data-bs-parent="#sidebarNav" id="kompetensi-teknis">
                        <ul class="nav sub-menu">
                            <li class="nav-item">
                                <a href="{{ route('admin.soal-kompetensi-teknis') }}" wire:navigate
                                    class="nav-link">Soal</a>
                            </li>
                        </ul>
                    </div>
                </li>

                @if (auth()->guard('admin')->user()->role === 'admin')
                <li class="nav-item nav-category">Akses dan Jejak</li>
                <li class="nav-item">
                    <a href="{{ route('admin.user') }}" wire:navigate class="nav-link">
                        <i class="link-icon" data-feather="message-square"></i>
                        <span class="link-title">User</span>
                    </a>
                </li>
                @if (auth()->user()->role === 'admin')
                    <li class="nav-item">
                        <a href="{{ route('admin.log-activity') }}" wire:navigate class="nav-link">
                            <i class="link-icon" data-feather="message-square"></i>
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
