<div>
    <x-breadcrumb :breadcrumbs="[
        ['url' => route('admin.dashboard'), 'title' => 'Dashboard'],
        ['url' => null, 'title' => 'Kesadaran Diri'],
        ['url' => null, 'title' => 'Data Referensi']
    ]" />
    <div class="row">
        <div class="col-md-12 stretch-card">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">Form Uraian Potensi</h6>
                    <form wire:submit="save">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <label class="form-label">Nama Indikator</label>
                                    <input
                                        type="text"
                                        wire:model="indikator_nama"
                                        class="form-control @error('indikator_nama') is-invalid @enderror"
                                        placeholder="masukkan nama indikator"
                                        value="{{ old('indikator_nama') }}"
                                    >
                                    @error('indikator_nama')
                                    <label class="error invalid-feedback">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div><!-- Col -->
                        </div><!-- Row -->
                        <div class="row">
                            <div class="col-sm-4">
                                <div class="mb-3">
                                    <label class="form-label">Nomor Indikator</label>
                                    <input
                                        type="number"
                                        wire:model="indikator_nomor"
                                        class="form-control @error('indikator_nomor') is-invalid @enderror"
                                        placeholder="masukkan nomor indikator"
                                        value="{{ old('indikator_nomor') }}"
                                    >
                                    @error('indikator_nomor')
                                    <label class="error invalid-feedback">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div><!-- Col -->
                        </div><!-- Row -->
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <label for="potensi" class="form-label">Uraian Potensi (Sangat Baik)</label>
                                    <textarea 
                                        class="form-control @error('kualifikasi.0.uraian_potensi') is-invalid @enderror"
                                        id="potensi"
                                        rows="5"
                                        wire:model="kualifikasi.0.uraian_potensi"
                                    >{{ old('kualifikasi.0.uraian_potensi') }}</textarea>
                                    @error('kualifikasi.0.uraian_potensi')
                                    <label class="error invalid-feedback">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div><!-- Col -->
                        </div><!-- Row -->
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <label for="potensi" class="form-label">Uraian Potensi (Baik)</label>
                                    <textarea
                                        class="form-control @error('kualifikasi.1.uraian_potensi') is-invalid @enderror"
                                        id="potensi"
                                        rows="5"
                                        wire:model="kualifikasi.1.uraian_potensi"
                                    >{{ old('kualifikasi.1.uraian_potensi') }}</textarea>
                                    @error('kualifikasi.1.uraian_potensi')
                                    <label class="error invalid-feedback">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div><!-- Col -->
                        </div><!-- Row -->
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <label for="potensi" class="form-label">Uraian Potensi (Cukup)</label>
                                    <textarea
                                        class="form-control @error('kualifikasi.2.uraian_potensi') is-invalid @enderror"
                                        id="potensi"
                                        rows="5"
                                        wire:model="kualifikasi.2.uraian_potensi"
                                    >{{ old('kualifikasi.2.uraian_potensi') }}</textarea>
                                    @error('kualifikasi.2.uraian_potensi')
                                    <label class="error invalid-feedback">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div><!-- Col -->
                        </div><!-- Row -->
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <label for="potensi" class="form-label">Uraian Potensi (Kurang/Sangat Kurang)</label>
                                    <textarea
                                        class="form-control @error('kualifikasi.3.uraian_potensi') is-invalid @enderror"
                                        id="potensi"
                                        rows="5"
                                        wire:model="kualifikasi.3.uraian_potensi"
                                    >{{ old('kualifikasi.3.uraian_potensi') }}</textarea>
                                    @error('kualifikasi.3.uraian_potensi')
                                    <label class="error invalid-feedback">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div><!-- Col -->
                        </div><!-- Row -->
                        <a href="{{ route('admin.ref-kesadaran-diri') }}" wire:navigate class="btn btn-sm btn-inverse-danger me-2">Batal</a>
                        <button
                            type="submit"
                            class="btn btn-sm btn-inverse-success"
                        >
                            Simpan
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>