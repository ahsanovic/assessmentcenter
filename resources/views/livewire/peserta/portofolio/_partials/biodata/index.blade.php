<div>
    <!-- Breadcrumb -->
    <div class="d-flex justify-content-end">
        <x-breadcrumb :breadcrumbs="[
            ['url' => route('peserta.dashboard'), 'title' => 'Dashboard'],
            ['url' => route('peserta.portofolio'), 'title' => 'Portofolio'],
            ['url' => null, 'title' => 'Biodata'],
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
    <div class="card border-0 shadow-sm mb-4 border-start border-4 border-info">
        <div class="card-body p-3">
            <div class="d-flex align-items-start">
                <div class="rounded-circle bg-info bg-opacity-10 p-2 me-3 flex-shrink-0" wire:ignore>
                    <i data-feather="info" class="text-info" style="width: 20px; height: 20px;"></i>
                </div>
                <div>
                    <strong class="text-info d-block mb-1">Petunjuk Pengisian</strong>
                    <small class="text-muted">
                        Lengkapi portofolio Anda dengan detail yang mencerminkan perjalanan profesional dan pribadi Anda secara menyeluruh.
                        Mulai dari biodata, pendidikan, pelatihan yang telah diikuti, riwayat karir, pengalaman kerja, hingga penilaian pribadi.
                    </small>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="row">
            @if ($portofolio->metode_tes_id == 1)
            <x-tab-nav :nav="[
                ['url' => route('peserta.biodata'), 'title' => 'Biodata', 'active' => 'active'],
                ['url' => route('peserta.pendidikan'), 'title' => 'Pendidikan', 'active' => null],
                ['url' => route('peserta.pelatihan'), 'title' => 'Pelatihan', 'active' => null],
                ['url' => route('peserta.karir'), 'title' => 'Karir', 'active' => null],
                ['url' => route('peserta.pengalaman'), 'title' => 'Pengalaman Spesifik', 'active' => null],
                ['url' => route('peserta.penilaian'), 'title' => 'Penilaian Pribadi', 'active' => null],
            ]" />
            @elseif ($portofolio->metode_tes_id == 2 || $portofolio->metode_tes_id == 4)
            <x-tab-nav :nav="[
                ['url' => route('peserta.biodata'), 'title' => 'Biodata', 'active' => 'active'],
            ]" />
            @endif
            <div class="col-8 col-md-10 ps-0">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3 border-0">
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle bg-primary bg-opacity-10 p-2 me-3" wire:ignore>
                                <i data-feather="user" class="text-primary" style="width: 20px; height: 20px;"></i>
                            </div>
                            <h5 class="mb-0 fw-semibold">Data Biodata</h5>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="mb-3">
                                    <label class="form-label fw-medium">
                                        <span wire:ignore><i data-feather="user" style="width: 14px; height: 14px;" class="me-1"></i></span>
                                        Nama
                                    </label>
                                    <input type="text" class="form-control bg-light" value="{{ $biodata->nama }}" readonly disabled>
                                </div>
                            </div>
                        </div>
                        @if ($biodata->jenis_peserta_id === 1)
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="mb-3">
                                    <label class="form-label fw-medium">
                                        <span wire:ignore><i data-feather="credit-card" style="width: 14px; height: 14px;" class="me-1"></i></span>
                                        NIP
                                    </label>
                                    <input type="text" class="form-control bg-light" value="{{ $biodata->nip }}" readonly disabled>
                                </div>
                            </div>
                        </div>
                        @elseif ($biodata->jenis_peserta_id === 2)
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="mb-3">
                                    <label class="form-label fw-medium">
                                        <span wire:ignore><i data-feather="credit-card" style="width: 14px; height: 14px;" class="me-1"></i></span>
                                        NIK
                                    </label>
                                    <input type="text" class="form-control bg-light" value="{{ $biodata->nik }}" readonly disabled>
                                </div>
                            </div>
                        </div>
                        @endif
                        @if ($biodata->jenis_peserta_id === 1)
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="mb-3">
                                    <label class="form-label fw-medium">
                                        <span wire:ignore><i data-feather="briefcase" style="width: 14px; height: 14px;" class="me-1"></i></span>
                                        Jabatan
                                    </label>
                                    <input type="text" class="form-control bg-light" value="{{ $biodata->jabatan }}" readonly disabled>
                                </div>
                            </div>
                        </div>
                        @endif
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="mb-3">
                                    <label class="form-label fw-medium">
                                        <span wire:ignore><i data-feather="home" style="width: 14px; height: 14px;" class="me-1"></i></span>
                                        Unit Kerja
                                    </label>
                                    <input type="text" class="form-control bg-light" value="{{ $biodata->unit_kerja }}" readonly disabled>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="mb-3">
                                    <label class="form-label fw-medium">
                                        <span wire:ignore><i data-feather="building" style="width: 14px; height: 14px;" class="me-1"></i></span>
                                        Instansi
                                    </label>
                                    <input type="text" class="form-control bg-light" value="{{ $biodata->instansi }}" readonly disabled>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="row">
                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <label class="form-label fw-medium">Gelar Depan</label>
                                    <input type="text" class="form-control" wire:model.blur="gelar_depan" placeholder="Contoh: Dr., Ir., Prof.">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <label class="form-label fw-medium">Gelar Belakang</label>
                                    <input type="text" class="form-control" wire:model.blur="gelar_belakang" placeholder="Contoh: S.Kom., M.M., Ph.D.">
                                </div>
                            </div>
                        </div>

                        @if ($biodata->jenis_peserta_id === 1)
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <label for="gol-pangkat" class="form-label fw-medium">
                                        <span wire:ignore><i data-feather="award" style="width: 14px; height: 14px;" class="me-1"></i></span>
                                        Golongan/Pangkat
                                    </label>
                                    <select wire:model.live="gol_pangkat_id"
                                        class="form-select @error('gol_pangkat_id') is-invalid @enderror"
                                        id="gol-pangkat">
                                        <option value="">- Pilih Golongan/Pangkat -</option>
                                        @foreach ($option_gol_pangkat as $item)
                                            <option value="{{ $item->id }}">
                                                {{ $item->pangkat . ' - ' . $item->golongan }}</option>
                                        @endforeach
                                    </select>
                                    @error('gol_pangkat_id')
                                        <label class="error invalid-feedback">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        @endif

                        @if ($biodata->jenis_peserta_id === 1)
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="mb-3">
                                    <label class="form-label fw-medium">
                                        <span wire:ignore><i data-feather="hash" style="width: 14px; height: 14px;" class="me-1"></i></span>
                                        NIK
                                    </label>
                                    <input type="text" wire:model.blur="nik"
                                        class="form-control @error('nik') is-invalid @enderror"
                                        wire:dirty.class="border-warning"
                                        placeholder="Masukkan NIK 16 digit">
                                    @error('nik')
                                        <label class="error invalid-feedback">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        @endif

                        <div class="row">
                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <label class="form-label fw-medium">
                                        <span wire:ignore><i data-feather="map-pin" style="width: 14px; height: 14px;" class="me-1"></i></span>
                                        Tempat Lahir
                                    </label>
                                    <input type="text" wire:model.blur="tempat_lahir"
                                        class="form-control @error('tempat_lahir') is-invalid @enderror"
                                        wire:dirty.class="border-warning"
                                        placeholder="Masukkan tempat lahir">
                                    @error('tempat_lahir')
                                        <label class="error invalid-feedback">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <label class="form-label fw-medium">
                                        <span wire:ignore><i data-feather="calendar" style="width: 14px; height: 14px;" class="me-1"></i></span>
                                        Tanggal Lahir
                                    </label>
                                    <div class="input-group flatpickr" id="flatpickr-date">
                                        <input type="text" wire:model.live="tgl_lahir"
                                            class="form-control flatpickr-input @error('tgl_lahir') is-invalid @enderror"
                                            placeholder="Pilih tanggal lahir" data-input="" readonly="readonly">
                                        <span class="input-group-text input-group-addon bg-light" data-toggle="">
                                            <i data-feather="calendar" style="width: 18px; height: 18px;"></i>
                                        </span>
                                        @error('tgl_lahir')
                                            <label class="error invalid-feedback">{{ $message }}</label>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-sm-12">
                                <label class="form-label fw-medium">
                                    <span wire:ignore><i data-feather="users" style="width: 14px; height: 14px;" class="me-1"></i></span>
                                    Jenis Kelamin
                                </label>
                                <div class="d-flex gap-4 mt-2">
                                    <div class="form-check">
                                        <input type="radio" class="form-check-input" wire:model.live="jk"
                                            id="radioInline" value="L" {{ $jk == 'L' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="radioInline">
                                            Laki-Laki
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input type="radio" class="form-check-input" wire:model.live="jk"
                                            id="radioInline1" value="P" {{ $jk == 'P' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="radioInline1">
                                            Perempuan
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <label for="agama-id" class="form-label fw-medium">
                                        <span wire:ignore><i data-feather="heart" style="width: 14px; height: 14px;" class="me-1"></i></span>
                                        Agama
                                    </label>
                                    <select wire:model.live="agama_id"
                                        class="form-select @error('agama_id') is-invalid @enderror" id="agama-id">
                                        <option value="">- Pilih Agama -</option>
                                        @foreach ($option_agama as $id => $item)
                                            <option value="{{ $id }}">{{ $item }}</option>
                                        @endforeach
                                    </select>
                                    @error('agama_id')
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
                                        Alamat
                                    </label>
                                    <input type="text" wire:model.blur="alamat"
                                        class="form-control @error('alamat') is-invalid @enderror"
                                        wire:dirty.class="border-warning"
                                        placeholder="Masukkan alamat lengkap">
                                    @error('alamat')
                                        <label class="error invalid-feedback">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <label class="form-label fw-medium">
                                        <span wire:ignore><i data-feather="phone" style="width: 14px; height: 14px;" class="me-1"></i></span>
                                        No. HP
                                    </label>
                                    <input type="text" wire:model.blur="no_hp"
                                        class="form-control @error('no_hp') is-invalid @enderror"
                                        wire:dirty.class="border-warning"
                                        placeholder="Contoh: 08123456789">
                                    @error('no_hp')
                                        <label class="error invalid-feedback">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-12">
                                <div class="mb-3">
                                    <label class="form-label fw-medium">
                                        <span wire:ignore><i data-feather="image" style="width: 14px; height: 14px;" class="me-1"></i></span>
                                        Foto <small class="text-muted">(Maksimal 200 KB)</small>
                                    </label>
                                    @if ($foto_url)
                                        <div class="mb-3">
                                            <img src="{{ $foto_url }}" class="img-fluid rounded shadow-sm" width="120">
                                        </div>
                                    @endif
                                    <input class="form-control @error('foto') is-invalid @enderror"
                                        wire:model.live="foto" type="file" id="formFile" accept="image/*">
                                    @error('foto')
                                        <label class="error invalid-feedback">{{ $message }}</label>
                                    @enderror
                                    <div wire:loading wire:target="foto" class="mt-2">
                                        <span class="spinner-border spinner-border-sm text-primary me-2"></span>
                                        <small class="text-muted">Mengupload foto...</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="d-flex justify-content-between align-items-center">
                            <a href="{{ route('peserta.portofolio') }}" class="btn btn-outline-primary" wire:navigate>
                                <span wire:ignore><i data-feather="eye" style="width: 16px; height: 16px;" class="me-1"></i></span>
                                Preview Portofolio
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
