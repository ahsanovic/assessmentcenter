<div>
    <x-breadcrumb :breadcrumbs="[
        ['url' => route('admin.dashboard'), 'title' => 'Dashboard'],
        ['url' => null, 'title' => 'Motivasi dan Komitmen'],
        ['url' => null, 'title' => 'Data Referensi']
    ]" />
    <div class="row">
        <div class="col-md-12 stretch-card">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">Form Indikator</h6>
                    <form wire:submit="save">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <label class="form-label">Nama Indikator</label>
                                    <input
                                        type="text"
                                        wire:model="indikator_nama"
                                        class="form-control @error('indikator_nama') is-invalid @enderror"
                                        placeholder="masukkan nama indikator"
                                        value="{{ old('indikator_nama') }}"
                                    >
                                    @error('indikator_nama')
                                    <label class="error invalid-feedback">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div><!-- Col -->
                        </div><!-- Row -->
                        <div class="row">
                            <div class="col-sm-4">
                                <div class="mb-3">
                                    <label class="form-label">Nomor Indikator</label>
                                    <input
                                        type="number"
                                        wire:model="indikator_nomor"
                                        class="form-control @error('indikator_nomor') is-invalid @enderror"
                                        placeholder="masukkan nomor indikator"
                                        value="{{ old('indikator_nomor') }}"
                                    >
                                    @error('indikator_nomor')
                                    <label class="error invalid-feedback">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div><!-- Col -->
                        </div><!-- Row -->

                        <div class="mt-3">
                            <a href="{{ route('admin.ref-motivasi-komitmen') }}" wire:navigate class="btn btn-sm btn-inverse-danger me-2">Batal</a>
                            <button
                                type="submit"
                                class="btn btn-sm btn-inverse-success"
                            >
                                Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>