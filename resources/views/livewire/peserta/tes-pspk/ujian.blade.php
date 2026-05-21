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
    .text-pspk { color: #6f42c1; }
    .bg-pspk-subtle { background-color: rgba(111, 66, 193, 0.1); }
    .phase-badge {
        font-size: 0.8rem;
        font-weight: 600;
        letter-spacing: 0.5px;
    }

    /* ═══════════════════════════════════════════════
       ANKAS FULLSCREEN IMMERSIVE LAYOUT (Level 3/4)
       ═══════════════════════════════════════════════ */
    .ankas-fs {
        position: fixed;
        inset: 0;
        z-index: 1050;
        display: flex;
        flex-direction: column;
        background: #f4f3f8;
    }
    .tooltip {
        z-index: 1100 !important;
    }

    .ankas-fs-topbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0 1rem;
        background: #fff;
        border-bottom: 2px solid rgba(111, 66, 193, 0.12);
        box-shadow: 0 2px 12px rgba(111, 66, 193, 0.06);
        flex-shrink: 0;
        height: 54px;
        gap: 0.5rem;
    }

    .ankas-fs-timer-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        background: linear-gradient(135deg, rgba(111,66,193,0.08) 0%, rgba(161,140,209,0.12) 100%);
        padding: 0.3rem 0.85rem;
        border-radius: 2rem;
        color: #6f42c1;
    }
    .ankas-fs-timer-pill .timer-badge {
        font-size: 1rem;
    }

    .ankas-fs-stat {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        font-size: 0.78rem;
        font-weight: 600;
        padding: 0.2rem 0.6rem;
        border-radius: 1rem;
    }

    .ankas-fs-content {
        flex: 1;
        display: flex;
        overflow: hidden;
        min-height: 0;
    }

    .ankas-fs-pdf {
        flex: 0 0 62%;
        max-width: 62%;
        display: flex;
        flex-direction: column;
        background: #eceef3;
        position: relative;
    }
    .ankas-fs-pdf-header {
        display: flex;
        align-items: center;
        padding: 0.45rem 1rem;
        background: rgba(255,255,255,0.95);
        border-bottom: 1px solid #e2e4ea;
        flex-shrink: 0;
        backdrop-filter: blur(4px);
    }
    .ankas-fs-pdf iframe {
        flex: 1;
        width: 100%;
        border: none;
        display: block;
    }
    .ankas-fs-pdf-empty {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #6c757d;
    }

    .ankas-fs-divider {
        width: 3px;
        background: linear-gradient(180deg, rgba(111,66,193,0.15) 0%, rgba(111,66,193,0.05) 100%);
        flex-shrink: 0;
    }

    .ankas-fs-soal {
        flex: 1;
        display: flex;
        flex-direction: column;
        overflow: hidden;
        background: #fff;
    }
    .ankas-fs-soal-head {
        padding: 0.65rem 1.25rem;
        border-bottom: 1px solid #eee;
        background: #fff;
        flex-shrink: 0;
    }
    .ankas-fs-soal-body {
        flex: 1;
        overflow-y: auto;
        padding: 1.25rem;
        scrollbar-width: thin;
        scrollbar-color: rgba(111,66,193,0.2) transparent;
    }
    .ankas-fs-soal-body::-webkit-scrollbar { width: 6px; }
    .ankas-fs-soal-body::-webkit-scrollbar-thumb {
        background: rgba(111,66,193,0.2);
        border-radius: 3px;
    }

    .ankas-fs-option {
        padding: 0.85rem 1rem;
        border-radius: 0.5rem;
        border: 2px solid #e9ecef;
        transition: all 0.15s ease;
        cursor: pointer;
        display: flex;
        align-items: center;
        width: 100%;
        background: #fff;
        font-size: 0.92rem;
    }
    .ankas-fs-option:hover {
        border-color: #6f42c1;
        background-color: rgba(111, 66, 193, 0.04);
    }
    .ankas-fs-option.selected {
        border-color: #6f42c1;
        background: rgba(111, 66, 193, 0.08);
        box-shadow: 0 0 0 1px rgba(111, 66, 193, 0.15);
    }

    .ankas-fs-actions {
        padding: 0.6rem 1.25rem;
        border-top: 1px solid #eee;
        background: #fff;
        flex-shrink: 0;
    }

    .ankas-fs-nav-panel {
        border-top: 2px solid rgba(111, 66, 193, 0.1);
        background: #faf9fe;
        padding: 0.75rem 1.25rem;
        flex-shrink: 0;
        max-height: 180px;
        overflow-y: auto;
    }
    .ankas-fs-nav-btn {
        min-width: 36px;
        height: 36px;
        position: relative;
        font-weight: 600;
        font-size: 0.8rem;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    .ankas-fs-nav-legend {
        display: flex;
        gap: 0.75rem;
        flex-wrap: wrap;
    }
    .ankas-fs-nav-legend-item {
        display: flex;
        align-items: center;
        gap: 0.3rem;
    }
    .ankas-fs-nav-legend-dot {
        width: 18px;
        height: 18px;
        border-radius: 4px;
        display: inline-block;
    }

    /* ═══════════════════════════
       STANDARD LAYOUT
       ═══════════════════════════ */
    .status-bar {
        position: sticky;
        top: 0;
        z-index: 100;
        background: white;
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
    @if($isLevel34 && $isAnkasPhase)
    {{-- ══════════════════════════════════════════════════════════════
         IMMERSIVE FULLSCREEN ANKAS (Level 3/4 - Tahap 1)
         PDF lebih lebar 62%, navigasi terintegrasi
         ══════════════════════════════════════════════════════════════ --}}
    <div class="ankas-fs" x-data="{ showNav: false }">
        {{-- Top Bar --}}
        <div class="ankas-fs-topbar">
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-pspk-subtle text-pspk px-2 py-1 phase-badge">
                    <span wire:ignore><i data-feather="layers" style="width:13px;height:13px" class="me-1"></i></span>
                    Tahap 1: Ankas
                </span>
                <span class="badge text-white px-2 py-1" style="background:#6f42c1;font-size:0.78rem;">
                    Soal {{ $phaseNomor }} / {{ $jml_soal }}
                </span>
            </div>

            <div class="ankas-fs-timer-pill" wire:ignore.self>
                <span wire:ignore><i data-feather="clock" style="width:15px;height:15px"></i></span>
                <strong class="time timer-badge"></strong>
            </div>

            <div class="d-flex align-items-center gap-2">
                <span class="ankas-fs-stat bg-success bg-opacity-10 text-success" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Sudah dijawab">
                    <span wire:ignore><i data-feather="check" style="width:12px;height:12px"></i></span>
                    {{ $jml_soal - $jawaban_kosong }}
                </span>
                <span class="ankas-fs-stat bg-danger bg-opacity-10 text-danger" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Belum dijawab">
                    <span wire:ignore><i data-feather="x" style="width:12px;height:12px"></i></span>
                    {{ $jawaban_kosong ?? 0 }}
                </span>
                <button class="btn btn-sm btn-outline-secondary rounded-pill px-2 py-1"
                    @click="showNav = !showNav"
                    :class="{ 'btn-pspk text-white border-0': showNav }"
                    data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Navigasi Soal">
                    <span wire:ignore><i data-feather="grid" style="width:15px;height:15px"></i></span>
                </button>
                <button class="btn btn-sm btn-pspk rounded-pill px-3"
                    wire:click="lanjutKeSjt"
                    @if(!$allAnkasAnswered) disabled @endif>
                    Lanjut SJT
                    <span wire:ignore><i data-feather="arrow-right" style="width:14px;height:14px" class="ms-1"></i></span>
                </button>
            </div>
        </div>

        {{-- Split Content Area --}}
        <div class="ankas-fs-content">
            {{-- PDF Panel (62%) --}}
            <div class="ankas-fs-pdf">
                <div class="ankas-fs-pdf-header">
                    <span wire:ignore><i data-feather="file-text" style="width:15px;height:15px" class="text-pspk me-2"></i></span>
                    <small class="fw-semibold text-muted">Lampiran PDF Analisa Kasus</small>
                </div>
                <div wire:ignore style="flex:1;display:flex;flex-direction:column;">
                    @if($soal->kasusLampiran?->lampiran_pdf_path)
                        <iframe
                            src="{{ route('peserta.tes-pspk.lampiran-baca', ['soal' => $soal->id]) }}"
                            title="Lampiran PDF"
                            sandbox="allow-scripts allow-same-origin"
                            referrerpolicy="same-origin"
                            style="flex:1;width:100%;border:none;"
                        ></iframe>
                    @else
                        <div class="ankas-fs-pdf-empty">
                            <div class="text-center">
                                <i data-feather="file-minus" style="width:48px;height:48px" class="text-muted mb-2"></i>
                                <p class="text-muted mb-0">PDF lampiran tidak tersedia</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="ankas-fs-divider"></div>

            {{-- Soal Panel --}}
            <div class="ankas-fs-soal">
                {{-- Soal Header --}}
                <div class="ankas-fs-soal-head">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge text-white px-3 py-2" style="font-size:0.9rem;background:#6f42c1;">
                                Soal {{ $phaseNomor }}
                            </span>
                            @if(isset($flagged[$nomor_sekarang]))
                                <span class="badge bg-warning text-dark px-2 py-1">🔖 Ditandai</span>
                            @endif
                        </div>
                        <small class="text-muted">{{ $phaseNomor }} dari {{ $jml_soal }}</small>
                    </div>
                </div>

                {{-- Soal Body (scrollable) --}}
                <div class="ankas-fs-soal-body">
                    <div class="mb-4">
                        <p class="mb-0 lh-base" style="font-size:1.05rem;">{{ $soal->soal }}</p>
                    </div>

                    <div class="d-flex flex-column gap-2" wire:key="ankas-opsi-block-{{ $nomor_sekarang }}">
                        <label wire:key="ankas-opsi-{{ $nomor_sekarang }}-A" class="ankas-fs-option {{ ($jawaban[$nomor_sekarang - 1] ?? '') == 'A' ? 'selected' : '' }}">
                            <input class="form-check-input me-3" type="radio"
                                wire:model="jawaban_user.{{ $nomor_sekarang - 1 }}" value="A" id="opsi-{{ $nomor_sekarang }}-A">
                            <span><strong class="me-2">A.</strong> {{ $soal->opsi_a }}</span>
                        </label>
                        <label wire:key="ankas-opsi-{{ $nomor_sekarang }}-B" class="ankas-fs-option {{ ($jawaban[$nomor_sekarang - 1] ?? '') == 'B' ? 'selected' : '' }}">
                            <input class="form-check-input me-3" type="radio"
                                wire:model="jawaban_user.{{ $nomor_sekarang - 1 }}" value="B" id="opsi-{{ $nomor_sekarang }}-B">
                            <span><strong class="me-2">B.</strong> {{ $soal->opsi_b }}</span>
                        </label>
                        <label wire:key="ankas-opsi-{{ $nomor_sekarang }}-C" class="ankas-fs-option {{ ($jawaban[$nomor_sekarang - 1] ?? '') == 'C' ? 'selected' : '' }}">
                            <input class="form-check-input me-3" type="radio"
                                wire:model="jawaban_user.{{ $nomor_sekarang - 1 }}" value="C" id="opsi-{{ $nomor_sekarang }}-C">
                            <span><strong class="me-2">C.</strong> {{ $soal->opsi_c }}</span>
                        </label>
                        <label wire:key="ankas-opsi-{{ $nomor_sekarang }}-D" class="ankas-fs-option {{ ($jawaban[$nomor_sekarang - 1] ?? '') == 'D' ? 'selected' : '' }}">
                            <input class="form-check-input me-3" type="radio"
                                wire:model="jawaban_user.{{ $nomor_sekarang - 1 }}" value="D" id="opsi-{{ $nomor_sekarang }}-D">
                            <span><strong class="me-2">D.</strong> {{ $soal->opsi_d }}</span>
                        </label>
                        <label wire:key="ankas-opsi-{{ $nomor_sekarang }}-E" class="ankas-fs-option {{ ($jawaban[$nomor_sekarang - 1] ?? '') == 'E' ? 'selected' : '' }}">
                            <input class="form-check-input me-3" type="radio"
                                wire:model="jawaban_user.{{ $nomor_sekarang - 1 }}" value="E" id="opsi-{{ $nomor_sekarang }}-E">
                            <span><strong class="me-2">E.</strong> {{ $soal->opsi_e }}</span>
                        </label>
                    </div>
                </div>

                {{-- Navigation Drawer (collapsible) --}}
                <div class="ankas-fs-nav-panel" x-show="showNav" x-collapse>
                    <div class="d-flex flex-wrap gap-1 mb-2">
                        @for ($i = 1; $i <= $jmlAnkas; $i++)
                            <button wire:click="navigate({{ $i }})"
                                class="btn ankas-fs-nav-btn btn-sm
                                    {{ ($jawaban_tersimpan[$i - 1] ?? '0') === '0' ? 'btn-outline-danger' : 'btn-success' }}
                                    {{ isset($flagged[$i]) ? 'flagged-btn' : '' }}"
                                style="{{ $i == $nomor_sekarang ? 'box-shadow: 0 0 0 3px rgba(111,66,193,0.5);' : '' }}"
                            >
                                {{ $i }}
                                @if(isset($flagged[$i]))
                                    <span class="flag-icon" style="font-size:11px;top:-4px;right:-4px;">🔖</span>
                                @endif
                            </button>
                        @endfor
                    </div>
                    <div class="ankas-fs-nav-legend">
                        <div class="ankas-fs-nav-legend-item">
                            <span class="ankas-fs-nav-legend-dot bg-success"></span>
                            <small class="text-muted" style="font-size:0.72rem;">Dijawab</small>
                        </div>
                        <div class="ankas-fs-nav-legend-item">
                            <span class="ankas-fs-nav-legend-dot border border-danger"></span>
                            <small class="text-muted" style="font-size:0.72rem;">Belum</small>
                        </div>
                        <div class="ankas-fs-nav-legend-item">
                            <span class="ankas-fs-nav-legend-dot" style="background:#ffd15c;"></span>
                            <small class="text-muted" style="font-size:0.72rem;">Ditandai</small>
                        </div>
                    </div>
                </div>

                {{-- Fixed Action Bar --}}
                <div class="ankas-fs-actions">
                    <div class="d-flex gap-2">
                        <button class="btn btn-outline-secondary btn-sm px-3"
                            wire:click="navigate({{ $nomor_sekarang - 1 }})"
                            @if ($nomor_sekarang == 1) disabled @endif>
                            <span wire:ignore><i data-feather="chevron-left" style="width:16px;height:16px"></i></span>
                            <span class="d-none d-xl-inline ms-1">Sebelumnya</span>
                        </button>
                        <button class="btn btn-pspk btn-sm flex-fill"
                            wire:click="saveAndNext({{ $nomor_sekarang }})" id="btn-simpan"
                            @if(($jawaban[$nomor_sekarang - 1] ?? '0') === '0') disabled @endif>
                            Simpan & Lanjut
                            <span wire:ignore><i data-feather="chevron-right" style="width:16px;height:16px" class="ms-1"></i></span>
                        </button>
                        <button class="btn btn-sm px-3 {{ isset($flagged[$nomor_sekarang]) ? 'btn-warning' : 'btn-outline-warning' }}"
                            wire:click="toggleFlag({{ $nomor_sekarang }})"
                            data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="{{ isset($flagged[$nomor_sekarang]) ? 'Batalkan Tanda' : 'Tandai Soal' }}">
                            🔖
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @else
    {{-- ══════════════════════════════════════════════
         STANDARD LAYOUT (Level 1/2, atau SJT phase)
         ══════════════════════════════════════════════ --}}

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

            <div class="row g-3 mb-4" wire:key="std-opsi-block-{{ $nomor_sekarang }}">
                <div class="col-12" wire:key="std-opsi-col-{{ $nomor_sekarang }}-A">
                    <label class="option-card d-flex align-items-center w-100 {{ ($jawaban[$nomor_sekarang - 1] ?? '') == 'A' ? 'selected' : '' }}">
                        <input class="form-check-input me-3" type="radio"
                            wire:model="jawaban_user.{{ $nomor_sekarang - 1 }}" value="A" id="opsi-{{ $nomor_sekarang }}-A">
                        <span><strong class="me-2">A.</strong> {{ $soal->opsi_a }}</span>
                    </label>
                </div>
                <div class="col-12" wire:key="std-opsi-col-{{ $nomor_sekarang }}-B">
                    <label class="option-card d-flex align-items-center w-100 {{ ($jawaban[$nomor_sekarang - 1] ?? '') == 'B' ? 'selected' : '' }}">
                        <input class="form-check-input me-3" type="radio"
                            wire:model="jawaban_user.{{ $nomor_sekarang - 1 }}" value="B" id="opsi-{{ $nomor_sekarang }}-B">
                        <span><strong class="me-2">B.</strong> {{ $soal->opsi_b }}</span>
                    </label>
                </div>
                <div class="col-12" wire:key="std-opsi-col-{{ $nomor_sekarang }}-C">
                    <label class="option-card d-flex align-items-center w-100 {{ ($jawaban[$nomor_sekarang - 1] ?? '') == 'C' ? 'selected' : '' }}">
                        <input class="form-check-input me-3" type="radio"
                            wire:model="jawaban_user.{{ $nomor_sekarang - 1 }}" value="C" id="opsi-{{ $nomor_sekarang }}-C">
                        <span><strong class="me-2">C.</strong> {{ $soal->opsi_c }}</span>
                    </label>
                </div>
                <div class="col-12" wire:key="std-opsi-col-{{ $nomor_sekarang }}-D">
                    <label class="option-card d-flex align-items-center w-100 {{ ($jawaban[$nomor_sekarang - 1] ?? '') == 'D' ? 'selected' : '' }}">
                        <input class="form-check-input me-3" type="radio"
                            wire:model="jawaban_user.{{ $nomor_sekarang - 1 }}" value="D" id="opsi-{{ $nomor_sekarang }}-D">
                        <span><strong class="me-2">D.</strong> {{ $soal->opsi_d }}</span>
                    </label>
                </div>
                <div class="col-12" wire:key="std-opsi-col-{{ $nomor_sekarang }}-E">
                    <label class="option-card d-flex align-items-center w-100 {{ ($jawaban[$nomor_sekarang - 1] ?? '') == 'E' ? 'selected' : '' }}">
                        <input class="form-check-input me-3" type="radio"
                            wire:model="jawaban_user.{{ $nomor_sekarang - 1 }}" value="E" id="opsi-{{ $nomor_sekarang }}-E">
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
                <button class="btn btn-pspk" wire:click="saveAndNext({{ $nomor_sekarang }})" id="btn-simpan"
                    @if(($jawaban[$nomor_sekarang - 1] ?? '0') === '0') disabled @endif>
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
                        class="btn nav-btn btn-sm {{ ($jawaban_tersimpan[$idx - 1] ?? '0') === '0' ? 'btn-outline-danger' : 'btn-success' }} {{ isset($flagged[$idx]) ? 'flagged-btn' : '' }}"
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
    function initAnkasTooltips() {
        document.querySelectorAll('.ankas-fs [data-bs-toggle="tooltip"]').forEach(function (el) {
            var instance = bootstrap.Tooltip.getInstance(el);
            if (instance) instance.dispose();
            new bootstrap.Tooltip(el);
        });
    }

    document.addEventListener('livewire:initialized', function () {
        initAnkasTooltips();
        Livewire.hook('morph.updated', function () {
            initAnkasTooltips();
        });
    });

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
    $(document).on('change', '.form-check-input', function() {
        $('#btn-simpan').removeAttr('disabled');
    });

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
