<div>
    @php
        $peserta = auth()->guard('peserta')->user();
        $isMultiTest = $participations->count() > 1;
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
                            <p class="text-muted mb-2">{{ $peserta->nip ?: $peserta->nik }}</p>
                            <span class="badge bg-primary-subtle text-primary px-3 py-2">
                                <i class="me-1" data-feather="calendar"></i>
                                {{ $groupLabel }}
                            </span>
                            @if($isMultiTest)
                                <span class="badge bg-info-subtle text-info px-3 py-2 ms-1">
                                    {{ $participations->count() }} jenis tes
                                </span>
                            @endif
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
            @if($isMultiTest)
                <p class="text-muted small mt-1 mb-0">Selesaikan semua tes di bawah tanpa perlu logout. Kembali ke dashboard setelah setiap tes selesai.</p>
            @endif
        </div>
    </div>

    <div class="row">
        @forelse($participations as $participation)
            @php
                $event = $participation->event;
                $metodeTesId = $event->metode_tes_id;
                $finished = $participationService->isTestFinished($participation);
                $canStart = $participationService->canStartTest($participation);
                $eventClosed = $event->is_finished === 'true';
                $label = $participationService->metodeTesLabel($metodeTesId);
            @endphp

            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100 border-0 shadow-sm {{ $finished ? 'opacity-75' : 'card-hover' }}">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="rounded-circle {{ $finished ? 'bg-success-subtle' : 'bg-primary-subtle' }} p-3 me-3">
                                <i class="{{ $finished ? 'text-success' : 'text-primary' }}" data-feather="{{ $finished ? 'check-circle' : 'layers' }}" style="width: 24px; height: 24px;"></i>
                            </div>
                            <div>
                                <h5 class="card-title mb-0">{{ $label }}</h5>
                                <small class="text-muted">{{ $event->metodeTes->metode_tes ?? 'Tes' }}</small>
                            </div>
                        </div>

                        @if($isMultiTest)
                            <p class="card-text text-muted small mb-2">
                                Event: {{ $event->nama_event }}
                            </p>
                        @endif

                        @if($finished)
                            <button class="btn btn-success w-100" disabled>
                                <i class="me-2" data-feather="check"></i>
                                Sudah Selesai
                            </button>
                        @elseif($eventClosed)
                            <button class="btn btn-secondary w-100" disabled>
                                <i class="me-2" data-feather="lock"></i>
                                Event Ditutup Admin
                            </button>
                        @elseif($metodeTesId === 1 && $event->is_open == 'false')
                            <button class="btn btn-info w-100 disabled" disabled>
                                <i class="me-2" data-feather="lock"></i>
                                Portofolio Ditutup
                            </button>
                        @elseif($canStart)
                            <button type="button" class="btn btn-primary w-100" wire:click="startTest({{ $participation->id }})" wire:loading.attr="disabled">
                                <span wire:loading.remove wire:target="startTest({{ $participation->id }})">
                                    <i class="me-2" data-feather="play"></i>
                                    Mulai Tes
                                </span>
                                <span wire:loading wire:target="startTest({{ $participation->id }})">Membuka...</span>
                            </button>
                        @else
                            <button class="btn btn-secondary w-100" disabled>
                                Tidak Tersedia
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-warning">Tidak ada tes aktif untuk akun Anda.</div>
            </div>
        @endforelse
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
                                @if($isMultiTest)
                                    <li>Anda dapat melanjutkan ke tes lain dari dashboard tanpa logout</li>
                                @endif
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
    .bg-info-subtle {
        background-color: rgba(13, 202, 240, 0.15) !important;
    }
</style>
@endpush
