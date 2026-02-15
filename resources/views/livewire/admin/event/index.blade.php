<div>
    <x-breadcrumb :breadcrumbs="[
        ['url' => route('admin.dashboard'), 'title' => 'Dashboard'],
        ['url' => null, 'title' => 'Event']
    ]" />
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        <div>
                            <x-modal.btn-add text="Tambah Event" icon="plus-circle" />
                        </div>
                        <div class="d-flex align-items-center flex-wrap text-nowrap gap-2">
                            <div class="badge bg-info-subtle text-dark">
                                <span wire:ignore><i class="link-icon" data-feather="info"></i></span>
                                Total Event: {{ $stats->total }}
                            </div>
                            <div class="badge bg-success-subtle text-dark">
                                <span wire:ignore><i class="link-icon" data-feather="play"></i></span>
                                Event Berlangsung: {{ $stats->berlangsung }}
                            </div>
                            <div class="badge bg-danger-subtle text-dark">
                                <span wire:ignore><i class="link-icon" data-feather="check"></i></span>
                                Event Selesai: {{ $stats->selesai }}
                            </div>
                        </div>
                    </div>
                    <div class="card mt-4 mb-4 bg-light-subtle">
                        <div class="card-body">
                            <h6 class="text-danger" wire:ignore><i class="link-icon" data-feather="filter"></i> Filter</h6>
                            <div class="row mt-2">
                                <div class="col-sm-3">
                                    <select wire:model.live="jabatan_diuji" class="form-select" id="jabatan-diuji">
                                        <option value="">jenis jabatan diujikan</option>
                                        @foreach ($option_jabatan_diuji as $key => $item)
                                            <option value="{{ $key }}">{{ $item }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-2">
                                    <div class="input-group flatpickr" id="flatpickr-date">
                                        <input type="text" wire:model.live="tgl_mulai"
                                            class="form-control flatpickr-input" placeholder="tgl pelaksanaan"
                                            data-input="" readonly="readonly">
                                        <span class="input-group-text input-group-addon" data-toggle="">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-calendar"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                                        </span>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="input-group" wire:ignore>
                                        <span class="input-group-text bg-white"><i data-feather="search"></i></span>
                                        <input wire:model.live.debounce="search" class="form-control" placeholder="cari event...">
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <x-btn-reset :text="'Reset'" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div wire:key="events-table" wire:ignore.self class="table-responsive">
                        <table class="table table-hover align-middle shadow-sm border rounded" style="overflow:hidden;">
                            <thead class="table-light border-bottom">
                                <tr>
                                    <th class="text-center" style="width: 45px;">#</th>
                                    <th>Nama Event</th>
                                    <th>Tgl Pelaksanaan</th>
                                    <th>Jabatan yg Diujikan<br><small class="text-muted">Metode Tes</small></th>
                                    <th>Jumlah Peserta</th>
                                    <th>Peserta Terinput</th>
                                    <th>Assessor</th>
                                    <th>Portofolio</th>
                                    <th>Status Event</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $index => $item)
                                    <tr class="@if($loop->iteration % 2 == 1) bg-body @endif border-bottom">
                                        <td class="text-center text-secondary fw-bold">{{ $data->firstItem() + $index }}</td>
                                        <td class="text-wrap fw-medium">{{ $item->nama_event }}</td>
                                        <td class="text-wrap">
                                            @if ($item->tgl_mulai == $item->tgl_selesai)
                                                {{ $item->tgl_mulai }}
                                            @else
                                                {{ $item->tgl_mulai . ' s/d ' . $item->tgl_selesai }}
                                            @endif
                                        </td>
                                        <td>
                                            {{ $item->jabatanDiuji->jenis ?? '' }} <br /> 
                                            <span class="badge bg-dark">{{ $item->metodeTes->metode_tes }}</span>
                                        </td>
                                        <td>{{ $item->jumlah_peserta }}</td>
                                        <td>
                                            <a class="btn btn-xs btn-warning" wire:navigate
                                                href="{{ route('admin.event.show-peserta', ['idEvent' => $item->id]) }}">
                                                    {{ $item->peserta_count }} orang
                                            </a>
                                        </td>
                                        @if ($item->metode_tes_id == 1)
                                        <td>
                                            <a class="btn btn-xs btn-primary" wire:navigate
                                                href="{{ route('admin.event.show-assessor', ['idEvent' => $item->id]) }}">
                                                {{ $item->assessor_count }} orang
                                            </a>
                                        </td>
                                        @else
                                        <td><span class="badge bg-secondary">Tidak ada assessor</span></td>
                                        @endif
                                        <td>
                                            @if ($item->metode_tes_id == 1)
                                                @if ($item->is_open == 'false')
                                                    <span class="badge bg-danger" wire:click="changeStatusPortofolioConfirmation('{{ $item->id }}')" style="cursor: pointer;">
                                                        ✖ Tutup
                                                    </span>
                                                @else
                                                    <span class="badge bg-success" wire:click="changeStatusPortofolioConfirmation('{{ $item->id }}')" style="cursor: pointer;">
                                                        ✔ Buka
                                                    </span>
                                                @endif
                                            @else
                                            <span class="badge bg-secondary">Tidak ada portofolio</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($item->is_finished == 'true')
                                                <span class="badge bg-danger" wire:click="changeStatusEventConfirmation('{{ $item->id }}')" style="cursor: pointer;">
                                                    ✖ Selesai
                                                </span>
                                            @else
                                                <span class="badge bg-success" wire:click="changeStatusEventConfirmation('{{ $item->id }}')" style="cursor: pointer;">
                                                    ✔ Berlangsung
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <x-table.btn-edit :id="$item->id" />
                                            <x-table.btn-delete :id="$item->id" :disabled="($item->is_finished == 'true')" />
                                        </td>
                                    </tr>
                                @endforeach

                                @if($data->count() === 0)
                                    <tr>
                                        <td colspan="10" class="text-center text-muted py-4">
                                            <i class="link-icon" data-feather="inbox" style="font-size: 24px; opacity: 0.7;"></i>
                                            <div class="mt-2 fw-semibold">Tidak ada data event...</div>
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

    <!-- Modal Form Event -->
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
                                {{ $isUpdate ? 'Edit Event' : 'Tambah Event Baru' }}
                            </h5>
                            <p class="text-white-50 mb-0 mt-1" style="font-size: 0.875rem;">
                                {{ $isUpdate ? 'Perbarui informasi event' : 'Isi form untuk menambahkan event' }}
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
                        <x-form.input
                            label="Nama Event"
                            icon="calendar"
                            model="nama_event"
                            placeholder="Masukkan nama event"
                            :required="true"
                        />

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">
                                        <i class="link-icon me-2" data-feather="clipboard"></i>
                                        Metode Tes <span class="text-danger">*</span>
                                    </label>
                                    <select wire:model.live="metode_tes_id" class="form-select @error('metode_tes_id') is-invalid @enderror">
                                        <option value="">- Pilih -</option>
                                        @foreach ($option_metode_tes as $key => $item)
                                            <option value="{{ $key }}">{{ $item }}</option>
                                        @endforeach
                                    </select>
                                    @error('metode_tes_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">
                                        <i class="link-icon me-2" data-feather="briefcase"></i>
                                        Jenis Jabatan yang Diujikan <span class="text-danger">*</span>
                                    </label>
                                    <select wire:model="jabatan_diuji_id" class="form-select @error('jabatan_diuji_id') is-invalid @enderror">
                                        <option value="">- Pilih -</option>
                                        @foreach ($option_jabatan_diuji as $key => $item)
                                            <option value="{{ $key }}">{{ $item }}</option>
                                        @endforeach
                                    </select>
                                    @error('jabatan_diuji_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        @if($metode_tes_id == 1)
                        <x-form.select2
                            label="Assessor"
                            icon="users"
                            model="assessor"
                            placeholder="Pilih assessor"
                            multiple
                        >
                            @foreach ($option_assessor as $key => $item)
                                <option value="{{ $key }}">{{ $item }}</option>
                            @endforeach
                        </x-form.select2>
                        @endif

                        <div class="row">
                            <div class="col-md-4">
                                <x-form.date
                                    label="Tanggal Mulai"
                                    icon="calendar"
                                    model="form_tgl_mulai"
                                    placeholder="Pilih tanggal mulai"
                                    :required="true"
                                />
                            </div>
                            <div class="col-md-4">
                                <x-form.date
                                    label="Tanggal Selesai"
                                    icon="calendar"
                                    model="form_tgl_selesai"
                                    placeholder="Pilih tanggal selesai"
                                    :required="true"
                                />
                            </div>
                            <div class="col-md-4">
                                <x-form.input
                                    label="Jumlah Peserta"
                                    icon="users"
                                    model="jumlah_peserta"
                                    type="number"
                                    placeholder="Jumlah peserta"
                                    :required="true"
                                />
                            </div>
                        </div>

                        <x-form.input
                            label="PIN Ujian"
                            icon="key"
                            model="pin_ujian"
                            placeholder="Masukkan PIN ujian (4 karakter)"
                            :required="true"
                        />

                        @if($metode_tes_id == 1)
                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                <i class="link-icon me-2" data-feather="unlock"></i>
                                Pengisian Portofolio Peserta <span class="text-danger">*</span>
                            </label>
                            <div>
                                <div class="form-check form-check-inline">
                                    <input type="radio" class="form-check-input @error('is_open') is-invalid @enderror" wire:model="is_open" id="portoBuka" value="true">
                                    <label class="form-check-label" for="portoBuka">Buka</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input type="radio" class="form-check-input @error('is_open') is-invalid @enderror" wire:model="is_open" id="portoTutup" value="false">
                                    <label class="form-check-label" for="portoTutup">Tutup</label>
                                </div>
                            </div>
                            @error('is_open')
                            <div class="text-danger mt-1" style="font-size: 0.875rem;">{{ $message }}</div>
                            @enderror
                        </div>
                        @endif

                        @if ($isUpdate)
                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                <i class="link-icon me-2" data-feather="toggle-left"></i>
                                Status Event
                            </label>
                            <div>
                                <div class="form-check form-check-inline">
                                    <input type="radio" class="form-check-input" wire:model="is_finished" id="eventBerlangsung" value="false">
                                    <label class="form-check-label" for="eventBerlangsung">Berlangsung</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input type="radio" class="form-check-input" wire:model="is_finished" id="eventSelesai" value="true">
                                    <label class="form-check-label" for="eventSelesai">Selesai</label>
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
            // Initialize flatpickr untuk modal (saat modal muncul)
            Livewire.on('modalOpened', () => {
                setTimeout(() => {
                    document.querySelectorAll('[data-flatpickr]').forEach(el => {
                        if (!el._flatpickr) {
                            flatpickr(el, {
                                dateFormat: 'd-m-Y',
                                onChange: function(selectedDates, dateStr) {
                                    const model = el.getAttribute('data-model');
                                    if (model) {
                                        @this.set(model, dateStr);
                                    }
                                }
                            });
                        }
                    });
                }, 200);
            });
        </script>
    @endscript
@endpush
