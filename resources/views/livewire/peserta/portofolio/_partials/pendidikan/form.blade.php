<div>
    <!-- Breadcrumb -->
    <div class="d-flex justify-content-end">
        <x-breadcrumb :breadcrumbs="[
            ['url' => route('peserta.dashboard'), 'title' => 'Dashboard'],
            ['url' => route('peserta.portofolio'), 'title' => 'Portofolio'],
            ['url' => route('peserta.pendidikan'), 'title' => 'Pendidikan'],
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
    <div class="card border-0 shadow-sm mb-4 border-start border-4 border-success">
        <div class="card-body p-3">
            <div class="d-flex align-items-start">
                <div class="rounded-circle bg-success bg-opacity-10 p-2 me-3 flex-shrink-0" wire:ignore>
                    <i data-feather="book-open" class="text-success" style="width: 20px; height: 20px;"></i>
                </div>
                <div>
                    <strong class="text-success d-block mb-1">Riwayat Pendidikan</strong>
                    <small class="text-muted">
                        Masukkan pendidikan formal dari tingkat SMA/SLTA/Sederajat hingga pendidikan terakhir
                    </small>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="row">
            <x-tab-nav :nav="[
                ['url' => route('peserta.biodata'), 'title' => 'Biodata', 'active' => null],
                ['url' => route('peserta.pendidikan'), 'title' => 'Pendidikan', 'active' => 'active'],
                ['url' => route('peserta.pelatihan'), 'title' => 'Pelatihan', 'active' => null],
                ['url' => route('peserta.karir'), 'title' => 'Karir', 'active' => null],
                ['url' => route('peserta.pengalaman'), 'title' => 'Pengalaman Spesifik', 'active' => null],
                ['url' => route('peserta.penilaian'), 'title' => 'Penilaian Pribadi', 'active' => null],
            ]" />
            <div class="col-8 col-md-10 ps-0">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3 border-0">
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle bg-success bg-opacity-10 p-2 me-3" wire:ignore>
                                <i data-feather="{{ $isUpdate ? 'edit' : 'plus-circle' }}" class="text-success" style="width: 20px; height: 20px;"></i>
                            </div>
                            <h5 class="mb-0 fw-semibold">{{ $isUpdate ? 'Edit' : 'Tambah' }} Data Pendidikan</h5>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <form wire:submit="save">
                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="mb-3">
                                        <label for="gol-pangkat" class="form-label fw-medium">
                                            <span wire:ignore><i data-feather="layers" style="width: 14px; height: 14px;" class="me-1"></i></span>
                                            Jenjang Pendidikan <span class="text-danger">*</span>
                                        </label>
                                        <select wire:model="form.jenjang_pendidikan_id"
                                            class="form-select @error('form.jenjang_pendidikan_id') is-invalid @enderror"
                                            id="gol-pangkat">
                                            <option value="">- Pilih Jenjang -</option>
                                            @foreach ($option_jenjang_pendidikan as $id => $item)
                                                <option value="{{ $id }}">{{ $item }}</option>
                                            @endforeach
                                        </select>
                                        @error('form.jenjang_pendidikan_id')
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
                                            Nama Sekolah / Universitas <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" wire:model="form.nama_sekolah"
                                            class="form-control @error('form.nama_sekolah') is-invalid @enderror"
                                            placeholder="Contoh: Universitas Indonesia">
                                        @error('form.nama_sekolah')
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
                                            Tahun Masuk <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" wire:model="form.thn_masuk"
                                            class="form-control @error('form.thn_masuk') is-invalid @enderror"
                                            placeholder="Contoh: 2015">
                                        @error('form.thn_masuk')
                                            <label class="error invalid-feedback">{{ $message }}</label>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="mb-3">
                                        <label class="form-label fw-medium">
                                            <span wire:ignore><i data-feather="calendar" style="width: 14px; height: 14px;" class="me-1"></i></span>
                                            Tahun Lulus <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" wire:model="form.thn_lulus"
                                            class="form-control @error('form.thn_lulus') is-invalid @enderror"
                                            placeholder="Contoh: 2019">
                                        @error('form.thn_lulus')
                                            <label class="error invalid-feedback">{{ $message }}</label>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="mb-3">
                                        <label class="form-label fw-medium">
                                            <span wire:ignore><i data-feather="bookmark" style="width: 14px; height: 14px;" class="me-1"></i></span>
                                            Jurusan / Program Studi <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" wire:model="form.jurusan"
                                            class="form-control @error('form.jurusan') is-invalid @enderror"
                                            placeholder="Contoh: Teknik Informatika">
                                        @error('form.jurusan')
                                            <label class="error invalid-feedback">{{ $message }}</label>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-3">
                                    <div class="mb-3">
                                        <label class="form-label fw-medium">
                                            <span wire:ignore><i data-feather="award" style="width: 14px; height: 14px;" class="me-1"></i></span>
                                            IPK / Nilai <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" wire:model="form.ipk"
                                            class="form-control @error('form.ipk') is-invalid @enderror"
                                            placeholder="Contoh: 3.75">
                                        @error('form.ipk')
                                            <label class="error invalid-feedback">{{ $message }}</label>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <hr class="my-4">

                            <x-form-action 
                                :cancelUrl="route('peserta.pendidikan')" 
                                :isUpdate="$isUpdate == true" 
                            />
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
