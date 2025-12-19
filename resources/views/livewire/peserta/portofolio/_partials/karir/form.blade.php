<div>
    <!-- Breadcrumb -->
    <div class="d-flex justify-content-end">
        <x-breadcrumb :breadcrumbs="[
            ['url' => route('peserta.dashboard'), 'title' => 'Dashboard'],
            ['url' => route('peserta.portofolio'), 'title' => 'Portofolio'],
            ['url' => route('peserta.karir'), 'title' => 'Karir'],
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
    <div class="card border-0 shadow-sm mb-4 border-start border-4 border-danger">
        <div class="card-body p-3">
            <div class="d-flex align-items-start">
                <div class="rounded-circle bg-danger bg-opacity-10 p-2 me-3 flex-shrink-0" wire:ignore>
                    <i data-feather="briefcase" class="text-danger" style="width: 20px; height: 20px;"></i>
                </div>
                <div>
                    <strong class="text-danger d-block mb-1">Riwayat Karir</strong>
                    <small class="text-muted">
                        Masukkan riwayat karir 5 tahun terakhir termasuk jabatan dan tanggung jawab Anda
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
                ['url' => route('peserta.pelatihan'), 'title' => 'Pelatihan', 'active' => null],
                ['url' => null, 'title' => 'Karir', 'active' => 'active'],
                ['url' => route('peserta.pengalaman'), 'title' => 'Pengalaman Spesifik', 'active' => null],
                ['url' => route('peserta.penilaian'), 'title' => 'Penilaian Pribadi', 'active' => null],
            ]" />
            <div class="col-8 col-md-10 ps-0">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3 border-0">
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle bg-danger bg-opacity-10 p-2 me-3" wire:ignore>
                                <i data-feather="{{ $isUpdate ? 'edit' : 'plus-circle' }}" class="text-danger" style="width: 20px; height: 20px;"></i>
                            </div>
                            <h5 class="mb-0 fw-semibold">{{ $isUpdate ? 'Edit' : 'Tambah' }} Data Karir</h5>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <form wire:submit="save">
                            <div class="row">
                                <div class="col-sm-3">
                                    <div class="mb-3">
                                        <label class="form-label fw-medium">
                                            <span wire:ignore><i data-feather="calendar" style="width: 14px; height: 14px;" class="me-1"></i></span>
                                            Bulan Mulai <span class="text-danger">*</span>
                                        </label>
                                        <select wire:model="form.bulan_mulai"
                                            class="form-select @error('form.bulan_mulai') is-invalid @enderror"
                                            id="bulan-mulai">
                                            <option value="">- Pilih Bulan -</option>
                                            @for ($i = 1; $i <= 12; $i++)
                                                <option value="{{ $i }}">
                                                    {{ \Carbon\Carbon::create()->month($i)->translatedFormat('F') }}
                                                </option>
                                            @endfor
                                        </select>
                                        @error('form.bulan_mulai')
                                            <label class="error invalid-feedback">{{ $message }}</label>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="mb-3">
                                        <label class="form-label fw-medium">
                                            <span wire:ignore><i data-feather="calendar" style="width: 14px; height: 14px;" class="me-1"></i></span>
                                            Tahun Mulai <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" wire:model="form.tahun_mulai"
                                            class="form-control @error('form.tahun_mulai') is-invalid @enderror"
                                            placeholder="Contoh: 2020">
                                        @error('form.tahun_mulai')
                                            <label class="error invalid-feedback">{{ $message }}</label>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-3">
                                    <div class="mb-3">
                                        <label class="form-label fw-medium">
                                            <span wire:ignore><i data-feather="calendar" style="width: 14px; height: 14px;" class="me-1"></i></span>
                                            Bulan Selesai <span class="text-danger">*</span>
                                        </label>
                                        <select wire:model="form.bulan_selesai"
                                            class="form-select @error('form.bulan_selesai') is-invalid @enderror"
                                            id="bulan-selesai">
                                            <option value="">- Pilih Bulan -</option>
                                            @for ($i = 1; $i <= 12; $i++)
                                                <option value="{{ $i }}">
                                                    {{ \Carbon\Carbon::create()->month($i)->translatedFormat('F') }}
                                                </option>
                                            @endfor
                                        </select>
                                        @error('form.bulan_selesai')
                                            <label class="error invalid-feedback">{{ $message }}</label>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="mb-3">
                                        <label class="form-label fw-medium">
                                            <span wire:ignore><i data-feather="calendar" style="width: 14px; height: 14px;" class="me-1"></i></span>
                                            Tahun Selesai <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" wire:model="form.tahun_selesai"
                                            class="form-control @error('form.tahun_selesai') is-invalid @enderror"
                                            placeholder="Contoh: 2024">
                                        @error('form.tahun_selesai')
                                            <label class="error invalid-feedback">{{ $message }}</label>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="mb-3">
                                        <label class="form-label fw-medium">
                                            <span wire:ignore><i data-feather="user-check" style="width: 14px; height: 14px;" class="me-1"></i></span>
                                            Jabatan <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" wire:model="form.jabatan"
                                            class="form-control @error('form.jabatan') is-invalid @enderror"
                                            placeholder="Contoh: Kepala Bidang Perencanaan">
                                        @error('form.jabatan')
                                            <label class="error invalid-feedback">{{ $message }}</label>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="mb-3">
                                        <label class="form-label fw-medium">
                                            <span wire:ignore><i data-feather="home" style="width: 14px; height: 14px;" class="me-1"></i></span>
                                            Instansi <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" wire:model="form.instansi"
                                            class="form-control @error('form.instansi') is-invalid @enderror"
                                            placeholder="Contoh: Badan Kepegawaian Daerah Kota Surabaya">
                                        @error('form.instansi')
                                            <label class="error invalid-feedback">{{ $message }}</label>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="mb-3">
                                        <label class="form-label fw-medium">
                                            <span wire:ignore><i data-feather="file-text" style="width: 14px; height: 14px;" class="me-1"></i></span>
                                            Uraian Tugas / Tanggung Jawab <span class="text-danger">*</span>
                                        </label>
                                        <textarea class="form-control @error('form.uraian_tugas') is-invalid @enderror" 
                                            wire:model="form.uraian_tugas"
                                            rows="4"
                                            placeholder="Jelaskan tugas dan tanggung jawab utama Anda pada posisi tersebut..."></textarea>
                                        @error('form.uraian_tugas')
                                            <label class="error invalid-feedback">{{ $message }}</label>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <hr class="my-4">

                            <x-form-action 
                                :cancelUrl="route('peserta.karir')" 
                                :isUpdate="$isUpdate == true" 
                            />
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
