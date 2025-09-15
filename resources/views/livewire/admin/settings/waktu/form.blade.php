<div>
    <x-breadcrumb :breadcrumbs="[
        ['url' => route('admin.dashboard'), 'title' => 'Dashboard'],
        ['url' => null, 'title' => 'Waktu Tes']
    ]" />
    <div class="row">
        <div class="col-md-12 stretch-card">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">Form Setting Waktu Tes</h6>
                    <form wire:submit="save">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <label for="jenis-tes" class="form-label">Jenis Tes</label>
                                    <select wire:model="form.jenis_tes"
                                        class="form-select @error('jenis_tes') is-invalid @enderror"
                                        id="metode-tes">
                                        <option value="">-pilih-</option>
                                        <option value="1">Tes Potensi</option>
                                        <option value="2">Tes Literasi Digital & Emerging</option>
                                        <option value="3">Tes Intelektual - Sub Tes 1</option>
                                        <option value="4">Tes Intelektual - Sub Tes 2</option>
                                        <option value="5">Tes Intelektual - Sub Tes 3</option>
                                        <option value="6">Tes Kompetensi Teknis</option>
                                    </select>
                                    @error('form.jenis_tes')
                                        <label class="error invalid-feedback">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <label class="form-label">Waktu tes (menit)</label>
                                    <input type="text" wire:model="form.waktu" class="form-control @error('form.waktu') is-invalid @enderror">
                                    @error('form.waktu')
                                    <label class="error invalid-feedback">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div><!-- Col -->
                        </div><!-- Row -->
                        <div class="row mb-4">
                            <div class="col-sm-12">
                                <div class="mb-1">
                                    <label class="form-label">Status</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input type="radio" class="form-check-input @error('form.is_active') is-invalid @enderror" wire:model="form.is_active"
                                        id="radioInline" value="true"
                                        {{ $is_active == 'true' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="radioInline">
                                        Aktif
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input type="radio" class="form-check-input @error('form.is_active') is-invalid @enderror" wire:model="form.is_active"
                                        id="radioInline1" value="false"
                                        {{ $is_active == 'false' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="radioInline1">
                                        Non Aktif
                                    </label>
                                </div>
                                @error('form.is_active')
                                <label class="error invalid-feedback d-block">{{ $message }}</label>
                                @enderror
                            </div><!-- Col -->
                        </div><!-- Row -->
                        <a href="{{ route('admin.settings.waktu') }}" wire:navigate class="btn btn-sm btn-inverse-danger me-2">Batal</a>
                        <button
                            type="submit"
                            class="btn btn-sm btn-inverse-success"
                        >
                            {{ $isUpdate == true ? 'Ubah' : 'Simpan' }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>