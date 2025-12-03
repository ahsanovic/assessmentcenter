<div>
    <x-breadcrumb :breadcrumbs="[
        ['url' => route('admin.dashboard'), 'title' => 'Dashboard'],
        ['url' => null, 'title' => 'Intelektual'],
        ['url' => null, 'title' => 'Model Soal Intelektual']
    ]" />
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <a href="{{ route('admin.model-soal-intelektual.create') }}" wire:navigate wire:ignore
                        class="btn btn-sm btn-outline-primary mb-4">
                        <i class="btn-icon-prepend" data-feather="edit"></i>
                        Tambah
                    </a>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Model Soal</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $index => $item)
                                    <tr>
                                        <td>{{ $data->firstItem() + $index }}</td>
                                        <td>{{ $item->jenis }}</td>
                                        <td>
                                            @if ($item->is_active == 'false')
                                                <span
                                                    class="badge bg-danger"
                                                    wire:click="changeStatusConfirmation('{{ $item->id }}')"
                                                    style="cursor: pointer;"
                                                >
                                                    Non Aktif
                                                </span>
                                            @else
                                                <span
                                                    class="badge bg-success"
                                                    wire:click="changeStatusConfirmation('{{ $item->id }}')"
                                                    style="cursor: pointer;"
                                                >
                                                    Aktif
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <a
                                                class="btn btn-sm btn-inverse-success btn-icon"
                                                wire:navigate
                                                href="{{ route('admin.model-soal-intelektual.edit', $item->id) }}"
                                                data-bs-toggle="tooltip" data-bs-placement="top" title="Edit"
                                            >
                                                <span wire:ignore><i class="link-icon" data-feather="edit-3"></i></span>
                                            </a>
                                            <button wire:click="deleteConfirmation('{{ $item->id }}')"
                                                class="btn btn-sm btn-inverse-danger btn-icon"
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
