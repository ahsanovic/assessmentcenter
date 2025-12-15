<div>
    <x-breadcrumb :breadcrumbs="[
        ['url' => route('admin.dashboard'), 'title' => 'Dashboard'],
        ['url' => route('admin.soal-kompetensi-teknis'), 'title' => 'Kompetensi Teknis'],
        ['url' => null, 'title' => 'Soal']
    ]" />
    <div class="row">
        <div class="col-md-12 stretch-card">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">Form Soal</h6>
                    <form wire:submit="save">
                        <div class="row">
                            <div class="col-sm-4">
                                <div class="mb-3">
                                    <label for="jenis-jabatan" class="form-label">Jenis Jabatan</label>
                                    <select wire:model="form.jenis_jabatan" class="form-select @error('form.jenis_jabatan') is-invalid @enderror" id="jenis-jabatan">
                                        <option value="">pilih jenis jabatan</option>
                                        @foreach ($jenis_jabatan_options as $key => $item)
                                            <option value="{{ $key }}">{{ $item }}</option>
                                        @endforeach
                                    </select>
                                    @error('form.jenis_jabatan')
                                    <label class="error invalid-feedback">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="mb-3">
                                    <label class="form-label">Deskripsi Soal</label>
                                    <textarea 
                                        class="form-control @error('form.soal') is-invalid @enderror"
                                        id="potensi"
                                        rows="5"
                                        wire:model="form.soal"
                                    >{{ old('soal') }}</textarea>
                                    @error('form.soal')
                                    <label class="error invalid-feedback">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div><!-- Col -->
                        </div><!-- Row -->
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <label class="form-label">Pilihan A</label>
                                    <textarea 
                                        class="form-control @error('form.opsi_a') is-invalid @enderror"
                                        id="potensi"
                                        rows="3"
                                        wire:model="form.opsi_a"
                                    >{{ old('opsi_a') }}</textarea>
                                    @error('form.opsi_a')
                                    <label class="error invalid-feedback">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div><!-- Col -->
                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <label class="form-label">Pilihan B</label>
                                    <textarea 
                                        class="form-control @error('form.opsi_b') is-invalid @enderror"
                                        id="potensi"
                                        rows="3"
                                        wire:model="form.opsi_b"
                                    >{{ old('opsi_b') }}</textarea>
                                    @error('form.opsi_b')
                                    <label class="error invalid-feedback">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div><!-- Col -->
                        </div><!-- Row -->
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <label class="form-label">Pilihan C</label>
                                    <textarea 
                                        class="form-control @error('form.opsi_c') is-invalid @enderror"
                                        id="potensi"
                                        rows="3"
                                        wire:model="form.opsi_c"
                                    >{{ old('opsi_c') }}</textarea>
                                    @error('form.opsi_c')
                                    <label class="error invalid-feedback">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div><!-- Col -->
                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <label class="form-label">Pilihan D</label>
                                    <textarea 
                                        class="form-control @error('form.opsi_d') is-invalid @enderror"
                                        id="potensi"
                                        rows="3"
                                        wire:model="form.opsi_d"
                                    >{{ old('opsi_d') }}</textarea>
                                    @error('form.opsi_d')
                                    <label class="error invalid-feedback">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div><!-- Col -->
                        </div><!-- Row -->
                        <div class="row">
                            <div class="col-sm-3">
                                <div class="mb-3">
                                    <label for="kunci-jawaban" class="form-label">Kunci Jawaban</label>
                                    <select wire:model="form.kunci_jawaban" class="form-select @error('form.kunci_jawaban') is-invalid @enderror" id="kunci-jawaban">
                                        <option value="">pilih kunci jawaban</option>
                                        <option value="A">A</option>
                                        <option value="B">B</option>
                                        <option value="C">C</option>
                                        <option value="D">D</option>
                                    </select>
                                    @error('form.kunci_jawaban')
                                    <label class="error invalid-feedback">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>
                        </div><!-- Row -->

                        <x-form-action :cancelUrl="route('admin.soal-kompetensi-teknis')" />
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>