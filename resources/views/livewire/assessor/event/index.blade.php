<div>
    <x-breadcrumb :breadcrumbs="[
        ['url' => route('assessor.dashboard'), 'title' => 'Dashboard'],
        ['url' => null, 'title' => 'Event']
    ]" />
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">Data Event</h6>
                    <h6 class="mt-4 text-danger"><i class="link-icon" data-feather="filter"></i> Filter</h6>
                    <div class="row mt-2">
                        <div class="col-sm-3">
                            <div class="mb-3">
                                <select wire:model.live="jabatan_diuji" class="form-select form-select-sm"
                                    id="jabatan-diuji">
                                    <option value="">jenis jabatan diujikan</option>
                                    @foreach ($option_jabatan_diuji as $key => $item)
                                        <option value="{{ $key }}">{{ $item }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="mb-3">
                                <div class="input-group flatpickr" id="flatpickr-date">
                                    <input type="text" wire:model.live="tgl_mulai"
                                        class="form-control flatpickr-input" placeholder="tgl pelaksanaan"
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
                        </div>
                        <div class="col-sm-3">
                            <div class="mb-3" wire:ignore>
                                <select wire:model.live="event" class="form-select form-select-sm" id="event">
                                    <option value="">event</option>
                                    @foreach ($option_event as $key => $item)
                                        <option value="{{ $key }}">{{ $item }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="mb-3">
                                <button wire:click="resetFilters" class="btn btn-sm btn-inverse-danger">Reset</button>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Nama Event</th>
                                    <th>Tgl Pelaksanaan</th>
                                    <th>Jabatan yg Diujikan</th>
                                    <th>Asesi</th>
                                    <th>Status</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $index => $item)
                                    <tr>
                                        <td>{{ $data->firstItem() + $index }}</td>
                                        <td class="text-wrap">{{ $item->nama_event }}</td>
                                        <td class="text-wrap">
                                            @if ($item->tgl_mulai == $item->tgl_selesai)
                                                {{ $item->tgl_mulai }}
                                            @else
                                                {{ $item->tgl_mulai . ' s/d ' . $item->tgl_selesai }}
                                            @endif
                                        </td>
                                        <td>{{ $item->jabatanDiuji->jenis ?? '' }}</td>
                                        <td>
                                            <a class="btn btn-xs btn-primary" wire:navigate
                                                href="{{ route('assessor.event.show-peserta', ['idEvent' => $item->id]) }}">
                                                Lihat
                                            </a>
                                        </td>
                                        <td>
                                            @if ($item->is_finished == 'true')
                                                <span class="badge bg-danger">Selesai</span>
                                            @else
                                                <span class="badge bg-success">Berlangsung</span>
                                            @endif
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
                        @this.set('event', $(this).val());
                    });
                
                Livewire.on('reset-select2', () => {
                    $('#event').val(null).trigger('change');
                });
            })
        </script>
    @endscript
@endpush