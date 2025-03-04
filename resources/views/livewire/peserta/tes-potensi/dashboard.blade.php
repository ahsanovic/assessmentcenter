<div>
    <div class="row">
        <div class="col-sm-1 col-md-3 col-lg-4">
            <div class="card mt-4">
                <div class="card-body">
                    <button class="btn btn-inverse-success" 
                        {{-- {{ session('current_test') !== null ? 'disabled' : '' }} --}}
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
