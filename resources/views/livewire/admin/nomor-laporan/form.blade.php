<div>
    <x-breadcrumb :breadcrumbs="[
        ['url' => route('admin.dashboard'), 'title' => 'Dashboard'],
        ['url' => null, 'title' => 'Data Nomor Laporan Penilaian']
    ]" />
    <div class="row">
        <div class="col-md-12 stretch-card">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">Form Nomor Laporan Penilaian</h6>
                    <form wire:submit="save">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <label for="event" class="form-label">Event</label>
                                    <div wire:ignore>
                                        <select wire:model="form.event_id" id="event"
                                            class="form-select @error('form.event_id') is-invalid @enderror">
                                            <option value="">pilih event</option>
                                            @foreach ($event as $key => $item)
                                                <option value="{{ $key }}">{{ $item }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @error('form.event_id')
                                        <label class="error invalid-feedback d-block">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="mb-3">
                                    <label class="form-label">Nomor Laporan Penilaian</label>
                                    <input type="text" wire:model="form.nomor" class="form-control @error('form.nomor') is-invalid @enderror">
                                    @error('form.nomor')
                                    <label class="error invalid-feedback">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div><!-- Col -->
                        </div><!-- Row -->
                        <div class="row">
                            <div class="col-sm-4">
                                <div class="mb-3">
                                    <label class="form-label">Tanggal Laporan Penilaian</label>
                                    <div class="input-group flatpickr" id="flatpickr-date">
                                        <input type="text" wire:model="form.tanggal"
                                            class="form-control flatpickr-input @error('form.tanggal') is-invalid @enderror"
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
                                        @error('form.tanggal')
                                            <label class="error invalid-feedback">{{ $message }}</label>
                                        @enderror
                                    </div>
                                </div>
                            </div><!-- Col -->
                        </div><!-- Row -->
                        <a href="{{ route('admin.nomor-laporan') }}" wire:navigate class="btn btn-sm btn-inverse-danger me-2">Batal</a>
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
@push('js')
    @script()
        <script>
            $(document).ready(function() {
                let eventSelect = $('#event');

                eventSelect.select2()
                    .on('change', function(e) {
                        @this.set('form.event_id', $(this).val());
                    });
            })
        </script>
    @endscript
@endpush