<div>
    @php
        $event_id = auth()->guard('peserta')->user()->event_id;
        $peserta_id = auth()->guard('peserta')->user()->id;
        $data = getFinishedTes($event_id, $peserta_id);
        $finished_all_test = count(array_filter($data)) === count($data);
    @endphp

    @if ($finished_all_test)
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm border-start border-4 border-success">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-success bg-opacity-10 p-3 me-3">
                            <i class="text-success" data-feather="check-circle" style="width: 24px; height: 24px;"></i>
                        </div>
                        <div>
                            <h6 class="mb-1 text-success">Tes Selesai!</h6>
                            <p class="mb-0 text-muted">Selamat! Anda sudah menyelesaikan semua tes potensi.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="row justify-content-center">
        <div class="col-md-10 col-lg-8">
            <!-- Header Card -->
            <div class="card border-0 shadow-sm mb-4" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                <div class="card-body p-4 text-white text-center">
                    <div class="rounded-circle bg-white bg-opacity-25 p-3 d-inline-flex mb-3">
                        <i data-feather="trending-up" style="width: 40px; height: 40px;"></i>
                    </div>
                    <h3 class="mb-2">Tes Potensi</h3>
                    <p class="mb-0 opacity-75">Pengembangan Kompetensi Diri</p>
                </div>
            </div>

            <!-- Instructions Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        <div class="rounded-circle bg-danger bg-opacity-10 p-3 d-inline-flex mb-3">
                            <i class="text-danger" data-feather="book-open" style="width: 32px; height: 32px;"></i>
                        </div>
                        <h5 class="mb-3">Petunjuk Pengerjaan</h5>
                    </div>

                    <p class="text-muted mb-3">
                        Pada bagian ini, Anda dihadapkan pada <strong>7 Sub Tes</strong> yaitu:
                    </p>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item bg-light border-0 rounded mb-2">
                                    <i class="text-primary me-2" data-feather="users" style="width: 16px; height: 16px;"></i>
                                    <strong>1.</strong> Interpersonal
                                </li>
                                <li class="list-group-item bg-light border-0 rounded mb-2">
                                    <i class="text-success me-2" data-feather="eye" style="width: 16px; height: 16px;"></i>
                                    <strong>2.</strong> Self Awareness
                                </li>
                                <li class="list-group-item bg-light border-0 rounded mb-2">
                                    <i class="text-warning me-2" data-feather="zap" style="width: 16px; height: 16px;"></i>
                                    <strong>3.</strong> Critical & Strategic Thinkings
                                </li>
                                <li class="list-group-item bg-light border-0 rounded mb-2">
                                    <i class="text-danger me-2" data-feather="target" style="width: 16px; height: 16px;"></i>
                                    <strong>4.</strong> Problem Solving
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item bg-light border-0 rounded mb-2">
                                    <i class="text-info me-2" data-feather="heart" style="width: 16px; height: 16px;"></i>
                                    <strong>5.</strong> Emotional Quotient
                                </li>
                                <li class="list-group-item bg-light border-0 rounded mb-2">
                                    <i class="me-2" data-feather="trending-up" style="width: 16px; height: 16px; color: #6f42c1;"></i>
                                    <strong>6.</strong> Growth Mindsets
                                </li>
                                <li class="list-group-item bg-light border-0 rounded mb-2">
                                    <i class="me-2" data-feather="award" style="width: 16px; height: 16px; color: #d63384;"></i>
                                    <strong>7.</strong> Grit
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="bg-light rounded-3 p-4 mb-4">
                        <p class="text-muted mb-2">
                            <i class="text-info me-2" data-feather="info" style="width: 18px; height: 18px;"></i>
                            Pada setiap Sub Tes, Anda akan disajikan pertanyaan dengan <strong>5 pilihan jawaban</strong>. 
                            Silahkan memilih salah satu diantara 5 jawaban yang tersedia, yang menurut Anda 
                            paling efektif untuk mengatasi kondisi tersebut atau jawaban yang Anda anggap 
                            paling menggambarkan diri dan perasaan Anda.
                        </p>
                    </div>

                    <div class="alert alert-warning border-0 mb-4">
                        <div class="d-flex align-items-center">
                            <i class="me-2" data-feather="alert-triangle" style="width: 20px; height: 20px;"></i>
                            <strong>Pastikan Anda menjawab setiap soal yang ada.</strong>
                        </div>
                    </div>

                    <div class="d-grid">
                        <button 
                            class="btn btn-danger btn-lg"
                            x-data
                            @click="Swal.fire({
                                title: 'Apakah Anda yakin memulai tes?',
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonText: 'Mulai Tes!',
                                cancelButtonText: 'Batal',
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    $wire.start();
                                }
                            })"
                            @disabled($finished_all_test)
                        >
                            <i class="me-2" data-feather="{{ $finished_all_test ? 'check-circle' : 'play-circle' }}"></i>
                            {{ $finished_all_test ? 'Tes Sudah Selesai' : 'Mulai Tes Sekarang' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
