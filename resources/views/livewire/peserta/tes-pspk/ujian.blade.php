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
    .swal2-popup button.swal2-deny.ujian-semua-terjawab-deny-btn {
        background-color: #2563eb !important;
        color: #fff !important;
    }
    .swal2-popup button.swal2-confirm.ujian-semua-terjawab-confirm-btn {
        background-color: #059669 !important;
    }
    .btn-pspk-selesai {
        --pspk-from: #6f42c1;
        --pspk-to: #a18cd1;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.4rem;
        padding: 0.55rem 1.25rem;
        border: 0;
        border-radius: 999px;
        color: #fff !important;
        font-weight: 600;
        letter-spacing: 0.01em;
        background: linear-gradient(135deg, var(--pspk-from) 0%, var(--pspk-to) 100%);
        box-shadow: 0 8px 18px rgba(111, 66, 193, 0.35);
        transition: transform 0.15s ease, box-shadow 0.15s ease, filter 0.15s ease;
    }
    .btn-pspk-selesai:hover,
    .btn-pspk-selesai:focus {
        color: #fff !important;
        filter: brightness(1.05);
        box-shadow: 0 10px 22px rgba(161, 140, 209, 0.4);
        transform: translateY(-1px);
    }
    .btn-pspk-selesai:active {
        transform: translateY(0);
        box-shadow: 0 4px 12px rgba(111, 66, 193, 0.3);
    }
    .btn-pspk-selesai svg,
    .btn-pspk-selesai i {
        width: 18px;
        height: 18px;
        stroke-width: 2.25;
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

@php
    $jawabanSaatIni = (string) ($jawaban[$nomor_sekarang - 1] ?? '');
    $selectedAwal = ($jawabanSaatIni !== '' && $jawabanSaatIni !== '0') ? $jawabanSaatIni : '';
    $navStart = $isLevel34 ? ($jmlAnkas + 1) : 1;
    $navEnd = $isLevel34 ? $totalSoalAll : $jml_soal;
@endphp

<div>
    @if($isLevel34 && $isAnkasPhase)
    <div
        wire:ignore
        x-data="tesPspkUjian({
            mode: 'ankas',
            nomor: {{ (int) $nomor_sekarang }},
            jml: {{ (int) $jmlAnkas }},
            jmlAnkas: {{ (int) $jmlAnkas }},
            phaseNomor: {{ (int) $phaseNomor }},
            phaseJml: {{ (int) $jml_soal }},
            jawabanKosong: {{ (int) ($jawaban_kosong ?? 0) }},
            allAnkasAnswered: @js($allAnkasAnswered),
            jawabanUser: @js(array_values($jawaban)),
            selected: @js($selectedAwal),
            teks: @js($soal->soal),
            opsiA: @js($soal->opsi_a),
            opsiB: @js($soal->opsi_b),
            opsiC: @js($soal->opsi_c),
            opsiD: @js($soal->opsi_d),
            opsiE: @js($soal->opsi_e),
            flagged: {}
        })"
        x-init="
            loadFlags();
            $nextTick(() => {
                initAnkasTooltips();
                if (typeof feather !== 'undefined') feather.replace();
            });
        "
        class="ankas-fs"
    >
        <div class="ankas-fs-topbar">
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-pspk-subtle text-pspk px-2 py-1 phase-badge">
                    <span><i data-feather="layers" style="width:13px;height:13px" class="me-1"></i></span>
                    Tahap 1: Ankas
                </span>
                <span class="badge text-white px-2 py-1" style="background:#6f42c1;font-size:0.78rem;">
                    Soal <span x-text="phaseNomor"></span> / <span x-text="phaseJml"></span>
                </span>
            </div>

            <div class="ankas-fs-timer-pill">
                <span><i data-feather="clock" style="width:15px;height:15px"></i></span>
                <strong class="time timer-badge"></strong>
            </div>

            <div class="d-flex align-items-center gap-2">
                <span class="ankas-fs-stat bg-success bg-opacity-10 text-success" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Sudah dijawab">
                    <span><i data-feather="check" style="width:12px;height:12px"></i></span>
                    <span x-text="phaseJml - jawabanKosong"></span>
                </span>
                <span class="ankas-fs-stat bg-danger bg-opacity-10 text-danger" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Belum dijawab">
                    <span><i data-feather="x" style="width:12px;height:12px"></i></span>
                    <span x-text="jawabanKosong"></span>
                </span>
                <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-2 py-1"
                    @click="showNav = !showNav"
                    :class="{ 'btn-pspk text-white border-0': showNav }"
                    data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Navigasi Soal">
                    <span><i data-feather="grid" style="width:15px;height:15px"></i></span>
                </button>
                <button type="button" class="btn btn-sm btn-pspk rounded-pill px-3"
                    @click="$wire.lanjutKeSjt()"
                    :disabled="!allAnkasAnswered">
                    Lanjut SJT
                    <span><i data-feather="arrow-right" style="width:14px;height:14px" class="ms-1"></i></span>
                </button>
            </div>
        </div>

        <div class="ankas-fs-content">
            <div class="ankas-fs-pdf">
                <div class="ankas-fs-pdf-header">
                    <span><i data-feather="file-text" style="width:15px;height:15px" class="text-pspk me-2"></i></span>
                    <small class="fw-semibold text-muted">Lampiran PDF Analisa Kasus</small>
                </div>
                <div style="flex:1;display:flex;flex-direction:column;">
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

            <div class="ankas-fs-soal">
                <div class="ankas-fs-soal-head">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge text-white px-3 py-2" style="font-size:0.9rem;background:#6f42c1;">
                                Soal <span x-text="phaseNomor"></span>
                            </span>
                            <span class="badge bg-warning text-dark px-2 py-1" x-show="isFlagged(nomor)" x-cloak>🔖 Ditandai</span>
                        </div>
                        <small class="text-muted"><span x-text="phaseNomor"></span> dari <span x-text="phaseJml"></span></small>
                    </div>
                </div>

                <div class="ankas-fs-soal-body">
                    <div class="mb-4">
                        <p class="mb-0 lh-base" style="font-size:1.05rem;" x-text="teks"></p>
                    </div>

                    <div class="d-flex flex-column gap-2">
                        <template x-for="opsi in opsiList" :key="'ankas-' + nomor + '-' + opsi.value">
                            <label class="ankas-fs-option" :class="{ 'selected': selected === opsi.value }">
                                <input class="form-check-input me-3" type="radio"
                                    :name="'jawaban-ankas-' + nomor"
                                    :value="opsi.value"
                                    x-model="selected">
                                <span><strong class="me-2" x-text="opsi.value + '.'"></strong> <span x-text="opsi.label"></span></span>
                            </label>
                        </template>
                    </div>
                </div>

                <div class="ankas-fs-nav-panel" x-show="showNav" x-collapse>
                    <div class="d-flex flex-wrap gap-1 mb-2">
                        <template x-for="n in nomorList" :key="'ankas-nav-' + n">
                            <button type="button"
                                class="btn ankas-fs-nav-btn btn-sm"
                                :class="navButtonClass(n)"
                                :style="n === nomor ? 'box-shadow: 0 0 0 3px rgba(111,66,193,0.5);' : ''"
                                @click="navigate(n)"
                                :disabled="saving"
                            >
                                <span x-text="n"></span>
                                <span class="flag-icon" style="font-size:11px;top:-4px;right:-4px;" x-show="isFlagged(n)" x-cloak>🔖</span>
                            </button>
                        </template>
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

                <div class="ankas-fs-actions">
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-outline-secondary btn-sm px-3"
                            @click="navigate(nomor - 1)"
                            :disabled="nomor === 1 || saving">
                            <span><i data-feather="chevron-left" style="width:16px;height:16px"></i></span>
                            <span class="d-none d-xl-inline ms-1">Sebelumnya</span>
                        </button>
                        <button type="button" class="btn btn-pspk btn-sm flex-fill"
                            @click="saveAndNext()"
                            :disabled="!isAnswered(selected) || saving">
                            <span x-show="!saving">Simpan & Lanjut</span>
                            <span x-show="saving" x-cloak>Menyimpan...</span>
                            <span><i data-feather="chevron-right" style="width:16px;height:16px" class="ms-1"></i></span>
                        </button>
                        <button type="button" class="btn btn-sm px-3"
                            :class="isFlagged(nomor) ? 'btn-warning' : 'btn-outline-warning'"
                            @click.stop="toggleFlag()"
                            data-bs-toggle="tooltip" data-bs-placement="bottom" :data-bs-title="isFlagged(nomor) ? 'Batalkan Tanda' : 'Tandai Soal'">
                            🔖
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @else
    <div
        wire:ignore
        x-data="tesPspkUjian({
            mode: 'std',
            nomor: {{ (int) $nomor_sekarang }},
            jml: {{ (int) $jml_soal }},
            jmlAnkas: {{ (int) $jmlAnkas }},
            totalSoalAll: {{ (int) $totalSoalAll }},
            isLevel34: @js($isLevel34),
            navStart: {{ (int) $navStart }},
            navEnd: {{ (int) $navEnd }},
            phaseNomor: {{ (int) $phaseNomor }},
            jawabanKosong: {{ (int) ($jawaban_kosong ?? 0) }},
            jawabanUser: @js(array_values($jawaban)),
            selected: @js($selectedAwal),
            teks: @js($soal->soal),
            opsiA: @js($soal->opsi_a),
            opsiB: @js($soal->opsi_b),
            opsiC: @js($soal->opsi_c),
            opsiD: @js($soal->opsi_d),
            opsiE: @js($soal->opsi_e),
            flagged: {}
        })"
        x-init="
            loadFlags();
            $nextTick(() => { if (typeof feather !== 'undefined') feather.replace(); });
        "
    >
    <!-- Header Card -->
    <div class="card border-0 shadow-sm mb-4" style="background: linear-gradient(135deg, #a18cd1 0%, #fbc2eb 100%);">
        <div class="card-body p-4 text-white">
            <div class="d-flex flex-column flex-md-row align-items-center justify-content-between">
                <div class="d-flex align-items-center mb-3 mb-md-0">
                    <div class="rounded-circle bg-white bg-opacity-25 p-2 me-3">
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
                <span class="badge phase-badge bg-info bg-opacity-10 text-info px-3 py-2">
                    Tahap 2: Situational Judgment Test (SJT)
                </span>
            </div>
            @endif
            <div class="row align-items-center g-3">
                <div class="col-6 col-md-3">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-success bg-opacity-10 p-2 me-2">
                            <i class="text-success" data-feather="check-circle" style="width: 20px; height: 20px;"></i>
                        </div>
                        <div>
                            <small class="text-muted d-block">Dijawab</small>
                            <strong class="text-success" x-text="jml - jawabanKosong"></strong>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-danger bg-opacity-10 p-2 me-2">
                            <i class="text-danger" data-feather="x-circle" style="width: 20px; height: 20px;"></i>
                        </div>
                        <div>
                            <small class="text-muted d-block">Belum Dijawab</small>
                            <strong class="text-danger" x-text="jawabanKosong"></strong>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-pspk-subtle p-2 me-2">
                            <i class="text-pspk" data-feather="clock" style="width: 20px; height: 20px;"></i>
                        </div>
                        <div>
                            <small class="text-muted d-block">Sisa Waktu</small>
                            <strong class="timer-badge text-pspk time"></strong>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3 text-md-end">
                    <button type="button" class="btn btn-pspk-selesai"
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
                        <i data-feather="check-circle"></i>
                        <span>Selesai</span>
                    </button>
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
                        Soal <span x-text="isLevel34 ? phaseNomor : nomor"></span>
                    </span>
                    <span class="badge bg-warning text-dark" x-show="isFlagged(nomor)" x-cloak>🔖 Ditandai</span>
                </div>
                <small class="text-muted">
                    <span x-text="isLevel34 ? phaseNomor : nomor"></span> dari <span x-text="jml"></span> soal
                </small>
            </div>
        </div>
        <div class="card-body p-4">
            <div class="mb-4">
                <p class="fs-5 mb-0" x-text="teks"></p>
            </div>

            <div class="row g-3 mb-4">
                <template x-for="opsi in opsiList" :key="'std-' + nomor + '-' + opsi.value">
                    <div class="col-12" x-show="opsi.show">
                        <label class="option-card d-flex align-items-center w-100" :class="{ 'selected': selected === opsi.value }">
                            <input class="form-check-input me-3" type="radio"
                                :name="'jawaban-std-' + nomor"
                                :value="opsi.value"
                                x-model="selected">
                            <span><strong class="me-2" x-text="opsi.value + '.'"></strong> <span x-text="opsi.label"></span></span>
                        </label>
                    </div>
                </template>
            </div>

            <div class="d-flex flex-wrap gap-2">
                <button type="button" class="btn btn-outline-secondary"
                    @click="navigate(nomor - 1)"
                    :disabled="nomor === prevMin || saving">
                    <span><i data-feather="chevron-left" style="width: 18px; height: 18px;"></i></span>
                    Sebelumnya
                </button>
                <button type="button" class="btn btn-pspk" @click="saveAndNext()"
                    :disabled="!isAnswered(selected) || saving">
                    <span x-show="!saving">Simpan & Lanjutkan</span>
                    <span x-show="saving" x-cloak>Menyimpan...</span>
                    <span><i data-feather="chevron-right" style="width: 18px; height: 18px;"></i></span>
                </button>
                <button type="button" class="btn"
                    :class="isFlagged(nomor) ? 'btn-warning' : 'btn-outline-warning'"
                    @click.stop="toggleFlag()">
                    <span x-text="isFlagged(nomor) ? '🔖 Batalkan Tanda' : '🔖 Tandai Soal'"></span>
                </button>
            </div>
        </div>
    </div>

    <!-- Navigation Grid -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0 py-3">
            <h6 class="mb-0">
                <span><i data-feather="grid" style="width: 18px; height: 18px;" class="me-2"></i></span>
                Navigasi Soal<span x-show="isLevel34"> SJT</span>
            </h6>
        </div>
        <div class="card-body p-4">
            <div class="d-flex flex-wrap gap-2">
                <template x-for="n in navList" :key="'std-nav-' + n">
                    <button type="button"
                        class="btn nav-btn btn-sm"
                        :class="navButtonClass(n)"
                        :style="n === nomor ? 'box-shadow: 0 0 0 3px rgba(111, 66, 193, 0.5);' : ''"
                        @click="navigate(n)"
                        :disabled="saving"
                    >
                        <span x-text="navDisplayNumber(n)"></span>
                        <span class="flag-icon" x-show="isFlagged(n)" x-cloak>🔖</span>
                    </button>
                </template>
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

    window.tesPspkUjian = function (initial) {
        return {
            mode: initial.mode || 'std',
            nomor: initial.nomor,
            jml: initial.jml,
            jmlAnkas: initial.jmlAnkas || 0,
            totalSoalAll: initial.totalSoalAll || initial.jml,
            isLevel34: !!initial.isLevel34,
            navStart: initial.navStart || 1,
            navEnd: initial.navEnd || initial.jml,
            phaseNomor: initial.phaseNomor || initial.nomor,
            phaseJml: initial.phaseJml || initial.jml,
            jawabanKosong: initial.jawabanKosong,
            allAnkasAnswered: !!initial.allAnkasAnswered,
            jawabanUser: initial.jawabanUser || [],
            selected: initial.selected || '',
            teks: initial.teks || '',
            opsiA: initial.opsiA || '',
            opsiB: initial.opsiB || '',
            opsiC: initial.opsiC || '',
            opsiD: initial.opsiD || '',
            opsiE: initial.opsiE || '',
            flagged: initial.flagged || {},
            saving: false,
            showNav: false,

            get nomorList() {
                const count = this.mode === 'ankas' ? this.jmlAnkas : this.jml;
                return Array.from({ length: count }, (_, i) => i + 1);
            },

            get navList() {
                const start = this.navStart || 1;
                const end = this.navEnd || this.jml;
                const list = [];
                for (let i = start; i <= end; i++) list.push(i);
                return list;
            },

            get prevMin() {
                return this.isLevel34 ? (this.jmlAnkas + 1) : 1;
            },

            get opsiList() {
                return [
                    { value: 'A', label: this.opsiA, show: true },
                    { value: 'B', label: this.opsiB, show: true },
                    { value: 'C', label: this.opsiC, show: true },
                    { value: 'D', label: this.opsiD, show: true },
                    { value: 'E', label: this.opsiE, show: !!this.opsiE },
                ];
            },

            isAnswered(value) {
                return ['A', 'B', 'C', 'D', 'E'].includes(String(value || ''));
            },

            isFlagged(n) {
                const key = String(n);
                return !!(this.flagged[key] || this.flagged[n]);
            },

            navButtonClass(n) {
                if (this.isFlagged(n)) return 'flagged-btn';
                return this.isAnswered(this.jawabanUser[n - 1]) ? 'btn-success' : 'btn-outline-danger';
            },

            navDisplayNumber(n) {
                return this.isLevel34 ? (n - this.jmlAnkas) : n;
            },

            loadFlags() {
                try {
                    const raw = JSON.parse(localStorage.getItem('flags_soal') || '{}') || {};
                    const normalized = {};
                    Object.keys(raw).forEach((key) => {
                        if (raw[key]) normalized[String(key)] = true;
                    });
                    this.flagged = normalized;
                } catch (e) {
                    this.flagged = {};
                }
            },

            persistFlags() {
                localStorage.setItem('flags_soal', JSON.stringify(this.flagged));
            },

            applyPayload(payload) {
                if (!payload) return;

                this.nomor = payload.nomor;
                this.phaseNomor = payload.phase_nomor ?? this.nomor;
                this.jawabanKosong = payload.jawaban_kosong ?? this.jawabanKosong;
                this.allAnkasAnswered = !!payload.all_ankas_answered;

                if (this.mode === 'ankas') {
                    this.phaseJml = payload.phase_jml_soal ?? this.phaseJml;
                } else {
                    this.jml = payload.phase_jml_soal ?? this.jml;
                }

                this.teks = payload.teks || '';
                this.opsiA = payload.opsi_a || '';
                this.opsiB = payload.opsi_b || '';
                this.opsiC = payload.opsi_c || '';
                this.opsiD = payload.opsi_d || '';
                this.opsiE = payload.opsi_e || '';
                this.selected = this.isAnswered(payload.selected) ? payload.selected : '';
                this.jawabanUser = Array.isArray(payload.jawaban_user)
                    ? payload.jawaban_user.slice()
                    : this.jawabanUser;

                if (payload.url) {
                    history.replaceState({}, '', payload.url);
                }

                this.$nextTick(() => {
                    if (typeof feather !== 'undefined') feather.replace();
                    if (this.mode === 'ankas') initAnkasTooltips();
                });
            },

            showAnkasTerakhirPrompt(payload) {
                if (typeof Swal === 'undefined') return;

                const kosong = payload?.jawaban_kosong ?? this.jawabanKosong;
                const targetTinjau = payload?.soal_belum_dijawab || 1;

                if (payload?.semua_terjawab) {
                    Swal.fire({
                        title: 'Semua soal Ankas telah dijawab',
                        text: 'Anda berada di soal terakhir tahap Ankas. Anda dapat melanjutkan ke bagian SJT atau meninjau kembali soal-soal sebelumnya.',
                        icon: 'info',
                        showCancelButton: true,
                        showDenyButton: true,
                        confirmButtonText: 'Lanjut ke SJT',
                        denyButtonText: 'Tinjau Soal',
                        cancelButtonText: 'Batal',
                        confirmButtonColor: '#059669',
                        denyButtonColor: '#2563eb',
                        customClass: {
                            denyButton: 'ujian-semua-terjawab-deny-btn',
                            confirmButton: 'ujian-semua-terjawab-confirm-btn',
                        },
                    }).then((result) => {
                        if (result.isConfirmed) {
                            this.$wire.lanjutKeSjt();
                        } else if (result.isDenied) {
                            this.navigate(1);
                        }
                    });
                    return;
                }

                Swal.fire({
                    title: 'Masih ada soal belum dijawab',
                    text: `Anda berada di soal terakhir tahap Ankas, tetapi masih ada ${kosong} soal yang belum dijawab. Selesaikan semua soal Ankas sebelum melanjutkan ke bagian SJT.`,
                    icon: 'warning',
                    showCancelButton: true,
                    showDenyButton: true,
                    showConfirmButton: false,
                    denyButtonText: 'Tinjau Soal',
                    cancelButtonText: 'Batal',
                    denyButtonColor: '#2563eb',
                    customClass: {
                        denyButton: 'ujian-semua-terjawab-deny-btn',
                    },
                }).then((result) => {
                    if (result.isDenied) {
                        this.navigate(targetTinjau);
                    }
                });
            },

            showSemuaTerjawabPrompt() {
                if (typeof Swal === 'undefined') return;

                Swal.fire({
                    title: 'Semua soal telah dijawab',
                    text: 'Anda berada di soal terakhir. Anda dapat mengakhiri ujian atau meninjau kembali soal-soal sebelumnya.',
                    icon: 'info',
                    showCancelButton: true,
                    showDenyButton: true,
                    confirmButtonText: 'Selesai Ujian',
                    denyButtonText: 'Tinjau Soal',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#059669',
                    denyButtonColor: '#2563eb',
                    customClass: {
                        denyButton: 'ujian-semua-terjawab-deny-btn',
                        confirmButton: 'ujian-semua-terjawab-confirm-btn',
                    },
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.$wire.finish();
                    } else if (result.isDenied) {
                        this.navigate(this.prevMin);
                    }
                });
            },

            showBelumSelesaiPrompt(payload) {
                if (typeof Swal === 'undefined') return;

                const kosong = payload?.jawaban_kosong ?? this.jawabanKosong;
                const targetTinjau = payload?.soal_belum_dijawab || this.prevMin;

                Swal.fire({
                    title: 'Masih ada soal belum dijawab',
                    text: `Anda berada di soal terakhir, tetapi masih ada ${kosong} soal yang belum dijawab. Soal yang belum dijawab akan dikosongkan jika Anda mengakhiri ujian.`,
                    icon: 'warning',
                    showCancelButton: true,
                    showDenyButton: true,
                    confirmButtonText: 'Selesai Ujian',
                    denyButtonText: 'Tinjau Soal',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#059669',
                    denyButtonColor: '#2563eb',
                    customClass: {
                        denyButton: 'ujian-semua-terjawab-deny-btn',
                        confirmButton: 'ujian-semua-terjawab-confirm-btn',
                    },
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.$wire.finish();
                    } else if (result.isDenied) {
                        this.navigate(targetTinjau);
                    }
                });
            },

            showSoalTerakhirPrompt(payload) {
                if (payload?.semua_terjawab) {
                    this.showSemuaTerjawabPrompt();
                    return;
                }
                this.showBelumSelesaiPrompt(payload);
            },

            toggleFlag() {
                const key = String(this.nomor);
                const currentSelected = this.selected;
                const next = { ...this.flagged };

                if (next[key]) {
                    delete next[key];
                } else {
                    next[key] = true;
                }

                this.flagged = next;
                this.persistFlags();

                this.$nextTick(() => {
                    this.selected = currentSelected;
                });
            },

            async saveAndNext() {
                if (!this.isAnswered(this.selected) || this.saving) return;

                this.saving = true;
                try {
                    const payload = await this.$wire.saveAndNext(this.nomor, this.selected);
                    this.applyPayload(payload);

                    if (payload?.prompt_ankas_terakhir) {
                        this.showAnkasTerakhirPrompt(payload);
                    } else if (payload?.prompt_soal_terakhir) {
                        this.showSoalTerakhirPrompt(payload);
                    }
                } finally {
                    this.saving = false;
                }
            },

            async navigate(target) {
                if (this.saving) return;

                const min = this.mode === 'ankas' ? 1 : this.prevMin;
                const max = this.mode === 'ankas' ? this.jmlAnkas : this.navEnd;

                if (target < min || target > max || target === this.nomor) return;

                this.saving = true;
                try {
                    const payload = await this.$wire.navigate(target);
                    this.applyPayload(payload);
                } finally {
                    this.saving = false;
                }
            },
        };
    };

    document.addEventListener('livewire:init', () => {
        Livewire.on('clear-flags-browser', () => {
            localStorage.removeItem('flags_soal');
        });
    });
</script>
<script>
    (function () {
        if (window.__pspkTimerStarted) {
            return;
        }
        window.__pspkTimerStarted = true;

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
    })();
</script>
@endpush
