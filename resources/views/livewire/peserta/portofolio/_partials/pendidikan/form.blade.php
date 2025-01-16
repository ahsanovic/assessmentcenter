<div>
    <x-breadcrumb :breadcrumbs="[
        ['url' => route('peserta.dashboard'), 'title' => 'Dashboard'],
        ['url' => route('peserta.portofolio'), 'title' => 'Portofolio'],
        ['url' => null, 'title' => 'Pendidikan'],
    ]" />
    <x-alert :type="'danger'" :teks="'Lengkapi portofolio Anda dengan detail yang mencerminkan perjalanan profesional dan pribadi Anda secara menyeluruh.
        Mulai dari biodata, pendidikan, pelatihan yang telah diikuti, riwayat karir, pengalaman kerja, hingga penilaian pribadi. 
        Tunjukkan potensi terbaik Anda dan ciptakan kesan mendalam melalui portofolio yang informatif dan menarik!'" />
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
                <div class="tab-content tab-content-vertical border p-3" id="v-tabContent">
                    <div class="tab-pane fade show active" role="tabpanel">
                        <h5 class="mb-4">Pendidikan</h5>
                        <x-alert :type="'success'" :teks="'Pendidikan formal dari Tingkat SMA/SLTA/Sederajat'" />
                        <form wire:submit="save">
                            <div class="row">
                                <div class="col-sm-2">
                                    <div class="mb-3">
                                        <label for="gol-pangkat" class="form-label">Jenjang Pendidikan</label>
                                        <select wire:model="form.jenjang_pendidikan_id"
                                            class="form-select @error('form.jenjang_pendidikan_id') is-invalid @enderror"
                                            id="gol-pangkat">
                                            <option value="">-pilih-</option>
                                            @foreach ($option_jenjang_pendidikan as $id => $item)
                                                <option value="{{ $id }}">
                                                    {{ $item }}</option>
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
                                        <label class="form-label">Nama Sekolah</label>
                                        <input type="text" wire:model="form.nama_sekolah"
                                            class="form-control @error('form.nama_sekolah') is-invalid @enderror"
                                            placeholder="masukkan nama sekolah/universitas">
                                        @error('form.nama_sekolah')
                                            <label class="error invalid-feedback">{{ $message }}</label>
                                        @enderror
                                    </div>
                                </div><!-- Col -->
                            </div><!-- Row -->
                            <div class="row">
                                <div class="col-sm-2">
                                    <div class="mb-3">
                                        <label class="form-label">Tahun Masuk</label>
                                        <input type="text" wire:model="form.thn_masuk"
                                            class="form-control @error('form.thn_masuk') is-invalid @enderror">
                                        @error('form.thn_masuk')
                                            <label class="error invalid-feedback">{{ $message }}</label>
                                        @enderror
                                    </div>
                                </div><!-- Col -->
                                <div class="col-sm-2">
                                    <div class="mb-3">
                                        <label class="form-label">Tahun Lulus</label>
                                        <input type="text" wire:model="form.thn_lulus"
                                            class="form-control @error('form.thn_lulus') is-invalid @enderror">
                                        @error('form.thn_lulus')
                                            <label class="error invalid-feedback">{{ $message }}</label>
                                        @enderror
                                    </div>
                                </div><!-- Col -->
                            </div><!-- Row -->
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="mb-3">
                                        <label class="form-label">Jurusan</label>
                                        <input type="text" wire:model="form.jurusan"
                                            class="form-control @error('form.jurusan') is-invalid @enderror"
                                            placeholder="masukkan jurusan">
                                        @error('form.jurusan')
                                            <label class="error invalid-feedback">{{ $message }}</label>
                                        @enderror
                                    </div>
                                </div><!-- Col -->
                            </div><!-- Row -->
                            <div class="row">
                                <div class="col-sm-2">
                                    <div class="mb-3">
                                        <label class="form-label">IPK / Nilai</label>
                                        <input type="text" wire:model="form.ipk"
                                            class="form-control @error('form.ipk') is-invalid @enderror">
                                        @error('form.ipk')
                                            <label class="error invalid-feedback">{{ $message }}</label>
                                        @enderror
                                    </div>
                                </div><!-- Col -->
                            </div><!-- Row -->

                            <div class="mt-3">
                                <a href="{{ route('peserta.pendidikan') }}" wire:navigate
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
