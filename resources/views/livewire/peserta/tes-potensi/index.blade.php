<div>
    <x-breadcrumb :breadcrumbs="[
        ['url' => route('assessor.dashboard'), 'title' => 'Dashboard'],
        ['url' => null, 'title' => 'Tes Potensi'],
    ]" />
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="row mt-3 justify-content-center">
                        <div class="col-sm-2">
                            <div class="mb-3">
                                <form wire:submit="submit">
                                    <input wire:model="pin_ujian" class="form-control form-control-lg @error('pin_ujian') is-invalid @enderror"
                                        placeholder="masukkan PIN ujian" autofocus />
                                        @error('pin_ujian')
                                    <label class="error invalid-feedback">{{ $message }}</label>
                                    @enderror
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
