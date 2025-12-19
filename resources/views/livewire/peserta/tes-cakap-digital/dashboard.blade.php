<div>
    <div class="row justify-content-center">
        <div class="col-md-10 col-lg-8">
            <!-- Header Card -->
            <div class="card border-0 shadow-sm mb-4" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                <div class="card-body p-4 text-white text-center">
                    <div class="rounded-circle bg-white bg-opacity-25 p-3 d-inline-flex mb-3">
                        <i data-feather="monitor" style="width: 40px; height: 40px;"></i>
                    </div>
                    <h3 class="mb-2">Tes Literasi Digital & Emerging Skill</h3>
                    <p class="mb-0 opacity-75">Pengembangan Kompetensi Digital</p>
                </div>
            </div>

            <!-- Instructions Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        <div class="rounded-circle bg-info bg-opacity-10 p-3 d-inline-flex mb-3">
                            <i class="text-info" data-feather="book-open" style="width: 32px; height: 32px;"></i>
                        </div>
                        <h5 class="mb-3">Selamat Datang!</h5>
                    </div>

                    <div class="bg-light rounded-3 p-4 mb-4">
                        <p class="mb-2">
                            <i class="text-primary me-2" data-feather="star" style="width: 18px; height: 18px;"></i>
                            Anda akan mengikuti <strong>Tes Literasi Digital dan Emerging Skill</strong> sebagai bagian dari pengembangan kompetensi diri.
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

                    <div class="alert alert-info border-0 mb-4">
                        <div class="d-flex align-items-center">
                            <i class="me-2" data-feather="zap" style="width: 20px; height: 20px;"></i>
                            <strong>Semangat dan selamat mengerjakan!</strong>
                        </div>
                    </div>

                    <div class="d-grid">
                        <button 
                            class="btn btn-info btn-lg text-white"
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
