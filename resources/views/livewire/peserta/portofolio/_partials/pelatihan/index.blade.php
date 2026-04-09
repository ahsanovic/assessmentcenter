<div>
    <!-- Breadcrumb -->
    <div class="d-flex justify-content-end">
        <x-breadcrumb :breadcrumbs="[
            ['url' => route('peserta.dashboard'), 'title' => 'Dashboard'],
            ['url' => route('peserta.portofolio'), 'title' => 'Portofolio'],
            ['url' => null, 'title' => 'Pelatihan'],
        ]" />
    </div>

    <!-- Header Card -->
    <x-portofolio.header-card />

    <x-portofolio.progress :progress="$portofolioProgress" />

    <div class="row">
        <div class="row">
            <x-portofolio.tab-nav :nav="[
                ['url' => route('peserta.biodata'), 'title' => 'Biodata', 'active' => null, 'icon' => 'user', 'color' => 'primary'],
                ['url' => route('peserta.pendidikan'), 'title' => 'Pendidikan', 'active' => null, 'icon' => 'book', 'color' => 'success'],
                ['url' => route('peserta.pelatihan'), 'title' => 'Pelatihan', 'active' => 'active', 'icon' => 'award', 'color' => 'warning'],
                ['url' => route('peserta.karir'), 'title' => 'Karir', 'active' => null, 'icon' => 'briefcase', 'color' => 'info'],
                ['url' => route('peserta.pengalaman'), 'title' => 'Pengalaman Spesifik', 'active' => null, 'icon' => 'star', 'color' => 'danger'],
                ['url' => route('peserta.penilaian'), 'title' => 'Penilaian Pribadi', 'active' => null, 'icon' => 'user-check', 'color' => 'primary'],
            ]" />
            <div class="col-8 col-md-10 ps-0">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3 border-0">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <div class="rounded-circle bg-warning bg-opacity-10 p-2 me-3" wire:ignore>
                                    <i data-feather="award" class="text-warning" style="width: 20px; height: 20px;"></i>
                                </div>
                                <div>
                                    <h5 class="mb-0 fw-semibold">Riwayat Pelatihan</h5>
                                    <small class="text-muted">Riwayat pelatihan 5 tahun terakhir</small>
                                </div>
                            </div>
                            <a href="{{ route('peserta.pelatihan.create') }}" wire:navigate
                                class="btn btn-primary btn-sm">
                                <span wire:ignore><i data-feather="plus" style="width: 16px; height: 16px;" class="me-1"></i></span>
                                Tambah Data
                            </a>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        @if(count($pelatihan) > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-4" style="width: 50px;">#</th>
                                        <th>Nama Institusi</th>
                                        <th>Tanggal Pelatihan</th>
                                        <th>Subjek Pelatihan</th>
                                        <th class="text-center" style="width: 120px;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($pelatihan as $index => $item)
                                        <tr>
                                            <td class="ps-4">
                                                <span class="badge bg-warning text-dark rounded-pill">{{ $index + 1 }}</span>
                                            </td>
                                            <td class="text-wrap">
                                                <strong>{{ $item->nama_institusi }}</strong>
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    {{ \Carbon\Carbon::parse($item->tgl_mulai)->translatedFormat('d M Y') }}
                                                    <br>s/d<br>
                                                    {{ \Carbon\Carbon::parse($item->tgl_selesai)->translatedFormat('d M Y') }}
                                                </small>
                                            </td>
                                            <td class="text-wrap">{{ $item->subjek_pelatihan }}</td>
                                            <td class="text-center">
                                                <div class="btn-group btn-group-sm">
                                                    <x-portofolio.btn-edit :id="$item->id" route="peserta.pelatihan.edit" />
                                                    <x-portofolio.btn-delete :id="$item->id" action="deleteConfirmation('{{ $item->id }}')" />
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
                            <h6 class="text-muted">Belum ada data pelatihan</h6>
                            <p class="text-muted small mb-3">Klik tombol "Tambah Data" untuk menambahkan riwayat pelatihan Anda</p>
                            <a href="{{ route('peserta.pelatihan.create') }}" wire:navigate class="btn btn-primary btn-sm">
                                <span wire:ignore><i data-feather="plus" style="width: 16px; height: 16px;" class="me-1"></i></span>
                                Tambah Pelatihan
                            </a>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
