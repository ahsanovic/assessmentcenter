<div>
    <x-breadcrumb :breadcrumbs="[
        ['url' => route('admin.dashboard'), 'title' => 'Dashboard'],
        ['url' => route('admin.pelanggaran-tes-cakap-digital'), 'title' => 'Data Pelanggaran Tes'],
        ['url' => null, 'title' => 'Detail Pelanggaran Tes']
    ]" />
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">Detail Pelanggaran Tes</h6>
                    <h6 class="mt-4 text-danger"><i class="link-icon" data-feather="filter"></i> Filter</h6>
                    <div class="row mt-2">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <input wire:model.live.debounce="search" class="form-control form-control-sm" placeholder="cari nama peserta" />
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="mb-3">
                                <button wire:click="resetFilters" class="btn btn-sm btn-inverse-danger">Reset</button>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th style="width: 5%">#</th>
                                    <th>Nama Peserta</th>
                                    <th>Pelanggaran</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($data as $index => $item)
                                    <tr>
                                        <td>{{ $data->firstItem() + $index }}</td>
                                        <td>{{ $item->nama }}</td>
                                        <td class="text-wrap">
                                            <div style="max-height: 150px; overflow-y: auto;">
                                                @foreach ($item->logPelanggaran as $log)
                                                    {{ $log->keterangan}} <br />
                                                @endforeach
                                            </div>
                                        </td>
                                        <td>
                                            <button
                                                wire:click="deleteConfirmation('{{ $item->id }}')"
                                                tabindex="0" class="btn btn-xs btn-outline-danger"
                                                @disabled(auth()->user()->role == 'user')
                                            >
                                                Hapus
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">- Tidak ada data -</td>
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
