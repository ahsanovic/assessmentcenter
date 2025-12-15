<div>
    <x-breadcrumb :breadcrumbs="[
        ['url' => route('admin.dashboard'), 'title' => 'Dashboard'],
        ['url' => null, 'title' => 'Intelektual'],
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
                                        wire:model="indikator"
                                        class="form-control @error('indikator') is-invalid @enderror"
                                        placeholder="masukkan nama indikator"
                                        value="{{ old('indikator') }}"
                                    >
                                    @error('indikator')
                                    <label class="error invalid-feedback">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div><!-- Col -->
                        </div><!-- Row -->
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <label class="form-label">Sub Tes ke-</label>
                                    <input
                                        type="number"
                                        wire:model="sub_tes"
                                        class="form-control @error('sub_tes') is-invalid @enderror"
                                        placeholder="masukkan nomor sub tes"
                                        value="{{ old('sub_tes') }}"
                                    >
                                    @error('sub_tes')
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
                                    <label for="potensi" class="form-label">Uraian Potensi (Kurang)</label>
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
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <label for="potensi" class="form-label">Uraian Potensi (Sangat Kurang)</label>
                                    <textarea
                                        class="form-control @error('kualifikasi.4.uraian_potensi') is-invalid @enderror"
                                        id="potensi"
                                        rows="5"
                                        wire:model="kualifikasi.4.uraian_potensi"
                                    >{{ old('kualifikasi.4.uraian_potensi') }}</textarea>
                                    @error('kualifikasi.4.uraian_potensi')
                                    <label class="error invalid-feedback">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div><!-- Col -->
                        </div><!-- Row -->

                        <x-form-action 
                            :cancelUrl="route('admin.ref-intelektual')" 
                            :isUpdate="$isUpdate == true" 
                        />
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>