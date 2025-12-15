<div>
    <x-breadcrumb :breadcrumbs="[
        ['url' => route('admin.dashboard'), 'title' => 'Dashboard'],
        ['url' => null, 'title' => 'Urutan Tes']
    ]" />
    <div class="row">
        <div class="col-md-12 stretch-card">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">Form Setting Urutan Tes</h6>
                    <form wire:submit="save">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <label for="jenis-indikator" class="form-label">Alat Tes</label>
                                    <select wire:model="form.alat_tes_id"
                                        class="form-select @error('form.alat_tes_id') is-invalid @enderror">
                                        <option value="">- pilih -</option>
                                        @foreach ($option_alat_tes as $key => $item)
                                            <option value="{{ $key }}">{{ $item }}</option>
                                        @endforeach
                                    </select>
                                    @error('form.alat_tes_id')
                                        <label class="error invalid-feedback">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="mb-3">
                                    <label class="form-label">Urutan tes</label>
                                    <input type="text" wire:model="form.urutan" class="form-control @error('form.urutan') is-invalid @enderror">
                                    @error('form.urutan')
                                    <label class="error invalid-feedback">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div><!-- Col -->
                        </div><!-- Row -->

                        <x-form-action 
                            :cancelUrl="route('admin.settings.urutan')" 
                            :isUpdate="$isUpdate == true" 
                        />
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>