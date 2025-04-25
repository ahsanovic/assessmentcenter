<div>
    @php
        $event_id = auth()->guard('peserta')->user()->event_id;
        $peserta_id = auth()->guard('peserta')->user()->id;
        $data = getFinishedTes($event_id, $peserta_id);
        $finished_all_test = count(array_filter($data)) === count($data);
    @endphp

    @if ($finished_all_test)
    <x-alert type="danger" teks="Anda sudah melakukan semua tes!" />
    @endif

    <div class="row">
        <div class="col-sm-12 col-md-12 col-lg-12">
            <div class="card mt-4">
                <div class="card-body d-flex justify-content-center text-center">
                    <button class="btn btn-inverse-success" 
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
                        {{-- @disabled($finished_all_test) --}}
                    >
                        Mulai Tes
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
