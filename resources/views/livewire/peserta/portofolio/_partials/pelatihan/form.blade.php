<div>
    <!-- Breadcrumb -->
    <div class="d-flex justify-content-end">
        <x-breadcrumb :breadcrumbs="[
            ['url' => route('peserta.dashboard'), 'title' => 'Dashboard'],
            ['url' => route('peserta.portofolio'), 'title' => 'Portofolio'],
            ['url' => route('peserta.pelatihan'), 'title' => 'Pelatihan'],
            ['url' => null, 'title' => $isUpdate ? 'Edit' : 'Tambah'],
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
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle bg-warning bg-opacity-10 p-2 me-3" wire:ignore>
                                <i data-feather="{{ $isUpdate ? 'edit' : 'plus-circle' }}" class="text-warning" style="width: 20px; height: 20px;"></i>
                            </div>
                            <div>
                                <h5 class="mb-0 fw-semibold">{{ $isUpdate ? 'Edit' : 'Tambah' }} Data Pelatihan</h5>
                                <small class="text-muted">Riwayat pelatihan 5 tahun terakhir</small>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <form wire:submit="save">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="mb-3">
                                        <label class="form-label fw-medium">
                                            <span wire:ignore><i data-feather="home" style="width: 14px; height: 14px;" class="me-1"></i></span>
                                            Nama Institusi <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" wire:model="form.nama_institusi"
                                            class="form-control @error('form.nama_institusi') is-invalid @enderror"
                                            placeholder="Contoh: Lembaga Administrasi Negara">
                                        @error('form.nama_institusi')
                                            <label class="error invalid-feedback">{{ $message }}</label>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="mb-3">
                                        <label class="form-label fw-medium">
                                            <span wire:ignore><i data-feather="calendar" style="width: 14px; height: 14px;" class="me-1"></i></span>
                                            Tanggal Mulai <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-group" wire:ignore>
                                            <input type="text" data-flatpickr wire:model="form.tgl_mulai"
                                                class="form-control @error('form.tgl_mulai') is-invalid @enderror"
                                                placeholder="Pilih tanggal" readonly="readonly">
                                            <span class="input-group-text bg-light">
                                                <i data-feather="calendar" style="width: 18px; height: 18px;"></i>
                                            </span>
                                        </div>
                                        @error('form.tgl_mulai')
                                            <label class="error invalid-feedback d-block">{{ $message }}</label>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="mb-3">
                                        <label class="form-label fw-medium">
                                            <span wire:ignore><i data-feather="calendar" style="width: 14px; height: 14px;" class="me-1"></i></span>
                                            Tanggal Selesai <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-group" wire:ignore>
                                            <input type="text" data-flatpickr wire:model="form.tgl_selesai"
                                                class="form-control @error('form.tgl_selesai') is-invalid @enderror"
                                                placeholder="Pilih tanggal" readonly="readonly">
                                            <span class="input-group-text bg-light">
                                                <i data-feather="calendar" style="width: 18px; height: 18px;"></i>
                                            </span>
                                        </div>
                                        @error('form.tgl_selesai')
                                            <label class="error invalid-feedback d-block">{{ $message }}</label>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="mb-3">
                                        <label class="form-label fw-medium">
                                            <span wire:ignore><i data-feather="book" style="width: 14px; height: 14px;" class="me-1"></i></span>
                                            Subjek Pelatihan <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" wire:model="form.subjek_pelatihan"
                                            class="form-control @error('form.subjek_pelatihan') is-invalid @enderror"
                                            placeholder="Contoh: Pelatihan Kepemimpinan Administrator">
                                        @error('form.subjek_pelatihan')
                                            <label class="error invalid-feedback">{{ $message }}</label>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <hr class="my-4">

                            <x-form-action 
                                :cancelUrl="route('peserta.pelatihan')" 
                                :isUpdate="$isUpdate == true" 
                            />
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
