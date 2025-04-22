<div>
    <x-breadcrumb :breadcrumbs="[
        ['url' => route('admin.dashboard'), 'title' => 'Dashboard'],
        ['url' => route('admin.tes-selesai'), 'title' => 'Data Tes Selesai'],
        ['url' => null, 'title' => 'Peserta']
    ]" />
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">Hasil Tes - {{ $event->nama_event }}</h6>
                    <h6 class="mt-4 text-danger"><i class="link-icon" data-feather="filter"></i> Filter</h6>
                    <div class="row mt-2">
                        <div class="col-sm-4">
                            <div class="mb-3">
                                <input wire:model.live.debounce="search" class="form-control form-control-sm" placeholder="cari peserta berdasar nama / nip / jabatan / instansi" />
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="mb-3">
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
                        </div>
                        <div class="col-sm-2">
                            <div class="mb-3">
                                <button wire:click="resetFilters" class="btn btn-sm btn-inverse-danger">Reset</button>
                            </div>
                        </div>
                        <div class="col-sm-4 text-end">
                            <div class="mb-3">
                                <a href="{{ route('admin.tes-selesai.download-all-laporan', ['idEvent' => $event->id]) }}" class="btn btn-sm btn-dark">Download Semua Laporan (.zip)</a>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Nama Peserta</th>
                                    <th>NIP - Pangkat/Gol</th>
                                    <th>Jabatan</th>
                                    <th>Instansi / Unit Kerja</th>
                                    <th>Mulai Tes</th>
                                    <th>Report</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $index => $item)
                                    <tr>
                                        <td>{{ $data->firstItem() + $index }}</td>
                                        <td class="text-wrap">{{ $item->nama }}</td>
                                        <td>{{ $item->nip }} <br/> {{ $item->golPangkat->pangkat ?? '' }} - {{  $item->golPangkat->golongan ?? '' }}</td>
                                        <td class="text-wrap">{{ $item->jabatan }}</td>
                                        <td class="text-wrap">{{ $item->instansi }} <br /> {{ $item->unit_kerja }}</td>
                                        <td class="text-wrap">{{ \Carbon\Carbon::parse($item->test_started_at)->format('d F Y / H:i:s') }}</td>
                                        <td>
                                            <a href="{{ route('admin.tes-selesai.show-report', [
                                                    'idEvent' => $item->event_id,
                                                    'nip' => $item->nip
                                                ]) }}" class="btn btn-sm btn-inverse-success"
                                                wire:navigate
                                            >
                                                Lihat
                                            </a>
                                            <a href="{{ route('admin.tes-selesai.download', [
                                                    'idEvent' => $item->event_id,
                                                    'nip' => $item->nip
                                                ]) }}" class="btn btn-sm btn-inverse-danger"
                                                target="_blank"
                                            >
                                                Download
                                            </a>
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