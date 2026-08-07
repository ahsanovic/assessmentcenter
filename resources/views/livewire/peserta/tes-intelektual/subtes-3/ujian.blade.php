@push('css')
<style>
    .form-check-input[type="radio"] {
        border: 2px solid #dee2e6;
        width: 1.25em;
        height: 1.25em;
        cursor: pointer;
    }
    .form-check-input[type="radio"]:checked {
        background-color: #dc3545;
        border-color: #dc3545;
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
        transition: border-color 0.15s ease, background-color 0.15s ease;
        cursor: pointer;
    }
    .option-card:hover {
        border-color: #dc3545;
        background-color: rgba(220, 53, 69, 0.05);
    }
    .option-card.selected {
        border-color: #dc3545;
        background-color: rgba(220, 53, 69, 0.1);
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
    .option-image-card {
        padding: 0.75rem;
        border-radius: 0.5rem;
        border: 2px solid #e9ecef;
        transition: all 0.2s ease;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .option-image-card:hover {
        border-color: #dc3545;
    }
    .option-image-card.selected {
        border-color: #dc3545;
        background-color: rgba(220, 53, 69, 0.1);
    }
    .btn-ujian-selesai {
        --ujian-from: #667eea;
        --ujian-to: #764ba2;
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
        background: linear-gradient(135deg, var(--ujian-from) 0%, var(--ujian-to) 100%);
        box-shadow: 0 8px 18px rgba(102, 126, 234, 0.35);
        transition: transform 0.15s ease, box-shadow 0.15s ease, filter 0.15s ease;
    }
    .btn-ujian-selesai:hover,
    .btn-ujian-selesai:focus {
        color: #fff !important;
        filter: brightness(1.05);
        box-shadow: 0 10px 22px rgba(118, 75, 162, 0.4);
        transform: translateY(-1px);
    }
    .btn-ujian-selesai:active {
        transform: translateY(0);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
    }
    .btn-ujian-selesai svg,
    .btn-ujian-selesai i {
        width: 18px;
        height: 18px;
        stroke-width: 2.25;
    }
    .swal2-popup button.swal2-deny.ujian-semua-terjawab-deny-btn {
        color: #fff !important;
    }
    .swal2-popup button.swal2-confirm.ujian-semua-terjawab-confirm-btn {
        color: #fff !important;
    }
</style>
@endpush

@php
    $jawabanSaatIni = (string) ($jawaban_user[$nomor_sekarang - 1] ?? '');
    $selectedAwal = ($jawabanSaatIni !== '' && $jawabanSaatIni !== '0') ? $jawabanSaatIni : '';
    $hasImageOptionsAwal = collect([
        $soal->image_opsi_a, $soal->image_opsi_b, $soal->image_opsi_c, $soal->image_opsi_d, $soal->image_opsi_e,
    ])->contains(fn ($path) => $path && preg_match('/\.(jpg|jpeg|png)$/i', $path));
@endphp

<div
    wire:ignore
    x-data="tesIntelektualSubtes3Ujian({
        nomor: {{ (int) $nomor_sekarang }},
        jml: {{ (int) $jml_soal }},
        jawabanKosong: {{ (int) ($jawaban_kosong ?? 0) }},
        jawabanUser: @js(array_values($jawaban_user)),
        selected: @js($selectedAwal),
        teks: @js($soal->soal),
        imageSoal: @js($soal->image_soal),
        opsiA: @js($soal->opsi_a),
        opsiB: @js($soal->opsi_b),
        opsiC: @js($soal->opsi_c),
        opsiD: @js($soal->opsi_d),
        opsiE: @js($soal->opsi_e),
        imageOpsiA: @js($soal->image_opsi_a),
        imageOpsiB: @js($soal->image_opsi_b),
        imageOpsiC: @js($soal->image_opsi_c),
        imageOpsiD: @js($soal->image_opsi_d),
        imageOpsiE: @js($soal->image_opsi_e),
        hasImageOptions: @js($hasImageOptionsAwal),
        storagePrefix: @js(rtrim(asset('storage'), '/')),
        flagged: {}
    })"
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

        loadFlags();
        $nextTick(() => { if (typeof feather !== 'undefined') feather.replace(); });
    "
>
    <div class="card border-0 shadow-sm mb-4" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
        <div class="card-body p-4 text-white">
            <div class="d-flex flex-column flex-md-row align-items-center justify-content-between">
                <div class="d-flex align-items-center mb-3 mb-md-0">
                    <div class="rounded-circle bg-white bg-opacity-25 p-2 me-3">
                        <i data-feather="box" style="width: 28px; height: 28px;"></i>
                    </div>
                    <div>
                        <h4 class="mb-0">Tes Intelektual - Sub Tes 3</h4>
                        <small class="opacity-75">Analisis rotasi kubus</small>
                    </div>
                </div>
                <span class="badge bg-white bg-opacity-25 px-3 py-2">Sub Tes 3 dari 3</span>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4 status-bar">
        <div class="card-body py-3">
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
                        <div class="rounded-circle bg-danger bg-opacity-10 p-2 me-2">
                            <i class="text-danger" data-feather="clock" style="width: 20px; height: 20px;"></i>
                        </div>
                        <div>
                            <small class="text-muted d-block">Sisa Waktu</small>
                            <strong class="timer-badge text-danger time"></strong>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3 text-md-end">
                    <button type="button" class="btn btn-ujian-selesai"
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

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-0 py-3">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <span class="badge bg-danger text-white me-3 px-3 py-2" style="font-size: 1rem;">
                        Soal <span x-text="nomor"></span>
                    </span>
                    <span class="badge bg-warning text-dark" x-show="isFlagged(nomor)" x-cloak>🔖 Ditandai</span>
                </div>
                <small class="text-muted"><span x-text="nomor"></span> dari <span x-text="jml"></span> soal</small>
            </div>
        </div>
        <div class="card-body p-4">
            <div class="mb-4">
                <div class="fs-5 mb-3" x-html="teks"></div>
                <div class="text-center" x-show="isImagePath(imageSoal)" x-cloak>
                    <img :src="storageUrl(imageSoal)" alt="Soal" class="img-fluid rounded shadow-sm" style="max-width:500px; height:auto;">
                </div>
            </div>

            <template x-if="hasImageOptions">
                <div class="d-flex flex-wrap gap-3 mb-4">
                    <template x-for="opsi in opsiList" :key="opsi.value">
                        <label class="option-image-card" :class="{ 'selected': selected === opsi.value }">
                            <input class="form-check-input" type="radio"
                                :name="'jawaban-soal-' + nomor"
                                :value="opsi.value"
                                x-model="selected">
                            <span class="fw-bold" x-text="opsi.value + '.'"></span>
                            <template x-if="isImagePath(opsi.image)">
                                <img :src="storageUrl(opsi.image)" :alt="'Opsi ' + opsi.value" class="rounded" style="max-width:150px; height:auto;">
                            </template>
                            <template x-if="!isImagePath(opsi.image)">
                                <span x-text="opsi.label"></span>
                            </template>
                        </label>
                    </template>
                </div>
            </template>
            <template x-if="!hasImageOptions">
                <div class="row g-3 mb-4">
                    <template x-for="opsi in opsiList" :key="opsi.value">
                        <div class="col-12">
                            <label class="option-card d-flex align-items-center w-100" :class="{ 'selected': selected === opsi.value }">
                                <input class="form-check-input me-3" type="radio"
                                    :name="'jawaban-soal-' + nomor"
                                    :value="opsi.value"
                                    x-model="selected">
                                <span><strong class="me-2" x-text="opsi.value + '.'"></strong> <span x-text="opsi.label"></span></span>
                            </label>
                        </div>
                    </template>
                </div>
            </template>

            <div class="d-flex flex-wrap gap-2">
                <button type="button" class="btn btn-outline-secondary" @click="navigate(nomor - 1)"
                    :disabled="nomor === 1 || saving">
                    <span><i data-feather="chevron-left" style="width: 18px; height: 18px;"></i></span>
                    Sebelumnya
                </button>
                <button type="button" class="btn btn-danger" @click="saveAndNext()"
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

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0 py-3">
            <h6 class="mb-0">
                <span><i data-feather="grid" style="width: 18px; height: 18px;" class="me-2"></i></span>
                Navigasi Soal
            </h6>
        </div>
        <div class="card-body p-4">
            <div class="d-flex flex-wrap gap-2">
                <template x-for="n in nomorList" :key="n">
                    <button type="button"
                        class="btn nav-btn btn-sm"
                        :class="navButtonClass(n)"
                        :style="n === nomor ? 'box-shadow: 0 0 0 3px rgba(220, 53, 69, 0.5);' : ''"
                        @click="navigate(n)"
                        :disabled="saving"
                    >
                        <span x-text="n"></span>
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

@push('js')
<script>
    window.tesIntelektualSubtes3Ujian = function (initial) {
        const base = {
            nomor: initial.nomor,
            jml: initial.jml,
            jawabanKosong: initial.jawabanKosong,
            jawabanUser: initial.jawabanUser || [],
            selected: initial.selected || '',
            teks: initial.teks || '',
            imageSoal: initial.imageSoal || '',
            opsiA: initial.opsiA || '',
            opsiB: initial.opsiB || '',
            opsiC: initial.opsiC || '',
            opsiD: initial.opsiD || '',
            opsiE: initial.opsiE || '',
            imageOpsiA: initial.imageOpsiA || '',
            imageOpsiB: initial.imageOpsiB || '',
            imageOpsiC: initial.imageOpsiC || '',
            imageOpsiD: initial.imageOpsiD || '',
            imageOpsiE: initial.imageOpsiE || '',
            hasImageOptions: !!initial.hasImageOptions,
            storagePrefix: initial.storagePrefix || '',
            flagged: initial.flagged || {},
            saving: false,

            get nomorList() {
                return Array.from({ length: this.jml }, (_, i) => i + 1);
            },

            get opsiList() {
                return [
                    { value: 'A', label: this.opsiA, image: this.imageOpsiA },
                    { value: 'B', label: this.opsiB, image: this.imageOpsiB },
                    { value: 'C', label: this.opsiC, image: this.imageOpsiC },
                    { value: 'D', label: this.opsiD, image: this.imageOpsiD },
                    { value: 'E', label: this.opsiE, image: this.imageOpsiE },
                ];
            },

            isImagePath(path) {
                return !!(path && /\.(jpg|jpeg|png)$/i.test(path));
            },

            storageUrl(path) {
                if (!path) return '';
                const clean = String(path).replace(/^\//, '');
                return this.storagePrefix + '/' + clean;
            },

            isAnswered(value) {
                return ['A', 'B', 'C', 'D', 'E'].includes(String(value || ''));
            },

            isFlagged(n) {
                const key = String(n);
                return !!(this.flagged[key] || this.flagged[n]);
            },

            navButtonClass(n) {
                if (this.isFlagged(n)) {
                    return 'flagged-btn';
                }
                return this.isAnswered(this.jawabanUser[n - 1]) ? 'btn-success' : 'btn-outline-danger';
            },

            loadFlags() {
                try {
                    const raw = JSON.parse(localStorage.getItem('flags_soal') || '{}') || {};
                    const normalized = {};
                    Object.keys(raw).forEach((key) => {
                        if (raw[key]) {
                            normalized[String(key)] = true;
                        }
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
                this.teks = payload.teks || '';
                this.imageSoal = payload.image_soal || '';
                this.opsiA = payload.opsi_a || '';
                this.opsiB = payload.opsi_b || '';
                this.opsiC = payload.opsi_c || '';
                this.opsiD = payload.opsi_d || '';
                this.opsiE = payload.opsi_e || '';
                this.imageOpsiA = payload.image_opsi_a || '';
                this.imageOpsiB = payload.image_opsi_b || '';
                this.imageOpsiC = payload.image_opsi_c || '';
                this.imageOpsiD = payload.image_opsi_d || '';
                this.imageOpsiE = payload.image_opsi_e || '';
                this.hasImageOptions = !!payload.has_image_options;
                this.selected = this.isAnswered(payload.selected) ? payload.selected : '';
                this.jawabanUser = Array.isArray(payload.jawaban_user)
                    ? payload.jawaban_user.slice()
                    : this.jawabanUser;
                this.jawabanKosong = payload.jawaban_kosong ?? this.jawabanKosong;

                if (payload.url) {
                    history.replaceState({}, '', payload.url);
                }

                this.$nextTick(() => {
                    if (typeof feather !== 'undefined') feather.replace();
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
                    denyButtonText: 'Lanjut Ujian',
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
                        this.navigate(1);
                    }
                });
            },

            showBelumSelesaiPrompt(payload) {
                if (typeof Swal === 'undefined') return;

                const kosong = payload?.jawaban_kosong ?? this.jawabanKosong;
                const targetTinjau = payload?.soal_belum_dijawab || 1;

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

                    if (payload?.prompt_soal_terakhir) {
                        this.showSoalTerakhirPrompt(payload);
                    }
                } finally {
                    this.saving = false;
                }
            },

            async navigate(target) {
                if (this.saving) return;
                if (target < 1 || target > this.jml || target === this.nomor) return;

                this.saving = true;
                try {
                    const payload = await this.$wire.navigate(target);
                    this.applyPayload(payload);
                } finally {
                    this.saving = false;
                }
            },
        };

        return base;
    };

    document.addEventListener('livewire:init', () => {
        Livewire.on('clear-flags-browser', () => {
            localStorage.removeItem('flags_soal');
        });
    });
</script>
<script>
    (function () {
        if (window.__intelektualSubtes3TimerStarted) {
            return;
        }
        window.__intelektualSubtes3TimerStarted = true;

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
