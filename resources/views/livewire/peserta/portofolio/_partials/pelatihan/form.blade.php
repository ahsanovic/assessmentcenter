<div>
    <x-breadcrumb :breadcrumbs="[
        ['url' => route('peserta.dashboard'), 'title' => 'Dashboard'],
        ['url' => route('peserta.portofolio'), 'title' => 'Portofolio'],
        ['url' => null, 'title' => 'Pelatihan'],
    ]" />
    <x-alert :type="'danger'" :teks="'Lengkapi portofolio Anda dengan detail yang mencerminkan perjalanan profesional dan pribadi Anda secara menyeluruh.
        Mulai dari biodata, pendidikan, pelatihan yang telah diikuti, riwayat karir, pengalaman kerja, hingga penilaian pribadi. 
        Tunjukkan potensi terbaik Anda dan ciptakan kesan mendalam melalui portofolio yang informatif dan menarik!'" />
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
                <div class="tab-content tab-content-vertical border p-3" id="v-tabContent">
                    <div class="tab-pane fade show active" role="tabpanel">
                        <h5 class="mb-4">Pelatihan</h5>
                        <x-alert :type="'success'" :teks="'Riwayat pelatihan 5 tahun terakhir'" />
                        <form wire:submit="save">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="mb-3">
                                        <label class="form-label">Nama Institusi</label>
                                        <input type="text" wire:model="form.nama_institusi"
                                            class="form-control @error('form.nama_institusi') is-invalid @enderror"
                                            placeholder="masukkan nama institusi pelatihan">
                                        @error('form.nama_institusi')
                                            <label class="error invalid-feedback">{{ $message }}</label>
                                        @enderror
                                    </div>
                                </div><!-- Col -->
                            </div><!-- Row -->
                            <div class="row">
                                <div class="col-sm-2">
                                    <div class="mb-3">
                                        <label class="form-label">Tanggal Mulai</label>
                                        <div class="input-group flatpickr" id="flatpickr-date">
                                            <input type="text" wire:model="form.tgl_mulai"
                                                class="form-control flatpickr-input @error('form.tgl_mulai') is-invalid @enderror"
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
                                            @error('form.tgl_mulai')
                                                <label class="error invalid-feedback">{{ $message }}</label>
                                            @enderror
                                        </div>
                                    </div>
                                </div><!-- Col -->
                                <div class="col-sm-2">
                                    <div class="mb-3">
                                        <label class="form-label">Tanggal Selesai</label>
                                        <div class="input-group flatpickr" id="flatpickr-date">
                                            <input type="text" wire:model="form.tgl_selesai"
                                                class="form-control flatpickr-input @error('form.tgl_selesai') is-invalid @enderror"
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
                                            @error('form.tgl_selesai')
                                                <label class="error invalid-feedback">{{ $message }}</label>
                                            @enderror
                                        </div>
                                    </div>
                                </div><!-- Col -->
                            </div><!-- Row -->
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="mb-3">
                                        <label class="form-label">Subjek Pelatihan</label>
                                        <input type="text" wire:model="form.subjek_pelatihan"
                                            class="form-control @error('form.subjek_pelatihan') is-invalid @enderror"
                                            placeholder="masukkan subjek pelatihan">
                                        @error('form.subjek_pelatihan')
                                            <label class="error invalid-feedback">{{ $message }}</label>
                                        @enderror
                                    </div>
                                </div><!-- Col -->
                            </div><!-- Row -->

                            <div class="mt-3">
                                <a href="{{ route('peserta.pelatihan') }}" wire:navigate
                                    class="btn btn-sm btn-inverse-danger me-2">Batal</a>
                                <button type="submit" class="btn btn-sm btn-inverse-success">
                                    {{ $isUpdate == true ? 'Ubah' : 'Simpan' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
