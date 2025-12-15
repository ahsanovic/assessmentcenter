<div>
    <x-breadcrumb :breadcrumbs="[
        ['url' => route('admin.dashboard'), 'title' => 'Dashboard'],
        ['url' => route('admin.soal-pspk'), 'title' => 'PSPK'],
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
                                    <label for="level-pspk" class="form-label">Level PSPK</label>
                                    <select wire:model="form.level_pspk_id" class="form-select @error('form.level_pspk_id') is-invalid @enderror" id="level-pspk">
                                        <option value="">pilih level pspk</option>
                                        @foreach ($level_pspk_options as $key => $item)
                                            <option value="{{ $key }}">{{ $item }}</option>
                                        @endforeach
                                    </select>
                                    @error('form.level_pspk_id')
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
                            <div class="col-sm-4">
                                <div class="mb-3">
                                    <label class="form-label">Poin</label>
                                    <input
                                        type="number"
                                        wire:model="form.poin_opsi_a"
                                        class="form-control @error('form.poin_opsi_a') is-invalid @enderror"
                                        placeholder="masukkan skor jawaban A"
                                        value="{{ old('poin_opsi_a') }}"
                                    >
                                    @error('form.poin_opsi_a')
                                    <label class="error invalid-feedback">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div><!-- Col -->
                        </div><!-- Row -->
                        <div class="row">
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
                            <div class="col-sm-4">
                                <div class="mb-3">
                                    <label class="form-label">Poin</label>
                                    <input
                                        type="number"
                                        wire:model="form.poin_opsi_b"
                                        class="form-control @error('form.poin_opsi_b') is-invalid @enderror"
                                        placeholder="masukkan skor jawaban B"
                                        value="{{ old('poin_opsi_b') }}"
                                    >
                                    @error('form.poin_opsi_b')
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
                            <div class="col-sm-4">
                                <div class="mb-3">
                                    <label class="form-label">Poin</label>
                                    <input
                                        type="number"
                                        wire:model="form.poin_opsi_c"
                                        class="form-control @error('form.poin_opsi_c') is-invalid @enderror"
                                        placeholder="masukkan skor jawaban C"
                                        value="{{ old('poin_opsi_c') }}"
                                    >
                                    @error('form.poin_opsi_c')
                                    <label class="error invalid-feedback">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div><!-- Col -->
                        </div><!-- Row -->
                        <div class="row">
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
                            <div class="col-sm-4">
                                <div class="mb-3">
                                    <label class="form-label">Poin</label>
                                    <input
                                        type="number"
                                        wire:model="form.poin_opsi_d"
                                        class="form-control @error('form.poin_opsi_d') is-invalid @enderror"
                                        placeholder="masukkan skor jawaban D"
                                        value="{{ old('poin_opsi_d') }}"
                                    >
                                    @error('form.poin_opsi_d')
                                    <label class="error invalid-feedback">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div><!-- Col -->
                        </div><!-- Row -->
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <label class="form-label">Pilihan E</label>
                                    <textarea 
                                        class="form-control @error('form.opsi_e') is-invalid @enderror"
                                        id="potensi"
                                        rows="3"
                                        wire:model="form.opsi_e"
                                    >{{ old('opsi_e') }}</textarea>
                                    @error('form.opsi_e')
                                    <label class="error invalid-feedback">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div><!-- Col -->
                            <div class="col-sm-4">
                                <div class="mb-3">
                                    <label class="form-label">Poin</label>
                                    <input
                                        type="number"
                                        wire:model="form.poin_opsi_e"
                                        class="form-control @error('form.poin_opsi_e') is-invalid @enderror"
                                        placeholder="masukkan skor jawaban E"
                                        value="{{ old('poin_opsi_e') }}"
                                    >
                                    @error('form.poin_opsi_e')
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
                                        @foreach (['A', 'B', 'C', 'D', 'E'] as $option)
                                            <option value="{{ $option }}">{{ $option }}</option>
                                        @endforeach
                                    </select>
                                    @error('form.kunci_jawaban')
                                    <label class="error invalid-feedback">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>
                        </div><!-- Row -->

                        <x-form-action :cancelUrl="route('admin.soal-pspk')" />
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>