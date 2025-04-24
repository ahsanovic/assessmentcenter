<div>
    <x-breadcrumb :breadcrumbs="[['url' => route('admin.dashboard'), 'title' => 'Dashboard'], ['url' => null, 'title' => 'Event']]" />
    <div class="row">
        <div class="col-md-12 stretch-card">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">Form Event</h6>
                    <form wire:submit="save">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="mb-3">
                                    <label class="form-label">Nama Event</label>
                                    <input type="text" wire:model="nama_event"
                                        class="form-control @error('nama_event') is-invalid @enderror"
                                        placeholder="masukkan nama event">
                                    @error('nama_event')
                                        <label class="error invalid-feedback">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div><!-- Col -->
                        </div><!-- Row -->
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <label for="metode-tes" class="form-label">Metode Tes</label>
                                    <select wire:model="metode_tes_id"
                                        class="form-select @error('metode_tes_id') is-invalid @enderror"
                                        id="metode-tes">
                                        <option value="">-pilih-</option>
                                        @foreach ($option_metode_tes as $key => $item)
                                            <option value="{{ $key }}">{{ $item }}</option>
                                        @endforeach
                                    </select>
                                    @error('metode_tes_id')
                                        <label class="error invalid-feedback">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <label for="jabatan-diuji" class="form-label">Jenis Jabatan yang Diujikan</label>
                                    <select wire:model="jabatan_diuji_id"
                                        class="form-select @error('jabatan_diuji_id') is-invalid @enderror"
                                        id="jabatan-diuji">
                                        <option value="">-pilih-</option>
                                        @foreach ($option_jabatan_diuji as $key => $item)
                                            <option value="{{ $key }}">{{ $item }}</option>
                                        @endforeach
                                    </select>
                                    @error('jabatan_diuji_id')
                                        <label class="error invalid-feedback">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        {{-- <div class="row">
                            <div class="col-sm-6">
                                <div class="mb-3" wire:ignore>
                                    <label for="jenis-indikator" class="form-label">Alat Tes</label>
                                    <select wire:model="alat_tes_id" id="alat_tes_id"
                                        class="form-select @error('alat_tes_id') is-invalid @enderror" multiple="multiple">
                                        @foreach ($option_alat_tes as $key => $item)
                                            <option value="{{ $key }}" {{ in_array($key, $alat_tes_id ?: []) ? 'selected' : '' }}>{{ $item }}</option>
                                        @endforeach
                                    </select>
                                    @error('alat_tes_id')
                                        <label class="error invalid-feedback">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>
                        </div> --}}
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="mb-3" wire:ignore>
                                    <label for="assessor" class="form-label">Assessor</label>
                                    <select wire:model="assessor" id="assessor"
                                        class="form-control @error('assessor') is-invalid @enderror" multiple="multiple">
                                        @foreach ($option_assessor as $key => $item)
                                            <option value="{{ $key }}" {{ in_array($key, $assessor ?: []) ? 'selected' : '' }}>{{ $item }}</option>
                                        @endforeach
                                    </select>
                                    @error('assessor')
                                        <label class="error invalid-feedback">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-4">
                                <div class="mb-3">
                                    <label class="form-label">Tanggal Mulai Pelaksanaan</label>
                                    <div class="input-group flatpickr" id="flatpickr-date">
                                        <input type="text" wire:model="tgl_mulai"
                                            class="form-control flatpickr-input @error('tgl_mulai') is-invalid @enderror"
                                            placeholder="Select date" data-input="" readonly="readonly">
                                        <span class="input-group-text input-group-addon" data-toggle="">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                class="feather feather-calendar">
                                                <rect x="3" y="4" width="18" height="18" rx="2"
                                                    ry="2"></rect>
                                                <line x1="16" y1="2" x2="16" y2="6">
                                                </line>
                                                <line x1="8" y1="2" x2="8" y2="6">
                                                </line>
                                                <line x1="3" y1="10" x2="21" y2="10">
                                                </line>
                                            </svg>
                                        </span>
                                        @error('tgl_mulai')
                                            <label class="error invalid-feedback">{{ $message }}</label>
                                        @enderror
                                    </div>
                                </div>
                            </div><!-- Col -->
                            <div class="col-sm-4">
                                <div class="mb-3">
                                    <label class="form-label">Tanggal Selesai Pelaksanaan</label>
                                    <div class="input-group flatpickr" id="flatpickr-date">
                                        <input type="text" wire:model="tgl_selesai"
                                            class="form-control flatpickr-input @error('tgl_selesai') is-invalid @enderror"
                                            placeholder="Select date" data-input="" readonly="readonly">
                                        <span class="input-group-text input-group-addon" data-toggle="">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                class="feather feather-calendar">
                                                <rect x="3" y="4" width="18" height="18" rx="2"
                                                    ry="2"></rect>
                                                <line x1="16" y1="2" x2="16" y2="6">
                                                </line>
                                                <line x1="8" y1="2" x2="8" y2="6">
                                                </line>
                                                <line x1="3" y1="10" x2="21" y2="10">
                                                </line>
                                            </svg>
                                        </span>
                                        @error('tgl_selesai')
                                            <label class="error invalid-feedback">{{ $message }}</label>
                                        @enderror
                                    </div>
                                </div>
                            </div><!-- Col -->
                        </div><!-- Row -->
                        <div class="row">
                            <div class="col-sm-4">
                                <div class="mb-3">
                                    <label class="form-label">Jumlah Peserta</label>
                                    <input type="number" wire:model="jumlah_peserta"
                                        class="form-control @error('jumlah_peserta') is-invalid @enderror"
                                        placeholder="masukkan jumlah peserta">
                                    @error('jumlah_peserta')
                                        <label class="error invalid-feedback">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div><!-- Col -->
                        </div><!-- Row -->
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="mb-3">
                                    <label class="form-label">PIN Ujian</label>
                                    <input type="text" wire:model="pin_ujian"
                                        class="form-control @error('pin_ujian') is-invalid @enderror"
                                        placeholder="masukkan pin ujian">
                                    @error('pin_ujian')
                                        <label class="error invalid-feedback">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div><!-- Col -->
                        </div><!-- Row -->
                        <div class="row mb-4">
                            <div class="col-sm-12">
                                <div class="mb-1">
                                    <label class="form-label">Pengisian Portofolio Peserta</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input type="radio"
                                        class="form-check-input @error('is_open') is-invalid @enderror"
                                        wire:model="is_open" id="radioInline" value="true"
                                        {{ $is_open == 'true' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="radioInline">
                                        Buka
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input type="radio"
                                        class="form-check-input @error('is_open') is-invalid @enderror"
                                        wire:model="is_open" id="radioInline1" value="false"
                                        {{ $is_open == 'false' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="radioInline1">
                                        Tutup
                                    </label>
                                </div>
                                @error('is_open')
                                    <label class="error invalid-feedback d-block">{{ $message }}</label>
                                @enderror
                            </div><!-- Col -->
                        </div><!-- Row -->
                        @if ($isUpdate == 'true')
                            <div class="row mb-4">
                                <div class="col-sm-12">
                                    <div class="mb-1">
                                        <label class="form-label">Status Event</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input type="radio" class="form-check-input" wire:model="is_finished"
                                            id="radioInline1" value="false"
                                            {{ $is_finished == 'false' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="radioInline1">
                                            Berlangsung
                                        </label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input type="radio" class="form-check-input" wire:model="is_finished"
                                            id="radioInline" value="true"
                                            {{ $is_finished == 'true' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="radioInline">
                                            Selesai
                                        </label>
                                    </div>
                                </div><!-- Col -->
                            </div><!-- Row -->
                        @endif
                        
                        <div class="mt-3">
                            <a href="{{ route('admin.event') }}" wire:navigate
                                class="btn btn-sm btn-inverse-danger me-2">Batal</a>
                            <button type="submit" class="btn btn-sm btn-inverse-success">
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
                $('#assessor').select2()
                $('#assessor').on('change', function() {
                    let data = $(this).val()
                    $wire.set('assessor', data, false)
                    $wire.assessor = data
                })
            })
        </script>
    @endscript
@endpush
