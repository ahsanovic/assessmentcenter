<div>
    <x-breadcrumb :breadcrumbs="[
        ['url' => route('admin.dashboard'), 'title' => 'Dashboard'],
        ['url' => null, 'title' => 'Data Metode Tes']
    ]" />
    <div class="row">
        <div class="col-md-12 stretch-card">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">Form Metode Tes</h6>
                    <form wire:submit="save">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="mb-3">
                                    <label class="form-label">Metode Tes</label>
                                    <input type="text" wire:model="form.metode_tes" class="form-control @error('form.metode_tes') is-invalid @enderror">
                                    @error('form.metode_tes')
                                    <label class="error invalid-feedback">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div><!-- Col -->
                        </div><!-- Row -->
                        <x-form-action 
                            :cancelUrl="route('admin.metode-tes')" 
                            :isUpdate="$isUpdate == true" 
                        />
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>