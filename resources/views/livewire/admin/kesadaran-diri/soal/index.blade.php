<div>
    <x-breadcrumb :breadcrumbs="[
        ['url' => route('admin.dashboard'), 'title' => 'Dashboard'],
        ['url' => null, 'title' => 'Kesadaran Diri'],
        ['url' => null, 'title' => 'Soal']
    ]" />
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <!-- Button Tambah dengan style modern -->
                    <x-modal.btn-add text="Tambah Soal Kesadaran Diri" icon="plus-circle" />
                    
                    <div class="card mt-4 mb-4 bg-light-subtle">
                        <div class="card-body">
                            <h6 class="text-danger" wire:ignore><i class="link-icon" data-feather="filter"></i> Filter</h6>
                            <div class="row mt-2">
                                <div class="col-sm-4">
                                    <select wire:model.live="jenis_indikator" class="form-select" id="jenis-indikator">
                                        <option value="">pilih jenis indikator</option>
                                        @foreach ($indikator as $key => $item)
                                            <option value="{{ $key }}">{{ $item }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-6">
                                    <div class="input-group" wire:ignore>
                                        <span class="input-group-text bg-white"><i data-feather="search"></i></span>
                                        <input wire:model.live.debounce="search" class="form-control" placeholder="cari soal...">
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
                                    <th>Deskripsi Soal</th>
                                    <th>Pilihan A</th>
                                    <th>Poin A</th>
                                    <th>Pilihan B</th>
                                    <th>Poin B</th>
                                    <th>Pilihan C</th>
                                    <th>Poin C</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $index => $item)
                                    <tr class="@if($loop->iteration % 2 == 1) bg-body @endif border-bottom">
                                        <td class="text-center text-secondary fw-bold">{{ $data->firstItem() + $index }}</td>
                                        <td class="text-wrap">{{ $item->soal }}</td>
                                        <td class="text-wrap">{{ $item->opsi_a }}</td>
                                        <td>{{ $item->poin_opsi_a }}</td>
                                        <td class="text-wrap">{{ $item->opsi_b }}</td>
                                        <td>{{ $item->poin_opsi_b }}</td>
                                        <td class="text-wrap">{{ $item->opsi_c }}</td>
                                        <td>{{ $item->poin_opsi_c }}</td>
                                        <td>
                                            <x-table.btn-edit :id="$item->id" />
                                            <x-table.btn-delete :id="$item->id" :disabled="auth()->user()->role == 'user'" />
                                        </td>
                                    </tr>
                                @endforeach

                                @if($data->count() === 0)
                                    <tr>
                                        <td colspan="9" class="text-center text-muted py-4">
                                            <i class="link-icon" data-feather="inbox" style="font-size: 24px; opacity: 0.7;"></i>
                                            <div class="mt-2 fw-semibold">Tidak ada data soal...</div>
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

    <!-- Modal Form dengan style modern -->
    @if($showModal)
    <div class="modal fade show" style="display: block; background: rgba(0,0,0,0.5);" tabindex="-1" 
         wire:key="modal-{{ $isUpdate ? 'edit-'.$editId : 'create' }}"
         x-data="{ init() { setTimeout(() => { if (typeof feather !== 'undefined') feather.replace(); }, 50); } }"
         x-init="init()">
        <div class="modal-dialog modal-dialog-centered modal-lg" style="animation: slideDown 0.3s ease-out;">
            <div class="modal-content" style="border: none; border-radius: 16px; overflow: hidden; box-shadow: 0 20px 60px rgba(0,0,0,0.3);">
                <!-- Modal Header -->
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
                                {{ $isUpdate ? 'Edit Soal Kesadaran Diri' : 'Tambah Soal Kesadaran Diri Baru' }}
                            </h5>
                            <p class="text-white-50 mb-0 mt-1" style="font-size: 0.875rem;">
                                {{ $isUpdate ? 'Perbarui informasi soal' : 'Isi form untuk menambahkan soal' }}
                            </p>
                        </div>
                    </div>
                    <button type="button" wire:click="closeModal" class="btn-close btn-close-white" 
                            style="filter: brightness(0) invert(1); opacity: 0.8; transition: opacity 0.2s;"
                            onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0.8'"></button>
                </div>

                <!-- Modal Body -->
                <div class="modal-body" style="padding: 32px; background: #f8f9fa; max-height: 70vh; overflow-y: auto;">
                    <form wire:submit="save">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">
                                        <i class="link-icon me-2" data-feather="layers"></i>
                                        Jenis Indikator
                                    </label>
                                    <select wire:model="form.jenis_indikator_id" class="form-select @error('form.jenis_indikator_id') is-invalid @enderror">
                                        <option value="">Pilih jenis indikator</option>
                                        @foreach ($indikator as $key => $item)
                                            <option value="{{ $key }}">{{ $key . ' - ' . $item }}</option>
                                        @endforeach
                                    </select>
                                    @error('form.jenis_indikator_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <x-form.input
                            label="Deskripsi Soal"
                            icon="file-text"
                            model="form.soal"
                            placeholder="Masukkan deskripsi soal"
                            :required="true"
                        />

                        <div class="row">
                            <div class="col-md-8">
                                <x-form.input
                                    label="Pilihan A"
                                    icon="circle"
                                    model="form.opsi_a"
                                    placeholder="Masukkan pilihan jawaban A"
                                    :required="true"
                                />
                            </div>
                            <div class="col-md-4">
                                <x-form.input
                                    label="Poin A"
                                    icon="award"
                                    model="form.poin_opsi_a"
                                    type="number"
                                    placeholder="Skor"
                                    :required="true"
                                />
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-8">
                                <x-form.input
                                    label="Pilihan B"
                                    icon="circle"
                                    model="form.opsi_b"
                                    placeholder="Masukkan pilihan jawaban B"
                                    :required="true"
                                />
                            </div>
                            <div class="col-md-4">
                                <x-form.input
                                    label="Poin B"
                                    icon="award"
                                    model="form.poin_opsi_b"
                                    type="number"
                                    placeholder="Skor"
                                    :required="true"
                                />
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-8">
                                <x-form.input
                                    label="Pilihan C"
                                    icon="circle"
                                    model="form.opsi_c"
                                    placeholder="Masukkan pilihan jawaban C"
                                    :required="true"
                                />
                            </div>
                            <div class="col-md-4">
                                <x-form.input
                                    label="Poin C"
                                    icon="award"
                                    model="form.poin_opsi_c"
                                    type="number"
                                    placeholder="Skor"
                                    :required="true"
                                />
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Modal Footer -->
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
    // Fungsi untuk initialize Feather icons
    function initFeatherIcons() {
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
    }

    // Initialize saat halaman dimuat
    document.addEventListener('DOMContentLoaded', initFeatherIcons);

    // Initialize setelah Livewire initialized
    document.addEventListener('livewire:initialized', () => {
        initFeatherIcons();

        // Hook untuk setiap morph update
        Livewire.hook('morph.updated', ({ el, component }) => {
            requestAnimationFrame(() => {
                initFeatherIcons();
            });
        });

        // Hook untuk setiap commit
        Livewire.hook('commit', ({ component, commit, respond, succeed, fail }) => {
            succeed(({ snapshot, effect }) => {
                requestAnimationFrame(() => {
                    initFeatherIcons();
                });
            });
        });
    });

    // Listen untuk event custom 'modalOpened'
    document.addEventListener('livewire:initialized', () => {
        Livewire.on('modalOpened', () => {
            setTimeout(() => {
                initFeatherIcons();
            }, 100);
        });
    });

    // MutationObserver untuk mendeteksi perubahan DOM
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

    // Mulai observe setelah DOM ready
    document.addEventListener('DOMContentLoaded', () => {
        observer.observe(document.body, {
            childList: true,
            subtree: true
        });
    });
</script>
@endpush
