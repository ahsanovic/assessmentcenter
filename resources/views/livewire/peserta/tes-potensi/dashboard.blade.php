<div>
    <div class="row">
        <div class="col-sm-1 col-md-3 col-lg-4">
            <div class="card mt-4">
                <div class="card-body">
                    <h5 class="card-title mb-4">Tes Belajar Cepat dan Pengembangan Diri</h5>
                    <button class="btn btn-inverse-success"
                    {{ isset($test_pengembangan_diri->is_finished) ? 'disabled' : '' }}
                        x-data
                        @click="Swal.fire({
                            title: 'Apakah Anda yakin memulai tes?',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Mulai Tes!',
                            cancelButtonText: 'Batal',
                        }).then((result) => {
                            if (result.isConfirmed) {
                                $wire.startTesPengembanganDiri();
                            }
                        })"
                    >
                        Mulai Tes
                    </button>
                </div>
            </div>
        </div>
        <div class="col-sm-1 col-md-3 col-lg-4">
            <div class="card mt-4">
                <div class="card-body">
                    <h5 class="card-title mb-4">Tes Interpersonal</h5>
                    <button class="btn btn-inverse-success"
                        {{ isset($test_interpersonal->is_finished) ? 'disabled' : '' }}
                        x-data
                        @click="Swal.fire({
                            title: 'Apakah Anda yakin memulai tes?',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Mulai Tes!',
                            cancelButtonText: 'Batal',
                        }).then((result) => {
                            if (result.isConfirmed) {
                                $wire.startTesInterpersonal();
                            }
                        })"
                    >
                        Mulai Tes
                    </button>
                </div>
            </div>
        </div>
        <div class="col-sm-1 col-md-3 col-lg-4">
            <div class="card mt-4">
                <div class="card-body">
                    <h5 class="card-title mb-4">Tes Kecerdasan Emosi</h5>
                    <button class="btn btn-inverse-success"
                        {{ isset($test_kecerdasan_emosi->is_finished) ? 'disabled' : '' }}
                        x-data
                        @click="Swal.fire({
                            title: 'Apakah Anda yakin memulai tes?',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Mulai Tes!',
                            cancelButtonText: 'Batal',
                        }).then((result) => {
                            if (result.isConfirmed) {
                                $wire.startTesKecerdasanEmosi();
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
