<div>
    <x-breadcrumb :breadcrumbs="[
        ['url' => route('admin.dashboard'), 'title' => 'Dashboard'],
        ['url' => null, 'title' => 'Intelektual'],
        ['url' => null, 'title' => 'Soal Sub Tes 3']
    ]" />
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <!-- Button Tambah dengan style modern -->
                    <x-modal.btn-add text="Tambah Soal Sub Tes 3" icon="plus-circle" />
                    
                    <div class="table-responsive mt-4">
                        <table class="table table-hover align-middle shadow-sm border rounded" style="overflow:hidden;">
                            <thead class="table-light border-bottom">
                                <tr>
                                    <th class="text-center" style="width: 45px;">#</th>
                                    <th>Model Soal</th>
                                    <th>Soal</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $index => $item)
                                    <tr class="@if($loop->iteration % 2 == 1) bg-body @endif border-bottom">
                                        <td class="text-center text-secondary fw-bold">{{ $data->firstItem() + $index }}</td>
                                        <td>{{ $item->modelSoal->jenis }}</td>
                                        <td class="text-wrap">
                                            @if($item->image_soal)
                                                <div class="mt-2">
                                                    <img src="{{ asset('storage/'.$item->image_soal) }}" class="img-fluid" style="max-height:200px;">
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            <x-table.btn-show :id="$item->id" />
                                            <x-table.btn-edit :id="$item->id" />
                                            <x-table.btn-delete :id="$item->id" :disabled="auth()->user()->role == 'user'" />
                                        </td>
                                    </tr>
                                @endforeach

                                @if($data->count() === 0)
                                    <tr>
                                        <td colspan="9" class="text-center text-muted py-4">
                                            <i class="link-icon" data-feather="inbox" style="font-size: 24px; opacity: 0.7;"></i>
                                            <div class="mt-2 fw-semibold">Tidak ada data soal sub tes 3...</div>
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>

                        {{-- Modal Detail --}}
                        <div class="modal fade @if($showDetailModal) show d-block @endif" tabindex="-1" 
                            style="@if($showDetailModal) display:block; background:rgba(0,0,0,.5) @endif">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    @if($selectedSoal)
                                        <div class="modal-header">
                                            <h5 class="modal-title">Detail Soal</h5>
                                            <button type="button" class="btn-close" wire:click="$set('showDetailModal', false)"></button>
                                        </div>
                                        <div class="modal-body table-responsive">
                                            <table class="table table-bordered">
                                                <tr>
                                                    <th>Model Soal</th>
                                                    <td>{{ $selectedSoal->modelSoal->jenis }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Soal</th>
                                                    <td>
                                                        @if($selectedSoal->image_soal)
                                                            <div class="mt-2">
                                                                <img src="{{ asset('storage/'.$selectedSoal->image_soal) }}" class="img-fluid" style="max-height:200px;">
                                                            </div>
                                                        @endif
                                                    </td>
                                                </tr>
                                                @foreach (['a','b','c','d','e'] as $opsi)
                                                    <tr>
                                                        <th>Opsi {{ strtoupper($opsi) }}</th>
                                                        <td>
                                                            @if($selectedSoal->{'image_opsi_'.$opsi})
                                                                <div class="mt-2">
                                                                    <img src="{{ asset('storage/'.$selectedSoal->{'image_opsi_'.$opsi}) }}" class="img-fluid" style="max-height:150px;">
                                                                </div>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                                <tr>
                                                    <th>Kunci Jawaban</th>
                                                    <td>
                                                        {{ $selectedSoal->kunci_jawaban }}
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                        <div class="modal-footer">
                                            <button class="btn btn-secondary" wire:click="$set('showDetailModal', false)">Tutup</button>
                                            <button class="btn btn-warning" wire:click="edit({{ $selectedSoal->id }})">Edit</button>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
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
                                {{ $isUpdate ? 'Edit Soal Sub Tes 3' : 'Tambah Soal Sub Tes 3 Baru' }}
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
                    <form wire:submit="save" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">
                                        <i class="link-icon me-2" data-feather="layers"></i>
                                        Model Soal
                                    </label>
                                    <select wire:model="form.model_id" class="form-select @error('form.model_id') is-invalid @enderror">
                                        <option value="">Pilih model soal</option>
                                        @foreach ($model_soal_options as $key => $item)
                                            <option value="{{ $key }}">{{ $item }}</option>
                                        @endforeach
                                    </select>
                                    @error('form.model_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <x-image-upload 
                                        label="Upload Gambar Soal" 
                                        model="form.image_soal" 
                                        field="image_soal"
                                        :value="$form['image_soal']"
                                        :old="$form['image_soal']"
                                    />
                                    @error('form.image_soal')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">
                                        <i class="link-icon me-2" data-feather="circle"></i>
                                        Pilihan A
                                    </label>
                                    <x-image-upload 
                                        label="" 
                                        model="form.image_opsi_a"
                                        field="image_opsi_a"
                                        :value="$form['image_opsi_a']"
                                        :old="$form['image_opsi_a']"
                                    />
                                    @error('form.image_opsi_a')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">
                                        <i class="link-icon me-2" data-feather="circle"></i>
                                        Pilihan B
                                    </label>
                                    <x-image-upload 
                                        label="" 
                                        model="form.image_opsi_b" 
                                        field="image_opsi_b"
                                        :value="$form['image_opsi_b']"
                                        :old="$form['image_opsi_b']"
                                    />
                                    @error('form.image_opsi_b')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">
                                        <i class="link-icon me-2" data-feather="circle"></i>
                                        Pilihan C
                                    </label>
                                    <x-image-upload 
                                        label="" 
                                        model="form.image_opsi_c" 
                                        field="image_opsi_c"
                                        :value="$form['image_opsi_c']"
                                        :old="$form['image_opsi_c']"
                                    />
                                    @error('form.image_opsi_c')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">
                                        <i class="link-icon me-2" data-feather="circle"></i>
                                        Pilihan D
                                    </label>
                                    <x-image-upload 
                                        label="" 
                                        model="form.image_opsi_d" 
                                        field="image_opsi_d"
                                        :value="$form['image_opsi_d']"
                                        :old="$form['image_opsi_d']"
                                    />
                                    @error('form.image_opsi_d')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">
                                        <i class="link-icon me-2" data-feather="circle"></i>
                                        Pilihan E
                                    </label>
                                    <x-image-upload 
                                        label="" 
                                        model="form.image_opsi_e" 
                                        field="image_opsi_e"
                                        :value="$form['image_opsi_e']"
                                        :old="$form['image_opsi_e']"
                                    />
                                    @error('form.image_opsi_e')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">
                                        <i class="link-icon me-2" data-feather="check-circle"></i>
                                        Kunci Jawaban
                                    </label>
                                    <select wire:model="form.kunci_jawaban" class="form-select @error('form.kunci_jawaban') is-invalid @enderror">
                                        <option value="">Pilih kunci jawaban</option>
                                        <option value="A">A</option>
                                        <option value="B">B</option>
                                        <option value="C">C</option>
                                        <option value="D">D</option>
                                        <option value="E">E</option>
                                    </select>
                                    @error('form.kunci_jawaban')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
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
            // Tunggu sebentar untuk memastikan DOM sudah ter-render
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
