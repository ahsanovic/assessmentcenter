<div>
    <x-breadcrumb :breadcrumbs="[
        ['url' => route('admin.dashboard'), 'title' => 'Dashboard'],
        ['url' => null, 'title' => 'Waktu Tes']
    ]" />
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <x-btn-add :url="route('admin.settings.waktu.create')" class="mb-4" />
                    <div class="table-responsive">
                        <table class="table table-hover align-middle shadow-sm border rounded" style="overflow:hidden;">
                            <thead class="table-light border-bottom">
                                <tr>
                                    <th class="text-center" style="width: 45px;">#</th>
                                    <th>Jenis Tes</th>
                                    <th>Waktu Tes</th>
                                    <th>Status</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $index => $item)
                                    <tr class="@if($loop->iteration % 2 == 1) bg-body @endif border-bottom">
                                        <td class="text-center text-secondary fw-bold">{{ $data->firstItem() + $index }}</td>
                                        <td>
                                            {{ $item->jenisTes->jenis_tes }}
                                        </td>
                                        <td><span class="badge bg-info-subtle text-dark">{{ $item->waktu }} menit</span></td>
                                        <td>
                                            @if ($item->is_active == 'true')
                                                <span
                                                    class="badge bg-success"
                                                    wire:click="changeStatusTimerConfirmation('{{ $item->id }}')"
                                                    style="cursor: pointer;"
                                                >
                                                    ✔ Aktif
                                                </span>
                                            @else
                                                <span
                                                    class="badge bg-danger"
                                                    wire:click="changeStatusTimerConfirmation('{{ $item->id }}')"
                                                    style="cursor: pointer;"
                                                >
                                                    ✖ Non Aktif
                                                </span>
                                            @endif
                                        <td>
                                            <a
                                                class="btn btn-sm btn-outline-success btn-icon rounded-circle border-0 shadow-sm" style="transition: background 0.2s;""
                                                wire:navigate
                                                href="{{ route('admin.settings.waktu.edit', $item->id) }}"
                                                data-bs-toggle="tooltip" data-bs-placement="top" title="Edit"
                                            >
                                                <span wire:ignore><i class="link-icon" data-feather="edit-3"></i></span>
                                            </a>
                                            <button wire:click="deleteConfirmation('{{ $item->id }}')"
                                                class="btn btn-sm btn-outline-danger btn-icon rounded-circle border-0 shadow-sm" style="transition: background 0.2s;""
                                                tabindex="0"
                                                data-bs-toggle="tooltip" data-bs-placement="top" title="Hapus"
                                            >
                                                <span wire:ignore><i class="link-icon" data-feather="trash"></i></span>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach

                                @if($data->count() === 0)
                                    <tr>
                                        <td colspan="9" class="text-center text-muted py-4">
                                            <i class="link-icon" data-feather="inbox" style="font-size: 24px; opacity: 0.7;"></i>
                                            <div class="mt-2 fw-semibold">Tidak ada data waktu tes...</div>
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>        
        </div>
    </div>
</div>
