<div>
    @php
        $event_id = auth()->guard('peserta')->user()->event_id;
        $peserta_id = auth()->guard('peserta')->user()->id;
        $data = getFinishedTesIntelektual($event_id, $peserta_id);
        $finished_all_test = count(array_filter($data)) === count($data);
    @endphp

    @if ($finished_all_test)
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm border-start border-4 border-success">
                <div class="card-body p-4">
                    <div class="d-flex flex-column flex-md-row align-items-center justify-content-between">
                        <div class="d-flex align-items-center mb-3 mb-md-0">
                            <div class="rounded-circle bg-success bg-opacity-10 p-3 me-3" wire:ignore>
                                <i class="text-success" data-feather="check-circle" style="width: 24px; height: 24px;"></i>
                            </div>
                            <div>
                                <h6 class="mb-1 text-success">Tes Intelektual Selesai!</h6>
                                <p class="mb-0 text-muted">Selamat! Silahkan lanjutkan ke Tes Potensi.</p>
                            </div>
                        </div>
                        <a href="{{ route('peserta.dashboard') }}" class="btn btn-primary" wire:navigate>
                            <span wire:ignore><i data-feather="home" style="width: 18px; height: 18px;" class="me-2"></i></span>
                            Kembali ke Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Header Card -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="card-body p-4 text-white">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <div class="d-flex align-items-center mb-3">
                                <div class="rounded-circle bg-white bg-opacity-25 p-3 me-3">
                                    <i data-feather="cpu" style="width: 32px; height: 32px;"></i>
                                </div>
                                <div>
                                    <h3 class="mb-0">Tes Intelektual</h3>
                                    <p class="mb-0 opacity-75">Pengembangan Kompetensi Diri</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 text-md-end">
                            <div class="d-flex justify-content-md-end gap-2 flex-wrap">
                                <span class="badge {{ $data['sub_tes_1'] ? 'bg-success' : 'bg-white bg-opacity-25' }} px-3 py-2">
                                    <i class="me-1" data-feather="{{ $data['sub_tes_1'] ? 'check' : 'circle' }}" style="width: 14px; height: 14px;"></i>
                                    Sub Tes 1
                                </span>
                                <span class="badge {{ $data['sub_tes_2'] ? 'bg-success' : 'bg-white bg-opacity-25' }} px-3 py-2">
                                    <i class="me-1" data-feather="{{ $data['sub_tes_2'] ? 'check' : 'circle' }}" style="width: 14px; height: 14px;"></i>
                                    Sub Tes 2
                                </span>
                                <span class="badge {{ $data['sub_tes_3'] ? 'bg-success' : 'bg-white bg-opacity-25' }} px-3 py-2">
                                    <i class="me-1" data-feather="{{ $data['sub_tes_3'] ? 'check' : 'circle' }}" style="width: 14px; height: 14px;"></i>
                                    Sub Tes 3
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Welcome Message -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4 text-center">
                    <p class="mb-2">Selamat datang! Anda akan mengikuti <strong>Tes Intelektual</strong> sebagai bagian dari pengembangan kompetensi diri.</p>
                    <p class="mb-2">Pastikan Anda mengerjakan dengan fokus, membaca setiap soal dengan cermat, dan menjawab sesuai pemahaman terbaik Anda.</p>
                    <p class="mb-0 text-muted"><em>Tes ini bukan hanya tentang hasil, tetapi juga kesempatan untuk mengenali potensi Anda. Semangat dan selamat mengerjakan!</em></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Sub Tes Cards -->
    <div class="row">
        <!-- Sub Tes 1 -->
        <div class="col-md-4 mb-4">
            <div class="card border-0 shadow-sm h-100 {{ $data['sub_tes_1'] ? 'opacity-75' : '' }}">
                <div class="card-header bg-white border-0 py-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle {{ $data['sub_tes_1'] ? 'bg-success' : 'bg-primary' }} bg-opacity-10 p-2 me-3">
                                <i class="{{ $data['sub_tes_1'] ? 'text-success' : 'text-primary' }}" data-feather="{{ $data['sub_tes_1'] ? 'check-circle' : 'layers' }}" style="width: 20px; height: 20px;"></i>
                            </div>
                            <h5 class="mb-0">Sub Tes 1</h5>
                        </div>
                        @if($data['sub_tes_1'])
                            <span class="badge bg-success">Selesai</span>
                        @endif
                    </div>
                </div>
                <div class="card-body p-4">
                    <h6 class="text-muted mb-3">Petunjuk Pengerjaan:</h6>
                    <p class="small text-muted mb-3">
                        Pada subtes 1 Anda disajikan 5 buah kata. Empat dari 5 kata memiliki kesamaan. Pilihlah 1 kata yang berbeda dari kata yang lain.
                    </p>
                    <div class="bg-light p-3 rounded-3 mb-3">
                        <small class="text-muted">
                            <strong>Contoh:</strong> a. kunci  b. gembok  c. password  d. kunci jawaban  e. Loker<br>
                            <strong>Jawaban:</strong> e (Loker adalah tempat penyimpanan, sementara yang lainnya berhubungan dengan penguncian)
                        </small>
                    </div>
                </div>
                <div class="card-footer bg-white border-0 p-4 pt-0">
                    <button 
                        class="btn {{ $data['sub_tes_1'] ? 'btn-success' : 'btn-primary' }} w-100"
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
                        <i class="me-2" data-feather="{{ $data['sub_tes_1'] ? 'check' : 'play' }}"></i>
                        {{ $data['sub_tes_1'] ? 'Sudah Selesai' : 'Mulai Sub Tes 1' }}
                    </button>
                </div>
            </div>
        </div>

        <!-- Sub Tes 2 -->
        <div class="col-md-4 mb-4">
            <div class="card border-0 shadow-sm h-100 {{ $data['sub_tes_2'] ? 'opacity-75' : (!$data['sub_tes_1'] ? 'opacity-50' : '') }}">
                <div class="card-header bg-white border-0 py-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle {{ $data['sub_tes_2'] ? 'bg-success' : 'bg-warning' }} bg-opacity-10 p-2 me-3">
                                <i class="{{ $data['sub_tes_2'] ? 'text-success' : 'text-warning' }}" data-feather="{{ $data['sub_tes_2'] ? 'check-circle' : 'image' }}" style="width: 20px; height: 20px;"></i>
                            </div>
                            <h5 class="mb-0">Sub Tes 2</h5>
                        </div>
                        @if($data['sub_tes_2'])
                            <span class="badge bg-success">Selesai</span>
                        @elseif(!$data['sub_tes_1'])
                            <span class="badge bg-secondary">Terkunci</span>
                        @endif
                    </div>
                </div>
                <div class="card-body p-4">
                    <h6 class="text-muted mb-3">Petunjuk Pengerjaan:</h6>
                    <p class="small text-muted mb-3">
                        Pada subtes 2 Anda disajikan soal yang berisi gambar-gambar. Setiap soal menanyakan hal yang berbeda-beda.
                    </p>
                    <div class="bg-light p-3 rounded-3 mb-3">
                        <small class="text-muted">
                            Anda akan diminta menentukan perbedaan gambar, menghitung menggunakan gambar, melanjutkan pola dan menentukan pola dari urutan gambar. Pilih jawaban paling tepat dari 5 pilihan (a, b, c, d, e).
                        </small>
                    </div>
                </div>
                <div class="card-footer bg-white border-0 p-4 pt-0">
                    <button 
                        class="btn {{ $data['sub_tes_2'] ? 'btn-success' : 'btn-warning' }} w-100"
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
                        @disabled(!$data['sub_tes_1'] || ($data['sub_tes_1'] && $data['sub_tes_2']))
                    >
                        <i class="me-2" data-feather="{{ $data['sub_tes_2'] ? 'check' : (!$data['sub_tes_1'] ? 'lock' : 'play') }}"></i>
                        {{ $data['sub_tes_2'] ? 'Sudah Selesai' : (!$data['sub_tes_1'] ? 'Selesaikan Sub Tes 1' : 'Mulai Sub Tes 2') }}
                    </button>
                </div>
            </div>
        </div>

        <!-- Sub Tes 3 -->
        <div class="col-md-4 mb-4">
            <div class="card border-0 shadow-sm h-100 {{ $data['sub_tes_3'] ? 'opacity-75' : ((!$data['sub_tes_1'] || !$data['sub_tes_2']) ? 'opacity-50' : '') }}">
                <div class="card-header bg-white border-0 py-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle {{ $data['sub_tes_3'] ? 'bg-success' : 'bg-danger' }} bg-opacity-10 p-2 me-3">
                                <i class="{{ $data['sub_tes_3'] ? 'text-success' : 'text-danger' }}" data-feather="{{ $data['sub_tes_3'] ? 'check-circle' : 'box' }}" style="width: 20px; height: 20px;"></i>
                            </div>
                            <h5 class="mb-0">Sub Tes 3</h5>
                        </div>
                        @if($data['sub_tes_3'])
                            <span class="badge bg-success">Selesai</span>
                        @elseif(!$data['sub_tes_1'] || !$data['sub_tes_2'])
                            <span class="badge bg-secondary">Terkunci</span>
                        @endif
                    </div>
                </div>
                <div class="card-body p-4">
                    <h6 class="text-muted mb-3">Petunjuk Pengerjaan:</h6>
                    <p class="small text-muted mb-3">
                        Dalam Subtes 3 disajikan 5 kubus yaitu kubus A, B, C, D dan E. Pada setiap sisi kubus tersebut memiliki tanda yang berbeda-beda.
                    </p>
                    <div class="bg-light p-3 rounded-3 mb-3">
                        <small class="text-muted">
                            Temukan kubus yang sama dari masing-masing soal dengan cara memutar kubus ke kanan/kiri, atau digulingkan kedepan/kebelakang dalam pikiran Anda.
                        </small>
                    </div>
                </div>
                <div class="card-footer bg-white border-0 p-4 pt-0">
                    <button 
                        class="btn {{ $data['sub_tes_3'] ? 'btn-success' : 'btn-danger' }} w-100"
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
                        @disabled((!$data['sub_tes_1'] || !$data['sub_tes_2']) || ($finished_all_test))
                    >
                        <i class="me-2" data-feather="{{ $data['sub_tes_3'] ? 'check' : ((!$data['sub_tes_1'] || !$data['sub_tes_2']) ? 'lock' : 'play') }}"></i>
                        {{ $data['sub_tes_3'] ? 'Sudah Selesai' : ((!$data['sub_tes_1'] || !$data['sub_tes_2']) ? 'Selesaikan Sub Tes Sebelumnya' : 'Mulai Sub Tes 3') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
