<div>
    <x-breadcrumb :breadcrumbs="[
        ['url' => route('admin.dashboard'), 'title' => 'Dashboard'],
        ['url' => route('admin.pelanggaran-tes-cakap-digital'), 'title' => 'Pelanggaran Tes Cakap Digital'],
        ['url' => null, 'title' => 'Detail Pelanggaran Tes Cakap Digital']
    ]" />
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title mb-0">Event: <span class="badge bg-warning text-dark"> {{ $event->nama_event }}</span></h6>
                    <div class="card mt-4 mb-4 bg-light-subtle">
                        <div class="card-body">
                            <h6 class="text-danger" wire:ignore><i class="link-icon" data-feather="filter"></i> Filter</h6>
                            <div class="row mt-2">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <div class="input-group" wire:ignore>
                                            <span class="input-group-text bg-white"><i data-feather="search"></i></span>
                                            <input wire:model.live.debounce="search" class="form-control" placeholder="cari peserta...">
                                        </div>
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
                                            <button
                                                wire:click="deleteConfirmation('{{ $item->id }}')"
                                                tabindex="0" class="btn btn-sm btn-outline-danger btn-icon rounded-circle border-0 shadow-sm"
                                                data-bs-toggle="tooltip" data-bs-placement="top" title="Hapus"
                                                style="transition: background 0.2s;"
                                                @disabled(auth()->user()->role == 'user')
                                            >
                                                <span wire:ignore><i class="link-icon" data-feather="trash"></i></span>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center text-muted py-4">
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
