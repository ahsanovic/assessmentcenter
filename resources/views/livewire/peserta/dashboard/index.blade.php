<div>
    @php
        $event_id = auth()->guard('peserta')->user()->event_id;
        $peserta_id = auth()->guard('peserta')->user()->id;
        $metode_tes_id = auth()->guard('peserta')->user()->event->metode_tes_id;
        $peserta = auth()->guard('peserta')->user();
    @endphp

    <!-- Welcome Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="d-flex flex-column flex-md-row align-items-center">
                        <div class="mb-3 mb-md-0 me-md-4">
                            @if($peserta->foto)
                                <img src="{{ asset('storage/' . $peserta->foto) }}" alt="Profile" class="rounded-circle" style="width: 80px; height: 80px; object-fit: cover;">
                            @else
                                <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center text-white fw-bold" style="width: 80px; height: 80px; font-size: 2rem;">
                                    {{ strtoupper(substr($peserta->nama ?? 'P', 0, 1)) }}
                                </div>
                            @endif
                        </div>
                        <div class="text-center text-md-start">
                            <h4 class="mb-1">Selamat Datang, {{ $peserta->nama ?? 'Peserta' }}! 👋</h4>
                            <p class="text-muted mb-2">{{ $peserta->nip ?? '-' }}</p>
                            <span class="badge bg-primary-subtle text-primary px-3 py-2">
                                <i class="me-1" data-feather="calendar"></i>
                                {{ $peserta->event->nama ?? 'Event' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Menu Tes Section -->
    <div class="row mb-3">
        <div class="col-12">
            <h5 class="text-muted mb-0">
                <i class="me-2" data-feather="clipboard"></i>
                Menu Tes Tersedia
            </h5>
        </div>
    </div>

    <div class="row">
        @if ($metode_tes_id === 1)
            <!-- Portofolio Card -->
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100 border-0 shadow-sm card-hover">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="rounded-circle bg-info-subtle p-3 me-3">
                                <i class="text-info" data-feather="folder" style="width: 24px; height: 24px;"></i>
                            </div>
                            <div>
                                <h5 class="card-title mb-0">Portofolio</h5>
                                <small class="text-muted">Assessment Center</small>
                            </div>
                        </div>
                        <p class="card-text text-muted mb-4">Lengkapi data portofolio Anda untuk penilaian assessment center.</p>
                        <a href="{{ route('peserta.portofolio') }}" class="btn btn-info w-100" wire:navigate>
                            <i class="me-2" data-feather="arrow-right"></i>
                            Mulai Mengisi
                        </a>
                    </div>
                </div>
            </div>
        @endif

        @if ($metode_tes_id === 2)
            <!-- Tes Intelektual Card -->
            @php
                $data_intelektual = getFinishedTesIntelektual($event_id, $peserta_id);
                $test_intelektual_started = collect($data_intelektual)->contains(true);
            @endphp
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100 border-0 shadow-sm {{ $test_intelektual_started ? 'opacity-75' : 'card-hover' }}">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="rounded-circle {{ $test_intelektual_started ? 'bg-success-subtle' : 'bg-primary-subtle' }} p-3 me-3">
                                <i class="{{ $test_intelektual_started ? 'text-success' : 'text-primary' }}" data-feather="{{ $test_intelektual_started ? 'check-circle' : 'cpu' }}" style="width: 24px; height: 24px;"></i>
                            </div>
                            <div>
                                <h5 class="card-title mb-0">Tes Intelektual</h5>
                                <small class="text-muted">Kemampuan Kognitif</small>
                            </div>
                        </div>
                        <p class="card-text text-muted mb-4">Uji kemampuan berpikir logis, analitis, dan pemecahan masalah Anda.</p>
                        @if($test_intelektual_started)
                            <button class="btn btn-success w-100" disabled>
                                <i class="me-2" data-feather="check"></i>
                                Sudah Selesai
                            </button>
                        @else
                            <a href="{{ route('peserta.tes-intelektual') }}" class="btn btn-primary w-100" wire:navigate>
                                <i class="me-2" data-feather="play"></i>
                                Mulai Tes
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Tes Potensi Card -->
            @php
                $data_potensi = getFinishedTes($event_id, $peserta_id);
                $test_potensi_started = collect($data_potensi)->contains(true);
            @endphp
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100 border-0 shadow-sm {{ $test_potensi_started ? 'opacity-75' : 'card-hover' }}">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="rounded-circle {{ $test_potensi_started ? 'bg-success-subtle' : 'bg-warning-subtle' }} p-3 me-3">
                                <i class="{{ $test_potensi_started ? 'text-success' : 'text-warning' }}" data-feather="{{ $test_potensi_started ? 'check-circle' : 'trending-up' }}" style="width: 24px; height: 24px;"></i>
                            </div>
                            <div>
                                <h5 class="card-title mb-0">Tes Potensi</h5>
                                <small class="text-muted">Kemampuan Dasar</small>
                            </div>
                        </div>
                        <p class="card-text text-muted mb-4">Uji potensi dan kemampuan dasar Anda untuk penilaian keseluruhan.</p>
                        @if($test_potensi_started)
                            <button class="btn btn-success w-100" disabled>
                                <i class="me-2" data-feather="check"></i>
                                Sudah Selesai
                            </button>
                        @else
                            <a href="{{ route('peserta.tes-potensi') }}" class="btn btn-warning w-100" wire:navigate>
                                <i class="me-2" data-feather="play"></i>
                                Mulai Tes
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        @if ($metode_tes_id === 3)
            <!-- Tes Cakap Digital Card -->
            @php
                $data_cakap_digital = getFinishedTesCakapDigital($event_id, $peserta_id);
                $test_cakap_digital_started = collect($data_cakap_digital)->contains(true);
            @endphp
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100 border-0 shadow-sm {{ $test_cakap_digital_started ? 'opacity-75' : 'card-hover' }}">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="rounded-circle {{ $test_cakap_digital_started ? 'bg-success-subtle' : 'bg-danger-subtle' }} p-3 me-3">
                                <i class="{{ $test_cakap_digital_started ? 'text-success' : 'text-danger' }}" data-feather="{{ $test_cakap_digital_started ? 'check-circle' : 'monitor' }}" style="width: 24px; height: 24px;"></i>
                            </div>
                            <div>
                                <h5 class="card-title mb-0">Tes Cakap Digital</h5>
                                <small class="text-muted">Kompetensi Digital</small>
                            </div>
                        </div>
                        <p class="card-text text-muted mb-4">Uji kemampuan dan literasi digital Anda di era teknologi modern.</p>
                        @if($test_cakap_digital_started)
                            <button class="btn btn-success w-100" disabled>
                                <i class="me-2" data-feather="check"></i>
                                Sudah Selesai
                            </button>
                        @else
                            <a href="{{ route('peserta.tes-cakap-digital') }}" class="btn btn-danger w-100" wire:navigate>
                                <i class="me-2" data-feather="play"></i>
                                Mulai Tes
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        @if ($metode_tes_id === 4)
            <!-- Tes Kompetensi Teknis Card -->
            @php
                $data_kompetensi_teknis = getFinishedTesKompetensiTeknis($event_id, $peserta_id);
                $test_kompetensi_teknis_started = collect($data_kompetensi_teknis)->contains(true);
            @endphp
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100 border-0 shadow-sm {{ $test_kompetensi_teknis_started ? 'opacity-75' : 'card-hover' }}">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="rounded-circle {{ $test_kompetensi_teknis_started ? 'bg-success-subtle' : 'bg-secondary-subtle' }} p-3 me-3">
                                <i class="{{ $test_kompetensi_teknis_started ? 'text-success' : 'text-secondary' }}" data-feather="{{ $test_kompetensi_teknis_started ? 'check-circle' : 'settings' }}" style="width: 24px; height: 24px;"></i>
                            </div>
                            <div>
                                <h5 class="card-title mb-0">Tes Kompetensi Teknis</h5>
                                <small class="text-muted">Kemampuan Teknis</small>
                            </div>
                        </div>
                        <p class="card-text text-muted mb-4">Uji kemampuan teknis sesuai dengan bidang keahlian Anda.</p>
                        @if($test_kompetensi_teknis_started)
                            <button class="btn btn-success w-100" disabled>
                                <i class="me-2" data-feather="check"></i>
                                Sudah Selesai
                            </button>
                        @else
                            <a href="{{ route('peserta.tes-kompetensi-teknis') }}" class="btn btn-secondary w-100" wire:navigate>
                                <i class="me-2" data-feather="play"></i>
                                Mulai Tes
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        @if ($metode_tes_id === 5)
            <!-- Tes PSPK Level 1 Card -->
            @php
                $data_pspk = getFinishedTesPspk($event_id, $peserta_id);
                $test_pspk_started = collect($data_pspk)->contains(true);
            @endphp
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100 border-0 shadow-sm {{ $test_pspk_started ? 'opacity-75' : 'card-hover' }}">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="rounded-circle {{ $test_pspk_started ? 'bg-success-subtle' : 'bg-purple' }} p-3 me-3" style="{{ !$test_pspk_started ? 'background-color: rgba(111, 66, 193, 0.15);' : '' }}">
                                <i class="{{ $test_pspk_started ? 'text-success' : '' }}" data-feather="{{ $test_pspk_started ? 'check-circle' : 'award' }}" style="width: 24px; height: 24px; {{ !$test_pspk_started ? 'color: #6f42c1;' : '' }}"></i>
                            </div>
                            <div>
                                <h5 class="card-title mb-0">Tes PSPK Level 1</h5>
                                <small class="text-muted">Penilaian Kepegawaian</small>
                            </div>
                        </div>
                        <p class="card-text text-muted mb-4">Uji kompetensi PSPK tingkat dasar untuk penilaian kepegawaian.</p>
                        @if($test_pspk_started)
                            <button class="btn btn-success w-100" disabled>
                                <i class="me-2" data-feather="check"></i>
                                Sudah Selesai
                            </button>
                        @else
                            <a href="{{ route('peserta.tes-pspk') }}" class="btn w-100 text-white" style="background-color: #6f42c1;" wire:navigate>
                                <i class="me-2" data-feather="play"></i>
                                Mulai Tes
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        @if ($metode_tes_id === 6)
            <!-- Tes PSPK Level 2 Card -->
            @php
                $data_pspk = getFinishedTesPspk($event_id, $peserta_id);
                $test_pspk_started = collect($data_pspk)->contains(true);
            @endphp
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100 border-0 shadow-sm {{ $test_pspk_started ? 'opacity-75' : 'card-hover' }}">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="rounded-circle {{ $test_pspk_started ? 'bg-success-subtle' : '' }} p-3 me-3" style="{{ !$test_pspk_started ? 'background-color: rgba(214, 51, 132, 0.15);' : '' }}">
                                <i class="{{ $test_pspk_started ? 'text-success' : '' }}" data-feather="{{ $test_pspk_started ? 'check-circle' : 'star' }}" style="width: 24px; height: 24px; {{ !$test_pspk_started ? 'color: #d63384;' : '' }}"></i>
                            </div>
                            <div>
                                <h5 class="card-title mb-0">Tes PSPK Level 2</h5>
                                <small class="text-muted">Penilaian Kepegawaian</small>
                            </div>
                        </div>
                        <p class="card-text text-muted mb-4">Uji kompetensi PSPK tingkat lanjutan untuk penilaian kepegawaian.</p>
                        @if($test_pspk_started)
                            <button class="btn btn-success w-100" disabled>
                                <i class="me-2" data-feather="check"></i>
                                Sudah Selesai
                            </button>
                        @else
                            <a href="{{ route('peserta.tes-pspk') }}" class="btn w-100 text-white" style="background-color: #d63384;" wire:navigate>
                                <i class="me-2" data-feather="play"></i>
                                Mulai Tes
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Info Section -->
    <div class="row mt-2">
        <div class="col-12">
            <div class="card border-0 shadow-sm bg-light">
                <div class="card-body p-4">
                    <div class="d-flex align-items-start">
                        <div class="me-3">
                            <i class="text-info" data-feather="info" style="width: 24px; height: 24px;"></i>
                        </div>
                        <div>
                            <h6 class="mb-2">Petunjuk Pengerjaan Tes</h6>
                            <ul class="mb-0 ps-3 text-muted">
                                <li>Pastikan koneksi internet Anda stabil sebelum memulai tes</li>
                                <li>Tes yang sudah dimulai tidak dapat diulang kembali</li>
                                <li>Jawab setiap pertanyaan dengan teliti dan jujur</li>
                                <li>Perhatikan waktu pengerjaan pada setiap tes</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('css')
<style>
    .card-hover {
        transition: all 0.3s ease;
    }
    .card-hover:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    }
    .bg-primary-subtle {
        background-color: rgba(13, 110, 253, 0.15) !important;
    }
    .bg-success-subtle {
        background-color: rgba(25, 135, 84, 0.15) !important;
    }
    .bg-warning-subtle {
        background-color: rgba(255, 193, 7, 0.15) !important;
    }
    .bg-danger-subtle {
        background-color: rgba(220, 53, 69, 0.15) !important;
    }
    .bg-info-subtle {
        background-color: rgba(13, 202, 240, 0.15) !important;
    }
    .bg-secondary-subtle {
        background-color: rgba(108, 117, 125, 0.15) !important;
    }
</style>
@endpush
