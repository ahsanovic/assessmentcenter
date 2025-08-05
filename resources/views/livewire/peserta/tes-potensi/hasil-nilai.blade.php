<div>
    <div class="row mb-4">
        <div class="col">
            <h3 class="text-center">Hasil Nilai Tes Potensi</h3>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body d-flex justify-content-center text-center">
                    <div style="font-family: 'Helvetica Neue', sans-serif;">
                        <div class="mb-2"><strong>Job Person Match (JPM)</strong> : {{ $nilai->jpm }} %</div>
                        <div><strong>Kategori</strong> : {{ $nilai->kategori }}</div>
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
