<div>
    <x-breadcrumb :breadcrumbs="[
        ['url' => route('admin.dashboard'), 'title' => 'Dashboard'],
        ['url' => null, 'title' => 'Interpersonal'],
        ['url' => null, 'title' => 'Data Referensi']
    ]" />
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <!-- Button Tambah dengan style modern -->
                    <x-modal.btn-add text="Tambah Referensi" icon="plus-circle" />
                    
                    <div class="table-responsive mt-4">
                        <table class="table table-hover align-middle shadow-sm border rounded" style="overflow:hidden;">
                            <thead class="table-light border-bottom">
                                <tr>
                                    <th class="text-center" style="width: 45px;">#</th>
                                    <th>Nama Indikator</th>
                                    <th>Nomor Indikator</th>
                                    <th>Kualifikasi/Uraian Potensi</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $index => $item)
                                    <tr class="@if($loop->iteration % 2 == 1) bg-body @endif border-bottom">
                                        <td class="text-center text-secondary fw-bold">{{ $data->firstItem() + $index }}</td>
                                        <td>{{ $item->indikator_nama }}</td>
                                        <td>{{ $item->indikator_nomor }}</td>
                                        <td class="text-wrap">
                                            @if(is_array($item->kualifikasi))
                                                @foreach($item->kualifikasi as $qual)
                                                    <b>{{ $qual['kualifikasi'] ?? '' }}:</b> <br />
                                                    {{ $qual['uraian_potensi'] ?? '' }} <br />
                                                @endforeach
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>
                                            <x-table.btn-edit :id="$item->id" />
                                            <x-table.btn-delete :id="$item->id" :disabled="auth()->user()->role == 'user'" />
                                        </td>
                                    </tr>
                                @endforeach

                                @if($data->count() === 0)
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">
                                            <i class="link-icon" data-feather="inbox" style="font-size: 24px; opacity: 0.7;"></i>
                                            <div class="mt-2 fw-semibold">Tidak ada data referensi...</div>
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
        <div class="modal-dialog modal-dialog-centered modal-xl" style="animation: slideDown 0.3s ease-out;">
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
                                {{ $isUpdate ? 'Edit Referensi' : 'Tambah Referensi Baru' }}
                            </h5>
                            <p class="text-white-50 mb-0 mt-1" style="font-size: 0.875rem;">
                                {{ $isUpdate ? 'Perbarui informasi referensi' : 'Isi form untuk menambahkan referensi' }}
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
                                <x-form.input
                                    label="Nama Indikator"
                                    icon="bookmark"
                                    model="indikator_nama"
                                    placeholder="Masukkan nama indikator"
                                    :required="true"
                                />
                            </div>
                            <div class="col-md-6">
                                <x-form.input
                                    label="Nomor Indikator"
                                    icon="hash"
                                    model="indikator_nomor"
                                    type="number"
                                    placeholder="Masukkan nomor indikator"
                                    :required="true"
                                />
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <x-form.textarea
                                    label="Uraian Potensi (Sangat Baik)"
                                    icon="star"
                                    model="kualifikasi.0.uraian_potensi"
                                    placeholder="Masukkan uraian potensi untuk kategori Sangat Baik"
                                    :required="true"
                                    :rows="4"
                                />
                            </div>
                            <div class="col-md-6">
                                <x-form.textarea
                                    label="Uraian Potensi (Baik)"
                                    icon="thumbs-up"
                                    model="kualifikasi.1.uraian_potensi"
                                    placeholder="Masukkan uraian potensi untuk kategori Baik"
                                    :required="true"
                                    :rows="4"
                                />
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <x-form.textarea
                                    label="Uraian Potensi (Cukup)"
                                    icon="check"
                                    model="kualifikasi.2.uraian_potensi"
                                    placeholder="Masukkan uraian potensi untuk kategori Cukup"
                                    :required="true"
                                    :rows="4"
                                />
                            </div>
                            <div class="col-md-6">
                                <x-form.textarea
                                    label="Uraian Potensi (Kurang/Sangat Kurang)"
                                    icon="minus"
                                    model="kualifikasi.3.uraian_potensi"
                                    placeholder="Masukkan uraian potensi untuk kategori Kurang/Sangat Kurang"
                                    :required="true"
                                    :rows="4"
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
