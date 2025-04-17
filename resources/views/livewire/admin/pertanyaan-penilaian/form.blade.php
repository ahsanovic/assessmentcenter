<div>
    <x-breadcrumb :breadcrumbs="[
        ['url' => route('admin.dashboard'), 'title' => 'Dashboard'],
        ['url' => null, 'title' => 'Referensi Pertanyaan Penilaian Pribadi']
    ]" />
    <div class="row">
        <div class="col-md-12 stretch-card">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">Form Referensi Pertanyaan Penilaian Pribadi</h6>
                    <form wire:submit="save">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="mb-3">
                                    <label class="form-label">Pertanyaan</label>
                                    <textarea
                                        wire:model="pertanyaan"
                                        class="form-control @error('pertanyaan') is-invalid @enderror"
                                        rows="3"
                                    >
                                    </textarea>
                                    @error('pertanyaan')
                                    <label class="error invalid-feedback">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div><!-- Col -->
                        </div><!-- Row -->
                        <div class="row">
                            <div class="col-sm-2">
                                <div class="mb-3">
                                    <label class="form-label">Urutan ke-</label>
                                    <input
                                        wire:model="urutan"
                                        class="form-control @error('urutan') is-invalid @enderror"
                                    />
                                    @error('urutan')
                                    <label class="error invalid-feedback">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div><!-- Col -->
                        </div><!-- Row -->

                        <div class="mt-3">
                            <a href="{{ route('admin.pertanyaan-penilaian') }}" wire:navigate class="btn btn-sm btn-inverse-danger me-2">Batal</a>
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