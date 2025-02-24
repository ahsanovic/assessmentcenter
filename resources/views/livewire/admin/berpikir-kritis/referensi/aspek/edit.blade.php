<div>
    <x-breadcrumb :breadcrumbs="[
        ['url' => route('admin.dashboard'), 'title' => 'Dashboard'],
        ['url' => null, 'title' => 'Berpikir Kritis'],
        ['url' => null, 'title' => 'Data Referensi Aspek'],
    ]" />
    <div class="row">
        <div class="col-md-12 stretch-card">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">Form Referensi Aspek</h6>
                    <form wire:submit="save">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <label class="form-label">Nama Aspek</label>
                                    <input type="text" wire:model="aspek"
                                        class="form-control @error('aspek') is-invalid @enderror"
                                        placeholder="masukkan nama aspek" value="{{ old('aspek') }}">
                                    @error('aspek')
                                        <label class="error invalid-feedback">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div><!-- Col -->
                        </div><!-- Row -->
                        <div class="row">
                            <div class="col-sm-4">
                                <div class="mb-3">
                                    <label class="form-label">Nomor Aspek</label>
                                    <input type="number" wire:model="aspek_nomor"
                                        class="form-control @error('aspek_nomor') is-invalid @enderror"
                                        placeholder="masukkan nomor aspek" value="{{ old('aspek_nomor') }}">
                                    @error('aspek_nomor')
                                        <label class="error invalid-feedback">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div><!-- Col -->
                        </div><!-- Row -->
                        <div class="row">
                            <div class="col-sm-4">
                                <label class="form-label">Nomor Indikator</label>
                                <div class="mb-3">
                                    @for ($i = 1; $i <= 8; $i++)
                                        <div class="form-check form-check-inline">
                                            <input type="checkbox" wire:model.defer="indikator_nomor"
                                                value="{{ $i }}" class="form-check-input @error('indikator_nomor') is-invalid @enderror"
                                                id="checkInline{{ $i }}">
                                            <label class="form-check-label" for="checkInline{{ $i }}">
                                                {{ $i }}
                                            </label>
                                        </div>
                                    @endfor
                                    @error('indikator_nomor')
                                        <label class="error invalid-feedback">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div><!-- Col -->
                        </div><!-- Row -->
                        <a href="{{ route('admin.ref-aspek-berpikir-kritis') }}" wire:navigate
                            class="btn btn-sm btn-inverse-danger me-2">Batal</a>
                        <button type="submit" class="btn btn-sm btn-inverse-success">
                            Update
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
