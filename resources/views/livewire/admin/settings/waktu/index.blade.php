<div>
    <x-breadcrumb :breadcrumbs="[
        ['url' => route('admin.dashboard'), 'title' => 'Dashboard'],
        ['url' => null, 'title' => 'Waktu Tes']
    ]" />
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <a href="{{ route('admin.settings.waktu.create') }}" wire:navigate wire:ignore
                        class="btn btn-sm btn-outline-primary mb-4">
                        <i class="btn-icon-prepend" data-feather="edit"></i>
                        Tambah
                    </a>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Jenis Tes</th>
                                    <th>Waktu Tes</th>
                                    <th>Status</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $index => $item)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            {{  $item->jenisTes->jenis_tes }}
                                        </td>
                                        <td>{{ $item->waktu }} menit</td>
                                        <td>
                                            @if ($item->is_active == 'true')
                                                <span
                                                    class="badge bg-success"
                                                    wire:click="changeStatusTimerConfirmation('{{ $item->id }}')"
                                                    style="cursor: pointer;"
                                                >
                                                    Aktif
                                                </span>
                                            @else
                                                <span
                                                    class="badge bg-danger"
                                                    wire:click="changeStatusTimerConfirmation('{{ $item->id }}')"
                                                    style="cursor: pointer;"
                                                >
                                                    Non Aktif
                                                </span>
                                            @endif
                                        <td>
                                            <a
                                                class="btn btn-sm btn-inverse-success btn-icon"
                                                wire:navigate
                                                href="{{ route('admin.settings.waktu.edit', $item->id) }}"
                                                data-bs-toggle="tooltip" data-bs-placement="top" title="Edit"
                                            >
                                                <span wire:ignore><i class="link-icon" data-feather="edit-3"></i></span>
                                            </a>
                                            <button wire:click="deleteConfirmation('{{ $item->id }}')"
                                                class="btn btn-sm btn-inverse-danger btn-icon"
                                                tabindex="0"
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
    </div>
</div>
