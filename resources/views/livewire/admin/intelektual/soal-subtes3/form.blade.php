<div>
    <x-breadcrumb :breadcrumbs="[
        ['url' => route('admin.dashboard'), 'title' => 'Dashboard'],
        ['url' => null, 'title' => 'Data Soal Sub Tes 3']
    ]" />
    <div class="row">
        <div class="col-md-12 stretch-card">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">Form Soal Intelektual Sub Tes 3</h6>
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
                                    <x-image-upload 
                                        label="Upload Gambar Soal" 
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
                                    <x-image-upload 
                                    label="" 
                                    model="form.image_opsi_a"
                                    field="image_opsi_a"
                                    :value="$form['image_opsi_a']"
                                    :old="$form['image_opsi_a']"
                                    />
                                    @error('form.image_opsi_a')
                                    <label class="error invalid-feedback mb-2">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div><!-- Col -->
                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <label class="form-label">Pilihan B</label>
                                    <x-image-upload 
                                        label="" 
                                        model="form.image_opsi_b" 
                                        field="image_opsi_b"
                                        :value="$form['image_opsi_b']"
                                        :old="$form['image_opsi_b']"
                                    />
                                    @error('form.image_opsi_b')
                                    <label class="error invalid-feedback mb-2">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div><!-- Col -->
                        </div><!-- Row -->
                        <div class="row mb-3">
                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <label class="form-label">Pilihan C</label>
                                    <x-image-upload 
                                        label="" 
                                        model="form.image_opsi_c" 
                                        field="image_opsi_c"
                                        :value="$form['image_opsi_c']"
                                        :old="$form['image_opsi_c']"
                                    />
                                    @error('form.image_opsi_c')
                                    <label class="error invalid-feedback mb-2">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div><!-- Col -->
                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <label class="form-label">Pilihan D</label>
                                    <x-image-upload 
                                        label="" 
                                        model="form.image_opsi_d" 
                                        field="image_opsi_d"
                                        :value="$form['image_opsi_d']"
                                        :old="$form['image_opsi_d']"
                                    />
                                    @error('form.image_opsi_d')
                                    <label class="error invalid-feedback mb-2">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div><!-- Col -->
                        </div><!-- Row -->
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <label class="form-label">Pilihan E</label>
                                    <x-image-upload 
                                        label="" 
                                        model="form.image_opsi_e" 
                                        field="image_opsi_e"
                                        :value="$form['image_opsi_e']"
                                        :old="$form['image_opsi_e']"
                                    />
                                    @error('form.image_opsi_e')
                                    <label class="error invalid-feedback mb-2">{{ $message }}</label>
                                    @enderror
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
                        
                        <x-form-action 
                            :cancelUrl="route('admin.soal-intelektual-subtes3')" 
                            :isUpdate="$isUpdate == true" 
                        />
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>