<div>
    <x-breadcrumb :breadcrumbs="[
        ['url' => route('admin.dashboard'), 'title' => 'Dashboard'],
        ['url' => null, 'title' => 'Data Assessor']
    ]" />
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <!-- Button Tambah dengan style modern -->
                    <x-modal.btn-add text="Tambah Assessor" icon="plus-circle" />

                    <div class="card mt-4 mb-4 bg-light-subtle">
                        <div class="card-body">
                            <h6 class="text-danger" wire:ignore><i class="link-icon" data-feather="filter"></i> Filter</h6>
                            <div class="row mt-2">
                                <div class="col-sm-3">
                                    <div wire:ignore>
                                        <select wire:model.live="event" class="form-select" id="event">
                                            <option value="">event</option>
                                            @foreach ($option_event as $key => $item)
                                                <option value="{{ $key }}">{{ $item }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <select wire:model.live="filter_is_active" class="form-select" id="status">
                                        <option value="">status</option>
                                        @foreach ($option_status as $key => $item)
                                            <option value="{{ $key }}">{{  $item }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-2">
                                    <select wire:model.live="filter_is_asn" class="form-select" id="jenis-assessor">
                                        <option value="">jenis assessor</option>
                                        <option value="true">ASN</option>
                                        <option value="false">Non ASN</option>
                                    </select>
                                </div>
                                <div class="col-sm-3">
                                    <div class="input-group" wire:ignore>
                                        <span class="input-group-text bg-white"><i data-feather="search"></i></span>
                                        <input wire:model.live.debounce="search" class="form-control" placeholder="cari assessor...">
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
                                    <th>Nama Assessor</th>
                                    <th>Jabatan</th>
                                    <th>Instansi</th>
                                    <th>Jenis</th>
                                    <th>Status</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $index => $item)
                                    <tr class="@if($loop->iteration % 2 == 1) bg-body @endif border-bottom">
                                        <td class="text-center text-secondary fw-bold">{{ $data->firstItem() + $index }}</td>
                                        <td>
                                            <span class="fw-medium">{{ $item->nama }}</span><br>
                                            <span class="text-muted small">
                                            @if ($item->is_asn == 'true')
                                                <div class="fw-medium">{{ $item->nip }}</div>
                                                @if (!empty($item->golPangkat?->pangkat) && !empty($item->golPangkat?->golongan))
                                                    <span class="badge bg-secondary-subtle text-dark mt-1">
                                                        {{ $item->golPangkat->pangkat . ' - ' . $item->golPangkat->golongan }}
                                                    </span>
                                                @else
                                                    <span class="text-muted d-block mt-1"></span>
                                                @endif
                                            @elseif ($item->is_asn == 'false')
                                                <div class="fw-medium">{{ $item->nik }}</div>
                                            @endif
                                            </span>
                                        </td>
                                        <td class="text-wrap">
                                            <span class="badge bg-info-subtle text-dark fw-normal">{{ $item->jabatan }}</span>
                                        </td>
                                        <td class="text-wrap fw-medium text-dark">{{ $item->instansi ?? '-' }}</td>
                                        <td>
                                            <span class="badge {{ $item->is_asn == 'true' ? 'bg-primary' : 'bg-dark' }}">
                                                {{ $item->is_asn == 'true' ? 'ASN' : 'Non ASN' }}
                                            </span>
                                        </td>
                                        <td>
                                            @if ($item->is_active == 'true')
                                                <span class="badge bg-success">✔ Aktif</span>    
                                            @else
                                                <span class="badge bg-danger">✖ Non Aktif</span>
                                            @endif 
                                        </td>
                                        <td class="text-center">
                                            <x-table.btn-edit :id="$item->id" />
                                            <x-table.btn-delete :id="$item->id" />
                                        </td>
                                    </tr>
                                @endforeach

                                @if($data->count() === 0)
                                    <tr>
                                        <td colspan="9" class="text-center text-muted py-4">
                                            <i class="link-icon" data-feather="inbox" style="font-size: 24px; opacity: 0.7;"></i>
                                            <div class="mt-2 fw-semibold">Tidak ada data assessor...</div>
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
                                {{ $isUpdate ? 'Edit Assessor' : 'Tambah Assessor Baru' }}
                            </h5>
                            <p class="text-white-50 mb-0 mt-1" style="font-size: 0.875rem;">
                                {{ $isUpdate ? 'Perbarui informasi assessor' : 'Isi form untuk menambahkan assessor' }}
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
                                        <i class="link-icon me-2" data-feather="users"></i>
                                        Jenis Assessor <span class="text-danger">*</span>
                                    </label>
                                    <select wire:model.live="is_asn" class="form-select @error('is_asn') is-invalid @enderror">
                                        <option value="">- Pilih -</option>
                                        <option value="true">ASN</option>
                                        <option value="false">Non ASN</option>
                                    </select>
                                    @error('is_asn')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <x-form.input
                            label="Nama Assessor"
                            icon="user"
                            model="nama"
                            placeholder="Masukkan nama assessor"
                            :required="true"
                        />

                        @if ($is_asn == 'true')
                        <div class="row">
                            <div class="col-md-6">
                                <x-form.input
                                    label="NIP"
                                    icon="credit-card"
                                    model="nip"
                                    placeholder="18 digit"
                                    :required="true"
                                />
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">
                                        <i class="link-icon me-2" data-feather="award"></i>
                                        Golongan/Pangkat <span class="text-danger">*</span>
                                    </label>
                                    <select wire:model="gol_pangkat_id" class="form-select @error('gol_pangkat_id') is-invalid @enderror">
                                        <option value="">- Pilih -</option>
                                        @foreach ($option_gol_pangkat as $item)
                                            <option value="{{ $item->id }}">{{ $item->pangkat . ' - ' . $item->golongan }}</option>
                                        @endforeach
                                    </select>
                                    @error('gol_pangkat_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        @elseif ($is_asn == 'false')
                        <div class="row">
                            <div class="col-md-6">
                                <x-form.input
                                    label="NIK"
                                    icon="credit-card"
                                    model="nik"
                                    placeholder="16 digit"
                                    :required="true"
                                />
                            </div>
                        </div>
                        @endif

                        <x-form.input
                            label="Jabatan"
                            icon="briefcase"
                            model="jabatan"
                            placeholder="Masukkan jabatan"
                            :required="true"
                        />

                        <x-form.input
                            label="Instansi"
                            icon="globe"
                            model="instansi"
                            placeholder="Masukkan instansi"
                            :required="true"
                        />

                        <div class="row">
                            <div class="col-md-6">
                                <x-form.input
                                    label="Password {{ $isUpdate ? '' : '*' }}"
                                    icon="lock"
                                    model="password"
                                    type="password"
                                    :placeholder="$isUpdate ? 'Kosongkan jika tidak diubah' : 'Minimal 8 karakter'"
                                    :required="!$isUpdate"
                                />
                            </div>
                        </div>

                        @if ($isUpdate)
                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                <i class="link-icon me-2" data-feather="toggle-left"></i>
                                Status
                            </label>
                            <div>
                                <div class="form-check form-check-inline">
                                    <input type="radio" class="form-check-input" wire:model="is_active" id="statusAktif" value="true">
                                    <label class="form-check-label" for="statusAktif">Aktif</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input type="radio" class="form-check-input" wire:model="is_active" id="statusNonAktif" value="false">
                                    <label class="form-check-label" for="statusNonAktif">Non Aktif</label>
                                </div>
                            </div>
                        </div>
                        @endif
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
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
    @endif
</div>

@push('scripts')
<script>
    function initFeatherIcons() { if (typeof feather !== 'undefined') feather.replace(); }
    document.addEventListener('DOMContentLoaded', initFeatherIcons);
    document.addEventListener('livewire:initialized', () => {
        initFeatherIcons();
        Livewire.hook('morph.updated', ({ el, component }) => { requestAnimationFrame(() => { initFeatherIcons(); }); });
        Livewire.hook('commit', ({ component, commit, respond, succeed, fail }) => { succeed(({ snapshot, effect }) => { requestAnimationFrame(() => { initFeatherIcons(); }); }); });
    });
    document.addEventListener('livewire:initialized', () => { Livewire.on('modalOpened', () => { setTimeout(() => { initFeatherIcons(); }, 100); }); });
    const observer = new MutationObserver((mutations) => { let shouldUpdate = false; mutations.forEach((mutation) => { if (mutation.addedNodes.length > 0) { mutation.addedNodes.forEach((node) => { if (node.nodeType === 1 && (node.classList?.contains('modal') || node.querySelector?.('[data-feather]'))) { shouldUpdate = true; } }); } }); if (shouldUpdate) { requestAnimationFrame(() => { initFeatherIcons(); }); } });
    document.addEventListener('DOMContentLoaded', () => { observer.observe(document.body, { childList: true, subtree: true }); });
</script>
@endpush

@push('js')
    @script()
        <script>
            $(document).ready(function() {
                $('#event').select2()
                    .on('change', function(e) {
                        @this.set('event', $(this).val());
                    });
                
                Livewire.on('reset-select2', () => {
                    $('#event').val(null).trigger('change');
                });
            })
        </script>
    @endscript
@endpush
