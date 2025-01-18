<div>
    <x-breadcrumb :breadcrumbs="[
        ['url' => route('peserta.dashboard'), 'title' => 'Dashboard'],
        ['url' => route('peserta.portofolio'), 'title' => 'Portofolio'],
        ['url' => null, 'title' => 'Biodata'],
    ]" />
    <x-alert :type="'danger'" :teks="'Lengkapi portofolio Anda dengan detail yang mencerminkan perjalanan profesional dan pribadi Anda secara menyeluruh.
        Mulai dari biodata, pendidikan, pelatihan yang telah diikuti, riwayat karir, pengalaman kerja, hingga penilaian pribadi. 
        Tunjukkan potensi terbaik Anda dan ciptakan kesan mendalam melalui portofolio yang informatif dan menarik!'" />
    <div class="row">
        <div class="row">
            <x-tab-nav :nav="[
                ['url' => route('peserta.biodata'), 'title' => 'Biodata', 'active' => 'active'],
                ['url' => route('peserta.pendidikan'), 'title' => 'Pendidikan', 'active' => null],
                ['url' => route('peserta.pelatihan'), 'title' => 'Pelatihan', 'active' => null],
                ['url' => route('peserta.karir'), 'title' => 'Karir', 'active' => null],
                ['url' => route('peserta.pengalaman'), 'title' => 'Pengalaman Spesifik', 'active' => null],
                ['url' => route('peserta.penilaian'), 'title' => 'Penilaian Pribadi', 'active' => null],
            ]" />
            <div class="col-8 col-md-10 ps-0">
                <div class="tab-content tab-content-vertical border p-3" id="v-tabContent">
                    <div class="tab-pane fade show active" role="tabpanel">
                        <h5 class="mb-4">Biodata</h5>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="mb-3">
                                    <label class="form-label">Nama</label>
                                    <input type="text" class="form-control" value="{{ $biodata->nama }}" readonly
                                        disabled>
                                </div>
                            </div><!-- Col -->
                        </div><!-- Row -->
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="mb-3">
                                    <label class="form-label">NIP</label>
                                    <input type="text" class="form-control" value="{{ $biodata->nip }}" readonly
                                        disabled>
                                </div>
                            </div><!-- Col -->
                        </div><!-- Row -->
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="mb-3">
                                    <label class="form-label">Jabatan</label>
                                    <input type="text" class="form-control" value="{{ $biodata->jabatan }}" readonly
                                        disabled>
                                </div>
                            </div><!-- Col -->
                        </div><!-- Row -->
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="mb-3">
                                    <label class="form-label">Unit Kerja</label>
                                    <input type="text" class="form-control" value="{{ $biodata->unit_kerja }}"
                                        readonly disabled>
                                </div>
                            </div><!-- Col -->
                        </div><!-- Row -->
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="mb-3">
                                    <label class="form-label">Instansi</label>
                                    <input type="text" class="form-control" value="{{ $biodata->instansi }}" readonly
                                        disabled>
                                </div>
                            </div><!-- Col -->
                        </div><!-- Row -->
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="mb-3">
                                    <label class="form-label">Gelar Depan</label>
                                    <input type="text" class="form-control" wire:model.blur="gelar_depan">
                                </div>
                            </div><!-- Col -->
                        </div><!-- Row -->
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="mb-3">
                                    <label class="form-label">Gelar Belakang</label>
                                    <input type="text" class="form-control" wire:model.blur="gelar_belakang">
                                </div>
                            </div><!-- Col -->
                        </div><!-- Row -->
                        <div class="row">
                            <div class="col-sm-4">
                                <div class="mb-3">
                                    <label for="gol-pangkat" class="form-label">Golongan/Pangkat</label>
                                    <select wire:model.blur="gol_pangkat_id"
                                        class="form-select @error('gol_pangkat_id') is-invalid @enderror"
                                        id="gol-pangkat">
                                        <option value="">-pilih-</option>
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
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="mb-3">
                                    <label class="form-label">NIK</label>
                                    <input type="text" wire:model.blur="nik"
                                        class="form-control @error('nik') is-invalid @enderror"
                                        wire:dirty.class="border-warning">
                                    @error('nik')
                                        <label class="error invalid-feedback">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div><!-- Col -->
                        </div><!-- Row -->
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="mb-3">
                                    <label class="form-label">Tempat Lahir</label>
                                    <input type="text" wire:model.blur="tempat_lahir"
                                        class="form-control @error('tempat_lahir') is-invalid @enderror"
                                        wire:dirty.class="border-warning">
                                    @error('tempat_lahir')
                                        <label class="error invalid-feedback">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div><!-- Col -->
                        </div><!-- Row -->
                        <div class="row">
                            <div class="col-sm-4">
                                <div class="mb-3">
                                    <label class="form-label">Tanggal Lahir</label>
                                    <div class="input-group flatpickr" id="flatpickr-date">
                                        <input type="text" wire:model.blur="tgl_lahir"
                                            class="form-control flatpickr-input @error('tgl_lahir') is-invalid @enderror"
                                            placeholder="Select date" data-input="" readonly="readonly">
                                        <span class="input-group-text input-group-addon" data-toggle="">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                class="feather feather-calendar">
                                                <rect x="3" y="4" width="18" height="18" rx="2"
                                                    ry="2"></rect>
                                                <line x1="16" y1="2" x2="16" y2="6">
                                                </line>
                                                <line x1="8" y1="2" x2="8" y2="6">
                                                </line>
                                                <line x1="3" y1="10" x2="21" y2="10">
                                                </line>
                                            </svg>
                                        </span>
                                        @error('tgl_lahir')
                                            <label class="error invalid-feedback">{{ $message }}</label>
                                        @enderror
                                    </div>
                                </div>
                            </div><!-- Col -->
                        </div><!-- Row -->
                        <div class="row mb-4">
                            <div class="col-sm-12">
                                <div class="mb-1">
                                    <label class="form-label">Jenis Kelamin</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input type="radio" class="form-check-input" wire:model.blur="jk"
                                        id="radioInline" value="L" {{ $jk == 'L' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="radioInline">
                                        Laki-Laki
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input type="radio" class="form-check-input" wire:model.blur="jk"
                                        id="radioInline1" value="P" {{ $jk == 'P' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="radioInline1">
                                        Perempuan
                                    </label>
                                </div>
                            </div><!-- Col -->
                        </div><!-- Row -->
                        <div class="row">
                            <div class="col-sm-4">
                                <div class="mb-3">
                                    <label for="gol-pangkat" class="form-label">Agama</label>
                                    <select wire:model.blur="agama_id"
                                        class="form-select @error('agama_id') is-invalid @enderror" id="agama-id">
                                        <option value="">-pilih-</option>
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
                                    <label class="form-label">Alamat</label>
                                    <input type="text" wire:model.blur="alamat"
                                        class="form-control @error('alamat') is-invalid @enderror"
                                        wire:dirty.class="border-warning">
                                    @error('alamat')
                                        <label class="error invalid-feedback">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div><!-- Col -->
                        </div><!-- Row -->
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="mb-3">
                                    <label class="form-label">No. HP</label>
                                    <input type="text" wire:model.blur="no_hp"
                                        class="form-control @error('no_hp') is-invalid @enderror"
                                        wire:dirty.class="border-warning">
                                    @error('no_hp')
                                        <label class="error invalid-feedback">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div><!-- Col -->
                        </div><!-- Row -->
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="mb-3">
                                    <label class="form-label">Foto (maksimal 200 KB)</label>
                                    @if ($foto_url)
                                        <br />
                                        <img src="{{ $foto_url }}" class="img-fluid rounded mb-3"
                                            width="100">
                                    @endif
                                    <input class="form-control @error('foto') is-invalid @enderror"
                                        wire:model.live="foto" type="file" id="formFile" accept="image/*">
                                    @error('foto')
                                        <label class="error invalid-feedback">{{ $message }}</label>
                                    @enderror
                                </div>
                                <div wire:loading wire:target="foto">Uploading...</div>
                            </div><!-- Col -->
                        </div><!-- Row -->
                    </div>
                </div>
            </div>
        </div>
    </div>
