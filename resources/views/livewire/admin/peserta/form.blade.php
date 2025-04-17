<div>
    <x-breadcrumb :breadcrumbs="[
        ['url' => route('admin.dashboard'), 'title' => 'Dashboard'],
        ['url' => null, 'title' => 'Peserta']
    ]" />
    <div class="row">
        <div class="col-md-12 stretch-card">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">Form Peserta</h6>
                    <form wire:submit="save">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <label for="event" class="form-label">Event</label>
                                    <select wire:model="event_id" class="form-select @error('event_id') is-invalid @enderror" id="event">
                                        <option value="">-pilih-</option>
                                        @foreach ($option_event as $key => $item)
                                            <option value="{{ $key }}">{{ $item }}</option>
                                        @endforeach
                                    </select>
                                    @error('event_id')
                                    <label class="error invalid-feedback">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="mb-3">
                                    <label class="form-label">Nama Peserta</label>
                                    <input
                                        type="text"
                                        wire:model="nama"
                                        class="form-control @error('nama') is-invalid @enderror"
                                        placeholder="masukkan nama peserta"
                                    >
                                    @error('nama')
                                    <label class="error invalid-feedback">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div><!-- Col -->
                        </div><!-- Row -->
                        <div class="row">
                            <div class="col-sm-4">
                                <div class="mb-3">
                                    <label class="form-label">NIP</label>
                                    <input
                                        type="number"
                                        wire:model="nip"
                                        class="form-control @error('nip') is-invalid @enderror"
                                        placeholder="masukkan nip"
                                    >
                                    @error('nip')
                                    <label class="error invalid-feedback">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div><!-- Col -->
                        </div><!-- Row -->
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="mb-3">
                                    <label class="form-label">Jabatan</label>
                                    <input
                                        type="text"
                                        wire:model="jabatan"
                                        class="form-control @error('jabatan') is-invalid @enderror"
                                        placeholder="masukkan jabatan"
                                    >
                                    @error('jabatan')
                                    <label class="error invalid-feedback">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div><!-- Col -->
                        </div><!-- Row -->
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="mb-3">
                                    <label class="form-label">Unit Kerja</label>
                                    <input
                                        type="text"
                                        wire:model="unit_kerja"
                                        class="form-control @error('unit_kerja') is-invalid @enderror"
                                        placeholder="masukkan unit kerja"
                                    >
                                    @error('unit_kerja')
                                    <label class="error invalid-feedback">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div><!-- Col -->
                        </div><!-- Row -->
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="mb-3">
                                    <label class="form-label">Instansi</label>
                                    <input
                                        type="text"
                                        wire:model="instansi"
                                        class="form-control @error('instansi') is-invalid @enderror"
                                        placeholder="masukkan instansi"
                                    >
                                    @error('instansi')
                                    <label class="error invalid-feedback">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div><!-- Col -->
                        </div><!-- Row -->
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="mb-3">
                                    <label class="form-label">Password</label>
                                    <input
                                        type="password"
                                        wire:model="password"
                                        class="form-control @error('password') is-invalid @enderror"
                                        placeholder="masukkan password"
                                    >
                                    @error('password')
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
                                        <input type="radio" class="form-check-input" wire:model="is_active" id="radioInline" value="true" {{ $is_active == 'true' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="radioInline">
                                                Aktif
                                            </label>
                                        </div>
                                    <div class="form-check form-check-inline">
                                        <input type="radio" class="form-check-input" wire:model="is_active" id="radioInline1" value="false" {{ $is_active == 'true' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="radioInline1">
                                            Tidak Aktif
                                        </label>
                                    </div>
                                </div><!-- Col -->
                            </div><!-- Row -->
                        @endif
                        
                        <div class="mt-3">
                            <a href="{{ route('admin.peserta') }}" wire:navigate class="btn btn-sm btn-inverse-danger me-2">Batal</a>
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
@push('js')
    @script()
        <script>
            $(document).ready(function() {
                $('#event').select2()
                $('#event').on('change', function() {
                    let data = $(this).val()
                    $wire.set('event_id', data, false)
                    $wire.assessor = data
                })
            })
        </script>
    @endscript
@endpush