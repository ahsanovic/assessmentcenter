@push('css')
<style>
    .form-check-input[type="radio"] {
        border: 2px solid #dee2e6;
        width: 1.25em;
        height: 1.25em;
        cursor: pointer;
    }
    .form-check-input[type="radio"]:checked {
        background-color: #6f42c1;
        border-color: #6f42c1;
    }
    .flagged-btn {
        background-color: #ffd15c !important;
        border-color: #e8b200 !important;
        color: #000 !important;
    }
    .flag-icon {
        position: absolute;
        top: -6px;
        right: -6px;
        font-size: 14px;
    }
    .option-card {
        padding: 1rem;
        border-radius: 0.5rem;
        border: 2px solid #e9ecef;
        transition: all 0.2s ease;
        cursor: pointer;
    }
    .option-card:hover {
        border-color: #6f42c1;
        background-color: rgba(111, 66, 193, 0.05);
    }
    .option-card.selected {
        border-color: #6f42c1;
        background-color: rgba(111, 66, 193, 0.1);
    }
    .timer-badge {
        font-size: 1.25rem;
        font-weight: 600;
        font-family: 'Courier New', monospace;
    }
    .nav-btn {
        min-width: 40px;
        height: 40px;
        position: relative;
        font-weight: 600;
    }
    .status-bar {
        position: sticky;
        top: 0;
        z-index: 100;
        background: white;
    }
    .btn-pspk {
        background-color: #6f42c1;
        border-color: #6f42c1;
        color: white;
    }
    .btn-pspk:hover {
        background-color: #5a32a3;
        border-color: #5a32a3;
        color: white;
    }
    .text-pspk {
        color: #6f42c1;
    }
    .bg-pspk-subtle {
        background-color: rgba(111, 66, 193, 0.1);
    }
    .ankas-pdf-panel {
        min-height: 70vh;
        border-right: 2px solid #dee2e6;
    }
    .ankas-pdf-panel iframe {
        width: 100%;
        height: calc(70vh - 52px);
        border: none;
        display: block;
    }
    .ankas-soal-panel {
        display: flex;
        flex-direction: column;
        max-height: 70vh;
    }
    .ankas-soal-panel .soal-body {
        flex: 1;
        overflow-y: auto;
        padding: 1.5rem;
    }
    .phase-badge {
        font-size: 0.8rem;
        font-weight: 600;
        letter-spacing: 0.5px;
    }
</style>
@endpush

{{-- <div x-data
x-init="
    document.addEventListener('visibilitychange', () => {
        if (document.hidden) {
            $wire.laporPelanggaran();
        }
    });

    Livewire.on('toast', e => {
        toastr.options = {
            positionClass: 'toast-top-center',
            closeButton: true,
            timeOut: 0,
            extendedTimeOut: 0,
        };
        toastr[e.type](e.message);
    });
"> --}}
<div>
    <!-- Header Card -->
    <div class="card border-0 shadow-sm mb-4" style="background: linear-gradient(135deg, #a18cd1 0%, #fbc2eb 100%);">
        <div class="card-body p-4 text-white">
            <div class="d-flex flex-column flex-md-row align-items-center justify-content-between">
                <div class="d-flex align-items-center mb-3 mb-md-0">
                    <div class="rounded-circle bg-white bg-opacity-25 p-2 me-3" wire:ignore>
                        <i data-feather="award" style="width: 28px; height: 28px;"></i>
                    </div>
                    <div>
                        <h4 class="mb-0">Tes {{ auth()->guard('peserta')->user()->event->metodeTes->metode_tes }}</h4>
                        <small class="opacity-75">Jawab semua pertanyaan dengan teliti</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Bar -->
    <div class="card border-0 shadow-sm mb-4 status-bar">
        <div class="card-body py-3">
            @if($isLevel34)
            <div class="mb-2">
                <span class="badge phase-badge {{ $isAnkasPhase ? 'bg-pspk-subtle text-pspk' : 'bg-info bg-opacity-10 text-info' }} px-3 py-2">
                    {{ $isAnkasPhase ? 'Tahap 1: Analisa Kasus (Ankas)' : 'Tahap 2: Situational Judgment Test (SJT)' }}
                </span>
            </div>
            @endif
            <div class="row align-items-center g-3">
                <div class="col-6 col-md-3">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-success bg-opacity-10 p-2 me-2" wire:ignore>
                            <i class="text-success" data-feather="check-circle" style="width: 20px; height: 20px;"></i>
                        </div>
                        <div>
                            <small class="text-muted d-block">Dijawab</small>
                            <strong class="text-success">{{ $jml_soal - $jawaban_kosong }}</strong>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-danger bg-opacity-10 p-2 me-2" wire:ignore>
                            <i class="text-danger" data-feather="x-circle" style="width: 20px; height: 20px;"></i>
                        </div>
                        <div>
                            <small class="text-muted d-block">Belum Dijawab</small>
                            <strong class="text-danger">{{ $jawaban_kosong ?? 0 }}</strong>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-pspk-subtle p-2 me-2" wire:ignore>
                            <i class="text-pspk" data-feather="clock" style="width: 20px; height: 20px;"></i>
                        </div>
                        <div>
                            <small class="text-muted d-block">Sisa Waktu</small>
                            <strong class="timer-badge text-pspk time"></strong>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3 text-md-end">
                    @if($isLevel34 && $isAnkasPhase)
                        <button class="btn btn-pspk" wire:click="lanjutKeSjt" @if(!$allAnkasAnswered) disabled @endif>
                            <span wire:ignore><i data-feather="arrow-right-circle" style="width: 18px; height: 18px;" class="me-1"></i></span>
                            Lanjut Tes Berikutnya
                        </button>
                    @else
                        <button class="btn btn-warning"
                            x-data
                            @click="Swal.fire({
                                title: 'Apakah Anda yakin mengakhiri tes?',
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonText: 'Akhiri Tes!',
                                cancelButtonText: 'Batal',
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    $wire.finish();
                                }
                            })"
                        >
                            <span wire:ignore><i data-feather="log-out" style="width: 18px; height: 18px;" class="me-1"></i></span>
                            Selesai
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- ==========================================
         ANKAS PHASE: Split View (PDF kiri, Soal kanan)
         ========================================== --}}
    @if($isLevel34 && $isAnkasPhase)

    <div class="card border-0 shadow-sm mb-4">
        <div class="row g-0">
            <!-- PDF Panel (Kiri) -->
            <div class="col-lg-6 ankas-pdf-panel">
                <div class="card-header bg-white border-bottom py-2 px-3">
                    <div class="d-flex align-items-center" wire:ignore>
                        <i data-feather="file-text" style="width: 18px; height: 18px;" class="text-pspk me-2"></i>
                        <h6 class="mb-0">Lampiran PDF Analisa Kasus</h6>
                    </div>
                </div>
                @if($soal->kasusLampiran?->lampiran_pdf_path)
                    <iframe
                        src="{{ route('peserta.tes-pspk.lampiran-baca', ['soal' => $soal->id]) }}"
                        title="Lampiran PDF"
                        sandbox="allow-scripts allow-same-origin"
                        referrerpolicy="same-origin"
                        class="iframe-lampiran-pdf"
                    ></iframe>
                @else
                    <div class="d-flex align-items-center justify-content-center" style="height: calc(70vh - 52px);">
                        <p class="text-muted mb-0">PDF lampiran tidak tersedia</p>
                    </div>
                @endif
            </div>

            <!-- Soal Panel (Kanan) -->
            <div class="col-lg-6 ankas-soal-panel">
                <div class="card-header bg-white border-bottom py-3 px-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <span class="badge text-white me-3 px-3 py-2" style="font-size: 1rem; background-color: #6f42c1;">
                                Soal {{ $phaseNomor }}
                            </span>
                            @if(isset($flagged[$nomor_sekarang]))
                                <span class="badge bg-warning text-dark">🔖 Ditandai</span>
                            @endif
                        </div>
                        <small class="text-muted">{{ $phaseNomor }} dari {{ $jml_soal }} soal</small>
                    </div>
                </div>
                <div class="soal-body">
                    <div class="mb-4">
                        <p class="fs-5 mb-0">{{ $soal->soal }}</p>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-12">
                            <label class="option-card d-flex align-items-center w-100 {{ ($jawaban[$nomor_sekarang - 1] ?? '') == 'A' ? 'selected' : '' }}">
                                <input class="form-check-input me-3" type="radio"
                                    wire:model="jawaban_user.{{ $nomor_sekarang - 1 }}" value="A" id="opsi1">
                                <span><strong class="me-2">A.</strong> {{ $soal->opsi_a }}</span>
                            </label>
                        </div>
                        <div class="col-12">
                            <label class="option-card d-flex align-items-center w-100 {{ ($jawaban[$nomor_sekarang - 1] ?? '') == 'B' ? 'selected' : '' }}">
                                <input class="form-check-input me-3" type="radio"
                                    wire:model="jawaban_user.{{ $nomor_sekarang - 1 }}" value="B" id="opsi2">
                                <span><strong class="me-2">B.</strong> {{ $soal->opsi_b }}</span>
                            </label>
                        </div>
                        <div class="col-12">
                            <label class="option-card d-flex align-items-center w-100 {{ ($jawaban[$nomor_sekarang - 1] ?? '') == 'C' ? 'selected' : '' }}">
                                <input class="form-check-input me-3" type="radio"
                                    wire:model="jawaban_user.{{ $nomor_sekarang - 1 }}" value="C" id="opsi3">
                                <span><strong class="me-2">C.</strong> {{ $soal->opsi_c }}</span>
                            </label>
                        </div>
                        <div class="col-12">
                            <label class="option-card d-flex align-items-center w-100 {{ ($jawaban[$nomor_sekarang - 1] ?? '') == 'D' ? 'selected' : '' }}">
                                <input class="form-check-input me-3" type="radio"
                                    wire:model="jawaban_user.{{ $nomor_sekarang - 1 }}" value="D" id="opsi4">
                                <span><strong class="me-2">D.</strong> {{ $soal->opsi_d }}</span>
                            </label>
                        </div>
                        <div class="col-12">
                            <label class="option-card d-flex align-items-center w-100 {{ ($jawaban[$nomor_sekarang - 1] ?? '') == 'E' ? 'selected' : '' }}">
                                <input class="form-check-input me-3" type="radio"
                                    wire:model="jawaban_user.{{ $nomor_sekarang - 1 }}" value="E" id="opsi5">
                                <span><strong class="me-2">E.</strong> {{ $soal->opsi_e }}</span>
                            </label>
                        </div>
                    </div>

                    <div class="d-flex flex-wrap gap-2">
                        <button class="btn btn-outline-secondary" wire:click="navigate({{ $nomor_sekarang - 1 }})"
                            @if ($nomor_sekarang == 1) disabled @endif>
                            <span wire:ignore><i data-feather="chevron-left" style="width: 18px; height: 18px;"></i></span>
                            Sebelumnya
                        </button>
                        <button class="btn btn-pspk" wire:click="saveAndNext({{ $nomor_sekarang }})" id="btn-simpan" disabled>
                            Simpan & Lanjutkan
                            <span wire:ignore><i data-feather="chevron-right" style="width: 18px; height: 18px;"></i></span>
                        </button>
                        <button class="btn {{ isset($flagged[$nomor_sekarang]) ? 'btn-warning' : 'btn-outline-warning' }}" wire:click="toggleFlag({{ $nomor_sekarang }})">
                            🔖 {{ isset($flagged[$nomor_sekarang]) ? 'Batalkan Tanda' : 'Tandai Soal' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <hr class="my-4" style="border-top: 2px solid rgba(111, 66, 193, 0.3);">

    <!-- Navigation Grid Ankas -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0 py-3">
            <h6 class="mb-0">
                <span wire:ignore><i data-feather="grid" style="width: 18px; height: 18px;" class="me-2"></i></span>
                Navigasi Soal Ankas
            </h6>
        </div>
        <div class="card-body p-4">
            <div class="d-flex flex-wrap gap-2">
                @for ($i = 1; $i <= $jmlAnkas; $i++)
                    <button wire:click="navigate({{ $i }})"
                        class="btn nav-btn btn-sm {{ $jawaban[$i - 1] === '0' ? 'btn-outline-danger' : 'btn-success' }} {{ isset($flagged[$i]) ? 'flagged-btn' : '' }}"
                        style="{{ $i == $nomor_sekarang ? 'box-shadow: 0 0 0 3px rgba(111, 66, 193, 0.5);' : '' }}"
                    >
                        {{ $i }}
                        @if(isset($flagged[$i]))
                            <span class="flag-icon">🔖</span>
                        @endif
                    </button>
                @endfor
            </div>
            <div class="mt-4 d-flex flex-wrap gap-3">
                <div class="d-flex align-items-center">
                    <span class="btn btn-sm btn-success me-2" style="width: 30px; height: 30px;"></span>
                    <small class="text-muted">Sudah Dijawab</small>
                </div>
                <div class="d-flex align-items-center">
                    <span class="btn btn-sm btn-outline-danger me-2" style="width: 30px; height: 30px;"></span>
                    <small class="text-muted">Belum Dijawab</small>
                </div>
                <div class="d-flex align-items-center">
                    <span class="btn btn-sm flagged-btn me-2" style="width: 30px; height: 30px;"></span>
                    <small class="text-muted">Ditandai</small>
                </div>
            </div>
        </div>
    </div>

    @else
    {{-- ==========================================
         STANDARD LAYOUT (Level 1/2, atau SJT phase)
         ========================================== --}}

    <!-- Question Card -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-0 py-3">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <span class="badge text-white me-3 px-3 py-2" style="font-size: 1rem; background-color: #6f42c1;">
                        Soal {{ $isLevel34 ? $phaseNomor : $nomor_sekarang }}
                    </span>
                    @if(isset($flagged[$nomor_sekarang]))
                        <span class="badge bg-warning text-dark">🔖 Ditandai</span>
                    @endif
                </div>
                <small class="text-muted">{{ $isLevel34 ? $phaseNomor : $nomor_sekarang }} dari {{ $jml_soal }} soal</small>
            </div>
        </div>
        <div class="card-body p-4">
            <div class="mb-4">
                <p class="fs-5 mb-0">{{ $soal->soal }}</p>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-12">
                    <label class="option-card d-flex align-items-center w-100 {{ ($jawaban[$nomor_sekarang - 1] ?? '') == 'A' ? 'selected' : '' }}">
                        <input class="form-check-input me-3" type="radio"
                            wire:model="jawaban_user.{{ $nomor_sekarang - 1 }}" value="A" id="opsi1">
                        <span><strong class="me-2">A.</strong> {{ $soal->opsi_a }}</span>
                    </label>
                </div>
                <div class="col-12">
                    <label class="option-card d-flex align-items-center w-100 {{ ($jawaban[$nomor_sekarang - 1] ?? '') == 'B' ? 'selected' : '' }}">
                        <input class="form-check-input me-3" type="radio"
                            wire:model="jawaban_user.{{ $nomor_sekarang - 1 }}" value="B" id="opsi2">
                        <span><strong class="me-2">B.</strong> {{ $soal->opsi_b }}</span>
                    </label>
                </div>
                <div class="col-12">
                    <label class="option-card d-flex align-items-center w-100 {{ ($jawaban[$nomor_sekarang - 1] ?? '') == 'C' ? 'selected' : '' }}">
                        <input class="form-check-input me-3" type="radio"
                            wire:model="jawaban_user.{{ $nomor_sekarang - 1 }}" value="C" id="opsi3">
                        <span><strong class="me-2">C.</strong> {{ $soal->opsi_c }}</span>
                    </label>
                </div>
                <div class="col-12">
                    <label class="option-card d-flex align-items-center w-100 {{ ($jawaban[$nomor_sekarang - 1] ?? '') == 'D' ? 'selected' : '' }}">
                        <input class="form-check-input me-3" type="radio"
                            wire:model="jawaban_user.{{ $nomor_sekarang - 1 }}" value="D" id="opsi4">
                        <span><strong class="me-2">D.</strong> {{ $soal->opsi_d }}</span>
                    </label>
                </div>
                <div class="col-12">
                    <label class="option-card d-flex align-items-center w-100 {{ ($jawaban[$nomor_sekarang - 1] ?? '') == 'E' ? 'selected' : '' }}">
                        <input class="form-check-input me-3" type="radio"
                            wire:model="jawaban_user.{{ $nomor_sekarang - 1 }}" value="E" id="opsi5">
                        <span><strong class="me-2">E.</strong> {{ $soal->opsi_e }}</span>
                    </label>
                </div>
            </div>

            <div class="d-flex flex-wrap gap-2">
                @php
                    $prevMin = $isLevel34 ? ($jmlAnkas + 1) : 1;
                @endphp
                <button class="btn btn-outline-secondary" wire:click="navigate({{ $nomor_sekarang - 1 }})"
                    @if ($nomor_sekarang == $prevMin) disabled @endif>
                    <span wire:ignore><i data-feather="chevron-left" style="width: 18px; height: 18px;"></i></span>
                    Sebelumnya
                </button>
                <button class="btn btn-pspk" wire:click="saveAndNext({{ $nomor_sekarang }})" id="btn-simpan" disabled>
                    Simpan & Lanjutkan
                    <span wire:ignore><i data-feather="chevron-right" style="width: 18px; height: 18px;"></i></span>
                </button>
                <button class="btn {{ isset($flagged[$nomor_sekarang]) ? 'btn-warning' : 'btn-outline-warning' }}" wire:click="toggleFlag({{ $nomor_sekarang }})">
                    🔖 {{ isset($flagged[$nomor_sekarang]) ? 'Batalkan Tanda' : 'Tandai Soal' }}
                </button>
            </div>
        </div>
    </div>

    <!-- Navigation Grid -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0 py-3">
            <h6 class="mb-0">
                <span wire:ignore><i data-feather="grid" style="width: 18px; height: 18px;" class="me-2"></i></span>
                Navigasi Soal{{ $isLevel34 ? ' SJT' : '' }}
            </h6>
        </div>
        <div class="card-body p-4">
            @php
                $navStart = $isLevel34 ? ($jmlAnkas + 1) : 1;
                $navEnd = $isLevel34 ? $totalSoalAll : $jml_soal;
                $nomor_display = 1;
            @endphp
            <div class="d-flex flex-wrap gap-2">
                @for ($idx = $navStart; $idx <= $navEnd; $idx++)
                    <button wire:click="navigate({{ $idx }})"
                        class="btn nav-btn btn-sm {{ $jawaban[$idx - 1] === '0' ? 'btn-outline-danger' : 'btn-success' }} {{ isset($flagged[$idx]) ? 'flagged-btn' : '' }}"
                        style="{{ $idx == $nomor_sekarang ? 'box-shadow: 0 0 0 3px rgba(111, 66, 193, 0.5);' : '' }}"
                    >
                        {{ $isLevel34 ? $nomor_display : $idx }}
                        @if(isset($flagged[$idx]))
                            <span class="flag-icon">🔖</span>
                        @endif
                    </button>
                    @php $nomor_display++; @endphp
                @endfor
            </div>
            <div class="mt-4 d-flex flex-wrap gap-3">
                <div class="d-flex align-items-center">
                    <span class="btn btn-sm btn-success me-2" style="width: 30px; height: 30px;"></span>
                    <small class="text-muted">Sudah Dijawab</small>
                </div>
                <div class="d-flex align-items-center">
                    <span class="btn btn-sm btn-outline-danger me-2" style="width: 30px; height: 30px;"></span>
                    <small class="text-muted">Belum Dijawab</small>
                </div>
                <div class="d-flex align-items-center">
                    <span class="btn btn-sm flagged-btn me-2" style="width: 30px; height: 30px;"></span>
                    <small class="text-muted">Ditandai</small>
                </div>
            </div>
        </div>
    </div>

    @endif
</div>

@push('js')
<script>
    document.addEventListener('livewire:init', () => {
        Livewire.on('load-flags-from-browser', () => {
            let flags = JSON.parse(localStorage.getItem('flags_soal') || '{}');
            Livewire.dispatch('updateFlagsFromBrowser', { flags });
        });

        Livewire.on('toggle-flag-in-browser', (data) => {
            let nomor = data.nomor;
            let flags = JSON.parse(localStorage.getItem('flags_soal') || '{}');
            if (flags[nomor]) {
                delete flags[nomor];
            } else {
                flags[nomor] = true;
            }
            localStorage.setItem('flags_soal', JSON.stringify(flags));
        });

        Livewire.on('request-flags-sync', () => {
            let flags = JSON.parse(localStorage.getItem('flags_soal') || '{}');
            Livewire.dispatch('updateFlagsFromBrowser', { flags });
        });

        Livewire.on('clear-flags-browser', () => {
            localStorage.removeItem('flags_soal');
        });
    });
</script>
<script>
    $(document).ready(function() {
        $('.form-check-input').change(function() {
            $('#btn-simpan').removeAttr('disabled');
        })
    })

    var waktuBerakhir = new Date({{ $timer }} * 1000).getTime();
    var isShow = false;

    var x = setInterval(function() {
        var now = new Date().getTime();
        var distance = waktuBerakhir - now;

        if (distance <= 0 && !isShow) {
            isShow = true;
            clearInterval(x);
            $('.time').html('Waktu Habis');
            Swal.fire({
                title: 'Waktu habis!',
                icon: 'warning',
                confirmButtonText: 'Akhiri Tes!',
                allowOutsideClick: false,
                allowEscapeKey: false
            }).then((result) => {
                if (result.isConfirmed) {
                    let el = document.querySelector('[wire\\:id]');
                    if (el) {
                        let component = Livewire.find(el.getAttribute('wire:id'));
                        if (component) {
                            component.finish();
                        }
                    }
                }
            })
            return;
        } else if (!isShow) {
            var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            var seconds = Math.floor((distance % (1000 * 60)) / 1000);
            $('.time').html(('0' + hours).slice(-2) + " : " + ('0' + minutes).slice(-2) + " : " + ('0' + seconds).slice(-2));
        }
    }, 1000);
</script>
@endpush
