<div>
    <x-breadcrumb :breadcrumbs="[
        ['url' => route('admin.dashboard'), 'title' => 'Dashboard'],
        ['url' => null, 'title' => 'PSPK'],
        ['url' => null, 'title' => 'Referensi Saran Pengembangan']
    ]" />
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <x-modal.btn-add text="Tambah Referensi" icon="plus-circle" wire:click="openModal" />
                    <div class="card mt-4 mb-4 bg-light-subtle">
                        <div class="card-body">
                            <h6 class="text-danger" wire:ignore><i class="link-icon" data-feather="filter"></i> Filter</h6>
                            <div class="row mt-2 align-items-center">
                                <div class="col-sm-3">
                                    <select wire:model.live="filter_level_pspk" class="form-select" id="level-pspk">
                                        <option value="">pilih level pspk</option>
                                        @foreach ($level_pspk_options as $key => $item)
                                            <option value="{{ $key }}">{{ $item }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-5">
                                    <div class="input-group">
                                        <span class="input-group-text bg-white" wire:ignore><i data-feather="search"></i></span>
                                        <input wire:model.live.debounce.300ms="search" class="form-control" placeholder="cari teks saran (semua aspek)...">
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
                                    <th style="min-width: 110px;">Level PSPK</th>
                                    @foreach ($saranColumns as $col)
                                        <th class="small text-uppercase" style="min-width: 140px;">{{ $saranLabels[$col] ?? $col }}</th>
                                    @endforeach
                                    <th style="width: 100px;"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $index => $item)
                                    <tr class="@if($loop->iteration % 2 == 1) bg-body @endif border-bottom">
                                        <td class="text-center text-secondary fw-bold">{{ $data->firstItem() + $index }}</td>
                                        <td>
                                            <span class="badge bg-primary-subtle text-primary">
                                                {{ $item->levelPspk->level_pspk ?? $item->level_pspk_id }}
                                            </span>
                                        </td>
                                        @foreach ($saranColumns as $col)
                                            <td class="text-wrap small text-muted" style="max-width: 14rem;">
                                                {{ \Illuminate\Support\Str::limit(strip_tags((string) ($item->{$col} ?? '')), 90) }}
                                            </td>
                                        @endforeach
                                        <td>
                                            <x-table.btn-edit :id="$item->id" wire:click="edit({{ $item->id }})" />
                                            <x-table.btn-delete :id="$item->id" :disabled="auth()->user()->role == 'user'" />
                                        </td>
                                    </tr>
                                @endforeach

                                @if($data->count() === 0)
                                    <tr>
                                        <td colspan="{{ 2 + count($saranColumns) + 1 }}" class="text-center text-muted py-4">
                                            <i class="link-icon" data-feather="inbox" style="font-size: 24px; opacity: 0.7;"></i>
                                            <div class="mt-2 fw-semibold">Tidak ada data referensi saran pengembangan...</div>
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
        <div class="modal-dialog modal-dialog-centered modal-xl" style="animation: slideDown 0.3s ease-out;">
            <div class="modal-content" style="border: none; border-radius: 16px; overflow: hidden; box-shadow: 0 20px 60px rgba(0,0,0,0.3);">
                <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; padding: 24px 32px;">
                    <div class="d-flex align-items-center gap-3">
                        <div class="d-flex align-items-center justify-content-center" 
                             style="width: 48px; height: 48px; background: rgba(255,255,255,0.2); border-radius: 12px; backdrop-filter: blur(10px);">
                            @if($isUpdate)
                                <i class="link-icon text-white" data-feather="edit-3" style="width: 24px; height: 24px;"></i>
                            @else
                                <i class="link-icon text-white" data-feather="plus-circle" style="width: 24px; height: 24px;"></i>
                            @endif
                        </div>
                        <div>
                            <h5 class="modal-title text-white fw-bold mb-0" style="font-size: 1.5rem;">
                                {{ $isUpdate ? 'Edit Referensi Saran Pengembangan' : 'Tambah Referensi Saran Pengembangan Baru' }}
                            </h5>
                            <p class="text-white-50 mb-0 mt-1" style="font-size: 0.875rem;">
                                {{ $isUpdate ? 'Perbarui teks saran per kode aspek' : 'Isi saran pengembangan untuk tiap kode aspek PSPK' }}
                            </p>
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
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">
                                        <i class="link-icon me-2" data-feather="layers"></i>
                                        Level PSPK
                                        <span class="text-danger">*</span>
                                    </label>
                                    <select wire:model="form.level_pspk_id" class="form-select @error('form.level_pspk_id') is-invalid @enderror">
                                        <option value="">Pilih level pspk</option>
                                        @foreach ($level_pspk_options as $key => $item)
                                            <option value="{{ $key }}">{{ $item }}</option>
                                        @endforeach
                                    </select>
                                    @error('form.level_pspk_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            @foreach ($saranColumns as $col)
                                <div class="col-md-6">
                                    <x-form.textarea
                                        :label="$saranLabels[$col] ?? strtoupper($col)"
                                        icon="message-square"
                                        :model="'form.' . $col"
                                        rows="3"
                                        :placeholder="'Saran untuk aspek ' . ($saranLabels[$col] ?? $col)"
                                        :required="false"
                                    />
                                </div>
                            @endforeach
                        </div>
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

        Livewire.hook('morph.updated', ({ el, component }) => {
            requestAnimationFrame(() => {
                initFeatherIcons();
            });
        });

        Livewire.hook('commit', ({ component, commit, respond, succeed, fail }) => {
            succeed(({ snapshot, effect }) => {
                requestAnimationFrame(() => {
                    initFeatherIcons();
                });
            });
        });
    });

    document.addEventListener('livewire:initialized', () => {
        Livewire.on('modalOpened', () => {
            setTimeout(() => {
                initFeatherIcons();
            }, 100);
        });
    });

    const observer = new MutationObserver((mutations) => {
        let shouldUpdate = false;
        mutations.forEach((mutation) => {
            if (mutation.addedNodes.length > 0) {
                mutation.addedNodes.forEach((node) => {
                    if (node.nodeType === 1 && (
                        node.classList?.contains('modal') || 
                        node.querySelector?.('[data-feather]')
                    )) {
                        shouldUpdate = true;
                    }
                });
            }
        });
        if (shouldUpdate) {
            requestAnimationFrame(() => {
                initFeatherIcons();
            });
        }
    });

    document.addEventListener('DOMContentLoaded', () => {
        observer.observe(document.body, {
            childList: true,
            subtree: true
        });
    });
</script>
@endpush
