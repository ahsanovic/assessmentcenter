<div>
    <x-breadcrumb :breadcrumbs="[
        ['url' => route('admin.dashboard'), 'title' => 'Dashboard'],
        ['url' => route('admin.tes-selesai.kompetensi-teknis'), 'title' => 'Hasil Tes Kompetensi Teknis'],
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
                                <div class="col-sm-1">
                                    <x-btn-reset :text="'Reset'" />
                                </div>
                            </div>
                    </x-monitoring.filter-panel>
                    <div class="d-flex flex-wrap justify-content-end gap-2 mb-3">
                        <x-btn-download 
                            :route="'admin.tes-selesai.kompetensi-teknis.download-rekap'"
                            :params="[$event->id]"
                            :query="['tanggalTes' => $tanggal_tes ? \Carbon\Carbon::parse($tanggal_tes)->format('Y-m-d') : '']"
                            text="Download Rekap Laporan (Excel)"
                            icon="download"
                            color="success"
                            :disabled="$data->isEmpty()"
                        />
                        <x-btn-download 
                            :route="'admin.tes-selesai.kompetensi-teknis.download-all-laporan'"
                            :params="[$event->id]"
                            :query="['tanggalTes' => $tanggal_tes ? \Carbon\Carbon::parse($tanggal_tes)->format('Y-m-d') : '']"
                            text="Download Semua Laporan PDF (.zip)"
                            icon="download"
                            color="dark"
                            :disabled="$data->isEmpty()"
                        />
                    </div>
                    <div class="table-responsive">
                        <table class="table ac-data-table table-hover align-middle" style="overflow:hidden;">
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
                                                <x-table.btn-delete :id="$item->hasil_kompetensi_teknis_id" />
                                            @endif
                                            <x-table.btn-link
                                                :route="'admin.tes-selesai.kompetensi-teknis.download'"
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