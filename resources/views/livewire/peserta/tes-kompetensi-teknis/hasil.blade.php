<div>
    <div class="row mb-4">
        <div class="col">
            <h3 class="text-center">Tes Kompetensi Teknis</h3>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body d-flex justify-content-center text-center">
                    <div style="font-family: 'Helvetica Neue', sans-serif;">
                        <h4 class="text-center mb-3">
                            Terima kasih, <strong>{{ auth()->guard('peserta')->user()->nama }}</strong>!
                        </h4>
                        <h6>Anda telah menyelesaikan Tes Kompetensi Teknis dengan baik dan penuh semangat!</h6>
                        <div class="row mt-4">
                            <form action="{{ route('peserta.logout') }}" method="post">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-danger">
                                    <i class="icon-md" data-feather="log-out"></i>
                                    <span class="link-title">Logout</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
