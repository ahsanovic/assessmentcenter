<div>
    <x-breadcrumb :breadcrumbs="[
        ['url' => route('admin.dashboard'), 'title' => 'Dashboard'],
        ['url' => route('admin.tes-selesai.cakap-digital'), 'title' => 'Hasil Tes Cakap Digital'],
        ['url' => null, 'title' => 'Peserta']
    ]" />
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <x-monitoring.event-header :nama="$event->nama_event" />
                    <x-monitoring.filter-panel>
                            <div class="row g-2 align-items-end">
                                <div class="col-sm-3">
                                    <div class="input-group">
                                        <span class="input-group-text bg-white"><i data-feather="search" style="width:16px;height:16px;"></i></span>
                                        <input wire:model.live.debounce="search" class="form-control" placeholder="cari peserta..." autocomplete="off">
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
                            </div>
                    </x-monitoring.filter-panel>
                    <div class="d-flex flex-wrap justify-content-end gap-2 mb-3">
                        <x-btn-download 
                            :route="'admin.tes-selesai.cakap-digital.download-rekap'"
                            :params="[$event->id]"
                            :query="['tanggalTes' => $tanggal_tes ? \Carbon\Carbon::parse($tanggal_tes)->format('Y-m-d') : '']"
                            text="Rekap Laporan (Excel)"
                            icon="download"
                            color="success"
                            :disabled="$data->isEmpty()"
                        />
                        @if($event->event_group_id)
                            <x-btn-download-rekap-gabungan
                                :event-id="$event->id"
                                :tanggal-tes="$tanggal_tes ? \Carbon\Carbon::parse($tanggal_tes)->format('Y-m-d') : ''"
                            />
                        @endif
                        <x-btn-download 
                            :route="'admin.tes-selesai.cakap-digital.download-all-laporan'"
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
                    <div class="table-responsive">
                        <table class="table ac-data-table table-hover align-middle" style="overflow:hidden;">
                            <thead class="table-light border-bottom">
                                <tr>
                                    <th class="text-center" style="width: 45px;">#</th>
                                    <th>Nama Peserta</th>
                                    <th>Jabatan</th>
                                    <th>Instansi<br><small class="text-muted">Unit Kerja</small></th>
                                    <th>Mulai Tes</th>
                                    <th>Selesai Tes</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $index => $item)
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
                                        <td class="text-wrap">
                                            <span class="badge bg-info-subtle text-dark fw-normal">{{ $item->jabatan }}</span>
                                        </td>
                                        <td class="text-wrap">
                                            <span class="fw-medium text-dark">{{ $item->instansi }}</span>
                                            <br>
                                            <span class="text-muted small">{{ $item->unit_kerja }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark border-1 border">
                                                {{ \Carbon\Carbon::parse($item->waktu_mulai)->translatedFormat('d F Y') }}
                                                <span class="text-muted px-1">/</span>
                                                {{ \Carbon\Carbon::parse($item->waktu_mulai)->translatedFormat('H:i:s') }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark border-1 border">
                                                {{ \Carbon\Carbon::parse($item->waktu_selesai)->translatedFormat('d F Y') }}
                                                <span class="text-muted px-1">/</span>
                                                {{ \Carbon\Carbon::parse($item->waktu_selesai)->translatedFormat('H:i:s') }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            @if ($item->is_finished == 'true')
                                                <button
                                                    type="button"
                                                    class="btn btn-sm btn-outline-warning btn-icon rounded-circle border-0 shadow-sm"
                                                    wire:click="setUjianKeBelumSelesaiConfirmation('{{ $item->ujian_cakap_digital_id }}')"
                                                    data-bs-toggle="tooltip"
                                                    data-bs-placement="top"
                                                    title="Set ujian ke belum selesai"
                                                    style="transition: background 0.2s;"
                                                >
                                                    <i class="link-icon" data-feather="refresh-cw"></i>
                                                </button>
                                                <x-table.btn-delete :id="$item->hasil_cakap_digital_id" />
                                            @endif
                                            <x-table.btn-link
                                                :route="'admin.tes-selesai.cakap-digital.download'"
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
                title: 'Set semua ujian Cakap Digital ke belum selesai?',
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
