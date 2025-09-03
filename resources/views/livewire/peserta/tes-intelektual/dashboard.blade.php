<div>
    @php
        $event_id = auth()->guard('peserta')->user()->event_id;
        $peserta_id = auth()->guard('peserta')->user()->id;
        $data = getFinishedTesIntelektual($event_id, $peserta_id);
        $finished_all_test = count(array_filter($data)) === count($data);
    @endphp

    @if ($finished_all_test)
    <x-alert type="danger" teks="Anda sudah melakukan semua tes!" />
    @endif

    <div class="row">
        <div class="col-sm-12 col-md-12 col-lg-12">
            <div class="card mt-4">
                <div class="card-body d-flex justify-content-center text-center">
                    <div style="font-family: 'Helvetica Neue', sans-serif;">
                        <h5 class="text-center mb-4">
                            <p class="mb-1">Selamat datang! Anda akan mengikuti Tes Intelektual sebagai bagian dari pengembangan kompetensi diri. </p>
                            <p class="mb-1">Pastikan Anda mengerjakan dengan fokus, membaca setiap soal dengan cermat, dan menjawab sesuai pemahaman terbaik Anda.  </p>
                            <p>Tes ini bukan hanya tentang hasil, tetapi juga kesempatan untuk mengenali potensi Anda. Semangat dan selamat mengerjakan!</p>
                        </h5>

                        <div class="mt-5">
                            <button
                                class="btn btn-primary me-3"
                                type="button"
                                data-bs-toggle="collapse"
                                data-bs-target="#subtes1"
                                aria-expanded="false"
                                aria-controls="subtes1"
                                @disabled($data['sub_tes_1'])
                            >
                                Sub Tes 1
                            </button>
                            <button
                                class="btn btn-primary me-3"
                                type="button"
                                data-bs-toggle="collapse"
                                data-bs-target="#subtes2"
                                aria-expanded="false"
                                aria-controls="subtes2"
                                @disabled(!$data['sub_tes_1'] || ($data['sub_tes_1'] && $data['sub_tes_2']))
                            >
                                Sub Tes 2
                            </button>
                            <button
                                class="btn btn-primary"
                                type="button"
                                data-bs-toggle="collapse"
                                data-bs-target="#subtes3"
                                aria-expanded="false"
                                aria-controls="subtes3"
                                @disabled((!$data['sub_tes_1'] || !$data['sub_tes_2']) || ($finished_all_test))
                            >
                                Sub Tes 3
                            </button>
                        </div>

                        <div class="collapse" id="subtes1">
                            <div class="card card-body">
                                <div class="row">
                                    <div class="col-12">
                                        <p class="mb-3"><strong>Petunjuk Pengerjaan:</strong></p>
                                        <p>
                                            Pada subtes 1 Anda disajikan 5 buah kata. Empat dari 5 kata memiliki kesamaan. Pilihlah 1 kata yang berbeda dari kata yang lain.
                                            <br />
                                            contoh : a. kunci  b. gembok  c. password  d. kunci jawaban  e. Loker
                                            <br />
                                            Jawaban yang benar adalah e
                                            <br />
                                            PENJELASAN :
                                            Loker adalah tempat penyimpanan, sementara yang lainnya berhubungan dengan penguncian
                                        </p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <button class="btn btn-inverse-success mt-4 me-4" 
                                            x-data
                                            @click="Swal.fire({
                                                title: 'Apakah Anda yakin memulai tes?',
                                                icon: 'warning',
                                                showCancelButton: true,
                                                confirmButtonText: 'Mulai Tes!',
                                                cancelButtonText: 'Batal',
                                            }).then((result) => {
                                                if (result.isConfirmed) {
                                                    $wire.startSubTes1();
                                                }
                                            })"
                                            @disabled($data['sub_tes_1'] || $finished_all_test)
                                        >
                                            Mulai Sub Tes 1
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="collapse" id="subtes2">
                            <div class="card card-body">
                                <div class="row">
                                    <div class="col-12">
                                        <p class="mb-3"><strong>Petunjuk Pengerjaan:</strong></p>
                                        <p>
                                            Pada subtes 2 Anda disajikan soal yang berisi gambar – gambar. <br />
                                            Setiap soal menanyakan hal yang berbeda-beda. <br />
                                            Anda akan diminta menentukan perbedaan gambar, menghitung menggunakan gambar, melanjutkan pola dan menentukan pola dari urutan gambar. <br />
                                            Terdapat 5 pilihan jawaban yaitu a, b, c, d, dan e. Pilihlah jawaban paling  tepat dari 5 pilihan jawaban yang tersedia
                                        </p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <button class="btn btn-inverse-success mt-4 me-4" 
                                            x-data
                                            @click="Swal.fire({
                                                title: 'Apakah Anda yakin memulai tes?',
                                                icon: 'warning',
                                                showCancelButton: true,
                                                confirmButtonText: 'Mulai Tes!',
                                                cancelButtonText: 'Batal',
                                            }).then((result) => {
                                                if (result.isConfirmed) {
                                                    $wire.startSubTes2();
                                                }
                                            })"
                                            @disabled($data['sub_tes_2'] || $finished_all_test)
                                        >
                                            Mulai Sub Tes 2
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="collapse" id="subtes3">
                            <div class="card card-body">
                                <div class="row">
                                    <div class="col-12">
                                        <p class="mb-3"><strong>Petunjuk Pengerjaan:</strong></p>
                                        <p>
                                            Dalam Subtes 3 disajikan 5 kubus yaitu kubus A, B, C, D dan E. <br />
                                            Pada setiap sisi kubus tersebut memiliki tanda yang berbeda-beda. <br />
                                            Tiga sisi tampak, namun 3 sisi yang lain tidak tampak. Kubus  A, B, C, D dan E adalah kubus yang berbeda, artinya setiap kubus memiliki tanda yang sama namun dengan urutan yang berbeda-beda. <br />
                                            Setiap soal menunjukkan salah satu dari kubus A, B, C, D dan E namun dalam posisi yang berbeda-beda. <br />
                                            Carilah salah satu kubus dari 5 pilihan jawaban yang tersedia, manakah yang memiliki  tanda dan posisi yang sama  dengan soal. <br />
                                            Anda dapat menemukan kubus yang sama dari masing-masing soal dengan cara  memutar kubus ke kanan atau  kekiri, atau digulingkan kedepan dan kebelakang dalam pikiran Anda.
                                        </p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
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
                                                    $wire.startSubTes3();
                                                }
                                            })"
                                            @disabled($data['sub_tes_3'] || $finished_all_test)
                                        >
                                            Mulai Sub Tes 3
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
