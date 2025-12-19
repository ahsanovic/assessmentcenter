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
    <div class="card border-0 shadow-sm mb-4" 
        style="background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);">
        <div class="card-body p-4">
            <div class="d-flex align-items-center">
                <div class="rounded-circle p-3 me-3"
                    style="background: rgba(102, 126, 234, 0.13); color: #667eea;" wire:ignore>
                    <i data-feather="folder" style="width: 32px; height: 32px;"></i>
                </div>
                <div>
                    <h3 class="mb-1" style="color: #3c3264; font-weight: 700;">
                        Kelengkapan Portofolio
                    </h3>
                    <p class="mb-0" style="color: #585e74; opacity: .85; font-weight: 500;">
                        Lengkapi data diri Anda dengan teliti
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Info Alert -->
    <div class="card border-0 shadow-sm mb-4 border-start border-4 border-warning">
        <div class="card-body p-3">
            <div class="d-flex align-items-start">
                <div class="rounded-circle bg-warning bg-opacity-10 p-2 me-3 flex-shrink-0" wire:ignore>
                    <i data-feather="award" class="text-warning" style="width: 20px; height: 20px;"></i>
                </div>
                <div>
                    <strong class="text-warning d-block mb-1">Riwayat Pelatihan</strong>
                    <small class="text-muted">
                        Masukkan riwayat pelatihan 5 tahun terakhir yang relevan dengan posisi yang dilamar
                    </small>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="row">
            <x-tab-nav :nav="[
                ['url' => route('peserta.biodata'), 'title' => 'Biodata', 'active' => null],
                ['url' => route('peserta.pendidikan'), 'title' => 'Pendidikan', 'active' => null],
                ['url' => route('peserta.pelatihan'), 'title' => 'Pelatihan', 'active' => 'active'],
                ['url' => route('peserta.karir'), 'title' => 'Karir', 'active' => null],
                ['url' => route('peserta.pengalaman'), 'title' => 'Pengalaman Spesifik', 'active' => null],
                ['url' => route('peserta.penilaian'), 'title' => 'Penilaian Pribadi', 'active' => null],
            ]" />
            <div class="col-8 col-md-10 ps-0">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3 border-0">
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle bg-warning bg-opacity-10 p-2 me-3" wire:ignore>
                                <i data-feather="{{ $isUpdate ? 'edit' : 'plus-circle' }}" class="text-warning" style="width: 20px; height: 20px;"></i>
                            </div>
                            <h5 class="mb-0 fw-semibold">{{ $isUpdate ? 'Edit' : 'Tambah' }} Data Pelatihan</h5>
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
                                        <div class="input-group flatpickr" id="flatpickr-date">
                                            <input type="text" wire:model="form.tgl_mulai"
                                                class="form-control flatpickr-input @error('form.tgl_mulai') is-invalid @enderror"
                                                placeholder="Pilih tanggal" data-input="" readonly="readonly">
                                            <span class="input-group-text input-group-addon bg-light" data-toggle="">
                                                <i data-feather="calendar" style="width: 18px; height: 18px;"></i>
                                            </span>
                                            @error('form.tgl_mulai')
                                                <label class="error invalid-feedback">{{ $message }}</label>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="mb-3">
                                        <label class="form-label fw-medium">
                                            <span wire:ignore><i data-feather="calendar" style="width: 14px; height: 14px;" class="me-1"></i></span>
                                            Tanggal Selesai <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-group flatpickr" id="flatpickr-date2">
                                            <input type="text" wire:model="form.tgl_selesai"
                                                class="form-control flatpickr-input @error('form.tgl_selesai') is-invalid @enderror"
                                                placeholder="Pilih tanggal" data-input="" readonly="readonly">
                                            <span class="input-group-text input-group-addon bg-light" data-toggle="">
                                                <i data-feather="calendar" style="width: 18px; height: 18px;"></i>
                                            </span>
                                            @error('form.tgl_selesai')
                                                <label class="error invalid-feedback">{{ $message }}</label>
                                            @enderror
                                        </div>
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
