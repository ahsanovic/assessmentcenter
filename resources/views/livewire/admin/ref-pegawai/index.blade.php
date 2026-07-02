<div>
    <x-breadcrumb :breadcrumbs="[
        ['url' => route('admin.dashboard'), 'title' => 'Dashboard'],
        ['url' => null, 'title' => 'Data Pegawai']
    ]" />
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <x-modal.btn-add text="Tambah Pegawai" icon="plus-circle" />

                    <div class="card mt-4 mb-4 bg-light-subtle">
                        <div class="card-body">
                            <h6 class="text-danger"><i class="link-icon" data-feather="filter"></i> Filter</h6>
                            <div class="row mt-2">
                                <div class="col-sm-6">
                                    <div class="input-group" wire:ignore>
                                        <span class="input-group-text bg-white"><i data-feather="search"></i></span>
                                        <input wire:model.live.debounce="search" class="form-control" placeholder="cari nama atau NIP...">
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <x-btn-reset :text="'Reset'" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle shadow-sm border rounded" style="overflow:hidden;">
                            <thead class="table-light border-bottom">
                                <tr>
                                    <th class="text-center" style="width: 45px;">#</th>
                                    <th>Nama</th>
                                    <th>NIP</th>
                                    <th class="text-center" style="width: 120px;">QR Code</th>
                                    <th style="width: 180px;"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $index => $item)
                                    <tr class="@if($loop->iteration % 2 == 1) bg-body @endif border-bottom">
                                        <td class="text-center text-secondary fw-bold">{{ $data->firstItem() + $index }}</td>
                                        <td>{{ $item->nama }}</td>
                                        <td>{{ $item->nip }}</td>
                                        <td class="text-center">
                                            @if ($item->hasQrCode())
                                                <a href="{{ route('admin.pegawai.qrcode', $item->id) }}" target="_blank" title="Lihat QR Code">
                                                    <img src="{{ route('admin.pegawai.qrcode', $item->id) }}" alt="QR {{ $item->nama }}" width="64" height="64" class="border rounded">
                                                </a>
                                            @else
                                                <span class="badge bg-secondary">belum dibuat</span>
                                            @endif
                                        </td>
                                        <td>
                                            <button type="button" wire:click="generateQrCode({{ $item->id }})" wire:loading.attr="disabled" wire:target="generateQrCode({{ $item->id }})"
                                                class="btn btn-sm btn-outline-primary" data-bs-toggle="tooltip" data-bs-placement="top" title="Generate QR Code">
                                                <i class="link-icon" data-feather="grid" style="width:14px;height:14px;"></i>
                                                <span wire:loading.remove wire:target="generateQrCode({{ $item->id }})">QR</span>
                                                <span wire:loading wire:target="generateQrCode({{ $item->id }})">...</span>
                                            </button>
                                            <x-table.btn-edit :id="$item->id" />
                                            <x-table.btn-delete :id="$item->id" :disabled="auth()->user()->role == 'user'" />
                                        </td>
                                    </tr>
                                @endforeach

                                @if($data->count() === 0)
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">
                                            <i class="link-icon" data-feather="inbox" style="font-size: 24px; opacity: 0.7;"></i>
                                            <div class="mt-2 fw-semibold">Tidak ada data pegawai...</div>
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

    @if($showModal)
    <div class="modal fade show" style="display: block; background: rgba(0,0,0,0.5);" tabindex="-1"
         wire:key="modal-{{ $isUpdate ? 'edit-'.$editId : 'create' }}"
         x-data="{ init() { setTimeout(() => { if (typeof feather !== 'undefined') feather.replace(); }, 50); } }"
         x-init="init()">
        <div class="modal-dialog modal-dialog-centered modal-lg" style="animation: slideDown 0.3s ease-out;">
            <div class="modal-content" style="border: none; border-radius: 16px; overflow: hidden; box-shadow: 0 20px 60px rgba(0,0,0,0.3);">
                <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; padding: 24px 32px;">
                    <div class="d-flex align-items-center gap-3">
                        <div class="d-flex align-items-center justify-content-center"
                             style="width: 48px; height: 48px; background: rgba(255,255,255,0.2); border-radius: 12px;">
                            @if($isUpdate)
                                <i class="link-icon text-white" data-feather="edit-3" style="width: 24px; height: 24px;"></i>
                            @else
                                <i class="link-icon text-white" data-feather="plus-circle" style="width: 24px; height: 24px;"></i>
                            @endif
                        </div>
                        <div>
                            <h5 class="modal-title text-white fw-bold mb-0" style="font-size: 1.5rem;">
                                {{ $isUpdate ? 'Edit Pegawai' : 'Tambah Pegawai Baru' }}
                            </h5>
                            <p class="text-white-50 mb-0 mt-1" style="font-size: 0.875rem;">
                                QR Code berisi teks Nama dan NIP pegawai
                            </p>
                        </div>
                    </div>
                    <button type="button" wire:click="closeModal" class="btn-close btn-close-white"></button>
                </div>

                <div class="modal-body" style="padding: 32px; background: #f8f9fa;">
                    <form wire:submit="save">
                        <x-form.input
                            label="Nama"
                            icon="user"
                            model="form.nama"
                            placeholder="Masukkan nama pegawai"
                            :required="true"
                        />
                        <x-form.input
                            label="NIP"
                            icon="hash"
                            model="form.nip"
                            placeholder="18 digit NIP"
                            maxlength="18"
                            :required="true"
                        />
                    </form>
                </div>

                <div class="modal-footer" style="background: white; border-top: 2px solid #f0f0f0; padding: 20px 32px; gap: 12px;">
                    <x-modal.btn-cancel />
                    <x-modal.btn-save :isUpdate="$isUpdate" />
                </div>
            </div>
        </div>
    </div>

    <style>
        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
    @endif
</div>

@push('scripts')
<script>
    function initFeatherIcons() {
        if (typeof feather !== 'undefined') feather.replace();
    }
    document.addEventListener('DOMContentLoaded', initFeatherIcons);
    document.addEventListener('livewire:initialized', () => {
        initFeatherIcons();
        Livewire.hook('morph.updated', () => requestAnimationFrame(initFeatherIcons));
        Livewire.on('modalOpened', () => setTimeout(initFeatherIcons, 100));
    });
</script>
@endpush
