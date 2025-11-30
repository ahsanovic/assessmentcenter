<div>
    <x-breadcrumb :breadcrumbs="[
        ['url' => route('admin.dashboard'), 'title' => 'Dashboard'],
        ['url' => null, 'title' => 'Nomor Laporan Penilaian']
    ]" />
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <a href="{{ route('admin.nomor-laporan.create') }}" wire:navigate wire:ignore
                        class="btn btn-sm btn-outline-primary">
                        <i class="btn-icon-prepend" data-feather="edit"></i>
                        Tambah
                    </a>
                    <div class="card mt-4 mb-4 bg-light-subtle">
                        <div class="card-body">
                            <h6 class="text-danger" wire:ignore><i class="link-icon" data-feather="filter"></i> Filter</h6>
                            <div class="row mt-2">
                                <div class="col-sm-3">
                                    <div class="input-group" wire:ignore>
                                        <span class="input-group-text bg-white"><i data-feather="search"></i></span>
                                        <input wire:model.live.debounce="search" class="form-control" placeholder="cari event...">
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="input-group flatpickr" id="flatpickr-date">
                                        <input type="text" wire:model.live="tanggal"
                                            class="form-control flatpickr-input" placeholder="tanggal laporan"
                                            data-input="" readonly="readonly">
                                        <span class="input-group-text input-group-addon" data-toggle="">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round"
                                                class="feather feather-calendar">
                                                <rect x="3" y="4" width="18" height="18" rx="2"
                                                    ry="2"></rect>
                                                <line x1="16" y1="2" x2="16" y2="6"></line>
                                                <line x1="8" y1="2" x2="8" y2="6"></line>
                                                <line x1="3" y1="10" x2="21" y2="10"></line>
                                            </svg>
                                        </span>
                                    </div>
                                </div>
                                <div class="col-sm-5">
                                    <div wire:ignore>
                                        <select wire:model.live="event_id" id="event" class="form-select">
                                            <option value="">pilih event</option>
                                            @foreach ($options_event as $key => $item)
                                                <option value="{{ $key}}">{{ $item }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <button wire:click="resetFilters" class="btn btn-sm btn-inverse-danger">
                                        <span wire:ignore><i class="btn-icon-prepend" data-feather="refresh-ccw"></i> Reset</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Event</th>
                                    <th>Nomor Laporan</th>
                                    <th>Tanggal Laporan</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $index => $item)
                                    <tr>
                                        <td>{{ $data->firstItem() + $index }}</td>
                                        <td class="text-wrap">{{ $item->event?->nama_event }}</td>
                                        <td>{{ $item->nomor }}</td>
                                        <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d F Y') }}</td>
                                        <td>
                                            <a
                                                class="btn btn-sm btn-inverse-success btn-icon"
                                                wire:navigate
                                                href="{{ route('admin.nomor-laporan.edit', $item->id) }}"
                                                data-bs-toggle="tooltip" data-bs-placement="top" title="Edit"
                                            >
                                                <span wire:ignore><i class="link-icon" data-feather="edit-3"></i></span>
                                            </a>
                                            <button wire:click="deleteConfirmation('{{ $item->id }}')"
                                                tabindex="0"
                                                class="btn btn-sm btn-inverse-danger btn-icon"
                                                data-bs-toggle="tooltip" data-bs-placement="top" title="Hapus"
                                            >
                                                <span wire:ignore><i class="link-icon" data-feather="trash"></i></span>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>        
        </div>
        <x-pagination :items="$data" />
    </div>
</div>
@push('js')
    @script()
        <script>
            $(document).ready(function() {
                $('#event').select2()
                    .on('change', function(e) {
                        @this.set('event_id', $(this).val());
                    });
                
                Livewire.on('reset-select2', () => {
                    $('#event').val(null).trigger('change');
                });
            })
        </script>
    @endscript
@endpush
