<div>
    <x-breadcrumb :breadcrumbs="[
        ['url' => route('admin.dashboard'), 'title' => 'Dashboard'],
        ['url' => route('admin.tes-selesai'), 'title' => 'Hasil Tes Potensi'],
        ['url' => null, 'title' => 'Peserta Tes Potensi']
    ]" />
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
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
                            :route="'admin.tes-selesai.download-rekap'"
                            :params="[$event->id]"
                            :query="['tanggalTes' => $tanggal_tes ? \Carbon\Carbon::parse($tanggal_tes)->format('Y-m-d') : '']"
                            text="Download Rekap Laporan (Excel)"
                            icon="download"
                            color="success"
                            :disabled="$data->isEmpty()"
                        />
                        <x-btn-download 
                            :route="'admin.tes-selesai.download-all-laporan'"
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
                                    <th>Jabatan</th>
                                    <th>Instansi<br><small class="text-muted">Unit Kerja</small></th>
                                    <th>Tanggal Tes</th>
                                    <th>Report</th>
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
                                                {{ \Carbon\Carbon::parse($item->test_started_at)->translatedFormat('d F Y') }}
                                                <span class="text-muted px-1">/</span>
                                                {{ \Carbon\Carbon::parse($item->test_started_at)->translatedFormat('H:i:s') }}
                                            </span>
                                        </td>
                                        <td>
                                            <x-table.btn-link
                                                :route="'admin.tes-selesai.show-report'"
                                                :params="['idEvent' => $item->event_id, 'identifier' => $item->nip ?: $item->nik]"
                                                :icon="'eye'"
                                                :tooltip="'Lihat Report'"
                                                :color="'success'"
                                                :navigate="true"
                                            />
                                            <x-table.btn-link
                                                :route="'admin.tes-selesai.download'"
                                                :params="['idEvent' => $item->event_id, 'identifier' => $item->nip ?: $item->nik]"
                                                :icon="'download'"
                                                :tooltip="'Download Pdf'"
                                                :color="'danger'"
                                                :target="'_blank'"
                                            />
                                            <x-table.btn-link
                                                :route="'admin.tes-selesai.rekomendasi-ai'"
                                                :params="['idEvent' => $item->event_id, 'identifier' => $item->nip ?: $item->nik]"
                                                :icon="'thumbs-up'"
                                                :tooltip="'Rekomendasi AI'"
                                                :color="'warning'"
                                                :navigate="true"
                                            />
                                        </td>
                                    </tr>
                                @endforeach

                                @if($data->count() === 0)
                                    <tr>
                                        <td colspan="9" class="text-center text-muted py-5 ac-empty-state">
                                            <i class="link-icon" data-feather="inbox" style="font-size: 24px; opacity: 0.7;"></i>
                                            <div class="mt-2 fw-semibold">Tidak ada data peserta...</div>
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
</div>