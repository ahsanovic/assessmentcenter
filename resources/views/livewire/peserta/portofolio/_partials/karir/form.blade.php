<div>
    <x-breadcrumb :breadcrumbs="[
        ['url' => route('peserta.dashboard'), 'title' => 'Dashboard'],
        ['url' => route('peserta.portofolio'), 'title' => 'Portofolio'],
        ['url' => null, 'title' => 'Karir'],
    ]" />
    <x-alert :type="'danger'" :teks="'Lengkapi portofolio Anda dengan detail yang mencerminkan perjalanan profesional dan pribadi Anda secara menyeluruh.
        Mulai dari biodata, pendidikan, pelatihan yang telah diikuti, riwayat karir, pengalaman kerja, hingga penilaian pribadi. 
        Tunjukkan potensi terbaik Anda dan ciptakan kesan mendalam melalui portofolio yang informatif dan menarik!'" />
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
                <div class="tab-content tab-content-vertical border p-3" id="v-tabContent">
                    <div class="tab-pane fade show active" role="tabpanel">
                        <h5 class="mb-4">Karir</h5>
                        <x-alert :type="'success'" :teks="'Riwayat karir 5 tahun terakhir'" />
                        <form wire:submit="save">
                            <div class="row">
                                <div class="col-sm-2">
                                    <div class="mb-3">
                                        <label class="form-label">Bulan Mulai</label>
                                        <select wire:model="form.bulan_mulai"
                                            class="form-select form-select-sm @error('form.bulan_mulai') is-invalid @enderror"
                                            id="bulan-mulai">
                                            <option value="">- pilih -</option>
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
                                </div><!-- Col -->
                                <div class="col-sm-2">
                                    <div class="mb-3">
                                        <label class="form-label">Tahun Mulai</label>
                                        <input type="text" wire:model="form.tahun_mulai"
                                            class="form-control @error('form.tahun_mulai') is-invalid @enderror">
                                        @error('form.tahun_mulai')
                                            <label class="error invalid-feedback">{{ $message }}</label>
                                        @enderror
                                    </div>
                                </div><!-- Col -->
                            </div><!-- Row -->
                            <div class="row">
                                <div class="col-sm-2">
                                    <div class="mb-3">
                                        <label class="form-label">Bulan Selesai</label>
                                        <select wire:model="form.bulan_selesai"
                                            class="form-select form-select-sm @error('form.bulan_selesai') is-invalid @enderror"
                                            id="bulan-selesai">
                                            <option value="">- pilih -</option>
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
                                </div><!-- Col -->
                                <div class="col-sm-2">
                                    <div class="mb-3">
                                        <label class="form-label">Tahun Selesai</label>
                                        <input type="text" wire:model="form.tahun_selesai"
                                            class="form-control @error('form.tahun_selesai') is-invalid @enderror">
                                        @error('form.tahun_selesai')
                                            <label class="error invalid-feedback">{{ $message }}</label>
                                        @enderror
                                    </div>
                                </div><!-- Col -->
                            </div><!-- Row -->
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="mb-3">
                                        <label class="form-label">Jabatan</label>
                                        <input type="text" wire:model="form.jabatan"
                                            class="form-control @error('form.jabatan') is-invalid @enderror"
                                            placeholder="masukkan jabatan">
                                        @error('form.jabatan')
                                            <label class="error invalid-feedback">{{ $message }}</label>
                                        @enderror
                                    </div>
                                </div><!-- Col -->
                            </div><!-- Row -->
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="mb-3">
                                        <label class="form-label">Instansi</label>
                                        <input type="text" wire:model="form.instansi"
                                            class="form-control @error('form.instansi') is-invalid @enderror"
                                            placeholder="masukkan nama instansi">
                                        @error('form.instansi')
                                            <label class="error invalid-feedback">{{ $message }}</label>
                                        @enderror
                                    </div>
                                </div><!-- Col -->
                            </div><!-- Row -->
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="mb-3">
                                        <label class="form-label">Uraian Tugas / Tanggung Jawab</label>
                                        <textarea class="form-control @error('form.uraian_tugas') is-invalid @enderror" wire:model="form.uraian_tugas"
                                            rows="3"></textarea>
                                        @error('form.uraian_tugas')
                                            <label class="error invalid-feedback">{{ $message }}</label>
                                        @enderror
                                    </div>
                                </div><!-- Col -->
                            </div><!-- Row -->

                            <div class="mt-3">
                                <a href="{{ route('peserta.karir') }}" wire:navigate
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
