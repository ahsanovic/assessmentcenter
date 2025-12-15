<div>
    <x-breadcrumb :breadcrumbs="[
        ['url' => route('admin.dashboard'), 'title' => 'Dashboard'],
        ['url' => null, 'title' => 'Data Kuesioner']
    ]" />
    <div class="row">
        <div class="col-md-12 stretch-card">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">Form Kuesioner</h6>
                    <form wire:submit="save">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="mb-3">
                                    <label class="form-label">Pertanyaan Kuesioner</label>
                                    <input type="text" wire:model="form.deskripsi" class="form-control @error('form.deskripsi') is-invalid @enderror">
                                    @error('form.deskripsi')
                                    <label class="error invalid-feedback">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div><!-- Col -->
                        </div><!-- Row -->
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="mb-1">
                                    <label class="form-label">Pertanyaan Esai ?</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input type="radio" class="form-check-input @error('form.is_esai') is-invalid @enderror" wire:model="form.is_esai"
                                        id="radioInline" value="t"
                                        {{ $is_esai == 't' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="radioInline">
                                        Ya
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input type="radio" class="form-check-input @error('form.is_esai') is-invalid @enderror" wire:model="form.is_esai"
                                        id="radioInline1" value="f"
                                        {{ $is_esai == 'f' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="radioInline1">
                                        Tidak
                                    </label>
                                </div>
                                @error('form.is_esai')
                                    <label class="error invalid-feedback d-block">{{ $message }}</label>
                                @enderror
                            </div><!-- Col -->
                        </div><!-- Row -->
                        <div class="row mt-4">
                            <div class="col-sm-12">
                                <div class="mb-1">
                                    <label class="form-label">Status Pertanyaan</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input type="radio" class="form-check-input @error('form.is_active') is-invalid @enderror" wire:model="form.is_active"
                                        id="radioInline2" value="t"
                                        {{ $is_active == 't' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="radioInline2">
                                        Aktif
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input type="radio" class="form-check-input @error('form.is_active') is-invalid @enderror" wire:model="form.is_active"
                                        id="radioInline3" value="f"
                                        {{ $is_active == 'f' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="radioInline3">
                                        Non Aktif
                                    </label>
                                </div>
                                @error('form.is_active')
                                    <label class="error invalid-feedback d-block">{{ $message }}</label>
                                @enderror
                            </div><!-- Col -->
                        </div><!-- Row -->
                        <x-form-action 
                            :cancelUrl="route('admin.kuesioner')" 
                            :isUpdate="$isUpdate == true" 
                        />
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>