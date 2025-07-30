<div>
    <x-breadcrumb :breadcrumbs="[
        ['url' => route('admin.dashboard'), 'title' => 'Dashboard'],
        ['url' => null, 'title' => 'Data Model Soal Intelektual']
    ]" />
    <div class="row">
        <div class="col-md-12 stretch-card">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">Form Model Soal Intelektual</h6>
                    <form wire:submit="save">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="mb-3">
                                    <label class="form-label">Model Soal</label>
                                    <input type="text" wire:model="form.jenis" class="form-control @error('form.jenis') is-invalid @enderror">
                                    @error('form.jenis')
                                    <label class="error invalid-feedback">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div><!-- Col -->
                        </div><!-- Row -->
                        @if ($isUpdate == 'true')
                            <div class="row mb-4">
                                <div class="col-sm-12">
                                    <div class="mb-1">
                                        <label class="form-label">Status</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input type="radio" class="form-check-input" wire:model="form.is_active" id="radioInline" value="true" {{ $is_active == 'true' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="radioInline">
                                                Aktif
                                            </label>
                                        </div>
                                    <div class="form-check form-check-inline">
                                        <input type="radio" class="form-check-input" wire:model="form.is_active" id="radioInline1" value="false" {{ $is_active == 'true' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="radioInline1">
                                            Tidak Aktif
                                        </label>
                                    </div>
                                </div><!-- Col -->
                            </div><!-- Row -->
                        @endif

                        <div class="mt-3">
                            <a href="{{ route('admin.model-soal-intelektual') }}" wire:navigate class="btn btn-sm btn-inverse-danger me-2">Batal</a>
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