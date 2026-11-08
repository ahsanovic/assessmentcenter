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
                                    <th>Metode Tes</th>
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
                                            @php
                                                $metodeTesList = $group->events
                                                    ->map(fn ($event) => $event->metodeTes)
                                                    ->filter()
                                                    ->unique('id')
                                                    ->sortBy('metode_tes');
                                            @endphp
                                            @forelse($metodeTesList as $metode)
                                                <span class="badge bg-dark me-1 mb-1">{{ $metode->metode_tes }}</span>
                                            @empty
                                                <span class="text-muted">-</span>
                                            @endforelse
                                        </td>
                                        <td>
                                            @if($group->is_active === 'true')
                                                <span class="badge bg-success">Aktif</span>
                                            @else
                                                <span class="badge bg-secondary">Nonaktif</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <div class="d-inline-flex align-items-center justify-content-end gap-1">
                                                <x-table.btn-edit :id="$group->id" tooltip="Ubah nama" />
                                                @if($eventIdsForDownload->get($group->id))
                                                    <x-btn-download-rekap-gabungan :event-id="$eventIdsForDownload->get($group->id)" />
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">Belum ada grup assessment.</td>
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

    @if($showModal)
        <div class="modal fade show" style="display: block; background: rgba(0,0,0,0.5);" tabindex="-1"
             wire:key="modal-edit-grup-{{ $editId }}"
             x-data="{ init() { setTimeout(() => { if (typeof feather !== 'undefined') feather.replace(); }, 50); } }"
             x-init="init()">
            <div class="modal-dialog modal-dialog-centered modal-lg" style="animation: slideDown 0.3s ease-out; max-width: min(720px, 92vw);">
                <div class="modal-content" style="border: none; border-radius: 16px; overflow: hidden; box-shadow: 0 20px 60px rgba(0,0,0,0.3);">
                    <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; padding: 24px 32px;">
                        <div class="d-flex align-items-center gap-3">
                            <div class="d-flex align-items-center justify-content-center"
                                 style="width: 48px; height: 48px; background: rgba(255,255,255,0.2); border-radius: 12px;">
                                <i class="link-icon text-white" data-feather="edit-3" style="width: 24px; height: 24px;"></i>
                            </div>
                            <div>
                                <h5 class="modal-title text-white fw-bold mb-0">Ubah Nama Grup</h5>
                                <p class="text-white-50 mb-0 mt-1" style="font-size: 0.875rem;">Nama ini tampil di daftar grup dan pilihan saat membuat event.</p>
                            </div>
                        </div>
                        <button type="button" wire:click="closeModal" class="btn-close btn-close-white"></button>
                    </div>
                    <div class="modal-body" style="padding: 32px; background: #f8f9fa;">
                        <form wire:submit="save">
                            <x-form.input
                                label="Nama Grup"
                                icon="layers"
                                model="nama"
                                placeholder="Assessment Eselon II 2026"
                                :required="true"
                                maxlength="255"
                                width="100%"
                                style="overflow-x: auto; text-overflow: clip;"
                            />
                        </form>
                    </div>
                    <div class="modal-footer" style="background: white; border-top: 2px solid #f0f0f0; padding: 20px 32px; gap: 12px;">
                        <x-modal.btn-cancel />
                        <x-modal.btn-save :isUpdate="true" text="Simpan" />
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
