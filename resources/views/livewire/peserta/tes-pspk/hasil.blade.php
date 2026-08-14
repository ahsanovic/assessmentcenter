<div>
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body p-5">
                    <div class="mb-4">
                        <div class="rounded-circle bg-success bg-opacity-10 p-4 d-inline-flex" wire:ignore>
                            <i class="text-success" data-feather="check-circle" style="width: 64px; height: 64px;"></i>
                        </div>
                    </div>

                    <h2 class="text-success mb-3">Selamat!</h2>
                    <h4 class="mb-4">
                        Terima kasih, <strong>{{ auth()->guard('peserta')->user()->nama }}</strong>!
                    </h4>
                    <p class="text-muted mb-4">
                        Anda telah menyelesaikan <strong>Tes {{ activePeserta()->event->metodeTes->metode_tes }}</strong> dengan baik dan penuh semangat!
                    </p>

                    <div class="mb-4" wire:ignore>
                        <span class="display-1">🎉</span>
                    </div>

                    <div class="bg-light rounded-3 p-4 mb-4">
                        <div class="d-flex align-items-start text-start">
                            <div class="me-3" wire:ignore>
                                <i class="text-info" data-feather="info" style="width: 24px; height: 24px;"></i>
                            </div>
                            <div>
                                <p class="mb-0 text-muted">
                                    Anda telah menyelesaikan tes ini. Kembali ke dashboard untuk melanjutkan tes lain dalam program yang sama, atau logout jika sudah selesai semua.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex flex-column flex-sm-row gap-3 justify-content-center">
                        <a href="{{ route('peserta.dashboard') }}" class="btn btn-primary btn-lg px-4" wire:navigate>
                            <span wire:ignore><i data-feather="home" style="width: 20px; height: 20px;" class="me-2"></i></span>
                            Kembali ke Dashboard
                        </a>
                        <form action="{{ route('peserta.logout') }}" method="post" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger btn-lg px-4">
                                <span wire:ignore><i data-feather="log-out" style="width: 20px; height: 20px;" class="me-2"></i></span>
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
