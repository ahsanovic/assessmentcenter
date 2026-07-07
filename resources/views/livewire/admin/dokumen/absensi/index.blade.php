<div>
    <x-breadcrumb :breadcrumbs="[
        ['url' => route('admin.dashboard'), 'title' => 'Dashboard'],
        ['url' => null, 'title' => 'Absensi'],
    ]" />

    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="card mt-0 mb-4 bg-light-subtle">
                        <div class="card-body">
                            <h6 class="text-danger" wire:ignore><i class="link-icon" data-feather="filter"></i> Filter</h6>
                            <div class="row mt-2 g-2 align-items-center">
                                <div class="col-sm-3">
                                    <div class="input-group" wire:ignore>
                                        <span class="input-group-text bg-white"><i data-feather="search"></i></span>
                                        <input wire:model.live.debounce="search" class="form-control" placeholder="cari judul / event...">
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="input-group flatpickr" data-filter-model="tanggal">
                                        <input type="text"
                                            class="form-control flatpickr-input" placeholder="tanggal"
                                            data-input="" readonly="readonly">
                                        <span class="input-group-text input-group-addon" data-toggle="">
                                            <i class="link-icon" data-feather="calendar"></i>
                                        </span>
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
                                    <th>Judul Presensi</th>
                                    <th>Tanggal</th>
                                    <th>Sesi</th>
                                    <th>No. Peserta</th>
                                    <th>Tempat</th>
                                    <th>Dibuat Oleh</th>
                                    <th class="text-center" style="width: 140px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $index => $item)
                                    <tr class="@if($loop->iteration % 2 == 1) bg-body @endif border-bottom">
                                        <td class="text-center text-secondary fw-bold">{{ $data->firstItem() + $index }}</td>
                                        <td class="text-wrap">
                                            <div class="fw-semibold">{{ $item->event?->nama_event }}</div>
                                            <small class="text-muted">{{ $item->event?->metodeTes?->metode_tes }}</small>
                                        </td>
                                        <td class="text-wrap">{{ \Illuminate\Support\Str::limit($item->judul, 80) }}</td>
                                        <td>{{ $item->tanggal }}</td>
                                        <td>{{ $item->sesi ?? '-' }}</td>
                                        <td>
                                            <span class="badge bg-primary-subtle text-primary">{{ $item->pesertaRangeLabel() }}</span>
                                        </td>
                                        <td class="text-wrap">{{ $item->tempat }}</td>
                                        <td>{{ $item->creator?->nama ?? '-' }}</td>
                                        <td class="text-center">
                                            <x-table.btn-link
                                                :route="'admin.dokumen.absensi.download'"
                                                :params="['id' => $item->id]"
                                                icon="external-link"
                                                tooltip="Buka PDF"
                                                target="_blank"
                                            />
                                            <x-table.btn-edit :id="$item->id" />
                                            <x-table.btn-delete :id="$item->id" />
                                        </td>
                                    </tr>
                                @endforeach

                                @if($data->count() === 0)
                                    <tr>
                                        <td colspan="9" class="text-center text-muted py-4">
                                            <i class="link-icon" data-feather="inbox" style="font-size: 24px; opacity: 0.7;"></i>
                                            <div class="mt-2 fw-semibold">Belum ada data absensi...</div>
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
         wire:key="modal-edit-absensi-{{ $editId }}"
         x-data="{ init() { setTimeout(() => { if (typeof feather !== 'undefined') feather.replace(); }, 50); } }"
         x-init="init()">
        <div class="modal-dialog modal-dialog-centered modal-lg" style="animation: slideDown 0.3s ease-out;">
            <div class="modal-content" style="border: none; border-radius: 16px; overflow: hidden; box-shadow: 0 20px 60px rgba(0,0,0,0.3);">
                <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; padding: 24px 32px;">
                    <div class="d-flex align-items-center gap-3">
                        <div class="d-flex align-items-center justify-content-center"
                             style="width: 48px; height: 48px; background: rgba(255,255,255,0.2); border-radius: 12px;">
                            <i class="link-icon text-white" data-feather="edit-3" style="width: 24px; height: 24px;"></i>
                        </div>
                        <div>
                            <h5 class="modal-title text-white fw-bold mb-0" style="font-size: 1.5rem;">
                                Edit Absensi
                            </h5>
                            <p class="text-white-50 mb-0 mt-1" style="font-size: 0.875rem;">
                                {{ $event_nama }}
                            </p>
                        </div>
                    </div>
                    <button type="button" wire:click="closeModal" class="btn-close btn-close-white"></button>
                </div>

                <div class="modal-body" style="padding: 32px; background: #f8f9fa;">
                    <x-form.textarea
                        label="Judul Presensi"
                        icon="type"
                        model="judul"
                        placeholder="Contoh: DAFTAR HADIR PENILAIAN KOMPETENSI DAN POTENSI..."
                        :required="true"
                        :rows="3"
                    />

                    <div class="row">
                        <div class="col-md-4">
                            <x-form.date
                                label="Tanggal"
                                icon="calendar"
                                model="tanggal_modal"
                                placeholder="pilih tanggal"
                                :required="true"
                            />
                        </div>
                        <div class="col-md-4">
                            <div class="mb-4">
                                <label class="form-label fw-semibold mb-2" style="color: #344054; font-size: 0.875rem;">
                                    <span class="d-flex align-items-center gap-2">
                                        <i class="link-icon" data-feather="sun" style="width: 16px; height: 16px;"></i>
                                        Hari
                                    </span>
                                </label>
                                <input type="text" class="form-control" style="padding: 12px 16px; border-radius: 10px; border: 2px solid #e0e0e0; background: #eef2ff;" value="{{ $hari ?? '-' }}" readonly>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <x-form.select label="Zona Waktu" icon="globe" model="zona_waktu" placeholder="- pilih -" :required="true">
                                <option value="WIB">WIB</option>
                                <option value="WITA">WITA</option>
                                <option value="WIT">WIT</option>
                            </x-form.select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <x-form.input
                                label="Sesi"
                                icon="layers"
                                model="sesi"
                                type="number"
                                placeholder="Contoh: 1"
                                min="1"
                                max="99"
                                :required="true"
                            />
                        </div>
                        <div class="col-md-4">
                            <x-form.input
                                label="Jumlah Peserta Sesi"
                                icon="users"
                                model="jumlah_peserta_sesi"
                                type="number"
                                placeholder="Contoh: 15"
                                min="1"
                                :required="true"
                                :live="true"
                            />
                        </div>
                        <div class="col-md-4">
                            <x-form.input
                                label="Baris Tambahan PDF"
                                icon="plus-square"
                                model="baris_tambahan"
                                type="number"
                                placeholder="Contoh: 10"
                                min="0"
                                max="100"
                                :required="true"
                            />
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <x-form.input
                                label="No. Peserta (Dari)"
                                icon="hash"
                                model="peserta_dari"
                                type="number"
                                min="1"
                                :required="true"
                                readonly
                            />
                        </div>
                        <div class="col-md-6">
                            <x-form.input
                                label="No. Peserta (Sampai)"
                                icon="hash"
                                model="peserta_sampai"
                                type="number"
                                min="1"
                                :required="true"
                                readonly
                            />
                        </div>
                    </div>

                    <small class="text-muted d-block mb-4">
                        Total peserta event: <strong>{{ $total_peserta }}</strong> orang
                    </small>

                    <div class="row">
                        <div class="col-md-6">
                            <x-form.time label="Pukul (Mulai)" icon="clock" model="waktu_mulai" placeholder="pilih waktu" :required="true" />
                        </div>
                        <div class="col-md-6">
                            <x-form.time label="Pukul (Selesai)" icon="clock" model="waktu_selesai" placeholder="pilih waktu" />
                        </div>
                    </div>

                    <x-form.input
                        label="Tempat"
                        icon="map-pin"
                        model="tempat"
                        placeholder="Contoh: SMKN 1 Bondowoso (Lab 1)"
                        :required="true"
                    />
                </div>

                <div class="modal-footer" style="background: white; border-top: 2px solid #f0f0f0; padding: 20px 32px; gap: 12px;">
                    <x-modal.btn-cancel />
                    <x-modal.btn-save :isUpdate="true" />
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
