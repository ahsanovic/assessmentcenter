<div>
    <x-breadcrumb :breadcrumbs="[
        ['url' => route('admin.dashboard'), 'title' => 'Dashboard'],
        ['url' => null, 'title' => 'Referensi Pertanyaan Pengalaman'],
    ]" />
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">Data Referensi Pertanyaan Pengalaman</h6>
                    <a href="{{ route('admin.pertanyaan-pengalaman.create') }}" wire:navigate
                        class="btn btn-xs btn-outline-primary mt-3">Tambah</a>
                    <h6 class="mt-4 text-danger"><i class="link-icon" data-feather="filter"></i> Filter</h6>
                    <div class="row mt-2">
                        <div class="col-sm-6">
                            <div class="mb-3">
                                <input wire:model.live.debounce="search" class="form-control form-control-sm"
                                    placeholder="cari pertanyaan" />
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Pertanyaan</th>
                                    <th>Kode</th>
                                    <th>Urutan ke-</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $index => $item)
                                    <tr>
                                        <td>{{ $data->firstItem() + $index }}</td>
                                        <td class="text-wrap">{{ $item->pertanyaan }}</td>
                                        <td>
                                            @php $item_kode = json_decode($item->kode, true); @endphp
                                            @foreach ($option_kode as $key => $kode)
                                                @foreach ($item_kode as $row)
                                                    @if ($row == $key)
                                                        <span class="badge bg-primary me-1">{{ $kode }}</span>
                                                    @endif
                                                @endforeach
                                            @endforeach
                                        </td>
                                        <td>{{ $item->urutan }}</td>
                                        <td>
                                            <div class="btn-group dropstart">
                                                <a class="btn btn-xs btn-outline-warning" wire:navigate
                                                    href="{{ route('admin.pertanyaan-pengalaman.edit', $item->id) }}">
                                                    Edit
                                                </a>
                                                <button wire:click="deleteConfirmation('{{ $item->id }}')"
                                                    tabindex="0" class="btn btn-xs btn-outline-danger">
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
