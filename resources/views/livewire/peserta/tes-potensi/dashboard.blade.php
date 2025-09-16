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

    <div class="row justify-content-center">
        <div class="col-sm-12 col-md-12 col-lg-6">
            <div class="card mt-4">
                <div class="card-body d-flex justify-content-center text-center">
                    <div style="font-family: 'Helvetica Neue', sans-serif;">
                        <div class="text-center mb-3">
                            <h5 class="fw-bold">Petunjuk Pengerjaan</h5>
                        </div>

                        <p class="text-justify">
                            Pada bagian ini, Anda dihadapkan pada <strong>7 Sub Tes</strong> yaitu:
                        </p>

                        <ol class="mb-3 text-start d-inline-block">
                            <li>Interpersonal</li>
                            <li>Self Awareness</li>
                            <li>Critical & Strategic Thinkings</li>
                            <li>Problem Solving</li>
                            <li>Emotional Quotient</li>
                            <li>Growth Mindsets</li>
                            <li>Grit</li>
                        </ol>

                        <p class="text-center mt-3">
                            Pada setiap Sub Tes, Anda akan disajikan pertanyaan dengan 5 pilihan jawaban. 
                            Silahkan memilih salah satu diantara 5 jawaban yang tersedia, yang menurut Anda 
                            paling efektif untuk mengatasi kondisi tersebut atau jawaban yang Anda anggap 
                            paling menggambarkan diri dan perasaan Anda.
                        </p>
                        <p class="text-center mt-2 mb-4">
                            <strong>Pastikan Anda menjawab setiap soal yang ada.</strong>
                        </p>

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
                            @disabled($finished_all_test)
                        >
                            Mulai Tes
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
