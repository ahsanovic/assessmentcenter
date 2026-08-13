<div>
    <x-breadcrumb :breadcrumbs="[
        ['url' => route('admin.dashboard'), 'title' => 'Dashboard'],
        ['url' => route('admin.pelanggaran-tes-pspk'), 'title' => 'Pelanggaran Tes PSPK'],
        ['url' => null, 'title' => 'Detail Pelanggaran Tes PSPK']
    ]" />
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <x-monitoring.event-header :nama="$event->nama_event" />
                    <x-monitoring.filter-panel>
                            <div class="row g-2 align-items-end">
                                <div class="col-md-6">
                                    <div class="input-group">
                                            <span class="input-group-text bg-white"><i data-feather="search" style="width:16px;height:16px;"></i></span>
                                            <input wire:model.live.debounce="search" class="form-control" placeholder="cari peserta..." autocomplete="off">
                                        </div>
                                </div>
                                <div class="col-md-2">
                                    <x-btn-reset :text="'Reset'" />
                                </div>
                            </div>
                    </x-monitoring.filter-panel>
                    <div class="table-responsive">
                        <table class="table ac-data-table table-hover align-middle" style="overflow:hidden;">
                            <thead class="table-light border-bottom">
                                <tr>
                                    <th class="text-center" style="width: 45px;">#</th>
                                    <th>Nama Peserta</th>
                                    <th>Pelanggaran</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($data as $index => $item)
                                    <tr class="@if($loop->iteration % 2 == 1) bg-body @endif border-bottom">
                                        <td class="text-center text-secondary fw-bold">{{ $data->firstItem() + $index }}</td>
                                        <td>{{ $item->nama }}</td>
                                        <td class="text-wrap">
                                            <button
                                                type="button"
                                                class="btn btn-outline-primary btn-sm rounded-pill px-3 py-1"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modalPelanggaran-{{ $item->id }}"
                                            >
                                                Lihat Pelanggaran
                                            </button>
                                            <!-- Modal -->
                                            <div class="modal fade" id="modalPelanggaran-{{ $item->id }}" tabindex="-1" aria-labelledby="modalLabel-{{ $item->id }}" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content border-0 shadow-md" style="border-radius: 18px;">
                                                        <div class="modal-header bg-light border-0" style="border-radius: 18px 18px 0 0;">
                                                            <h6 class="modal-title fw-semibold" id="modalLabel-{{ $item->id }}">
                                                                Detail Pelanggaran
                                                            </h6>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                                                        </div>
                                                        <div class="modal-body" style="font-size: 15px;">
                                                            <ul class="list-group list-group-flush rounded">
                                                                @forelse ($item->logPelanggaran as $log)
                                                                    <li class="list-group-item border-0 ps-0 py-2 d-flex align-items-start">
                                                                        <span class="badge bg-danger-subtle me-2" title="Pelanggaran" style="font-size:0.95em;"><i class="link-icon" data-feather="alert-circle"></i></span>
                                                                        <span class="fw-normal text-secondary">{{ $log->keterangan }}</span>
                                                                    </li>
                                                                @empty
                                                                    <li class="list-group-item border-0 ps-0 py-2 text-muted">Tidak ada catatan pelanggaran.</li>
                                                                @endforelse
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <x-table.btn-delete :id="$item->id" />
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center text-muted py-5 ac-empty-state">
                                            <i class="link-icon" data-feather="inbox" style="font-size: 24px; opacity: 0.7;"></i>
                                            <div class="mt-2 fw-semibold">Tidak ada data peserta...</div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>        
        </div>
        <x-pagination :items="$data" />
    </div>
</div>
