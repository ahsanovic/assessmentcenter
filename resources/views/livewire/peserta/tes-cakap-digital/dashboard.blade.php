<div>
    <div class="row">
        <div class="col-sm-12 col-md-12 col-lg-12">
            <div class="card mt-4">
                <div class="card-body d-flex justify-content-center text-center">
                    <div style="font-family: monospace">
                        <h5 class="text-center">
                            Selamat datang! Anda akan mengikuti Tes Literasi Digital dan Emerging Skill sebagai bagian dari pengembangan kompetensi diri.  
                            <br>
                            Pastikan Anda mengerjakan dengan fokus, membaca setiap soal dengan cermat, dan menjawab sesuai pemahaman terbaik Anda.  
                            <br>
                            Tes ini bukan hanya tentang hasil, tetapi juga kesempatan untuk mengenali potensi Anda. Semangat dan selamat mengerjakan!
                        </h5>

                        <button class="btn btn-inverse-success mt-4" 
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
                            Mulai Tes
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
