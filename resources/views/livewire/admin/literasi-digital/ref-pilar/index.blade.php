<div>
    <x-breadcrumb :breadcrumbs="[
        ['url' => route('admin.dashboard'), 'title' => 'Dashboard'],
        ['url' => null, 'title' => 'Literasi Digital'],
        ['url' => null, 'title' => 'Referensi Pilar']
    ]" />
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <p class="text-muted mb-3">Ujian mengambil 15 soal aktif dari masing-masing pilar, lalu mengacak 60 soal itu sebagai satu paket. Deskripsi pilar nanti tampil di laporan.</p>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle shadow-sm border rounded" style="overflow:hidden;">
                            <thead class="table-light border-bottom">
                                <tr>
                                    <th class="text-center" style="width: 45px;">#</th>
                                    <th>Urutan</th>
                                    <th>Pilar</th>
                                    <th>Deskripsi</th>
                                    <th>Kode</th>
                                    <th>Soal aktif</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $item)
                                    <tr wire:key="pilar-{{ $item->id }}" class="@if($loop->iteration % 2 == 1) bg-body @endif border-bottom">
                                        <td class="text-center text-secondary fw-bold">{{ $loop->iteration }}</td>
                                        <td>{{ $item->urutan }}</td>
                                        <td>{{ $item->nama }}</td>
                                        <td class="text-wrap">
                                            @if (filled($item->deskripsi))
                                                {{ \Illuminate\Support\Str::limit($item->deskripsi, 180) }}
                                            @else
                                                <span class="badge bg-light text-muted border">Belum diisi</span>
                                            @endif
                                        </td>
                                        <td><code>{{ $item->kode }}</code></td>
                                        <td>{{ $item->soal_aktif_count }} / {{ $item->soal_count }}</td>
                                        <td>
                                            @if ($item->is_active === 'true')
                                                <span class="badge bg-success">Aktif</span>
                                            @else
                                                <span class="badge bg-secondary">Nonaktif</span>
                                            @endif
                                        </td>
                                        <td>
                                            <x-table.btn-edit :id="$item->id" />
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

    @if($showModal)
    <div class="modal fade show" style="display: block; background: rgba(0,0,0,0.5);" tabindex="-1"
         wire:key="modal-edit-{{ $editId }}"
         x-data="{ init() { setTimeout(() => { if (typeof feather !== 'undefined') feather.replace(); }, 50); } }"
         x-init="init()">
        <div class="modal-dialog modal-dialog-centered modal-lg" style="animation: slideDown 0.3s ease-out;">
            <div class="modal-content" style="border: none; border-radius: 16px; overflow: hidden; box-shadow: 0 20px 60px rgba(0,0,0,0.3);">
                <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; padding: 24px 32px;">
                    <div class="d-flex align-items-center gap-3">
                        <div class="d-flex align-items-center justify-content-center"
                             style="width: 48px; height: 48px; background: rgba(255,255,255,0.2); border-radius: 12px; backdrop-filter: blur(10px);">
                            <i class="link-icon text-white" data-feather="edit-3" style="width: 24px; height: 24px;"></i>
                        </div>
                        <div>
                            <h5 class="modal-title text-white fw-bold mb-0" style="font-size: 1.5rem;">Edit Referensi Pilar</h5>
                            <p class="text-white-50 mb-0 mt-1" style="font-size: 0.875rem;">Perbarui informasi pilar</p>
                        </div>
                    </div>
                    <button type="button" wire:click="closeModal" class="btn-close btn-close-white"
                            style="filter: brightness(0) invert(1); opacity: 0.8; transition: opacity 0.2s;"
                            onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0.8'"></button>
                </div>

                <div class="modal-body" style="padding: 32px; background: #f8f9fa; max-height: 70vh; overflow-y: auto;">
                    <form wire:submit="save">
                        <div class="row">
                            <div class="col-md-6">
                                <x-form.input
                                    label="Kode"
                                    icon="hash"
                                    model="kode"
                                    :readonly="true"
                                />
                            </div>
                            <div class="col-md-6">
                                <x-form.input
                                    label="Nama"
                                    icon="bookmark"
                                    model="nama"
                                    placeholder="Masukkan nama pilar"
                                    :required="true"
                                />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <x-form.input
                                    label="Urutan"
                                    icon="list"
                                    model="urutan"
                                    type="number"
                                    placeholder="Masukkan urutan"
                                    :required="true"
                                    min="1"
                                />
                            </div>
                            <div class="col-md-6">
                                <x-form.select
                                    label="Status"
                                    icon="toggle-right"
                                    model="is_active"
                                    placeholder=""
                                    :required="true"
                                >
                                    <option value="true">Aktif</option>
                                    <option value="false">Nonaktif</option>
                                </x-form.select>
                            </div>
                        </div>
                        <x-form.textarea
                            label="Deskripsi"
                            icon="align-left"
                            model="deskripsi"
                            placeholder="Narasi pilar yang nanti tampil di laporan"
                            :rows="6"
                        />
                    </form>
                </div>

                <div class="modal-footer" style="background: white; border-top: 2px solid #f0f0f0; padding: 20px 32px; gap: 12px;">
                    <x-modal.btn-cancel />
                    <x-modal.btn-save :isUpdate="true" />
                </div>
            </div>
        </div>
    </div>

    <style>
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
    @endif
</div>

@push('scripts')
<script>
    function initFeatherIcons() {
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
    }

    document.addEventListener('DOMContentLoaded', initFeatherIcons);

    document.addEventListener('livewire:initialized', () => {
        initFeatherIcons();

        Livewire.hook('morphed', () => {
            setTimeout(() => {
                initFeatherIcons();
            }, 0);
        });

        Livewire.on('modalOpened', () => {
            setTimeout(() => {
                initFeatherIcons();
            }, 100);
        });
    });
</script>
@endpush
