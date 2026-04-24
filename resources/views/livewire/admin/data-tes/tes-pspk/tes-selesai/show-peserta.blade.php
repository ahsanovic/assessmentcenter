<div>
    <x-breadcrumb :breadcrumbs="[
        ['url' => route('admin.dashboard'), 'title' => 'Dashboard'],
        ['url' => route('admin.tes-selesai.pspk'), 'title' => 'Tes PSPK Selesai'],
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
                                <div class="col-sm-3">
                                    <div class="input-group" wire:ignore>
                                        <span class="input-group-text bg-white"><i data-feather="search"></i></span>
                                        <input wire:model.live.debounce="search" class="form-control" placeholder="cari peserta...">
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="input-group flatpickr" id="flatpickr-date">
                                        <input type="text" wire:model.live="tanggal_tes"
                                            class="form-control flatpickr-input" placeholder="tanggal tes"
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
                                <div class="col-auto">
                                    <x-btn-reset :text="'Reset'" />
                                </div>
                                <div class="col d-flex justify-content-end flex-wrap gap-2 align-items-end">
                                    <x-btn-download 
                                        :route="'admin.tes-selesai.pspk.download-rekap'"
                                        :params="[$event->id]"
                                        :query="['tanggalTes' => $tanggal_tes ? \Carbon\Carbon::parse($tanggal_tes)->format('Y-m-d') : '']"
                                        text="Rekap Laporan (Excel)"
                                        icon="download"
                                        color="success"
                                        :disabled="$data->isEmpty()"
                                    />
                                    <x-btn-download 
                                        :route="'admin.tes-selesai.pspk.download-all-laporan'"
                                        :params="[$event->id]"
                                        :query="['tanggalTes' => $tanggal_tes ? \Carbon\Carbon::parse($tanggal_tes)->format('Y-m-d') : '']"
                                        text="Laporan PDF (.zip)"
                                        icon="download"
                                        color="dark"
                                        :disabled="$data->isEmpty()"
                                    />
                                    <button type="button" class="btn btn-sm btn-icon-text btn-warning text-dark" wire:click="setUjianKeBelumSelesaiMassalConfirmation">
                                        <span wire:ignore>
                                            <i class="btn-icon-prepend" data-feather="refresh-cw"></i>
                                        </span>
                                        Set belum selesai (massal)
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
                                    <th>Instansi / Unit Kerja</th>
                                    <th>Mulai / Selesai Tes</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $index => $item)
                                    <tr>
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
                                        <td class="text-wrap">{{ $item->instansi }} <br/> {{ $item->unit_kerja }}</td>
                                        <td class="text-wrap">
                                            {{ \Carbon\Carbon::parse($item->waktu_mulai)->translatedFormat('d F Y / H:i:s') }} <br/>
                                            {{ \Carbon\Carbon::parse($item->waktu_selesai)->translatedFormat('d F Y / H:i:s') }}
                                        </td>
                                        <td>
                                            @if ($item->is_finished == 'true')
                                            <button
                                                type="button"
                                                class="btn btn-sm btn-outline-warning btn-icon rounded-circle border-0 shadow-sm"
                                                wire:click="setUjianKeBelumSelesaiConfirmation('{{ $item->ujian_pspk_id }}')"
                                                data-bs-toggle="tooltip"
                                                data-bs-placement="top"
                                                title="Set ujian ke belum selesai"
                                                style="transition: background 0.2s;"
                                            >
                                                <i class="link-icon" data-feather="refresh-cw"></i>
                                            </button>
                                                <x-table.btn-delete :id="$item->hasil_pspk_id" />
                                            @endif
                                            <x-table.btn-link
                                                :route="'admin.tes-selesai.pspk.download'"
                                                :params="['idEvent' => $item->event_id, 'identifier' => $item->nip ?: $item->nik]"
                                                :icon="'download'"
                                                :tooltip="'Download Pdf'"
                                                :color="'success'"
                                                :target="'_blank'"
                                            />
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>        
        </div>
        <x-pagination :items="$data" />
    </div>
</div>
@push('js')
    <script>
        window.addEventListener('set-ujian-ke-belum-selesai-confirmation', () => {
            Swal.fire({
                title: 'Set ujian ke belum selesai?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, ubah status',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    Livewire.dispatch('setUjianKeBelumSelesai');
                }
            });
        });
        window.addEventListener('set-ujian-ke-belum-selesai-massal-confirmation', () => {
            Swal.fire({
                title: 'Set semua ujian PSPK ke belum selesai?',
                html: 'Semua ujian pada event ini yang berstatus <b>selesai</b> akan diubah menjadi <b>belum selesai</b>.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ffc107',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, terapkan ke semua',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    Livewire.dispatch('setUjianKeBelumSelesaiMassal');
                }
            });
        });
    </script>
@endpush