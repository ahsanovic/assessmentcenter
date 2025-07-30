<div>
    <x-breadcrumb :breadcrumbs="[
        ['url' => route('admin.dashboard'), 'title' => 'Dashboard'],
        ['url' => null, 'title' => 'Cakap Digital'],
        ['url' => null, 'title' => 'Soal']
    ]" />
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">Data Soal Cakap Digital</h6>
                    <a href="{{ route('admin.soal-cakap-digital.create') }}" wire:navigate class="btn btn-xs btn-outline-primary mt-3">Tambah</a>
                    <h6 class="mt-4 text-danger"><i class="link-icon" data-feather="filter"></i> Filter</h6>
                    <div class="row mt-2">
                        <div class="col-sm-4">
                            <div class="mb-3">
                                <select wire:model.live="jenis_soal" class="form-select form-select-sm" id="jenis-soal">
                                    <option value="">pilih jenis soal</option>
                                    <option value="1">1 - Literasi Digital</option>
                                    <option value="2">2 - Emerging Skill</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="mb-3">
                                <input wire:model.live.debounce="search" class="form-control form-control-sm" placeholder="cari soal" />
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
                                    <th>Jenis Soal</th>
                                    <th>Deskripsi Soal</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $index => $item)
                                    <tr>
                                        <td>{{ $data->firstItem() + $index }}</td>
                                        <td>
                                            @if ($item->jenis_soal == 1)
                                                <span class="badge bg-dark">
                                                    Literasi Digital
                                                </span>
                                            @else
                                                <span class="badge bg-primary">
                                                    Emerging Skill
                                                </span>
                                            @endif
                                        </td>
                                        <td class="text-wrap">{{ $item->soal }}</td>
                                        <td>
                                            <div class="btn-group dropstart">
                                                <a
                                                    class="btn btn-xs btn-outline-warning"
                                                    wire:navigate
                                                    href="{{ route('admin.soal-cakap-digital.edit', $item->id) }}"
                                                >
                                                    Edit
                                                </a>
                                                <button wire:click="deleteConfirmation({{ $item->id }})" class="btn btn-xs btn-outline-danger">
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
