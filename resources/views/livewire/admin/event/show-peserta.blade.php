<div>
    <x-breadcrumb :breadcrumbs="[
        ['url' => route('admin.dashboard'), 'title' => 'Dashboard'],
        ['url' => route('admin.event'), 'title' => 'Event'],
        ['url' => null, 'title' => 'Peserta']
    ]" />
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="card-title mb-0">Data Peserta Event: <span class="badge bg-warning text-dark"> {{ $event->nama_event }}</span></h6>
                        <div>
                            <button wire:click="downloadTemplate" class="btn btn-sm btn-outline-secondary btn-icon-text" wire:loading.attr="disabled" wire:target="downloadTemplate">
                                <span wire:ignore><i class="btn-icon-prepend" data-feather="download"></i></span> Template
                            </button>
                            <button wire:click="openImportModal" class="btn btn-sm btn-outline-success btn-icon-text">
                                <span wire:ignore><i class="btn-icon-prepend" data-feather="upload"></i></span> Import
                            </button>
                            <button wire:click="openCreateModal" class="btn btn-sm btn-outline-primary btn-icon-text">
                                <span wire:ignore><i class="btn-icon-prepend" data-feather="edit"></i></span> Tambah Peserta
                            </button>
                        </div>
                    </div>

                    <div class="card mt-3 mb-4 bg-light-subtle">
                        <div class="card-body">
                            <h6 class="text-danger" wire:ignore><i class="link-icon" data-feather="filter"></i> Filter</h6>
                            <div class="row mt-2">
                                <div class="col-sm-3">
                                    <select wire:model.live="filter_jenis_peserta" class="form-select form-select-sm">
                                        <option value="">semua jenis peserta</option>
                                        @foreach ($option_jenis_peserta as $key => $item)
                                            <option value="{{ $key }}">{{ $item }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @if ($event->metode_tes_id == 1)
                                <div class="col-sm-2">
                                    <select wire:model.live="is_portofolio_completed" class="form-select form-select-sm">
                                        <option value="">portofolio</option>
                                        <option value="true">Sudah Lengkap</option>
                                        <option value="false">Belum Lengkap</option>
                                    </select>
                                </div>
                                @endif
                                <div class="col-sm-2">
                                    <select wire:model.live="is_active" class="form-select" id="status">
                                        <option value="">semua status</option>
                                        @foreach ($option_status as $key => $item)
                                            <option value="{{ $key }}">{{  $item }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-4">
                                    <div class="input-group input-group-sm" wire:ignore>
                                        <span class="input-group-text bg-white"><i data-feather="search"></i></span>
                                        <input wire:model.live.debounce="search" class="form-control" placeholder="cari nama/nip/nik/jabatan...">
                                    </div>
                                </div>
                                <div class="col-sm-1">
                                    <x-btn-reset :text="'Reset'" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Nama Peserta</th>
                                    <th>Jenis</th>
                                    <th>NIK / NIP</th>
                                    <th>Jabatan</th>
                                    <th>Unit Kerja / Instansi</th>
                                    @if ($event->metode_tes_id == 1)
                                        <th>Portofolio</th>
                                    @endif
                                    <th>Status</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($data as $index => $item)
                                    <tr>
                                        <td>{{ $data->firstItem() + $index }}</td>
                                        <td class="text-wrap">{{ $item->nama }}</td>
                                        <td>
                                            <span class="badge {{ $item->jenis_peserta_id == 1 ? 'bg-primary' : 'bg-info' }}">
                                                {{ $item->jenisPeserta->jenis_peserta ?? '-' }}
                                            </span>
                                        </td>
                                        <td>
                                            @if ($item->jenis_peserta_id == 1)
                                                {{ $item->nip }} 
                                                @if($item->golPangkat)
                                                    <br/><small class="text-muted">{{ $item->golPangkat->pangkat ?? '' }} - {{ $item->golPangkat->golongan ?? '' }}</small>
                                                @endif
                                            @elseif ($item->jenis_peserta_id == 2)
                                                {{ $item->nik }}
                                            @endif
                                        </td>
                                        <td class="text-wrap">{{ $item->jabatan ?? '-' }}</td>
                                        <td class="text-wrap">
                                            {{ $item->unit_kerja ?? '-' }} 
                                            <br/><small class="text-muted">{{ $item->instansi ?? '-' }}</small>
                                        </td>
                                        @if ($event->metode_tes_id == 1)
                                        <td>
                                            @if($item->is_portofolio_lengkap)
                                                <span data-bs-toggle="tooltip" data-bs-placement="top" title="Lengkap" class="text-success">✔</span>
                                                @else
                                                    <span data-bs-toggle="tooltip" data-bs-placement="top" title="Belum Lengkap" class="text-danger">✖</span>
                                                @endif
                                            </td>
                                        @endif
                                        <td>
                                            @if ($item->is_active == 'false')
                                                <span
                                                    class="badge bg-danger"
                                                    wire:click="changeStatusPesertaConfirmation('{{ $item->id }}')"
                                                    style="cursor: pointer;"
                                                    data-bs-toggle="tooltip" data-bs-placement="top" title="Klik untuk aktifkan"
                                                >
                                                    Non Aktif
                                                </span>
                                            @else
                                                <span
                                                    class="badge bg-success"
                                                    wire:click="changeStatusPesertaConfirmation('{{ $item->id }}')"
                                                    style="cursor: pointer;"
                                                    data-bs-toggle="tooltip" data-bs-placement="top" title="Klik untuk nonaktifkan"
                                                >
                                                    Aktif
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <button
                                                wire:click="openEditModal('{{ $item->id }}')"
                                                class="btn btn-sm btn-inverse-success btn-icon"
                                                data-bs-toggle="tooltip" data-bs-placement="top" title="Edit"
                                            >
                                                <span wire:ignore><i class="link-icon" data-feather="edit-3"></i></span>
                                            </button>
                                            <button
                                                wire:click="deleteConfirmation('{{ $item->id }}')"
                                                class="btn btn-sm btn-inverse-danger btn-icon"
                                                @disabled($item->test_started_at != null)
                                                data-bs-toggle="tooltip" data-bs-placement="top" title="Hapus"
                                            >
                                                <span wire:ignore><i class="link-icon" data-feather="trash"></i></span>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center text-muted py-4">Belum ada data peserta</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>        
        </div>
        <x-pagination :items="$data" />
    </div>

    <!-- Modal Form Peserta -->
    <div class="modal fade" id="modalFormPeserta" tabindex="-1" aria-labelledby="modalFormPesertaLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalFormPesertaLabel">{{ $isUpdate ? 'Edit' : 'Tambah' }} Peserta</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form wire:submit="save">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Jenis Peserta <span class="text-danger">*</span></label>
                                    <select wire:model.live="jenis_peserta_id" class="form-select @error('jenis_peserta_id') is-invalid @enderror">
                                        <option value="">- Pilih -</option>
                                        @foreach ($option_jenis_peserta as $key => $item)
                                            <option value="{{ $key }}">{{ $item }}</option>
                                        @endforeach
                                    </select>
                                    @error('jenis_peserta_id')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">Nama Peserta (tanpa gelar) <span class="text-danger">*</span></label>
                                    <input type="text" wire:model="nama" class="form-control @error('nama') is-invalid @enderror" placeholder="Masukkan nama peserta">
                                    @error('nama')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        @if ($jenis_peserta_id == 1)
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">NIP <span class="text-danger">*</span></label>
                                    <input type="text" wire:model="nip" class="form-control @error('nip') is-invalid @enderror" placeholder="18 digit">
                                    @error('nip')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">Jabatan <span class="text-danger">*</span></label>
                                    <input type="text" wire:model="jabatan" class="form-control @error('jabatan') is-invalid @enderror" placeholder="Masukkan jabatan">
                                    @error('jabatan')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        @endif

                        @if ($jenis_peserta_id == 2)
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">NIK <span class="text-danger">*</span></label>
                                    <input type="text" wire:model="nik" class="form-control @error('nik') is-invalid @enderror" placeholder="16 digit">
                                    @error('nik')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        @endif

                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">Unit Kerja <span class="text-danger">*</span></label>
                                    <input type="text" wire:model="unit_kerja" class="form-control @error('unit_kerja') is-invalid @enderror" placeholder="Masukkan unit kerja">
                                    @error('unit_kerja')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">Instansi <span class="text-danger">*</span></label>
                                    <input type="text" wire:model="instansi" class="form-control @error('instansi') is-invalid @enderror" placeholder="Masukkan instansi">
                                    @error('instansi')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Password {{ $isUpdate ? '' : '*' }}</label>
                                    <input type="password" wire:model="password" class="form-control @error('password') is-invalid @enderror" placeholder="{{ $isUpdate ? 'Kosongkan jika tidak diubah' : 'Minimal 8 karakter' }}">
                                    @error('password')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        @if ($isUpdate)
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">Status</label>
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
                            </div>
                        </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-inverse-danger btn-icon-text" data-bs-dismiss="modal">
                            <i class="btn-icon-prepend" data-feather="x"></i>
                            Batal
                        </button>
                        <button type="submit" class="btn btn-inverse-success btn-icon-text">
                            <i class="btn-icon-prepend" data-feather="save"></i>
                            <span wire:loading.remove wire:target="save">Simpan</span>
                            <span wire:loading wire:target="save">Menyimpan...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Import Peserta -->
    <div class="modal fade" id="modalImportPeserta" tabindex="-1" aria-labelledby="modalImportPesertaLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalImportPesertaLabel">Import Peserta</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form wire:submit="importPeserta">
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <small>
                                <strong>Petunjuk:</strong>
                                <ol class="mb-0 ps-3">
                                    <li>Download template terlebih dahulu</li>
                                    <li>Isi data sesuai format template</li>
                                    <li>Upload file yang sudah diisi</li>
                                </ol>
                            </small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">File Excel (.xlsx, .xls)</label>
                            <input type="file" wire:model="file_import" class="form-control @error('file_import') is-invalid @enderror" accept=".xlsx,.xls">
                            @error('file_import')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                            <div wire:loading wire:target="file_import" class="text-muted mt-1">
                                <small>Mengupload file...</small>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-inverse-danger btn-icon-text" data-bs-dismiss="modal">
                            <i class="btn-icon-prepend" data-feather="x"></i>
                            Batal
                        </button>
                        <button type="submit" class="btn btn-inverse-success btn-icon-text" wire:loading.attr="disabled" wire:target="importPeserta,file_import">
                            <i class="btn-icon-prepend" data-feather="upload"></i>
                            <span wire:loading.remove wire:target="importPeserta">Import</span>
                            <span wire:loading wire:target="importPeserta">Mengimport...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Import Errors -->
    <div class="modal fade" id="modalImportErrors" tabindex="-1" aria-labelledby="modalImportErrorsLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="modalImportErrorsLabel">
                        <i class="link-icon" data-feather="alert-circle"></i>
                        Detail Error Import
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Summary Card -->
                    <div class="card mb-3">
                        <div class="card-body">
                            <h6 class="card-title">Ringkasan Import</h6>
                            <div class="row" id="importSummary">
                                <div class="col-md-3">
                                    <div class="text-center p-3 border rounded bg-success-subtle">
                                        <h4 class="text-success mb-1" id="successCount">0</h4>
                                        <small class="text-muted">Berhasil</small>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="text-center p-3 border rounded bg-danger-subtle">
                                        <h4 class="text-danger mb-1" id="failedCount">0</h4>
                                        <small class="text-muted">Gagal</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="p-2 border rounded bg-light">
                                        <small><strong>Kategori Error:</strong></small>
                                        <ul class="list-unstyled mb-0 mt-2" id="errorCategories" style="font-size: 0.875rem;">
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Error Details -->
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0">Detail Error per Baris</h6>
                        </div>
                        <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                            <div class="table-responsive">
                                <table class="table table-sm table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 80px;">Baris</th>
                                            <th>Keterangan Error</th>
                                        </tr>
                                    </thead>
                                    <tbody id="importErrorList">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('js')
    @script()
        <script>
            let modalForm = null;
            let modalImport = null;
            let modalErrors = null;

            // Initialize modals when document is ready
            document.addEventListener('DOMContentLoaded', function() {
                const modalFormEl = document.getElementById('modalFormPeserta');
                const modalImportEl = document.getElementById('modalImportPeserta');
                const modalErrorsEl = document.getElementById('modalImportErrors');
                
                console.log('Initializing modals...');
                console.log('modalFormEl:', modalFormEl);
                console.log('modalImportEl:', modalImportEl);
                console.log('modalErrorsEl:', modalErrorsEl);
                
                if (modalFormEl) modalForm = new bootstrap.Modal(modalFormEl);
                if (modalImportEl) modalImport = new bootstrap.Modal(modalImportEl);
                if (modalErrorsEl) modalErrors = new bootstrap.Modal(modalErrorsEl);
                
                console.log('Modals initialized:', { modalForm, modalImport, modalErrors });
            });

            // Fallback dengan jQuery jika ada
            $(document).ready(function() {
                if (!modalForm || !modalImport || !modalErrors) {
                    const modalFormEl = document.getElementById('modalFormPeserta');
                    const modalImportEl = document.getElementById('modalImportPeserta');
                    const modalErrorsEl = document.getElementById('modalImportErrors');
                    
                    if (modalFormEl && !modalForm) modalForm = new bootstrap.Modal(modalFormEl);
                    if (modalImportEl && !modalImport) modalImport = new bootstrap.Modal(modalImportEl);
                    if (modalErrorsEl && !modalErrors) modalErrors = new bootstrap.Modal(modalErrorsEl);
                    
                    console.log('Modals initialized via jQuery:', { modalForm, modalImport, modalErrors });
                }
            });

            // Function untuk menampilkan error import dengan detail
            window.showImportErrors = function(data) {
                console.log('showImportErrors called with data:', data);
                
                const { errors, summary, imported, failed } = data;
                
                console.log('Extracted values - errors:', errors, 'imported:', imported, 'failed:', failed);
                
                // Function untuk update konten modal
                const updateModalContent = function() {
                    console.log('Updating modal content...');
                    
                    // Update summary counts
                    const successCountEl = document.getElementById('successCount');
                    const failedCountEl = document.getElementById('failedCount');
                    
                    if (successCountEl) {
                        successCountEl.textContent = imported || 0;
                        console.log('Updated success count to:', imported);
                    }
                    if (failedCountEl) {
                        failedCountEl.textContent = failed || 0;
                        console.log('Updated failed count to:', failed);
                    }
                    
                    // Update error categories
                    const categoriesEl = document.getElementById('errorCategories');
                    
                    if (categoriesEl) {
                        categoriesEl.innerHTML = '';
                        
                        const categoryLabels = {
                            'duplikat_database': '🔴 Data duplikat (sudah ada di database)',
                            'duplikat_file': '🟠 Data duplikat (dalam file import)',
                            'format_salah': '🟡 Format data salah',
                            'data_kosong': '🔵 Data wajib tidak diisi',
                            'lainnya': '⚪ Error lainnya'
                        };
                        
                        let hasCategories = false;
                        Object.keys(summary).forEach(key => {
                            if (summary[key] > 0) {
                                hasCategories = true;
                                const li = document.createElement('li');
                                li.innerHTML = `${categoryLabels[key]}: <strong>${summary[key]}</strong> baris`;
                                categoriesEl.appendChild(li);
                                console.log('Added category:', key, summary[key]);
                            }
                        });
                        
                        if (!hasCategories) {
                            categoriesEl.innerHTML = '<li>Tidak ada kategori error</li>';
                        }
                        
                        console.log('Categories updated, innerHTML:', categoriesEl.innerHTML);
                    }
                    
                    // Update error list dengan format table
                    const errorList = document.getElementById('importErrorList');
                    console.log('Error list element:', errorList);
                    
                    if (errorList && errors && errors.length > 0) {
                        errorList.innerHTML = '';
                        
                        errors.forEach((error, index) => {
                            console.log('Processing error', index, ':', error);
                            const tr = document.createElement('tr');
                            
                            // Extract baris number dan error message
                            const match = error.match(/Baris (\d+): (.+)/);
                            if (match) {
                                const barisNum = match[1];
                                const errorMsg = match[2];
                                
                                // Determine icon based on error type
                                let icon = '❌';
                                let badgeClass = 'badge bg-danger';
                                
                                if (errorMsg.includes('sudah terdaftar')) {
                                    icon = '🔴';
                                    badgeClass = 'badge bg-danger';
                                } else if (errorMsg.includes('duplikat dalam file')) {
                                    icon = '🟠';
                                    badgeClass = 'badge bg-warning text-dark';
                                } else if (errorMsg.includes('harus') && (errorMsg.includes('digit') || errorMsg.includes('karakter'))) {
                                    icon = '🟡';
                                    badgeClass = 'badge bg-warning text-dark';
                                } else if (errorMsg.includes('harus diisi')) {
                                    icon = '🔵';
                                    badgeClass = 'badge bg-info text-dark';
                                }
                                
                                tr.innerHTML = `
                                    <td><span class="${badgeClass}">${barisNum}</span></td>
                                    <td>
                                        <span style="margin-right: 8px;">${icon}</span>
                                        ${errorMsg}
                                    </td>
                                `;
                            } else {
                                tr.innerHTML = `
                                    <td><span class="badge bg-secondary">-</span></td>
                                    <td>${error}</td>
                                `;
                            }
                            
                            errorList.appendChild(tr);
                        });
                    }
                };
                
                // Show modal dan update content setelah modal shown
                if (modalErrors) {
                    // Get modal element
                    const modalEl = document.getElementById('modalImportErrors');
                    
                    // Update content immediately
                    updateModalContent();
                    
                    // Listen untuk shown.bs.modal event untuk re-update jika perlu
                    modalEl.addEventListener('shown.bs.modal', function handler() {
                        console.log('Modal fully shown, updating content again...');
                        updateModalContent();
                        // Remove listener setelah digunakan
                        modalEl.removeEventListener('shown.bs.modal', handler);
                    });
                    
                    // Show modal
                    modalErrors.show();
                    console.log('Modal show() called');
                } else {
                    console.error('modalErrors is not initialized');
                }
            };

            $wire.on('show-import-errors', (event) => {
                console.log('Event received:', event);
                // Livewire v3 passes data sebagai object dalam array
                const data = Array.isArray(event) ? event[0] : event;
                console.log('Processed data:', data);
                
                // Tunggu sebentar untuk memastikan DOM siap
                setTimeout(() => {
                    window.showImportErrors(data);
                }, 100);
            });

            $wire.on('open-modal-form', () => {
                modalForm.show();
            });

            $wire.on('close-modal-form', () => {
                modalForm.hide();
            });

            $wire.on('open-modal-import', () => {
                modalImport.show();
            });

            $wire.on('close-modal-import', () => {
                modalImport.hide();
            });

            $wire.on('show-delete-confirmation', () => {
                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Data peserta akan dihapus permanen!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $wire.dispatch('delete');
                    }
                });
            });

            $wire.on('change-status-peserta-confirmation', () => {
                Swal.fire({
                    title: 'Ubah Status Peserta?',
                    text: "Status peserta akan diubah",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, ubah!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $wire.dispatch('changeStatusPeserta');
                    }
                });
            });
        </script>
    @endscript
@endpush
