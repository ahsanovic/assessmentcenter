<div>
    <!-- Breadcrumb -->
    <div class="d-flex justify-content-end">
        <x-breadcrumb :breadcrumbs="[
            ['url' => route('peserta.dashboard'), 'title' => 'Dashboard'],
            ['url' => route('peserta.portofolio'), 'title' => 'Portofolio'],
            ['url' => null, 'title' => 'Pendidikan'],
        ]" />
    </div>

    <!-- Header Card -->
    <x-portofolio.header-card />

    <!-- Info Alert -->
    <x-portofolio.alert-info
        title="Petunjuk Pengisian Portofolio"
        description="Lengkapi portofolio Anda dengan detail yang mencerminkan perjalanan profesional dan pribadi Anda secara menyeluruh. Mulai dari biodata, pendidikan, pelatihan yang telah diikuti, riwayat karir, pengalaman kerja, hingga penilaian pribadi. Tunjukkan potensi terbaik Anda dan ciptakan kesan mendalam melalui portofolio yang informatif dan menarik!"
        icon="info"
        color="info" />

    <div class="row">
        <div class="row">
            <x-portofolio.tab-nav :nav="[
                ['url' => route('peserta.biodata'), 'title' => 'Biodata', 'active' => null, 'icon' => 'user', 'color' => 'primary'],
                ['url' => route('peserta.pendidikan'), 'title' => 'Pendidikan', 'active' => 'active', 'icon' => 'book', 'color' => 'success'],
                ['url' => route('peserta.pelatihan'), 'title' => 'Pelatihan', 'active' => null, 'icon' => 'award', 'color' => 'warning'],
                ['url' => route('peserta.karir'), 'title' => 'Karir', 'active' => null, 'icon' => 'briefcase', 'color' => 'info'],
                ['url' => route('peserta.pengalaman'), 'title' => 'Pengalaman Spesifik', 'active' => null, 'icon' => 'star', 'color' => 'danger'],
                ['url' => route('peserta.penilaian'), 'title' => 'Penilaian Pribadi', 'active' => null, 'icon' => 'user-check', 'color' => 'primary'],
            ]" />
            <div class="col-8 col-md-10 ps-0">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3 border-0">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <div class="rounded-circle bg-success bg-opacity-10 p-2 me-3" wire:ignore>
                                    <i data-feather="book-open" class="text-success" style="width: 20px; height: 20px;"></i>
                                </div>
                                <div>
                                    <h5 class="mb-0 fw-semibold">Riwayat Pendidikan</h5>
                                    <small class="text-muted">Pendidikan formal dari tingkat SMA/SLTA/Sederajat</small>
                                </div>
                            </div>
                            <a href="{{ route('peserta.pendidikan.create') }}" wire:navigate
                                class="btn btn-primary btn-sm">
                                <span wire:ignore><i data-feather="plus" style="width: 16px; height: 16px;" class="me-1"></i></span>
                                Tambah Data
                            </a>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        @if(count($pendidikan) > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-4" style="width: 50px;">#</th>
                                        <th>Jenjang Pendidikan</th>
                                        <th>Nama Sekolah</th>
                                        <th>Tahun</th>
                                        <th>Jurusan</th>
                                        <th class="text-center">IPK/Nilai</th>
                                        <th class="text-center" style="width: 120px;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($pendidikan as $index => $item)
                                        <tr>
                                            <td class="ps-4">
                                                <span class="badge bg-primary rounded-pill">{{ $index + 1 }}</span>
                                            </td>
                                            <td>
                                                <span class="badge bg-info bg-opacity-10 text-info">
                                                    {{ $item->jenjangPendidikan->jenjang ?? '-' }}
                                                </span>
                                            </td>
                                            <td class="text-wrap">
                                                <strong>{{ $item->nama_sekolah }}</strong>
                                            </td>
                                            <td>
                                                <small class="text-muted">{{ $item->thn_masuk }} - {{ $item->thn_lulus }}</small>
                                            </td>
                                            <td class="text-wrap">{{ $item->jurusan }}</td>
                                            <td class="text-center">
                                                <span class="badge bg-success">{{ $item->ipk }}</span>
                                            </td>
                                            <td class="text-center">
                                                <div class="btn-group btn-group-sm">
                                                    <a class="btn btn-outline-warning" wire:navigate
                                                        href="{{ route('peserta.pendidikan.edit', $item->id) }}">
                                                        <span wire:ignore><i data-feather="edit-2" style="width: 14px; height: 14px;"></i></span>
                                                    </a>
                                                    <button wire:click="deleteConfirmation('{{ $item->id }}')"
                                                        class="btn btn-outline-danger">
                                                        <span wire:ignore><i data-feather="trash-2" style="width: 14px; height: 14px;"></i></span>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                        <div class="text-center py-5">
                            <div class="rounded-circle bg-light p-4 d-inline-flex mb-3" wire:ignore>
                                <i data-feather="inbox" style="width: 48px; height: 48px;" class="text-muted"></i>
                            </div>
                            <h6 class="text-muted">Belum ada data pendidikan</h6>
                            <p class="text-muted small mb-3">Klik tombol "Tambah Data" untuk menambahkan riwayat pendidikan Anda</p>
                            <a href="{{ route('peserta.pendidikan.create') }}" wire:navigate class="btn btn-primary btn-sm">
                                <span wire:ignore><i data-feather="plus" style="width: 16px; height: 16px;" class="me-1"></i></span>
                                Tambah Pendidikan
                            </a>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
