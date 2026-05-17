<div>
    <x-breadcrumb :breadcrumbs="[
        ['url' => route('admin.dashboard'), 'title' => 'Dashboard'],
        ['url' => null, 'title' => 'PSPK'],
        ['url' => null, 'title' => 'Paket analisa kasus (PDF)']
    ]" />
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <p class="text-muted small mb-3">
                        Satu PDF dipakai bersama oleh semua soal <strong>Analisa Kasus</strong> level 3 atau 4.
                        Pasangkan paket pada tiap soal di menu Soal PSPK.
                    </p>
                    <x-modal.btn-add text="Tambah paket kasus" icon="plus-circle" wire:click="openModal" />
                    <div class="table-responsive mt-4">
                        <table class="table table-hover align-middle shadow-sm border rounded">
                            <thead class="table-light border-bottom">
                                <tr>
                                    <th class="text-center" style="width: 45px;">#</th>
                                    <th>Level</th>
                                    <th>Nama paket</th>
                                    <th>Soal terhubung</th>
                                    <th>Preview</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $index => $item)
                                    <tr>
                                        <td class="text-center text-secondary fw-bold">{{ $data->firstItem() + $index }}</td>
                                        <td>{{ $level_options[$item->level_pspk_id] ?? $item->level_pspk_id }}</td>
                                        <td>{{ $item->nama ?: 'Paket #'.$item->id }}</td>
                                        <td>{{ $item->soal_pspk_count }}</td>
                                        <td>
                                            @if(filled($item->lampiran_pdf_path))
                                                <a href="{{ route('admin.pspk-kasus-lampiran.pdf', $item) }}"
                                                   target="_blank" rel="noopener noreferrer"
                                                   class="btn btn-sm btn-icon btn-danger"
                                                   data-bs-toggle="tooltip"
                                                   data-bs-placement="top"
                                                   title="Lihat PDF">
                                                    <i data-feather="file-text"></i>
                                                </a>
                                            @else
                                                <span class="text-muted small">—</span>
                                            @endif
                                        </td>
                                        <td>
                                            <x-table.btn-edit :id="$item->id" wire:click="edit({{ $item->id }})" />
                                            <x-table.btn-delete :id="$item->id" :disabled="auth()->user()->role == 'user'" />
                                        </td>
                                    </tr>
                                @endforeach
                                @if($data->count() === 0)
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">Belum ada paket kasus.</td>
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
         wire:key="kasus-modal-{{ $isUpdate ? 'edit-'.$editId : 'create' }}"
         x-data="{ init() { setTimeout(() => { if (typeof feather !== 'undefined') feather.replace(); }, 50); } }"
         x-init="init()">
        <div class="modal-dialog modal-dialog-centered modal-lg" style="animation: kasusModalSlideDown 0.3s ease-out;">
            <div class="modal-content" style="border: none; border-radius: 16px; overflow: hidden; box-shadow: 0 20px 60px rgba(0,0,0,0.3);">
                <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; padding: 24px 32px;">
                    <div class="d-flex align-items-center gap-3">
                        <div class="d-flex align-items-center justify-content-center"
                             style="width: 48px; height: 48px; background: rgba(255,255,255,0.2); border-radius: 12px; backdrop-filter: blur(10px);">
                            @if($isUpdate)
                                <i class="link-icon text-white" data-feather="edit-3" style="width: 24px; height: 24px;"></i>
                            @else
                                <i class="link-icon text-white" data-feather="file-text" style="width: 24px; height: 24px;"></i>
                            @endif
                        </div>
                        <div>
                            <h5 class="modal-title text-white fw-bold mb-0" style="font-size: 1.5rem;">
                                {{ $isUpdate ? 'Edit paket analisa kasus' : 'Paket analisa kasus baru' }}
                            </h5>
                            <p class="text-white-50 mb-0 mt-1" style="font-size: 0.875rem;">
                                {{ $isUpdate ? 'Perbarui PDF atau informasi paket' : 'Unggah satu PDF untuk semua soal Ankas level terkait' }}
                            </p>
                        </div>
                    </div>
                    <button type="button" wire:click="closeModal" class="btn-close btn-close-white"
                            style="filter: brightness(0) invert(1); opacity: 0.8; transition: opacity 0.2s;"
                            onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0.8'"></button>
                </div>

                <div class="modal-body" style="padding: 32px; background: #f8f9fa; max-height: 70vh; overflow-y: auto;">
                    <form wire:submit="save">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                <i class="link-icon me-2" data-feather="layers"></i>
                                Level PSPK
                            </label>
                            <select wire:model="level_pspk_id" class="form-select @error('level_pspk_id') is-invalid @enderror">
                                <option value="">Pilih level (3 atau 4)</option>
                                @foreach ($level_options as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('level_pspk_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                <i class="link-icon me-2" data-feather="tag"></i>
                                Nama paket (opsional)
                            </label>
                            <input type="text" wire:model="nama" class="form-control @error('nama') is-invalid @enderror" placeholder="mis. Kasus kepegawaian 2026">
                            @error('nama')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                <i class="link-icon me-2" data-feather="file-text"></i>
                                Berkas PDF @if(!$isUpdate)<span class="text-danger">*</span>@endif <span class="fw-normal text-muted">(maks. 5 MB)</span>
                            </label>
                            <input type="file" wire:model="lampiran_pdf" accept="application/pdf,.pdf" class="form-control @error('lampiran_pdf') is-invalid @enderror">
                            <div wire:loading wire:target="lampiran_pdf" class="form-text text-muted">Mengunggah…</div>
                            @if($isUpdate)
                                <div class="form-text text-muted">Kosongkan jika tidak mengganti PDF.</div>
                            @endif
                            @error('lampiran_pdf')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
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

    @endif

    <style>
        @keyframes kasusModalSlideDown {
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

    <div wire:ignore>
        <script>
            (function () {
                function initKasusModalFeather() {
                    if (typeof feather !== 'undefined') {
                        feather.replace();
                    }
                }

                document.addEventListener('DOMContentLoaded', initKasusModalFeather);

                document.addEventListener('livewire:initialized', () => {
                    initKasusModalFeather();

                    Livewire.hook('morph.updated', () => {
                        requestAnimationFrame(() => initKasusModalFeather());
                    });

                    Livewire.hook('commit', ({ succeed }) => {
                        succeed(() => {
                            requestAnimationFrame(() => initKasusModalFeather());
                        });
                    });

                    Livewire.on('modalOpened', () => {
                        setTimeout(() => initKasusModalFeather(), 100);
                    });
                });
            })();
        </script>
    </div>
</div>
