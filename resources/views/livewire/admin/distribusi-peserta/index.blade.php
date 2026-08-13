<div>
    <x-breadcrumb :breadcrumbs="[
        ['url' => route('admin.dashboard'), 'title' => 'Dashboard'],
        ['url' => null, 'title' => 'Distribusi Peserta']
    ]" />
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <x-monitoring.filter-panel>
                            <div class="row g-2 align-items-end">
                                <div class="col-sm-3">
                                    <select wire:model.live="jabatan_diuji" class="form-select"
                                        id="jabatan-diuji">
                                        <option value="">jenis jabatan diujikan</option>
                                        @foreach ($option_jabatan_diuji as $key => $item)
                                            <option value="{{ $key }}">{{ $item }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-2">
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
                                <div class="col-sm-4">
                                    <div class="input-group">
                                        <span class="input-group-text bg-white"><i data-feather="search" style="width:16px;height:16px;"></i></span>
                                        <input wire:model.live.debounce.400ms="search" class="form-control" placeholder="Ketik nama event…" autocomplete="off">
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <x-btn-reset :text="'Reset'" />
                                </div>
                            </div>
                        
                    </x-monitoring.filter-panel>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle shadow-sm border rounded" style="overflow:hidden;">
                            <thead class="table-light border-bottom">
                                <tr>
                                    <th class="text-center" style="width: 45px;">#</th>
                                    <th>Nama Event</th>
                                    <th>Tgl Pelaksanaan</th>
                                    <th>Jabatan yg Diujikan</th>
                                    <th>Jumlah Peserta</th>
                                    <th>Assessor</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $index => $item)
                                    <tr class="@if($loop->iteration % 2 == 1) bg-body @endif border-bottom">
                                        <td class="text-center text-secondary fw-bold">{{ $data->firstItem() + $index }}</td>
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
                                            <a class="btn btn-xs btn-warning" wire:navigate
                                            href="{{ route('admin.event.show-peserta', ['idEvent' => $item->id]) }}">
                                            {{ $item->peserta_count }}
                                            </a> /
                                            {{ $item->jumlah_peserta }}
                                        </td>
                                        <td>
                                            <a class="btn btn-xs btn-primary" wire:navigate
                                                href="{{ route('admin.distribusi-peserta.show-assessor', ['idEvent' => $item->id]) }}">
                                                {{ $item->assessor_count }} orang
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach

                                @if($data->count() === 0)
                                    <tr>
                                        <td colspan="9" class="text-center text-muted py-4">
                                            <i class="link-icon" data-feather="inbox" style="font-size: 24px; opacity: 0.7;"></i>
                                            <div class="mt-2 fw-semibold">Tidak ada data distribusi peserta...</div>
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <x-pagination :items="$data" />
    </div>
</div>