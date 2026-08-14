<div>
    <x-breadcrumb :breadcrumbs="[
        ['url' => route('admin.dashboard'), 'title' => 'Dashboard'],
        ['url' => null, 'title' => 'Grup Assessment'],
    ]" />

    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <x-monitoring.page-header
                        icon="layers"
                        color="primary"
                        title="Grup Assessment (Multi-Tes)"
                        description="Grup menghubungkan beberapa event dengan metode tes berbeda. Peserta dapat mengerjakan semua tes tanpa logout, dan rekap Excel gabungan tersedia di sini atau halaman hasil tes."
                    />

                    <x-monitoring.filter-panel :badge="$groups->total().' grup'">
                        <div class="row g-2 align-items-end">
                            <div class="col-md-6">
                                <div class="input-group">
                                    <span class="input-group-text bg-white"><i data-feather="search" style="width:16px;height:16px;"></i></span>
                                    <input wire:model.live.debounce.300ms="search" class="form-control" placeholder="cari grup..." autocomplete="off">
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
                                    <th>Nama Grup</th>
                                    <th>Periode</th>
                                    <th>Jumlah Event</th>
                                    <th>Status</th>
                                    <th class="text-end">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($groups as $index => $group)
                                    <tr wire:key="group-{{ $group->id }}" class="@if($loop->iteration % 2 == 1) bg-body @endif border-bottom">
                                        <td class="text-center text-secondary fw-bold">{{ $groups->firstItem() + $index }}</td>
                                        <td class="fw-medium">{{ $group->nama }}</td>
                                        <td>{{ $group->periode ?? '-' }}</td>
                                        <td><span class="badge bg-primary-subtle text-primary">{{ $group->events_count }} event</span></td>
                                        <td>
                                            @if($group->is_active === 'true')
                                                <span class="badge bg-success">Aktif</span>
                                            @else
                                                <span class="badge bg-secondary">Nonaktif</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            @if($eventIdsForDownload->get($group->id))
                                                <x-btn-download-rekap-gabungan :event-id="$eventIdsForDownload->get($group->id)" />
                                            @else
                                                <span class="text-muted small">Belum ada event</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">Belum ada grup assessment.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        {{ $groups->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
