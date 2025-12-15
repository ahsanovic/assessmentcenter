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
                                    <div wire:ignore>
                                        <select wire:model="event_id" class="form-select @error('event_id') is-invalid @enderror" id="event">
                                            <option value="">-pilih-</option>
                                            @foreach ($option_event as $key => $item)
                                                <option value="{{ $key }}">{{ $item }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @error('event_id')
                                    <label class="error invalid-feedback d-block">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <label for="jenis-peserta" class="form-label">Jenis Peserta</label>
                                    <select wire:model.live="jenis_peserta_id" class="form-select @error('jenis_peserta_id') is-invalid @enderror" id="jenis-peserta">
                                        <option value="">-pilih-</option>
                                        @foreach ($option_jenis_peserta as $key => $item)
                                            <option value="{{ $key }}">{{ $item }}</option>
                                        @endforeach
                                    </select>
                                    @error('jenis_peserta_id')
                                    <label class="error invalid-feedback">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="mb-3">
                                    <label class="form-label">Nama Peserta (tanpa gelar)</label>
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
                        @if ($jenis_peserta_id == 1) {{-- ASN --}}
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
                        @endif
                        @if ($jenis_peserta_id == 2) {{-- Non ASN --}}
                        <div class="row">
                            <div class="col-sm-4">
                                <div class="mb-3">
                                    <label class="form-label">NIK</label>
                                    <input
                                        type="number"
                                        wire:model="nik"
                                        class="form-control @error('nik') is-invalid @enderror"
                                        placeholder="masukkan nik"
                                    >
                                    @error('nik')
                                    <label class="error invalid-feedback">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div><!-- Col -->
                        </div><!-- Row -->
                        @endif
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
                        
                        <x-form-action 
                            :cancelUrl="route('admin.peserta')" 
                            :isUpdate="$isUpdate == 'true'" 
                        />
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
                let eventSelect = $('#event');

                eventSelect.select2()
                    .on('change', function(e) {
                        @this.set('event_id', $(this).val());
                    });
            })
        </script>
    @endscript
@endpush