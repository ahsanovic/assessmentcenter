<div>
    <div class="d-flex justify-content-end">
        <x-breadcrumb :breadcrumbs="[
            ['url' => route('peserta.dashboard'), 'title' => 'Dashboard'],
            ['url' => null, 'title' => 'Tes PSPK'],
        ]" />
    </div>
    
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <!-- Header Card -->
            <div class="card border-0 shadow-sm mb-4" style="background: linear-gradient(135deg, #a18cd1 0%, #fbc2eb 100%);">
                <div class="card-body p-4 text-white text-center">
                    <div class="rounded-circle bg-white bg-opacity-25 p-3 d-inline-flex mb-3" wire:ignore>
                        <i data-feather="award" style="width: 40px; height: 40px;"></i>
                    </div>
                    <h3 class="mb-2">Tes PSPK</h3>
                    <p class="mb-0 opacity-75">Penilaian Kompetensi Kepegawaian</p>
                </div>
            </div>

            <!-- PIN Entry Card -->
            <div class="card border-0 shadow-sm">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <div class="rounded-circle p-3 d-inline-flex mb-3" style="background-color: rgba(111, 66, 193, 0.1);" wire:ignore>
                            <i style="color: #6f42c1; width: 32px; height: 32px;" data-feather="key"></i>
                        </div>
                        <h5 class="mb-2">Masukkan PIN Ujian</h5>
                        <p class="text-muted mb-0">Masukkan 4 digit PIN yang diberikan oleh pengawas</p>
                    </div>

                    <form wire:submit="submit">
                        <div class="mb-4">
                            <div class="input-group input-group-lg">
                                <span class="input-group-text bg-light border-end-0" wire:ignore>
                                    <i class="text-muted" data-feather="lock"></i>
                                </span>
                                <input
                                    wire:model.live.debounce="pin_ujian"
                                    type="text"
                                    class="form-control form-control-lg border-start-0 text-center fw-bold"
                                    placeholder="• • • •"
                                    maxlength="4"
                                    style="letter-spacing: 1rem; font-size: 1.5rem;"
                                    autofocus
                                />
                            </div>
                        </div>
                        <div class="d-grid">
                            <button
                                type="submit"
                                class="btn btn-lg text-white"
                                style="background-color: #6f42c1;"
                                @disabled(strlen($pin_ujian) !== 4)
                            >
                                <span wire:ignore>
                                    <i class="me-2" data-feather="log-in"></i>
                                </span>
                                Masuk ke Tes
                            </button>
                        </div>
                    </form>

                    <!-- Info Box -->
                    <div class="mt-4 p-3 bg-light rounded-3">
                        <div class="d-flex align-items-start" wire:ignore>
                            <i class="text-info me-2 flex-shrink-0" data-feather="info" style="width: 18px; height: 18px;"></i>
                            <small class="text-muted">
                                PIN akan diberikan oleh pengawas ujian. Pastikan Anda sudah siap sebelum memasukkan PIN karena tes akan langsung dimulai.
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
