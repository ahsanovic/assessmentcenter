<div>
    @php
        $metodeTes = auth()->guard('peserta')->user()->event->metodeTes->metode_tes;
    @endphp

    <div class="row justify-content-center">
        <div class="col-md-10 col-lg-8">
            <!-- Header Card -->
            <div class="card border-0 shadow-sm mb-4" style="background: linear-gradient(135deg, #a18cd1 0%, #fbc2eb 100%);">
                <div class="card-body p-4 text-white text-center">
                    <div class="rounded-circle bg-white bg-opacity-25 p-3 d-inline-flex mb-3">
                        <i data-feather="award" style="width: 40px; height: 40px;"></i>
                    </div>
                    <h3 class="mb-2">Tes {{ $metodeTes }}</h3>
                    <p class="mb-0 opacity-75">Penilaian Kompetensi Kepegawaian</p>
                </div>
            </div>

            <!-- Instructions Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        <div class="rounded-circle p-3 d-inline-flex mb-3" style="background-color: rgba(111, 66, 193, 0.1);">
                            <i style="color: #6f42c1; width: 32px; height: 32px;" data-feather="book-open"></i>
                        </div>
                        <h5 class="mb-3">Selamat Datang!</h5>
                    </div>

                    <div class="bg-light rounded-3 p-4 mb-4">
                        <p class="mb-2">
                            <i class="text-primary me-2" data-feather="star" style="width: 18px; height: 18px;"></i>
                            Anda akan mengikuti <strong>Tes {{ $metodeTes }}</strong> sebagai bagian dari pengembangan kompetensi diri.
                        </p>
                        <p class="mb-2">
                            <i class="text-success me-2" data-feather="eye" style="width: 18px; height: 18px;"></i>
                            Pastikan Anda mengerjakan dengan fokus, membaca setiap soal dengan cermat, dan menjawab sesuai pemahaman terbaik Anda.
                        </p>
                        <p class="mb-0">
                            <i class="text-warning me-2" data-feather="award" style="width: 18px; height: 18px;"></i>
                            Tes ini bukan hanya tentang hasil, tetapi juga kesempatan untuk mengenali potensi Anda.
                        </p>
                    </div>

                    <div class="alert border-0 mb-4" style="background-color: rgba(111, 66, 193, 0.1); color: #6f42c1;">
                        <div class="d-flex align-items-center">
                            <i class="me-2" data-feather="zap" style="width: 20px; height: 20px;"></i>
                            <strong>Semangat dan selamat mengerjakan!</strong>
                        </div>
                    </div>

                    <div class="d-grid">
                        <button 
                            class="btn btn-lg text-white"
                            style="background-color: #6f42c1;"
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
                        >
                            <i class="me-2" data-feather="play-circle"></i>
                            Mulai Tes Sekarang
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
