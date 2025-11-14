<div>
    <x-breadcrumb :breadcrumbs="[
        ['url' => route('admin.dashboard'), 'title' => 'Dashboard'],
        ['url' => route('admin.tes-selesai.kompetensi-teknis'), 'title' => 'Data Tes Selesai'],
        ['url' => null, 'title' => 'Peserta']
    ]" />
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">Hasil Tes PSPK - {{ $event->nama_event }}</h6>
                    <h6 class="mt-4 text-danger"><i class="link-icon" data-feather="filter"></i> Filter</h6>
                    <div class="row mt-2">
                        <div class="col-sm-4">
                            <div class="mb-3">
                                <input wire:model.live.debounce="search" class="form-control form-control-sm" placeholder="nama / nip / nik / jabatan / instansi" />
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
                        <div class="col-sm-4 mb-3 d-flex justify-content-end">
                            <div class="me-2">
                                <a href="{{ route('admin.tes-selesai.pspk.download-rekap', $event->id) }}?tanggalTes={{ $tanggal_tes ? \Carbon\Carbon::parse($tanggal_tes)->format('Y-m-d') : '' }}"
                                    class="btn btn-sm btn-success {{ $data->isEmpty() ? 'disabled' : '' }}">Download Rekap Laporan (Excel)
                                </a>
                            </div>
                            <div class="me-2">
                                <a href="{{ route('admin.tes-selesai.pspk.download-all-laporan', $event->id) }}?tanggalTes={{ $tanggal_tes ? \Carbon\Carbon::parse($tanggal_tes)->format('Y-m-d') : '' }}"
                                    class="btn btn-sm btn-dark {{ $data->isEmpty() ? 'disabled' : '' }}">Download Semua Laporan PDF (.zip)
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
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
                                        <td>{{ $data->firstItem() + $index }}</td>
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
                                                <button wire:click="deleteConfirmation('{{ $item->hasil_pspk_id }}')" tabindex="0" class="me-2 btn btn-xs btn-outline-danger">
                                                    Hapus
                                                </button>
                                            @endif
                                            <a href="{{ route('admin.tes-selesai.pspk.download', [
                                                    'idEvent' => $item->event_id,
                                                    'identifier' => $item->nip ?: $item->nik
                                                ]) }}" class="btn btn-xs btn-inverse-danger"
                                                target="_blank"
                                            >
                                                Download Pdf
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