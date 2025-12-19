@push('css')
<style>
    .form-check-input[type="radio"] {
        border: 2px solid #dee2e6;
        width: 1.25em;
        height: 1.25em;
        cursor: pointer;
    }
    .form-check-input[type="radio"]:checked {
        background-color: #0dcaf0;
        border-color: #0dcaf0;
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
        border-color: #0dcaf0;
        background-color: rgba(13, 202, 240, 0.05);
    }
    .option-card.selected {
        border-color: #0dcaf0;
        background-color: rgba(13, 202, 240, 0.1);
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
</style>
@endpush

<div x-data
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
">
    <!-- Header Card -->
    <div class="card border-0 shadow-sm mb-4" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
        <div class="card-body p-4 text-white">
            <div class="d-flex flex-column flex-md-row align-items-center justify-content-between">
                <div class="d-flex align-items-center mb-3 mb-md-0">
                    <div class="rounded-circle bg-white bg-opacity-25 p-2 me-3" wire:ignore>
                        <i data-feather="monitor" style="width: 28px; height: 28px;"></i>
                    </div>
                    <div>
                        <h4 class="mb-0">Tes Literasi Digital dan Emerging Skill</h4>
                        <small class="opacity-75">Jawab semua pertanyaan dengan teliti</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Bar -->
    <div class="card border-0 shadow-sm mb-4 status-bar">
        <div class="card-body py-3">
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
                        <div class="rounded-circle bg-info bg-opacity-10 p-2 me-2" wire:ignore>
                            <i class="text-info" data-feather="clock" style="width: 20px; height: 20px;"></i>
                        </div>
                        <div>
                            <small class="text-muted d-block">Sisa Waktu</small>
                            <strong class="timer-badge text-info time"></strong>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3 text-md-end">
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
                </div>
            </div>
        </div>
    </div>

    <!-- Question Card -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-0 py-3">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <span class="badge bg-info text-white me-3 px-3 py-2" style="font-size: 1rem;">
                        Soal {{ $nomor_sekarang }}
                    </span>
                    @if(isset($flagged[$nomor_sekarang]))
                        <span class="badge bg-warning text-dark">🔖 Ditandai</span>
                    @endif
                </div>
                <small class="text-muted">{{ $nomor_sekarang }} dari {{ $jml_soal }} soal</small>
            </div>
        </div>
        <div class="card-body p-4">
            <!-- Question Text -->
            <div class="mb-4">
                <p class="fs-5 mb-0">{{ $soal->soal }}</p>
            </div>

            <!-- Options -->
            <div class="row g-3 mb-4">
                <div class="col-12">
                    <label class="option-card d-flex align-items-center w-100 {{ ($jawaban_user[$nomor_sekarang - 1] ?? '') == 'A' ? 'selected' : '' }}">
                        <input class="form-check-input me-3" type="radio"
                            wire:model="jawaban_user.{{ $nomor_sekarang - 1 }}" value="A" id="opsi1">
                        <span><strong class="me-2">A.</strong> {{ $soal->opsi_a }}</span>
                    </label>
                </div>
                <div class="col-12">
                    <label class="option-card d-flex align-items-center w-100 {{ ($jawaban_user[$nomor_sekarang - 1] ?? '') == 'B' ? 'selected' : '' }}">
                        <input class="form-check-input me-3" type="radio"
                            wire:model="jawaban_user.{{ $nomor_sekarang - 1 }}" value="B" id="opsi2">
                        <span><strong class="me-2">B.</strong> {{ $soal->opsi_b }}</span>
                    </label>
                </div>
                <div class="col-12">
                    <label class="option-card d-flex align-items-center w-100 {{ ($jawaban_user[$nomor_sekarang - 1] ?? '') == 'C' ? 'selected' : '' }}">
                        <input class="form-check-input me-3" type="radio"
                            wire:model="jawaban_user.{{ $nomor_sekarang - 1 }}" value="C" id="opsi3">
                        <span><strong class="me-2">C.</strong> {{ $soal->opsi_c }}</span>
                    </label>
                </div>
                <div class="col-12">
                    <label class="option-card d-flex align-items-center w-100 {{ ($jawaban_user[$nomor_sekarang - 1] ?? '') == 'D' ? 'selected' : '' }}">
                        <input class="form-check-input me-3" type="radio"
                            wire:model="jawaban_user.{{ $nomor_sekarang - 1 }}" value="D" id="opsi4">
                        <span><strong class="me-2">D.</strong> {{ $soal->opsi_d }}</span>
                    </label>
                </div>
                @if ($soal->opsi_e)
                <div class="col-12">
                    <label class="option-card d-flex align-items-center w-100 {{ ($jawaban_user[$nomor_sekarang - 1] ?? '') == 'E' ? 'selected' : '' }}">
                        <input class="form-check-input me-3" type="radio"
                            wire:model="jawaban_user.{{ $nomor_sekarang - 1 }}" value="E" id="opsi5">
                        <span><strong class="me-2">E.</strong> {{ $soal->opsi_e }}</span>
                    </label>
                </div>
                @endif
            </div>

            <!-- Action Buttons -->
            <div class="d-flex flex-wrap gap-2">
                <button class="btn btn-outline-secondary" wire:click="navigate({{ $nomor_sekarang - 1 }})"
                    @if ($nomor_sekarang == 1) disabled @endif>
                    <span wire:ignore><i data-feather="chevron-left" style="width: 18px; height: 18px;"></i></span>
                    Sebelumnya
                </button>
                <button class="btn btn-info text-white" wire:click="saveAndNext({{ $nomor_sekarang }})" id="btn-simpan" disabled>
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
                Navigasi Soal
            </h6>
        </div>
        <div class="card-body p-4">
            @php
                $nomor_soal = 1;
                $kosong = 0;
            @endphp
            <div class="d-flex flex-wrap gap-2">
                @for ($i = 0; $i < 6; $i++)
                    @for ($j = 1; $j <= 20; $j++)
                        @if($nomor_soal <= $jml_soal)
                        <button wire:click="navigate({{ $nomor_soal }})"
                            class="btn nav-btn btn-sm {{ $jawaban[$nomor_soal - 1] === '0' ? 'btn-outline-danger' : 'btn-success' }} {{ isset($flagged[$nomor_soal]) ? 'flagged-btn' : '' }} {{ $nomor_soal == $nomor_sekarang ? 'ring ring-info' : '' }}"
                            style="{{ $nomor_soal == $nomor_sekarang ? 'box-shadow: 0 0 0 3px rgba(13, 202, 240, 0.5);' : '' }}"
                        >
                            {{ $nomor_soal }}
                            @if(isset($flagged[$nomor_soal]))
                                <span class="flag-icon">🔖</span>
                            @endif
                        </button>
                        @php 
                            if($jawaban[$nomor_soal - 1] === '0') $kosong++;
                            $nomor_soal++; 
                        @endphp
                        @endif
                    @endfor
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
