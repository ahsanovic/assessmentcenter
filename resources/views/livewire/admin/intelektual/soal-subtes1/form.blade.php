<div>
    <x-breadcrumb :breadcrumbs="[
        ['url' => route('admin.dashboard'), 'title' => 'Dashboard'],
        ['url' => null, 'title' => 'Data Soal Sub Tes 1']
    ]" />
    <div class="row">
        <div class="col-md-12 stretch-card">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">Form Soal Intelektual Sub Tes 1</h6>
                    <form wire:submit="save">
                        <div class="row">
                            <div class="col-sm-4">
                                <div class="mb-3">
                                    <label for="model-soal" class="form-label">Model Soal</label>
                                    <select wire:model="form.model_id" class="form-select @error('form.model_id') is-invalid @enderror" id="model-soal">
                                        <option value="">pilih model soal</option>
                                        @foreach ($model_soal_options as $key => $item)
                                            <option value="{{ $key }}">{{ $item }}</option>
                                        @endforeach
                                    </select>
                                    @error('form.model_id')
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
                                        rows="2"
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
                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <label class="form-label">Pilihan E</label>
                                    <textarea 
                                        class="form-control @error('form.opsi_e') is-invalid @enderror"
                                        rows="3"
                                        wire:model="form.opsi_e"
                                    >{{ old('opsi_e') }}</textarea>
                                    @error('form.opsi_e')
                                    <label class="error invalid-feedback">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div><!-- Col -->
                            <div class="col-sm-3">
                                <div class="mb-3">
                                    <label for="kunci-jawaban" class="form-label">Kunci Jawaban</label>
                                    <select wire:model="form.kunci_jawaban" class="form-select @error('form.kunci_jawaban') is-invalid @enderror" id="kunci-jawaban">
                                        <option value="">pilih kunci jawaban</option>
                                        <option value="A">A</option>
                                        <option value="B">B</option>
                                        <option value="C">C</option>
                                        <option value="D">D</option>
                                        <option value="E">E</option>
                                    </select>
                                    @error('form.kunci_jawaban')
                                    <label class="error invalid-feedback">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>
                        </div><!-- Row -->
                        {{-- <div class="row">
                            <div class="col-sm-4">
                                <div class="mb-3">
                                    <label class="form-label">Sub Tes</label>
                                    <select wire:model="form.sub_tes" class="form-select @error('form.sub_tes') is-invalid @enderror">
                                        <option value="">pilih sub tes</option>
                                        <option value="1">Sub Tes 1</option>
                                        <option value="2">Sub Tes 2</option>
                                        <option value="3">Sub Tes 3</option>
                                    </select>
                                    @error('form.sub_tes') <label class="error invalid-feedback">{{ $message }}</label> @enderror
                                </div>
                            </div>
                        </div> --}}

                        {{-- Quill Editors --}}
                        {{-- <div class="row">
                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <x-quill label="Soal" model="form.soal" />
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <x-quill label="Opsi A" model="form.opsi_a" />
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <x-quill label="Opsi B" model="form.opsi_b" />
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <x-quill label="Opsi C" model="form.opsi_c" />
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <x-quill label="Opsi D" model="form.opsi_d" />
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <x-quill label="Opsi E" model="form.opsi_e" />
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <x-quill label="Kunci Jawaban" model="form.kunci_jawaban" />
                                </div>
                            </div>
                        </div> --}}

                        <x-form-action 
                            :cancelUrl="route('admin.soal-intelektual-subtes1')" 
                            :isUpdate="$isUpdate == true" 
                        />
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>