<div>
    <x-breadcrumb :breadcrumbs="[
        ['url' => route('admin.dashboard'), 'title' => 'Dashboard'],
        ['url' => null, 'title' => 'Data Nomor Laporan Penilaian']
    ]" />
    <div class="row">
        <div class="col-md-12 stretch-card">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">Form Ttd Laporan Penilaian</h6>
                    <form wire:submit="save">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="mb-3">
                                    <label class="form-label">Nama</label>
                                    <input type="text" wire:model="nama" class="form-control @error('nama') is-invalid @enderror">
                                    @error('nama')
                                    <label class="error invalid-feedback">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div><!-- Col -->
                        </div><!-- Row -->
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="mb-3">
                                    <label class="form-label">NIP</label>
                                    <input type="text" wire:model="nip" class="form-control @error('nip') is-invalid @enderror">
                                    @error('nip')
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
                                        <input type="radio" class="form-check-input" wire:model="is_active"
                                            id="radioInline" value="t"
                                            {{ $is_active == 'true' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="radioInline">
                                            Aktif
                                        </label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input type="radio" class="form-check-input" wire:model="is_active"
                                            id="radioInline1" value="f"
                                            {{ $is_active == 'false' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="radioInline1">
                                            Non Aktif
                                        </label>
                                    </div>
                                </div><!-- Col -->
                            </div><!-- Row -->
                        @endif

                        <div class="row">
                            <div class="col-sm-12">
                                <div class="mb-3">
                                    <label class="form-label">TTE (maksimal 200 KB)</label>
                                    @if ($ttd_url)
                                        <br />
                                        <img src="{{ $ttd_url }}" class="img-fluid rounded mb-3"
                                            width="100">
                                    @endif
                                    <input class="form-control @error('ttd') is-invalid @enderror"
                                        wire:model="ttd" type="file" id="formFile" accept="image/*">
                                    @error('ttd')
                                        <label class="error invalid-feedback">{{ $message }}</label>
                                    @enderror
                                </div>
                                <div wire:loading wire:target="ttd">Uploading...</div>
                            </div><!-- Col -->
                        </div><!-- Row -->

                        <div class="mt-3">
                            <a href="{{ route('admin.ttd-laporan') }}" wire:navigate class="btn btn-sm btn-inverse-danger me-2">Batal</a>
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