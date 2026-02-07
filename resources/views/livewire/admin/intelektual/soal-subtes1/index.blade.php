<div>
    <x-breadcrumb :breadcrumbs="[
        ['url' => route('admin.dashboard'), 'title' => 'Dashboard'],
        ['url' => null, 'title' => 'Intelektual'],
        ['url' => null, 'title' => 'Soal Sub Tes 1']
    ]" />
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <x-btn-add :url="route('admin.soal-intelektual-subtes1.create')" class="mb-4" />
                    <div class="table-responsive">
                        <table class="table table-hover align-middle shadow-sm border rounded" style="overflow:hidden;">
                            <thead class="table-light border-bottom">
                                <tr>
                                    <th class="text-center" style="width: 45px;">#</th>
                                    <th>Model Soal</th>
                                    <th>Soal</th>
                                    <th>Opsi A</th>
                                    <th>Opsi B</th>
                                    <th>Opsi C</th>
                                    <th>Opsi D</th>
                                    <th>Opsi E</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $index => $item)
                                    <tr class="@if($loop->iteration % 2 == 1) bg-body @endif border-bottom">
                                        <td class="text-center text-secondary fw-bold">{{ $data->firstItem() + $index }}</td>
                                        <td>{{ $item->modelSoal->jenis }}</td>
                                        <td class="text-wrap">{{ $item->soal }}</td>
                                        <td>{{ $item->opsi_a }}</td>
                                        <td>{{ $item->opsi_b }}</td>
                                        <td>{{ $item->opsi_c }}</td>
                                        <td>{{ $item->opsi_d }}</td>
                                        <td>{{ $item->opsi_e }}</td>
                                        <td>
                                            <a
                                                class="btn btn-sm btn-outline-success btn-icon rounded-circle border-0 shadow-sm"
                                                wire:navigate
                                                href="{{ route('admin.soal-intelektual-subtes1.edit', $item->id) }}"
                                                data-bs-toggle="tooltip" data-bs-placement="top" title="Edit"
                                                style="transition: background 0.2s;"
                                            >
                                                <span wire:ignore><i class="link-icon" data-feather="edit-3"></i></span>
                                            </a>
                                            <button wire:click="deleteConfirmation('{{ $item->id }}')"
                                                class="btn btn-sm btn-outline-danger btn-icon rounded-circle border-0 shadow-sm"
                                                style="transition: background 0.2s;"
                                                @disabled(auth()->user()->role == 'user')
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
                                            <div class="mt-2 fw-semibold">Tidak ada data soal sub tes 1...</div>
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
