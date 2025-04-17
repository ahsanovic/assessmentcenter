<div>
    <x-breadcrumb :breadcrumbs="[
        ['url' => route('admin.dashboard'), 'title' => 'Dashboard'],
        ['url' => null, 'title' => 'Problem Solving'],
        ['url' => null, 'title' => 'Data Referensi Indikator']
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
                                    <textarea 
                                        class="form-control @error('indikator_nama') is-invalid @enderror"
                                        rows="3"
                                        wire:model="indikator_nama"
                                    >{{ old('indikator_nama') }}</textarea>
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
                                    <label for="potensi" class="form-label">Kualifikasi (Rendah)</label>
                                    <textarea 
                                        class="form-control @error('kualifikasi_deskripsi.0.deskripsi') is-invalid @enderror"
                                        id="potensi"
                                        rows="5"
                                        wire:model="kualifikasi_deskripsi.0.deskripsi"
                                    >{{ old('kualifikasi_deskripsi.0.deskripsi') }}</textarea>
                                    @error('kualifikasi_deskripsi.0.deskripsi')
                                    <label class="error invalid-feedback">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div><!-- Col -->
                        </div><!-- Row -->
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <label for="potensi" class="form-label">Kualifikasi (Sedang)</label>
                                    <textarea
                                        class="form-control @error('kualifikasi_deskripsi.1.deskripsi') is-invalid @enderror"
                                        id="potensi"
                                        rows="5"
                                        wire:model="kualifikasi_deskripsi.1.deskripsi"
                                    >{{ old('kualifikasi_deskripsi.1.deskripsi') }}</textarea>
                                    @error('kualifikasi_deskripsi.1.deskripsi')
                                    <label class="error invalid-feedback">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div><!-- Col -->
                        </div><!-- Row -->
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <label for="potensi" class="form-label">Kualifikasi (Tinggi)</label>
                                    <textarea
                                        class="form-control @error('kualifikasi_deskripsi.2.deskripsi') is-invalid @enderror"
                                        id="potensi"
                                        rows="5"
                                        wire:model="kualifikasi_deskripsi.2.deskripsi"
                                    >{{ old('kualifikasi_deskripsi.2.deskripsi') }}</textarea>
                                    @error('kualifikasi_deskripsi.2.deskripsi')
                                    <label class="error invalid-feedback">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div><!-- Col -->
                        </div><!-- Row -->

                        <div class="mt-3">
                            <a href="{{ route('admin.ref-indikator-problem-solving') }}" wire:navigate class="btn btn-sm btn-inverse-danger me-2">Batal</a>
                            <button
                                type="submit"
                                class="btn btn-sm btn-inverse-success"
                            >
                                Update
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>