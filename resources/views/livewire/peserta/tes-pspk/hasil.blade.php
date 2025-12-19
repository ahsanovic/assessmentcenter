<div>
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <!-- Success Card -->
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body p-5">
                    <!-- Success Icon -->
                    <div class="mb-4">
                        <div class="rounded-circle bg-success bg-opacity-10 p-4 d-inline-flex" wire:ignore>
                            <i class="text-success" data-feather="check-circle" style="width: 64px; height: 64px;"></i>
                        </div>
                    </div>

                    <!-- Success Message -->
                    <h2 class="text-success mb-3">Selamat!</h2>
                    <h4 class="mb-4">
                        Terima kasih, <strong>{{ auth()->guard('peserta')->user()->nama }}</strong>!
                    </h4>
                    <p class="text-muted mb-4">
                        Anda telah menyelesaikan <strong>Tes {{ auth()->guard('peserta')->user()->event->metodeTes->metode_tes }}</strong> dengan baik dan penuh semangat!
                    </p>

                    <!-- Decorative Element -->
                    <div class="mb-4" wire:ignore>
                        <span class="display-1">🎉</span>
                    </div>

                    <!-- Info Box -->
                    <div class="bg-light rounded-3 p-4 mb-4">
                        <div class="d-flex align-items-start text-start">
                            <div class="me-3" wire:ignore>
                                <i class="text-info" data-feather="info" style="width: 24px; height: 24px;"></i>
                            </div>
                            <div>
                                <p class="mb-0 text-muted">
                                    Hasil tes Anda akan diproses oleh tim penilai. Silahkan logout dari sistem dan tunggu informasi selanjutnya.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Logout Button -->
                    <form action="{{ route('peserta.logout') }}" method="post">
                        @csrf
                        <button type="submit" class="btn btn-danger btn-lg px-5">
                            <span wire:ignore><i data-feather="log-out" style="width: 20px; height: 20px;" class="me-2"></i></span>
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
