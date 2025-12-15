<div>
    <x-breadcrumb :breadcrumbs="[
        ['url' => route('admin.dashboard'), 'title' => 'Dashboard'],
        ['url' => null, 'title' => 'Interpersonal'],
        ['url' => null, 'title' => 'Soal']
    ]" />
    <div class="row">
        <div class="col-md-12 stretch-card">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">Form Soal</h6>
                    <form wire:submit="save">
                        <div class="row">
                            <div class="mb-3">
                                <label for="jenis-indikator" class="form-label">Jenis Indikator</label>
                                <select wire:model="form.jenis_indikator_id" class="form-select" id="jenis-indikator">
                                    @foreach ($indikator as $key => $item)
                                        <option value="{{ $key }}">{{ $item }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="mb-3">
                                    <label class="form-label">Deskripsi Soal</label>
                                    <input type="text" wire:model="form.soal" class="form-control @error('form.soal') is-invalid @enderror">
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
                                    <input type="text" wire:model="form.opsi_a" class="form-control @error('form.opsi_a') is-invalid @enderror">
                                    @error('form.opsi_a')
                                    <label class="error invalid-feedback">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div><!-- Col -->
                            <div class="col-sm-4">
                                <div class="mb-3">
                                    <label class="form-label">Poin</label>
                                    <input type="number" wire:model="form.poin_opsi_a" class="form-control @error('form.poin_opsi_a') is-invalid @enderror">
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
                                    <input type="text" wire:model="form.opsi_b" class="form-control @error('form.opsi_b') is-invalid @enderror">
                                    @error('form.opsi_b')
                                    <label class="error invalid-feedback">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div><!-- Col -->
                            <div class="col-sm-4">
                                <div class="mb-3">
                                    <label class="form-label">Poin</label>
                                    <input type="number" wire:model="form.poin_opsi_b" class="form-control @error('form.poin_opsi_b') is-invalid @enderror">
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
                                    <input type="text" wire:model="form.opsi_c" class="form-control @error('form.opsi_c') is-invalid @enderror">
                                    @error('form.opsi_c')
                                    <label class="error invalid-feedback">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div><!-- Col -->
                            <div class="col-sm-4">
                                <div class="mb-3">
                                    <label class="form-label">Poin</label>
                                    <input type="number" wire:model="form.poin_opsi_c" class="form-control @error('form.poin_opsi_c') is-invalid @enderror">
                                    @error('form.poin_opsi_c')
                                    <label class="error invalid-feedback">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div><!-- Col -->
                        </div><!-- Row -->

                        <x-form-action :cancelUrl="route('admin.soal-interpersonal')" :isUpdate="true" />
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>