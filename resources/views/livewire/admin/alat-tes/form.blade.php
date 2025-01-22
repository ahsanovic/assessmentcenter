<div>
    <x-breadcrumb :breadcrumbs="[
        ['url' => route('admin.dashboard'), 'title' => 'Dashboard'],
        ['url' => null, 'title' => 'Data Alat Tes']
    ]" />
    <div class="row">
        <div class="col-md-12 stretch-card">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">Form Alat Tes</h6>
                    <form wire:submit="save">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="mb-3">
                                    <label class="form-label">Alat Tes</label>
                                    <input type="text" wire:model="form.alat_tes" class="form-control @error('form.alat_tes') is-invalid @enderror">
                                    @error('form.alat_tes')
                                    <label class="error invalid-feedback">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div><!-- Col -->
                        </div><!-- Row -->
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="mb-3">
                                    <label class="form-label">Definisi Aspek Potensi</label>
                                    <input type="text" wire:model="form.definisi_aspek_potensi" class="form-control @error('form.definisi_aspek_potensi') is-invalid @enderror">
                                    @error('form.definisi_aspek_potensi')
                                    <label class="error invalid-feedback">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div><!-- Col -->
                        </div><!-- Row -->
                        <a href="{{ route('admin.alat-tes') }}" wire:navigate class="btn btn-sm btn-inverse-danger me-2">Batal</a>
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