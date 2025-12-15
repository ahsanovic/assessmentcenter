<div>
    <x-breadcrumb :breadcrumbs="[
        ['url' => route('admin.dashboard'), 'title' => 'Dashboard'],
        ['url' => route('admin.soal-pspk'), 'title' => 'PSPK'],
        ['url' => null, 'title' => 'Referensi Deskripsi']
    ]" />
    <div class="row">
        <div class="col-md-12 stretch-card">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">Form Referensi Deskripsi</h6>
                    <form wire:submit="save">
                        <div class="row">
                            <div class="col-sm-4">
                                <div class="mb-3">
                                    <label for="level-pspk" class="form-label">Level PSPK</label>
                                    <select wire:model="form.level_pspk" class="form-select @error('form.level_pspk') is-invalid @enderror" id="level-pspk">
                                        <option value="">pilih level pspk</option>
                                        @foreach ($level_pspk_options as $key => $item)
                                            <option value="{{ $key }}">{{ $item }}</option>
                                        @endforeach
                                    </select>
                                    @error('form.level_pspk')
                                    <label class="error invalid-feedback">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-4">
                                <div class="mb-3">
                                    <label for="aspek" class="form-label">Aspek</label>
                                    <select wire:model="form.aspek" class="form-select @error('form.aspek') is-invalid @enderror" id="aspek">
                                        <option value="">pilih aspek</option>
                                        @foreach ($aspek_options as $key => $item)
                                            <option value="{{ $key }}">{{ $item }}</option>
                                        @endforeach
                                    </select>
                                    @error('form.aspek')
                                    <label class="error invalid-feedback">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="mb-3">
                                    <label class="form-label">Deskripsi (-)</label>
                                    <textarea 
                                        class="form-control @error('form.deskripsi_min') is-invalid @enderror"
                                        id="potensi"
                                        rows="5"
                                        wire:model="form.deskripsi_min"
                                    >{{ old('deskripsi_min') }}</textarea>
                                    @error('form.deskripsi_min')
                                    <label class="error invalid-feedback">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div><!-- Col -->
                        </div><!-- Row -->
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="mb-3">
                                    <label class="form-label">Deskripsi</label>
                                    <textarea 
                                        class="form-control @error('form.deskripsi') is-invalid @enderror"
                                        id="potensi"
                                        rows="5"
                                        wire:model="form.deskripsi"
                                    >{{ old('deskripsi') }}</textarea>
                                    @error('form.deskripsi')
                                    <label class="error invalid-feedback">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div><!-- Col -->
                        </div><!-- Row -->
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="mb-3">
                                    <label class="form-label">Deskripsi (+)</label>
                                    <textarea 
                                        class="form-control @error('form.deskripsi_plus') is-invalid @enderror"
                                        id="potensi"
                                        rows="5"
                                        wire:model="form.deskripsi_plus"
                                    >{{ old('deskripsi_plus') }}</textarea>
                                    @error('form.deskripsi_plus')
                                    <label class="error invalid-feedback">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div><!-- Col -->
                        </div><!-- Row -->

                        <x-form-action :cancelUrl="route('admin.ref-pspk')" />
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>