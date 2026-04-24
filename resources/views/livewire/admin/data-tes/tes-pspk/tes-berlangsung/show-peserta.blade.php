<div>
    <x-breadcrumb :breadcrumbs="[
        ['url' => route('admin.dashboard'), 'title' => 'Dashboard'],
        ['url' => route('admin.tes-berlangsung.pspk'), 'title' => 'Tes PSPK Berlangsung'],
        ['url' => null, 'title' => 'Peserta']
    ]" />
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title mb-0">Event: <span class="badge bg-warning text-dark">{{ $event->nama_event }}</span></h6>
                    <div class="card mt-4 mb-4 bg-light-subtle">
                        <div class="card-body">
                            <h6 class="text-danger" wire:ignore><i class="link-icon" data-feather="filter"></i> Filter</h6>
                            <div class="row mt-2 align-items-end">
                                <div class="col-sm-4">
                                    <div class="input-group" wire:ignore>
                                        <span class="input-group-text bg-white"><i data-feather="search"></i></span>
                                        <input wire:model.live.debounce="search" class="form-control" placeholder="cari peserta...">
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <x-btn-reset :text="'Reset'" />
                                </div>
                                <div class="col-auto ms-auto d-flex align-items-end">
                                    <button type="button" class="btn btn-primary d-flex align-items-center gap-2" wire:click="openModalMassal">
                                        <i class="link-icon" data-feather="users" style="width: 18px; height: 18px;"></i>
                                        Tambah waktu massal
                                    </button>
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
                                    <th>NIK / NIP - Pangkat/Gol</th>
                                    <th>Jabatan</th>
                                    <th>Instansi</th>
                                    <th>Mulai Tes</th>
                                    <th>Status Tes</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $index => $item)
                                    <tr class="@if($loop->iteration % 2 == 1) bg-body @endif border-bottom">
                                        <td class="text-center text-secondary fw-bold">{{ $data->firstItem() + $index }}</td>
                                        <td class="text-wrap">{{ $item->nama }}</td>
                                        <td>
                                            @if ($item->jenis_peserta_id == 1)
                                                {{ $item->nip }}
                                                @if (!empty($item->golPangkat?->pangkat) && !empty($item->golPangkat?->golongan))
                                                    <br/> {{ $item->golPangkat->pangkat . ' - ' . $item->golPangkat->golongan }}
                                                @else
                                                    <br/> -
                                                @endif
                                            @elseif ($item->jenis_peserta_id == 2)
                                                {{ $item->nik }}
                                            @endif
                                        </td>
                                        <td class="text-wrap">{{ $item->jabatan }}</td>
                                        <td class="text-wrap">{{ $item->instansi }} <br /> {{ $item->unit_kerja }}</td>
                                        <td class="text-wrap">
                                            <span class="badge bg-light text-dark border">
                                                {{ \Carbon\Carbon::parse($item->mulai_tes)->translatedFormat('d F Y / H:i:s') }}
                                            </span>
                                        </td>
                                        <td>
                                            @if ($item->is_finished == 'false')
                                                <span class="badge bg-danger text-white">Belum Selesai</span>
                                            @else
                                                <span class="badge bg-success text-white">Selesai</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($item->is_finished == 'false')
                                            <button
                                                type="button"
                                                class="btn btn-sm btn-outline-primary btn-icon rounded-circle border-0 shadow-sm"
                                                wire:click="openModal('{{ $item->ujian_pspk_id }}')"
                                                data-bs-toggle="tooltip"
                                                data-bs-placement="top"
                                                title="Tambah waktu tes"
                                                style="transition: background 0.2s;"
                                            >
                                                <i class="link-icon" data-feather="clock"></i>
                                            </button>
                                            <x-table.btn-delete :id="$item->ujian_pspk_id" />
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                @if($data->count() === 0)
                                    <tr>
                                        <td colspan="8" class="text-center text-muted py-4">
                                            <i class="link-icon" data-feather="inbox" style="font-size: 24px; opacity: 0.7;"></i>
                                            <div class="mt-2 fw-semibold">Tidak ada data peserta...</div>
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>

                    @if($showModal)
                    <div class="modal fade show" style="display: block; background: rgba(0,0,0,0.5);" tabindex="-1"
                        wire:key="modal-tambah-waktu-{{ $selected_id }}"
                        x-data="{ init() { setTimeout(() => { if (typeof feather !== 'undefined') feather.replace(); }, 50); } }"
                        x-init="init()">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content" style="border: none; border-radius: 16px; overflow: hidden; box-shadow: 0 20px 60px rgba(0,0,0,0.3);">
                                <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; padding: 24px 32px;">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="d-flex align-items-center justify-content-center"
                                             style="width: 48px; height: 48px; background: rgba(255,255,255,0.2); border-radius: 12px; backdrop-filter: blur(10px);">
                                            <i class="link-icon text-white" data-feather="clock" style="width: 24px; height: 24px;"></i>
                                        </div>
                                        <div>
                                            <h5 class="modal-title text-white fw-bold mb-0" style="font-size: 1.5rem;">Tambah waktu tes</h5>
                                            <p class="text-white-50 mb-0 mt-1" style="font-size: 0.875rem;">Tambahan menit untuk satu peserta</p>
                                        </div>
                                    </div>
                                    <button wire:click="closeModal" type="button" class="btn-close btn-close-white"
                                            style="filter: brightness(0) invert(1); opacity: 0.8;"></button>
                                </div>
                                <div class="modal-body" style="padding: 32px; background: #f8f9fa;">
                                    <x-form.input
                                        label="Tambahan waktu (menit)"
                                        icon="clock"
                                        model="waktu"
                                        type="number"
                                        placeholder="menit"
                                        :required="true"
                                        min="1"
                                        :max="$maxTambahanMenit"
                                    />
                                </div>
                                <div class="modal-footer" style="background: white; border-top: 2px solid #f0f0f0; padding: 20px 32px; gap: 12px;">
                                    <x-modal.btn-cancel action="closeModal" />
                                    <x-modal.btn-save :isUpdate="false" action="tambahWaktu" />
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($showModalMassal)
                    <div class="modal fade show" style="display: block; background: rgba(0,0,0,0.5);" tabindex="-1"
                        wire:key="modal-tambah-waktu-massal"
                        x-data="{ init() { setTimeout(() => { if (typeof feather !== 'undefined') feather.replace(); }, 50); } }"
                        x-init="init()">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content" style="border: none; border-radius: 16px; overflow: hidden; box-shadow: 0 20px 60px rgba(0,0,0,0.3);">
                                <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; padding: 24px 32px;">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="d-flex align-items-center justify-content-center"
                                             style="width: 48px; height: 48px; background: rgba(255,255,255,0.2); border-radius: 12px;">
                                            <i class="link-icon text-white" data-feather="users" style="width: 24px; height: 24px;"></i>
                                        </div>
                                        <div>
                                            <h5 class="modal-title text-white fw-bold mb-0" style="font-size: 1.5rem;">Tambah waktu massal</h5>
                                            <p class="text-white-50 mb-0 mt-1" style="font-size: 0.875rem;">Semua peserta ujian berlangsung (belum selesai) pada event ini</p>
                                        </div>
                                    </div>
                                    <button wire:click="closeModalMassal" type="button" class="btn-close btn-close-white"
                                            style="filter: brightness(0) invert(1); opacity: 0.8;"></button>
                                </div>
                                <div class="modal-body" style="padding: 32px; background: #f8f9fa;">
                                    <x-form.input
                                        label="Tambahan waktu (menit)"
                                        icon="clock"
                                        model="waktuMassal"
                                        type="number"
                                        placeholder="menit"
                                        :required="true"
                                        min="1"
                                        :max="$maxTambahanMenit"
                                    />
                                </div>
                                <div class="modal-footer" style="background: white; border-top: 2px solid #f0f0f0; padding: 20px 32px; gap: 12px;">
                                    <x-modal.btn-cancel action="closeModalMassal" />
                                    <x-modal.btn-save :isUpdate="false" action="tambahWaktuMassal" text="Terapkan ke semua" />
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        <x-pagination :items="$data" />
    </div>
</div>
