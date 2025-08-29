<div>
    <x-breadcrumb :breadcrumbs="[
        ['url' => route('admin.dashboard'), 'title' => 'Dashboard'],
        ['url' => null, 'title' => 'Data Soal Sub Tes 2']
    ]" />
    <div class="row">
        <div class="col-md-12 stretch-card">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">Form Soal Intelektual Sub Tes 2</h6>
                    <form wire:submit="save" enctype="multipart/form-data">
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
                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <label class="form-label">Deskripsi Soal</label>
                                    <textarea 
                                        class="form-control @error('form.soal') is-invalid @enderror"
                                        rows="3"
                                        wire:model="form.soal"
                                    >{{ old('soal') }}</textarea>
                                    @error('form.soal')
                                    <label class="error invalid-feedback">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div><!-- Col -->
                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <x-image-upload 
                                        label="Upload Gambar Soal (jika ada)" 
                                        model="form.image_soal" 
                                        field="image_soal"
                                        :value="$form['image_soal']"
                                        :old="$form['image_soal']"
                                    />
                                    @error('form.image_soal')
                                    <label class="error invalid-feedback">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div><!-- Col -->
                        </div><!-- Row -->
                        <div class="row mb-3">
                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <label class="form-label">Pilihan A</label>
                                    <input type="text" class="form-control mb-2 @error('form.opsi_a') is-invalid @enderror" placeholder="Teks pilihan A" wire:model="form.opsi_a">
                                    @error('form.opsi_a')
                                    <label class="error invalid-feedback mb-2">{{ $message }}</label>
                                    @enderror
                                    <x-image-upload 
                                        label="Upload Gambar Pilihan A (jika ada)" 
                                        model="form.image_opsi_a"
                                        field="image_opsi_a"
                                        :value="$form['image_opsi_a']"
                                        :old="$form['image_opsi_a']"
                                    />
                                </div>
                            </div><!-- Col -->
                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <label class="form-label">Pilihan B</label>
                                    <input type="text" class="form-control mb-2 @error('form.opsi_b') is-invalid @enderror" placeholder="Teks pilihan B" wire:model="form.opsi_b">
                                    @error('form.opsi_b')
                                    <label class="error invalid-feedback mb-2">{{ $message }}</label>
                                    @enderror
                                    <x-image-upload 
                                        label="Upload Gambar Pilihan B (jika ada)" 
                                        model="form.image_opsi_b" 
                                        field="image_opsi_b"
                                        :value="$form['image_opsi_b']"
                                        :old="$form['image_opsi_b']"
                                    />
                                </div>
                            </div><!-- Col -->
                        </div><!-- Row -->
                        <div class="row mb-3">
                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <label class="form-label">Pilihan C</label>
                                    <input type="text" class="form-control mb-2 @error('form.opsi_c') is-invalid @enderror" placeholder="Teks pilihan C" wire:model="form.opsi_c">
                                    @error('form.opsi_c')
                                    <label class="error invalid-feedback mb-2">{{ $message }}</label>
                                    @enderror
                                    <x-image-upload 
                                        label="Upload Gambar Pilihan C (jika ada)" 
                                        model="form.image_opsi_c" 
                                        field="image_opsi_c"
                                        :value="$form['image_opsi_c']"
                                        :old="$form['image_opsi_c']"
                                    />
                                </div>
                            </div><!-- Col -->
                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <label class="form-label">Pilihan D</label>
                                    <input type="text" class="form-control mb-2 @error('form.opsi_d') is-invalid @enderror" placeholder="Teks pilihan D" wire:model="form.opsi_d">
                                    @error('form.opsi_d')
                                    <label class="error invalid-feedback mb-2">{{ $message }}</label>
                                    @enderror
                                    <x-image-upload 
                                        label="Upload Gambar Pilihan D (jika ada)" 
                                        model="form.image_opsi_d" 
                                        field="image_opsi_d"
                                        :value="$form['image_opsi_d']"
                                        :old="$form['image_opsi_d']"
                                    />
                                </div>
                            </div><!-- Col -->
                        </div><!-- Row -->
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <label class="form-label">Pilihan E</label>
                                    <input type="text" class="form-control mb-2 @error('form.opsi_e') is-invalid @enderror" placeholder="Teks pilihan E" wire:model="form.opsi_e">
                                    @error('form.opsi_e')
                                    <label class="error invalid-feedback mb-2">{{ $message }}</label>
                                    @enderror
                                    <x-image-upload 
                                        label="Upload Gambar Pilihan E (jika ada)" 
                                        model="form.image_opsi_e" 
                                        field="image_opsi_e"
                                        :value="$form['image_opsi_e']"
                                        :old="$form['image_opsi_e']"
                                    />
                                </div>
                            </div><!-- Col -->
                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <label for="kunci-jawaban" class="form-label">Kunci Jawaban</label>
                                    <select wire:model="form.kunci_jawaban" class="mb-2 form-select @error('form.kunci_jawaban') is-invalid @enderror" id="kunci-jawaban">
                                        <option value="">pilih kunci jawaban</option>
                                        <option value="A">A</option>
                                        <option value="B">B</option>
                                        <option value="C">C</option>
                                        <option value="D">D</option>
                                        <option value="E">E</option>
                                    </select>
                                    @error('form.kunci_jawaban')
                                    <label class="error invalid-feedback mb-2">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>
                        </div><!-- Row -->
                        
                        <div class="mt-3">
                            <a href="{{ route('admin.soal-intelektual-subtes2') }}" wire:navigate class="btn btn-sm btn-inverse-danger me-2">Batal</a>
                            <button
                                type="submit"
                                class="btn btn-sm btn-inverse-success"
                            >
                                {{ $isUpdate == true ? 'Ubah' : 'Simpan' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>