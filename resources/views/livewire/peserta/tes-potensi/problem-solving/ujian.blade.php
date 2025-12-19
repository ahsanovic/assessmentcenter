@push('css')
<style>
    .form-check-input[type="radio"] {
        border: 2px solid #dee2e6;
        width: 1.25em;
        height: 1.25em;
        cursor: pointer;
    }
    .form-check-input[type="radio"]:checked {
        background-color: #f857a6;
        border-color: #f857a6;
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
        border-color: #f857a6;
        background-color: rgba(248, 87, 166, 0.05);
    }
    .option-card.selected {
        border-color: #f857a6;
        background-color: rgba(248, 87, 166, 0.1);
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
    <div class="card border-0 shadow-sm mb-4" style="background: linear-gradient(135deg, #f857a6 0%, #ff5858 100%);">
        <div class="card-body p-4 text-white">
            <div class="d-flex flex-column flex-md-row align-items-center justify-content-between">
                <div class="d-flex align-items-center mb-3 mb-md-0">
                    <div class="rounded-circle bg-white bg-opacity-25 p-2 me-3" wire:ignore>
                        <i data-feather="target" style="width: 28px; height: 28px;"></i>
                    </div>
                    <div>
                        <h4 class="mb-0">Tes Problem Solving</h4>
                        <small class="opacity-75">Tes Potensi - Sub Tes {{ $current_sequence }} dari 7</small>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    @for($i = 1; $i <= 7; $i++)
                        <span class="badge {{ $i <= $current_sequence ? 'bg-white text-danger' : 'bg-white bg-opacity-25' }}" style="width: 28px; height: 28px; display: flex; align-items: center; justify-content: center;">{{ $i }}</span>
                    @endfor
                </div>
            </div>
        </div>
    </div>

    <!-- Status Bar -->
    <div class="card border-0 shadow-sm mb-4 status-bar">
        <div class="card-body py-3">
            <div class="row align-items-center g-3">
                <div class="col-6 col-md-2">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-success bg-opacity-10 p-2 me-2" wire:ignore>
                            <i class="text-success" data-feather="check-circle" style="width: 18px; height: 18px;"></i>
                        </div>
                        <div>
                            <small class="text-muted d-block">Dijawab</small>
                            <strong class="text-success">{{ $jml_soal - $jawaban_kosong }}</strong>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-2">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-danger bg-opacity-10 p-2 me-2" wire:ignore>
                            <i class="text-danger" data-feather="x-circle" style="width: 18px; height: 18px;"></i>
                        </div>
                        <div>
                            <small class="text-muted d-block">Belum</small>
                            <strong class="text-danger">{{ $jawaban_kosong ?? 0 }}</strong>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-info bg-opacity-10 p-2 me-2" wire:ignore>
                            <i class="text-info" data-feather="clock" style="width: 18px; height: 18px;"></i>
                        </div>
                        <div>
                            <small class="text-muted d-block">Sisa Waktu</small>
                            <strong class="timer-badge text-info time"></strong>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-2">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-primary bg-opacity-10 p-2 me-2" wire:ignore>
                            <i class="text-primary" data-feather="list" style="width: 18px; height: 18px;"></i>
                        </div>
                        <div>
                            <small class="text-muted d-block">Tes ke-</small>
                            <strong class="text-primary">{{ $current_sequence }} / 7</strong>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-3 text-md-end">
                    <button class="btn btn-warning w-100 w-md-auto"
                        x-data
                        @click="Swal.fire({
                            title: '{{ $current_sequence == 7 ? 'Apakah Anda yakin mengakhiri tes?' : 'Apakah Anda yakin melanjutkan tes berikutnya?' }}',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: '{{ $current_sequence == 7 ? 'Akhiri Tes!' : 'Tes Berikutnya' }}',
                            cancelButtonText: 'Batal',
                        }).then((result) => {
                            if (result.isConfirmed) {
                                $wire.finish();
                            }
                        })"
                        @disabled($jawaban_kosong != 0)
                    >
                        <span wire:ignore><i data-feather="{{ $current_sequence == 7 ? 'log-out' : 'arrow-right' }}" style="width: 18px; height: 18px;" class="me-1"></i></span>
                        {{ $current_sequence == 7 ? 'Selesai' : 'Lanjut Tes' }}
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
                    <span class="badge text-white me-3 px-3 py-2" style="font-size: 1rem; background-color: #f857a6;">
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
                <div class="col-12">
                    <label class="option-card d-flex align-items-center w-100 {{ ($jawaban_user[$nomor_sekarang - 1] ?? '') == 'E' ? 'selected' : '' }}">
                        <input class="form-check-input me-3" type="radio"
                            wire:model="jawaban_user.{{ $nomor_sekarang - 1 }}" value="E" id="opsi5">
                        <span><strong class="me-2">E.</strong> {{ $soal->opsi_e }}</span>
                    </label>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="d-flex flex-wrap gap-2">
                <button class="btn btn-outline-secondary" wire:click="navigate({{ $nomor_sekarang - 1 }})"
                    @if ($nomor_sekarang == 1) disabled @endif>
                    <span wire:ignore><i data-feather="chevron-left" style="width: 18px; height: 18px;"></i></span>
                    Sebelumnya
                </button>
                <button class="btn text-white" style="background-color: #f857a6;" wire:click="saveAndNext({{ $nomor_sekarang }})" id="btn-simpan" disabled>
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
                @for ($i = 0; $i < $jml_soal; $i++)
                    @if($nomor_soal <= $jml_soal)
                    <button wire:click="navigate({{ $nomor_soal }})"
                        class="btn nav-btn btn-sm {{ $jawaban[$nomor_soal - 1] === '0' ? 'btn-outline-danger' : 'btn-success' }} {{ isset($flagged[$nomor_soal]) ? 'flagged-btn' : '' }}"
                        style="{{ $nomor_soal == $nomor_sekarang ? 'box-shadow: 0 0 0 3px rgba(248, 87, 166, 0.5);' : '' }}"
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

    function timeout(id) {
        Swal.fire({
            title: 'Waktu Habis',
            text: 'Waktu ujian telah habis, tetapi Anda masih dapat mengerjakan soal sampai selesai. Silahkan mengisi semua jawaban dan pastikan jangan ada yang terlewat!',
            icon: 'warning',
            allowOutsideClick: false,
        })
    }

    var waktuBerakhir = new Date({{ $timer }} * 1000).getTime();
    
    var x = setInterval(function() {
        var now = new Date().getTime();
        var distance = waktuBerakhir - now;
        
        var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        var seconds = Math.floor((distance % (1000 * 60)) / 1000);
        
        $('.time').html(('0' + hours).slice(-2) + " : " + ('0' + minutes).slice(-2) + " : " + ('0' + seconds).slice(-2));
        
        if (distance < 0) {
            if (!localStorage.getItem("popup")) {
                clearInterval(x); 
                $('.time').html('Waktu Habis');
                timeout({{ $id_ujian }});
                localStorage.setItem("popup", 'viewed');
            } else {
                clearInterval(x); 
                $('.time').html('Waktu Habis');
            }
        }
    }, 1000);
</script>
@endpush
