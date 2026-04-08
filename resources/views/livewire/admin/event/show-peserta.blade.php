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
                        <div class="d-flex gap-2">
                            @if ($event->metode_tes_id == 1)
                            <button wire:click="downloadPortofolio" class="btn btn-sm btn-outline-danger d-flex align-items-center gap-2" style="border-radius: 6px; padding: 6px 14px; font-size: 0.875rem;" wire:loading.attr="disabled" wire:target="downloadPortofolio">
                                <i class="link-icon" data-feather="download" style="width: 16px; height: 16px;"></i>
                                <span class="fw-semibold" wire:ignore>Download Portofolio</span>
                            </button>
                            @endif
                            <button wire:click="downloadTemplate" class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-2" style="border-radius: 6px; padding: 6px 14px; font-size: 0.875rem;" wire:loading.attr="disabled" wire:target="downloadTemplate">
                                <i class="link-icon" data-feather="download" style="width: 16px; height: 16px;"></i>
                                <span class="fw-semibold" wire:ignore>Template</span>
                            </button>
                            <button wire:click="openImportModal" class="btn btn-sm btn-outline-success d-flex align-items-center gap-2" style="border-radius: 6px; padding: 6px 14px; font-size: 0.875rem;">
                                <i class="link-icon" data-feather="upload" style="width: 16px; height: 16px;"></i>
                                <span class="fw-semibold" wire:ignore>Import</span>
                            </button>
                            <x-modal.btn-add text="Tambah Peserta" icon="plus-circle" action="openCreateModal" />
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
                        <table class="table table-hover align-middle shadow-sm border rounded" style="overflow:hidden;">
                            <thead class="table-light border-bottom">
                                <tr>
                                    <th class="text-center" style="width: 45px;">#</th>
                                    <th>Nama Peserta</th>
                                    <th>Jenis Peserta</th>
                                    <th>Jabatan</th>
                                    <th>Unit Kerja <br><small class="text-muted">Instansi</small></th>
                                    @if ($event->metode_tes_id == 1)
                                        <th>Portofolio</th>
                                    @endif
                                    <th>Status</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($data as $index => $item)
                                    <tr class="@if($loop->iteration % 2 == 1) bg-body @endif border-bottom">
                                        <td class="text-center text-secondary fw-bold">{{ $data->firstItem() + $index }}</td>
                                        <td>
                                            <span class="fw-medium">{{ $item->nama }}</span><br>
                                            <span class="text-muted small">
                                            @if ($item->jenis_peserta_id == 1)
                                                <div class="fw-medium">{{ $item->nip }}</div>
                                                @if (!empty($item->golPangkat?->pangkat) && !empty($item->golPangkat?->golongan))
                                                    <span class="badge bg-secondary-subtle text-dark mt-1">
                                                        {{ $item->golPangkat->pangkat . ' - ' . $item->golPangkat->golongan }}
                                                    </span>
                                                @else
                                                    <span class="text-muted d-block mt-1"></span>
                                                @endif
                                            @elseif ($item->jenis_peserta_id == 2)
                                                <div class="fw-medium">{{ $item->nik }}</div>
                                            @endif
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge {{ $item->jenis_peserta_id == 1 ? 'bg-primary' : 'bg-info' }}">
                                                {{ $item->jenisPeserta->jenis_peserta ?? '-' }}
                                            </span>
                                        </td>
                                        <td class="text-wrap">
                                            {{ $item->jabatan }}
                                        </td>
                                        <td>
                                            <span class="fw-medium text-dark">{{ $item->unit_kerja ?? '-' }}</span>
                                            <br>
                                            <span class="text-muted small">{{ $item->instansi ?? '-' }}</span>
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
                                                    ✖ Non Aktif
                                                </span>
                                            @else
                                                <span
                                                    class="badge bg-success"
                                                    wire:click="changeStatusPesertaConfirmation('{{ $item->id }}')"
                                                    style="cursor: pointer;"
                                                    data-bs-toggle="tooltip" data-bs-placement="top" title="Klik untuk nonaktifkan"
                                                >
                                                    ✔ Aktif
                                                </span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if ($event->metode_tes_id == 1)
                                            <button
                                                type="button"
                                                wire:click="downloadPortofolioPeserta('{{ $item->id }}')"
                                                class="btn btn-sm btn-outline-primary btn-icon rounded-circle border-0 shadow-sm me-1"
                                                wire:loading.attr="disabled"
                                                wire:target="downloadPortofolioPeserta"
                                                data-bs-toggle="tooltip"
                                                data-bs-placement="top"
                                                title="PDF portofolio"
                                            >
                                                <span wire:ignore><i class="link-icon" data-feather="file-text"></i></span>
                                            </button>
                                            @endif
                                            <x-table.btn-edit :id="$item->id" action="openEditModal" />
                                            <button
                                                wire:click="deleteConfirmation('{{ $item->id }}')"
                                                class="btn btn-sm btn-outline-danger btn-icon rounded-circle border-0 shadow-sm" style="transition: background 0.2s;"
                                                @disabled($item->test_started_at != null)
                                                data-bs-toggle="tooltip" data-bs-placement="top" title="Hapus"
                                            >
                                                <span wire:ignore><i class="link-icon" data-feather="trash"></i></span>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center text-muted py-4">
                                            <i class="link-icon" data-feather="inbox" style="font-size: 24px; opacity: 0.7;"></i>
                                            <div class="mt-2 fw-semibold">Tidak ada data peserta...</div>
                                        </td>
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
    @if($showModal)
    <div class="modal fade show" style="display: block; background: rgba(0,0,0,0.5);" tabindex="-1" 
         wire:key="modal-{{ $isUpdate ? 'edit-'.$selected_id : 'create' }}"
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
                                {{ $isUpdate ? 'Edit Peserta' : 'Tambah Peserta Baru' }}
                            </h5>
                            <p class="text-white-50 mb-0 mt-1" style="font-size: 0.875rem;">
                                {{ $isUpdate ? 'Perbarui informasi peserta' : 'Isi form untuk menambahkan peserta' }}
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
                                        Jenis Peserta <span class="text-danger">*</span>
                                    </label>
                                    <select wire:model.live="jenis_peserta_id" class="form-select @error('jenis_peserta_id') is-invalid @enderror">
                                        <option value="">- Pilih -</option>
                                        @foreach ($option_jenis_peserta as $key => $item)
                                            <option value="{{ $key }}">{{ $item }}</option>
                                        @endforeach
                                    </select>
                                    @error('jenis_peserta_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <x-form.input
                            label="Nama Peserta"
                            icon="user"
                            model="nama"
                            placeholder="Contoh: Dr. BUDI SANTOSO, S.H., M.H."
                            :required="true"
                        />
                        <small class="text-muted d-block mb-3" style="margin-top: -10px;">
                            <i class="link-icon" data-feather="info" style="width: 12px; height: 12px;"></i>
                            Gelar akan terdeteksi otomatis. Contoh: "Dr. BUDI SANTOSO, S.H., M.H." atau "BUDI SANTOSO" (tanpa gelar)
                        </small>

                        @if ($jenis_peserta_id == 1)
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
                        </div>
                        <x-form.input
                            label="Jabatan"
                            icon="briefcase"
                            model="jabatan"
                            placeholder="Masukkan jabatan"
                            :required="true"
                        />
                        @endif

                        @if ($jenis_peserta_id == 2)
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
                            label="Unit Kerja"
                            icon="home"
                            model="unit_kerja"
                            placeholder="Masukkan unit kerja"
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
    @endif

    <!-- Modal Import Peserta -->
    @if($showImportModal)
    <div class="modal fade show" style="display: block; background: rgba(0,0,0,0.5);" tabindex="-1" 
         wire:key="modal-import"
         x-data="{ init() { setTimeout(() => { if (typeof feather !== 'undefined') feather.replace(); }, 50); } }"
         x-init="init()">
        <div class="modal-dialog modal-dialog-centered" style="animation: slideDown 0.3s ease-out;">
            <div class="modal-content" style="border: none; border-radius: 16px; overflow: hidden; box-shadow: 0 20px 60px rgba(0,0,0,0.3);">
                <!-- Modal Header -->
                <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; padding: 24px 32px;">
                    <div class="d-flex align-items-center gap-3">
                        <div class="d-flex align-items-center justify-content-center" 
                             style="width: 48px; height: 48px; background: rgba(255,255,255,0.2); border-radius: 12px; backdrop-filter: blur(10px);">
                            <i class="link-icon text-white" data-feather="upload" style="width: 24px; height: 24px;"></i>
                        </div>
                        <div>
                            <h5 class="modal-title text-white fw-bold mb-0" style="font-size: 1.5rem;">
                                Import Peserta
                            </h5>
                            <p class="text-white-50 mb-0 mt-1" style="font-size: 0.875rem;">
                                Upload file excel untuk import data peserta
                            </p>
                        </div>
                    </div>
                    <button type="button" wire:click="closeImportModal" class="btn-close btn-close-white" 
                            style="filter: brightness(0) invert(1); opacity: 0.8; transition: opacity 0.2s;"
                            onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0.8'"></button>
                </div>

                <!-- Modal Body -->
                <div class="modal-body" style="padding: 32px; background: #f8f9fa;">
                    <form wire:submit="importPeserta">
                        <div class="alert alert-warning" style="border-radius: 10px; border: none;">
                            <small>
                                <strong><i class="link-icon me-1" data-feather="warning" style="width: 14px; height: 14px;"></i> Petunjuk:</strong>
                                <ol class="mb-0 ps-3 mt-1">
                                    <li>Download template terlebih dahulu</li>
                                    <li>Isi data sesuai format template</li>
                                    <li>Nama bisa ditulis dengan gelar (contoh: "Dr. BUDI SANTOSO, S.H., M.H.")</li>
                                    <li>Gelar depan dan belakang akan terdeteksi otomatis</li>
                                    <li>Upload file yang sudah diisi</li>
                                </ol>
                            </small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                <i class="link-icon me-2" data-feather="file"></i>
                                File Excel (.xlsx, .xls)
                            </label>
                            <input type="file" wire:model="file_import" class="form-control @error('file_import') is-invalid @enderror" accept=".xlsx,.xls">
                            @error('file_import')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div wire:loading wire:target="file_import" class="text-muted mt-1">
                                <small>Mengupload file...</small>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Modal Footer -->
                <div class="modal-footer" style="background: white; border-top: 2px solid #f0f0f0; padding: 20px 32px; gap: 12px;">
                    <x-modal.btn-cancel action="closeImportModal" />
                    <button 
                        type="button" 
                        wire:click="importPeserta"
                        class="btn btn-primary d-flex align-items-center gap-2" 
                        style="padding: 10px 24px; border-radius: 10px; font-weight: 600; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3); transition: all 0.2s ease;"
                        onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 16px rgba(102, 126, 234, 0.4)'"
                        onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(102, 126, 234, 0.3)'"
                        wire:loading.attr="disabled" wire:target="importPeserta,file_import"
                    >
                        <i class="link-icon" data-feather="upload" style="width: 18px; height: 18px;"></i>
                        <span wire:loading.remove wire:target="importPeserta">Import</span>
                        <span wire:loading wire:target="importPeserta">Mengimport...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Modal Import Errors -->
    <div class="modal fade" id="modalImportErrors" tabindex="-1" aria-labelledby="modalImportErrorsLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content" style="border: none; border-radius: 16px; overflow: hidden; box-shadow: 0 20px 60px rgba(0,0,0,0.3);">
                <div class="modal-header" style="background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%); border: none; padding: 24px 32px;">
                    <div class="d-flex align-items-center gap-3">
                        <div class="d-flex align-items-center justify-content-center" 
                             style="width: 48px; height: 48px; background: rgba(255,255,255,0.2); border-radius: 12px; backdrop-filter: blur(10px);">
                            <i class="link-icon text-white" data-feather="alert-circle" style="width: 24px; height: 24px;"></i>
                        </div>
                        <div>
                            <h5 class="modal-title text-white fw-bold mb-0" style="font-size: 1.5rem;">
                                Detail Error Import
                            </h5>
                            <p class="text-white-50 mb-0 mt-1" style="font-size: 0.875rem;">
                                Berikut detail data yang gagal diimport
                            </p>
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"
                            style="filter: brightness(0) invert(1); opacity: 0.8; transition: opacity 0.2s;"
                            onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0.8'"></button>
                </div>
                <div class="modal-body" style="padding: 32px; background: #f8f9fa; max-height: 70vh; overflow-y: auto;">
                    <!-- Summary Card -->
                    <div class="card mb-3" style="border-radius: 12px; border: none; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                        <div class="card-body">
                            <h6 class="card-title fw-bold">Ringkasan Import</h6>
                            <div class="row" id="importSummary">
                                <div class="col-md-3">
                                    <div class="text-center p-3 border rounded bg-success-subtle" style="border-radius: 10px !important;">
                                        <h4 class="text-success mb-1" id="successCount">0</h4>
                                        <small class="text-muted">Berhasil</small>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="text-center p-3 border rounded bg-danger-subtle" style="border-radius: 10px !important;">
                                        <h4 class="text-danger mb-1" id="failedCount">0</h4>
                                        <small class="text-muted">Gagal</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="p-2 border rounded bg-light" style="border-radius: 10px !important;">
                                        <small><strong>Kategori Error:</strong></small>
                                        <ul class="list-unstyled mb-0 mt-2" id="errorCategories" style="font-size: 0.875rem;">
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Error Details -->
                    <div class="card" style="border-radius: 12px; border: none; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                        <div class="card-header" style="border-radius: 12px 12px 0 0;">
                            <h6 class="mb-0 fw-bold">Detail Error per Baris</h6>
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
                <div class="modal-footer" style="background: white; border-top: 2px solid #f0f0f0; padding: 20px 32px;">
                    <button type="button" class="btn btn-light d-flex align-items-center gap-2" data-bs-dismiss="modal"
                            style="padding: 10px 24px; border-radius: 10px; font-weight: 600; border: 2px solid #e0e0e0;">
                        <i class="link-icon" data-feather="x" style="width: 18px; height: 18px;"></i>
                        Tutup
                    </button>
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

@push('js')
    @script()
        <script>
            let modalErrors = null;

            // Initialize modal errors (masih pakai Bootstrap JS karena dikelola JS)
            document.addEventListener('DOMContentLoaded', function() {
                const modalErrorsEl = document.getElementById('modalImportErrors');
                if (modalErrorsEl) modalErrors = new bootstrap.Modal(modalErrorsEl);
            });

            $(document).ready(function() {
                if (!modalErrors) {
                    const modalErrorsEl = document.getElementById('modalImportErrors');
                    if (modalErrorsEl && !modalErrors) modalErrors = new bootstrap.Modal(modalErrorsEl);
                }
            });

            // Function untuk menampilkan error import dengan detail
            window.showImportErrors = function(data) {
                const { errors, summary, imported, failed } = data;
                
                const updateModalContent = function() {
                    const successCountEl = document.getElementById('successCount');
                    const failedCountEl = document.getElementById('failedCount');
                    
                    if (successCountEl) successCountEl.textContent = imported || 0;
                    if (failedCountEl) failedCountEl.textContent = failed || 0;
                    
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
                            }
                        });
                        
                        if (!hasCategories) {
                            categoriesEl.innerHTML = '<li>Tidak ada kategori error</li>';
                        }
                    }
                    
                    const errorList = document.getElementById('importErrorList');
                    
                    if (errorList && errors && errors.length > 0) {
                        errorList.innerHTML = '';
                        
                        errors.forEach((error, index) => {
                            const tr = document.createElement('tr');
                            
                            const match = error.match(/Baris (\d+): (.+)/);
                            if (match) {
                                const barisNum = match[1];
                                const errorMsg = match[2];
                                
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

                    // Re-init feather icons in modal
                    setTimeout(() => { if (typeof feather !== 'undefined') feather.replace(); }, 50);
                };
                
                if (modalErrors) {
                    const modalEl = document.getElementById('modalImportErrors');
                    updateModalContent();
                    
                    modalEl.addEventListener('shown.bs.modal', function handler() {
                        updateModalContent();
                        modalEl.removeEventListener('shown.bs.modal', handler);
                    });
                    
                    modalErrors.show();
                }
            };

            $wire.on('show-import-errors', (event) => {
                const data = Array.isArray(event) ? event[0] : event;
                setTimeout(() => {
                    window.showImportErrors(data);
                }, 100);
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
