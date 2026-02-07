<div>
    <x-breadcrumb :breadcrumbs="[
        ['url' => route('admin.dashboard'), 'title' => 'Dashboard'],
        ['url' => null, 'title' => 'Aktifitas Log Pengguna']
    ]" />
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="card mt-4 mb-4 bg-light-subtle">
                        <div class="card-body">
                            <h6 class="text-danger" wire:ignore><i class="link-icon" data-feather="filter"></i> Filter</h6>
                            <div class="row mt-2">
                                <div class="col-md-4">
                                    <div class="input-group" wire:ignore>
                                        <span class="input-group-text bg-white"><i data-feather="search"></i></span>
                                        <input wire:model.live.debounce="search" class="form-control" placeholder="cari pengguna...">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <select wire:model.live="action" class="form-select" id="jabatan-diuji">
                                        <option value="">pilih aksi</option>
                                        <option value="create">create</option>
                                        <option value="update">update</option>
                                        <option value="delete">delete</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <div class="input-group flatpickr" id="flatpickr-date">
                                        <input type="text" wire:model.live="tgl_aktifitas"
                                            class="form-control form-control-sm flatpickr-input" placeholder="tgl aktifitas"
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
                                <div class="col-md-2">
                                    <x-btn-reset :text="'Reset'" />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle shadow-sm border rounded" style="overflow:hidden;">
                            <thead class="table-light border-bottom">
                                <tr>
                                    <th class="text-center" style="width: 45px;">#</th>
                                    <th>Pengguna</th>
                                    <th>Modul</th>
                                    <th>Action</th>
                                    <th>Data yang Diubah</th>
                                    <th>Ip Address / User Agent</th>
                                    <th>Dibuat pada</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $index => $item)
                                    <tr class="@if($loop->iteration % 2 == 1) bg-body @endif border-bottom">
                                        <td class="text-center text-secondary fw-bold">{{ $data->firstItem() + $index }}</td>
                                        <td>{{ $item->user->username }}</td>
                                        <td>{{ $item->modul }}</td>
                                        <td>
                                            @if ($item->action == 'create')
                                                <span class="badge bg-success">✔ Create</span>
                                            @elseif ($item->action == 'update')
                                                <span class="badge bg-warning">✔ Update</span>
                                            @elseif ($item->action == 'delete')
                                                <span class="badge bg-danger">✖ Delete</span>
                                            @else
                                                <span class="badge bg-secondary">{{ $item->action }}</span>
                                            @endif
                                        </td>                                        
                                        <td class="text-center">
                                            <button 
                                                type="button"
                                                class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1"
                                                data-bs-toggle="modal"
                                                data-bs-target="#logModal-{{ $item->id }}">
                                                Lihat Data
                                            </button>

                                            <!-- Modal -->
                                            <div class="modal fade" id="logModal-{{ $item->id }}" tabindex="-1" aria-labelledby="logModalLabel-{{ $item->id }}" aria-hidden="true">
                                                <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                                                    <div class="modal-content" style="border-radius: 1rem;">
                                                        <div class="modal-header bg-light" style="border-radius: 1rem 1rem 0 0;">
                                                            <h5 class="modal-title fw-bold" id="logModalLabel-{{ $item->id }}">
                                                                <i class="link-icon text-primary" data-feather="list"></i>
                                                                Detail Perubahan Data
                                                            </h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                                                        </div>
                                                        <div class="modal-body py-4" style="background-color: #f8f9fb;">
                                                            <div class="row g-3 align-items-start">
                                                                <div class="col-md-6">
                                                                    <div class="border rounded bg-white shadow-sm p-3 h-100">
                                                                        <div class="d-flex align-items-center mb-2">
                                                                            <span class="badge bg-danger me-2" style="font-size:0.95em;">
                                                                                <i class="link-icon" data-feather="arrow-left-circle"></i>
                                                                            </span>
                                                                            <strong class="text-danger-emphasis">Data Lama</strong>
                                                                        </div>
                                                                        @php
                                                                            $decoded = json_decode($item->old_data, true);
                                                                            $json_old_data = is_array($decoded)
                                                                                ? json_encode(sanitize_log_data($decoded), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
                                                                                : '-';
                                                                        @endphp
                                                                        <pre class="bg-light rounded small p-2 mb-0" style="min-height:120px;max-height:320px;overflow:auto;width:100%;">{{ $json_old_data }}</pre>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="border rounded bg-white shadow-sm p-3 h-100">
                                                                        <div class="d-flex align-items-center mb-2">
                                                                            <span class="badge bg-success me-2" style="font-size:0.95em;">
                                                                                <i class="link-icon" data-feather="arrow-right-circle"></i>
                                                                            </span>
                                                                            <strong class="text-success-emphasis">Data Baru</strong>
                                                                        </div>
                                                                        @php
                                                                            $decoded = json_decode($item->new_data, true);
                                                                            $json_new_data = is_array($decoded)
                                                                                ? json_encode(sanitize_log_data($decoded), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
                                                                                : '-';
                                                                        @endphp
                                                                        <pre class="bg-light rounded small p-2 mb-0" style="min-height:120px;max-height:320px;overflow:auto;width:100%;">{{ $json_new_data }}</pre>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer bg-light" style="border-radius: 0 0 1rem 1rem;">
                                                            <button type="button" class="btn btn-sm btn-secondary btn-icon-text" data-bs-dismiss="modal">
                                                                <i class="btn-icon-prepend" data-feather="x"></i> Tutup
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-wrap">{{ $item->ip_address }} <br />{{ $item->user_agent }}</td>
                                        <td>{{ $item->created_at->format('d M Y H:i:s') }}</td>
                                    </tr>
                                @endforeach

                                @if($data->count() === 0)
                                    <tr>
                                        <td colspan="9" class="text-center text-muted py-4">
                                            <i class="link-icon" data-feather="inbox" style="font-size: 24px; opacity: 0.7;"></i>
                                            <div class="mt-2 fw-semibold">Tidak ada data aktifitas log pengguna...</div>
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
