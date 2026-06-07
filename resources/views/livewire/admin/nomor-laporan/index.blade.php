<div>
    <x-breadcrumb :breadcrumbs="[
        ['url' => route('admin.dashboard'), 'title' => 'Dashboard'],
        ['url' => null, 'title' => 'Nomor Laporan Penilaian']
    ]" />
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <x-modal.btn-add text="Tambah Nomor Laporan Penilaian" icon="plus-circle" />
                    <div class="card mt-4 mb-4 bg-light-subtle">
                        <div class="card-body">
                            <h6 class="text-danger" wire:ignore><i class="link-icon" data-feather="filter"></i> Filter</h6>
                            <div class="row mt-2">
                                <div class="col-sm-3">
                                    <div class="input-group" wire:ignore>
                                        <span class="input-group-text bg-white"><i data-feather="search"></i></span>
                                        <input wire:model.live.debounce="search" class="form-control" placeholder="cari event...">
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="input-group flatpickr" id="flatpickr-date">
                                        <input type="text" wire:model.live="tanggal"
                                            class="form-control flatpickr-input" placeholder="tanggal laporan"
                                            data-input="" readonly="readonly">
                                        <span class="input-group-text input-group-addon" data-toggle="">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round"
                                                class="feather feather-calendar">
                                                <rect x="3" y="4" width="18" height="18" rx="2"
                                                    ry="2"></rect>
                                                <line x1="16" y1="2" x2="16" y2="6"></line>
                                                <line x1="8" y1="2" x2="8" y2="6"></line>
                                                <line x1="3" y1="10" x2="21" y2="10"></line>
                                            </svg>
                                        </span>
                                    </div>
                                </div>
                                <div class="col-sm-5">
                                    <div wire:ignore>
                                        <select wire:model.live="event_id" id="event" class="form-select">
                                            <option value="">pilih event</option>
                                            @foreach ($options_event as $key => $item)
                                                <option value="{{ $key}}">{{ $item }}</option>
                                            @endforeach
                                        </select>
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
                                    <th>Event</th>
                                    <th>Metode Tes</th>
                                    <th>Nomor Laporan</th>
                                    <th>Tanggal Laporan</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $index => $item)
                                    <tr class="@if($loop->iteration % 2 == 1) bg-body @endif border-bottom">
                                        <td class="text-center text-secondary fw-bold">{{ $data->firstItem() + $index }}</td>
                                        <td class="text-wrap">{{ $item->event?->nama_event }}</td>
                                        <td>{{ $item->event?->metodeTes?->metode_tes }}</td>
                                        <td>{{ $item->nomor }}</td>
                                        <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d F Y') }}</td>
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
                                            <div class="mt-2 fw-semibold">Tidak ada data nomor laporan...</div>
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
                                {{ $isUpdate ? 'Edit Nomor Laporan Penilaian' : 'Tambah Nomor Laporan Penilaian Baru' }}
                            </h5>
                            <p class="text-white-50 mb-0 mt-1" style="font-size: 0.875rem;">
                                {{ $isUpdate ? 'Perbarui data nomor laporan penilaian' : 'Isi form untuk menambahkan nomor laporan penilaian' }}
                            </p>
                        </div>
                    </div>
                    <button type="button" wire:click="closeModal" class="btn-close btn-close-white" 
                            style="filter: brightness(0) invert(1); opacity: 0.8; transition: opacity 0.2s;"
                            onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0.8'"></button>
                </div>

                <!-- Modal Body -->
                <div class="modal-body" style="padding: 32px; background: #f8f9fa;">
                    <form wire:submit="save">
                        <x-form.select2
                            label="Event"
                            icon="calendar"
                            model="event_id_modal"
                            placeholder="pilih event"
                            :required="true"
                        >
                            @foreach ($options_event as $key => $item)
                                <option value="{{ $key }}">{{ $item }}</option>
                            @endforeach
                        </x-form.select2>

                        <x-form.input
                            label="Nomor Laporan Penilaian"
                            icon="file-text"
                            model="nomor"
                            placeholder="Masukkan nomor laporan penilaian"
                            :required="true"
                        />

                        <x-form.date
                            label="Tanggal Laporan Penilaian"
                            icon="calendar"
                            model="tanggal_modal"
                            placeholder="pilih tanggal"
                            :required="true"
                        />
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
@push('js')
    @script()
        <script>
            $(document).ready(function() {
                // Select2 untuk filter event
                $('#event').select2()
                    .on('change', function(e) {
                        @this.set('event_id', $(this).val());
                    });
                
                Livewire.on('reset-select2', () => {
                    $('#event').val(null).trigger('change');
                });
            })
        </script>
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
    @endscript
@endpush
