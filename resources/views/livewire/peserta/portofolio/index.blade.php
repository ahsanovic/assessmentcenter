<div>
    <div class="d-flex justify-content-end">
        <x-breadcrumb :breadcrumbs="[
            ['url' => route('peserta.dashboard'), 'title' => 'Dashboard'],
            ['url' => null, 'title' => 'Portofolio'],
        ]" />
    </div>

    @if ($portofolio->is_open == 'false')
        <!-- Alert Closed -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm border-start border-4 border-danger">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle bg-danger bg-opacity-10 p-3 me-3">
                                <i class="text-danger" data-feather="x-circle" style="width: 24px; height: 24px;"></i>
                            </div>
                            <div>
                                <h6 class="mb-1 text-danger">Akses Ditutup</h6>
                                <p class="mb-0 text-muted">Pelaksanaan tes masih / sudah ditutup!</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <!-- Alert Info -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm border-start border-4 border-info">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-start">
                            <div class="me-3">
                                <i class="text-info" data-feather="info" style="width: 24px; height: 24px;"></i>
                            </div>
                            <div>
                                <h6 class="mb-2 text-info">Petunjuk Pengisian Portofolio</h6>
                                <p class="mb-0 text-muted">Lengkapi portofolio Anda dengan detail yang mencerminkan perjalanan profesional dan pribadi Anda secara menyeluruh. Mulai dari biodata, pendidikan, pelatihan yang telah diikuti, riwayat karir, pengalaman kerja, hingga penilaian pribadi. Tunjukkan potensi terbaik Anda dan ciptakan kesan mendalam melalui portofolio yang informatif dan menarik!</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            @if ($portofolio->metode_tes_id == 1)
            <x-tab-nav :nav="[
                ['url' => route('peserta.biodata'), 'title' => 'Biodata', 'active' => null],
                ['url' => route('peserta.pendidikan'), 'title' => 'Pendidikan', 'active' => null],
                ['url' => route('peserta.pelatihan'), 'title' => 'Pelatihan', 'active' => null],
                ['url' => route('peserta.karir'), 'title' => 'Karir', 'active' => null],
                ['url' => route('peserta.pengalaman'), 'title' => 'Pengalaman Spesifik', 'active' => null],
                ['url' => route('peserta.penilaian'), 'title' => 'Penilaian Pribadi', 'active' => null],
            ]" />
            @endif
            <div class="col-8 col-md-10 ps-0">
                <div class="tab-content tab-content-vertical border-0 p-0" id="v-tabContent">
                    <div>
                        <!-- Header -->
                        <div class="card border-0 shadow-sm mb-4" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                            <div class="card-body p-4 text-white">
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle bg-white bg-opacity-25 p-3 me-3">
                                        <i data-feather="folder" style="width: 32px; height: 32px;"></i>
                                    </div>
                                    <div>
                                        <h3 class="mb-1">Portofolio Peserta</h3>
                                        <p class="mb-0 opacity-75">Ringkasan data diri dan pengalaman Anda</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Biodata Card -->
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-header bg-white border-0 py-3">
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle bg-primary bg-opacity-10 p-2 me-3">
                                        <i class="text-primary" data-feather="user" style="width: 20px; height: 20px;"></i>
                                    </div>
                                    <h5 class="mb-0 text-primary">Biodata</h5>
                                </div>
                            </div>
                            <div class="card-body p-4">
                                <div class="row">
                                    <div class="col-md-3 text-center mb-4 mb-md-0">
                                        @if ($biodata->foto)
                                            <img src="{{ $biodata->foto }}" class="img-fluid rounded-circle shadow" style="width: 120px; height: 120px; object-fit: cover;">
                                        @else
                                            <div class="rounded-circle bg-primary d-inline-flex align-items-center justify-content-center text-white fw-bold shadow" style="width: 120px; height: 120px; font-size: 3rem;">
                                                {{ strtoupper(substr($biodata->nama ?? 'P', 0, 1)) }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="col-md-9">
                                        <div class="row g-3">
                                            <div class="col-12">
                                                <div class="d-flex align-items-center p-3 bg-light rounded-3">
                                                    <i class="text-muted me-3" data-feather="user"></i>
                                                    <div>
                                                        <small class="text-muted d-block">Nama Lengkap</small>
                                                        <strong>{{ $biodata->gelar_depan ? $biodata->gelar_depan : '' }} {{ $biodata->nama }}{{ $biodata->gelar_belakang ? ', ' . $biodata->gelar_belakang : '' }}</strong>
                                                    </div>
                                                </div>
                                            </div>
                                            @if ($biodata->jenis_peserta_id == 1)
                                            <div class="col-md-6">
                                                <div class="d-flex align-items-center p-3 bg-light rounded-3">
                                                    <i class="text-muted me-3" data-feather="credit-card"></i>
                                                    <div>
                                                        <small class="text-muted d-block">NIP</small>
                                                        <strong>{{ $biodata->nip }}</strong>
                                                    </div>
                                                </div>
                                            </div>
                                            @endif
                                            <div class="col-md-6">
                                                <div class="d-flex align-items-center p-3 bg-light rounded-3">
                                                    <i class="text-muted me-3" data-feather="credit-card"></i>
                                                    <div>
                                                        <small class="text-muted d-block">NIK</small>
                                                        <strong>{{ $biodata->nik }}</strong>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="d-flex align-items-center p-3 bg-light rounded-3">
                                                    <i class="text-muted me-3" data-feather="calendar"></i>
                                                    <div>
                                                        <small class="text-muted d-block">Tempat / Tanggal Lahir</small>
                                                        <strong>{{ $biodata->tempat_lahir }}, {{ $biodata->tgl_lahir }}</strong>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="d-flex align-items-center p-3 bg-light rounded-3">
                                                    <i class="text-muted me-3" data-feather="heart"></i>
                                                    <div>
                                                        <small class="text-muted d-block">Agama</small>
                                                        <strong>{{ $biodata->agama->agama ?? '-' }}</strong>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="d-flex align-items-center p-3 bg-light rounded-3">
                                                    <i class="text-muted me-3" data-feather="users"></i>
                                                    <div>
                                                        <small class="text-muted d-block">Jenis Kelamin</small>
                                                        <strong>{{ $biodata->jk == 'L' ? 'Laki-Laki' : 'Perempuan' }}</strong>
                                                    </div>
                                                </div>
                                            </div>
                                            @if ($biodata->jenis_peserta_id == 1)
                                            <div class="col-md-6">
                                                <div class="d-flex align-items-center p-3 bg-light rounded-3">
                                                    <i class="text-muted me-3" data-feather="award"></i>
                                                    <div>
                                                        <small class="text-muted d-block">Pangkat / Golongan</small>
                                                        <strong>{{ $biodata->golPangkat->pangkat ?? '' }} - {{ $biodata->golPangkat->golongan ?? '' }}</strong>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="d-flex align-items-center p-3 bg-light rounded-3">
                                                    <i class="text-muted me-3" data-feather="briefcase"></i>
                                                    <div>
                                                        <small class="text-muted d-block">Jabatan</small>
                                                        <strong>{{ $biodata->jabatan }}</strong>
                                                    </div>
                                                </div>
                                            </div>
                                            @endif
                                            <div class="col-md-6">
                                                <div class="d-flex align-items-center p-3 bg-light rounded-3">
                                                    <i class="text-muted me-3" data-feather="home"></i>
                                                    <div>
                                                        <small class="text-muted d-block">Unit Kerja</small>
                                                        <strong>{{ $biodata->unit_kerja }}</strong>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="d-flex align-items-center p-3 bg-light rounded-3">
                                                    <i class="text-muted me-3" data-feather="building"></i>
                                                    <div>
                                                        <small class="text-muted d-block">Instansi</small>
                                                        <strong>{{ $biodata->instansi }}</strong>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="d-flex align-items-center p-3 bg-light rounded-3">
                                                    <i class="text-muted me-3" data-feather="map-pin"></i>
                                                    <div>
                                                        <small class="text-muted d-block">Alamat</small>
                                                        <strong>{{ $biodata->alamat }}</strong>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="d-flex align-items-center p-3 bg-light rounded-3">
                                                    <i class="text-muted me-3" data-feather="phone"></i>
                                                    <div>
                                                        <small class="text-muted d-block">Nomor HP</small>
                                                        <strong>{{ $biodata->no_hp }}</strong>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if ($portofolio->metode_tes_id == 1)
                        <!-- Pendidikan Card -->
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-header bg-white border-0 py-3">
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle bg-success bg-opacity-10 p-2 me-3">
                                        <i class="text-success" data-feather="book" style="width: 20px; height: 20px;"></i>
                                    </div>
                                    <h5 class="mb-0 text-success">Pendidikan Formal</h5>
                                </div>
                            </div>
                            <div class="card-body p-4">
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="border-0 rounded-start">#</th>
                                                <th class="border-0">Jenjang</th>
                                                <th class="border-0">Nama Sekolah</th>
                                                <th class="border-0">Tahun</th>
                                                <th class="border-0">Jurusan</th>
                                                <th class="border-0 rounded-end">IPK / Nilai</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($pendidikan as $index => $item)
                                                <tr>
                                                    <td><span class="badge bg-success">{{ $index + 1 }}</span></td>
                                                    <td>{{ $item->jenjangPendidikan->jenjang ?? '' }}</td>
                                                    <td class="text-wrap">{{ $item->nama_sekolah }}</td>
                                                    <td>{{ $item->thn_masuk }} - {{ $item->thn_lulus }}</td>
                                                    <td class="text-wrap">{{ $item->jurusan }}</td>
                                                    <td><span class="badge bg-primary">{{ $item->ipk }}</span></td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="6" class="text-center py-4">
                                                        <div class="text-muted">
                                                            <i data-feather="inbox" style="width: 48px; height: 48px;"></i>
                                                            <p class="mt-2 mb-0">Tidak ada data pendidikan</p>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Pelatihan Card -->
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-header bg-white border-0 py-3">
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle bg-warning bg-opacity-10 p-2 me-3">
                                        <i class="text-warning" data-feather="award" style="width: 20px; height: 20px;"></i>
                                    </div>
                                    <h5 class="mb-0 text-warning">Pelatihan / Kursus</h5>
                                </div>
                            </div>
                            <div class="card-body p-4">
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="border-0 rounded-start">#</th>
                                                <th class="border-0">Nama Institusi</th>
                                                <th class="border-0">Tanggal Pelatihan</th>
                                                <th class="border-0 rounded-end">Subjek Pelatihan</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($pelatihan as $index => $item)
                                                <tr>
                                                    <td><span class="badge bg-warning text-dark">{{ $index + 1 }}</span></td>
                                                    <td class="text-wrap">{{ $item->nama_institusi }}</td>
                                                    <td>{{ $item->tgl_mulai }} - {{ $item->tgl_selesai }}</td>
                                                    <td class="text-wrap">{{ $item->subjek_pelatihan }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="4" class="text-center py-4">
                                                        <div class="text-muted">
                                                            <i data-feather="inbox" style="width: 48px; height: 48px;"></i>
                                                            <p class="mt-2 mb-0">Tidak ada data pelatihan</p>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Riwayat Karir Card -->
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-header bg-white border-0 py-3">
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle bg-info bg-opacity-10 p-2 me-3">
                                        <i class="text-info" data-feather="briefcase" style="width: 20px; height: 20px;"></i>
                                    </div>
                                    <h5 class="mb-0 text-info">Riwayat Karir</h5>
                                </div>
                            </div>
                            <div class="card-body p-4">
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="border-0 rounded-start">#</th>
                                                <th class="border-0">Jangka Waktu</th>
                                                <th class="border-0">Instansi</th>
                                                <th class="border-0">Jabatan</th>
                                                <th class="border-0 rounded-end">Uraian Tugas</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($karir as $index => $item)
                                                <tr>
                                                    <td><span class="badge bg-info">{{ $index + 1 }}</span></td>
                                                    <td>
                                                        <small>
                                                            {{ \Carbon\Carbon::create()->month($item->bulan_mulai)->translatedFormat('F') }} {{ $item->tahun_mulai }}<br>
                                                            <span class="text-muted">s/d</span><br>
                                                            {{ \Carbon\Carbon::create()->month($item->bulan_selesai)->translatedFormat('F') }} {{ $item->tahun_selesai }}
                                                        </small>
                                                    </td>
                                                    <td class="text-wrap">{{ $item->instansi }}</td>
                                                    <td class="text-wrap">{{ $item->jabatan }}</td>
                                                    <td class="text-wrap">{{ $item->uraian_tugas }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="5" class="text-center py-4">
                                                        <div class="text-muted">
                                                            <i data-feather="inbox" style="width: 48px; height: 48px;"></i>
                                                            <p class="mt-2 mb-0">Tidak ada data riwayat karir</p>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Pengalaman Spesifik Card -->
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-header bg-white border-0 py-3">
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle bg-danger bg-opacity-10 p-2 me-3">
                                        <i class="text-danger" data-feather="star" style="width: 20px; height: 20px;"></i>
                                    </div>
                                    <h5 class="mb-0 text-danger">Pengalaman Spesifik</h5>
                                </div>
                            </div>
                            <div class="card-body p-4">
                                @forelse ($pertanyaan as $index => $item)
                                    <div class="p-3 bg-light rounded-3 mb-3">
                                        <div class="d-flex align-items-start">
                                            <span class="badge bg-danger rounded-circle me-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 28px; height: 28px;">
                                                {{ $index + 1 }}
                                            </span>
                                            <div>
                                                <h6 class="mb-2">{{ $item->pertanyaan }}</h6>
                                                <p class="mb-0 text-muted">{!! $item->jawaban->first()->jawaban ?? '<em>Belum dijawab</em>' !!}</p>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-4 text-muted">
                                        <i data-feather="inbox" style="width: 48px; height: 48px;"></i>
                                        <p class="mt-2 mb-0">Tidak ada data pengalaman spesifik</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>

                        <!-- Penilaian Pribadi Card -->
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-header bg-white border-0 py-3">
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle p-2 me-3" style="background-color: rgba(111, 66, 193, 0.1);">
                                        <i style="color: #6f42c1; width: 20px; height: 20px;" data-feather="edit-3"></i>
                                    </div>
                                    <h5 class="mb-0" style="color: #6f42c1;">Penilaian Pribadi</h5>
                                </div>
                            </div>
                            <div class="card-body p-4">
                                @forelse ($penilaian as $index => $item)
                                    <div class="p-3 bg-light rounded-3 mb-3">
                                        <div class="d-flex align-items-start">
                                            <span class="badge rounded-circle me-3 d-flex align-items-center justify-content-center flex-shrink-0 text-white" style="width: 28px; height: 28px; background-color: #6f42c1;">
                                                {{ $index + 1 }}
                                            </span>
                                            <div>
                                                <h6 class="mb-2">{{ $item->pertanyaan }}</h6>
                                                <p class="mb-0 text-muted">{!! $item->jawaban->first()->jawaban ?? '<em>Belum dijawab</em>' !!}</p>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-4 text-muted">
                                        <i data-feather="inbox" style="width: 48px; height: 48px;"></i>
                                        <p class="mt-2 mb-0">Tidak ada data penilaian pribadi</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
