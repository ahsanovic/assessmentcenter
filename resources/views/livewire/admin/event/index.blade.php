<div>
    <x-breadcrumb :breadcrumbs="[
        ['url' => route('admin.dashboard'), 'title' => 'Dashboard'],
        ['url' => null, 'title' => 'Event']
    ]" />
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        <div>
                            <x-btn-add :url="route('admin.event.create')" />
                        </div>
                        <div class="d-flex align-items-center flex-wrap text-nowrap gap-2">
                            <div class="badge bg-info-subtle text-dark">
                                <span wire:ignore><i class="link-icon" data-feather="info"></i></span>
                                Total Event: {{ $stats->total }}
                            </div>
                            <div class="badge bg-success-subtle text-dark">
                                <span wire:ignore><i class="link-icon" data-feather="play"></i></span>
                                Event Berlangsung: {{ $stats->berlangsung }}
                            </div>
                            <div class="badge bg-danger-subtle text-dark">
                                <span wire:ignore><i class="link-icon" data-feather="check"></i></span>
                                Event Selesai: {{ $stats->selesai }}
                            </div>
                        </div>
                    </div>
                    <div class="card mt-4 mb-4 bg-light-subtle">
                        <div class="card-body">
                            <h6 class="text-danger" wire:ignore><i class="link-icon" data-feather="filter"></i> Filter</h6>
                            <div class="row mt-2">
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
                                    <div class="input-group" wire:ignore>
                                        <span class="input-group-text bg-white"><i data-feather="search"></i></span>
                                        <input wire:model.live.debounce="search" class="form-control" placeholder="cari event...">
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

                    <div wire:key="events-table" wire:ignore.self class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Nama Event</th>
                                    <th>Tgl Pelaksanaan</th>
                                    <th>Jabatan yg Diujikan / Metode Tes</th>
                                    <th>Jumlah Peserta</th>
                                    <th>Peserta Terinput</th>
                                    <th>Assessor</th>
                                    <th>Portofolio</th>
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
                                        <td>
                                            {{ $item->jabatanDiuji->jenis ?? '' }} <br /> 
                                            <span class="badge bg-dark">{{ $item->metodeTes->metode_tes }}</span>
                                        </td>
                                        <td>{{ $item->jumlah_peserta }}</td>
                                        <td>
                                            <a class="btn btn-xs btn-warning" wire:navigate
                                                href="{{ route('admin.event.show-peserta', ['idEvent' => $item->id]) }}">
                                                    {{ $item->peserta_count }} orang
                                            </a>
                                        </td>
                                        @if ($item->metode_tes_id == 1)
                                        <td>
                                            <a class="btn btn-xs btn-primary" wire:navigate
                                                href="{{ route('admin.event.show-assessor', ['idEvent' => $item->id]) }}">
                                                {{ $item->assessor_count }} orang
                                            </a>
                                        </td>
                                        @else
                                        <td></td>
                                        @endif
                                        <td>
                                            @if ($item->is_open == 'false')
                                                <span
                                                    class="badge bg-danger"
                                                    wire:click="changeStatusPortofolioConfirmation('{{ $item->id }}')"
                                                    style="cursor: pointer;"
                                                >
                                                    Tutup
                                                </span>
                                            @else
                                                <span
                                                    class="badge bg-success"
                                                    wire:click="changeStatusPortofolioConfirmation('{{ $item->id }}')"
                                                    style="cursor: pointer;"
                                                >
                                                    Buka
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($item->is_finished == 'true')
                                                <span
                                                    class="badge bg-danger"
                                                    wire:click="changeStatusEventConfirmation('{{ $item->id }}')"
                                                    style="cursor: pointer;"
                                                >
                                                    Selesai
                                                </span>
                                            @else
                                                <span
                                                    class="badge bg-success"
                                                    wire:click="changeStatusEventConfirmation('{{ $item->id }}')"
                                                    style="cursor: pointer;"
                                                >
                                                    Berlangsung
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <a class="btn btn-sm btn-inverse-success btn-icon {{ 
                                                    ($item->is_finished == 'true' && auth()->user()->role == 'user') ? 'disabled' : ''
                                                }}"
                                                wire:navigate
                                                href="{{ route('admin.event.edit', $item->id) }}"
                                                data-bs-toggle="tooltip" data-bs-placement="top" title="Edit"
                                            >
                                                <span wire:ignore><i class="link-icon" data-feather="edit-3"></i></span>
                                            </a>
                                            <button wire:click="deleteConfirmation('{{ $item->id }}')"
                                                class="btn btn-sm btn-inverse-danger btn-icon"
                                                @disabled($item->is_finished == 'true')
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