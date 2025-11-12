<div>
    <x-breadcrumb :breadcrumbs="[
        ['url' => route('admin.dashboard'), 'title' => 'Dashboard'],
        ['url' => null, 'title' => 'PSPK'],
        ['url' => null, 'title' => 'Referensi Deskripsi']
    ]" />
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">Referensi Deskripsi PSPK</h6>
                    <a href="{{ route('admin.ref-pspk.create') }}" wire:navigate class="btn btn-xs btn-outline-primary mt-3">Tambah</a>
                    <h6 class="mt-4 text-danger"><i class="link-icon" data-feather="filter"></i> Filter</h6>
                    <div class="row mt-2">
                        <div class="col-sm-2">
                            <div class="mb-3">
                                <select wire:model.live="level_pspk" class="form-select form-select-sm" id="level-pspk">
                                    <option value="">pilih level pspk</option>
                                    @foreach ($level_pspk_options as $key => $item)
                                        <option value="{{ $key }}">{{ $item }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="mb-3">
                                <select wire:model.live="aspek_id" class="form-select form-select-sm" id="aspek">
                                    <option value="">pilih aspek</option>
                                    @foreach ($aspek_options as $key => $item)
                                        <option value="{{ $key }}">{{ $item }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="mb-3">
                                <input wire:model.live.debounce="search" class="form-control form-control-sm" placeholder="cari deskripsi" />
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
                                    <th>Nama Aspek</th>
                                    <th>Level PSPK</th>
                                    <th>Deskripsi (-)</th>
                                    <th>Deskripsi</th>
                                    <th>Deskripsi (+)</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $index => $item)
                                    <tr>
                                        <td>{{ $data->firstItem() + $index }}</td>
                                        <td><span class="badge bg-dark">
                                                {{ $item->aspek->nama_aspek }}
                                            </span></td>
                                        <td>{{ $item->level_pspk }}</td>
                                        <td class="text-wrap">{{ $item->deskripsi_min }}</td>
                                        <td class="text-wrap">{{ $item->deskripsi }}</td>
                                        <td class="text-wrap">{{ $item->deskripsi_plus }}</td>
                                        <td>
                                            <div class="btn-group">
                                                <a
                                                    class="btn btn-xs btn-outline-warning"
                                                    wire:navigate
                                                    href="{{ route('admin.ref-pspk.edit', $item->id) }}"
                                                >
                                                    Edit
                                                </a>
                                                <button wire:click="deleteConfirmation('{{ $item->id }}')" class="btn btn-xs btn-outline-danger">
                                                    Hapus
                                                </button>
                                            </div>
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
