<div>
    <x-breadcrumb :breadcrumbs="[
        ['url' => route('admin.dashboard'), 'title' => 'Dashboard'],
        ['url' => route('admin.distribusi-peserta'), 'title' => 'Distribusi Peserta'],
        ['url' => route('admin.distribusi-peserta.show-assessor', ['idEvent' => $event->id]), 'title' => 'Data Assessor Event ' . $event->nama_event],
        ['url' => null, 'title' => 'Data Asessee Assessor ' . $assessor->nama_event],
    ]" />
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">Data Asessee Assessor: <span class="badge bg-warning text-dark"> {{ $assessor->nama }}</span></h6>
                    <div class="card mt-3 mb-4 bg-light-subtle">
                        <div class="card-body">
                            <h6 class="text-danger"><i class="link-icon" data-feather="filter"></i> Filter</h6>
                            <div class="row mt-2">
                                <div class="col-sm-4">
                                    <div class="mb-3">
                                        <input wire:model.live.debounce="search" class="form-control form-control-sm" placeholder="cari peserta berdasar nama / nip / jabatan / instansi" />
                                    </div>
                                </div>
                                <div class="col-sm-4">
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
                                    <th>NIP / NIK <br><small class="text-muted">Pangkat/Gol</small></th>
                                    <th>Jabatan</th>
                                    <th>Instansi <br><small class="text-muted">Unit Kerja</small></th>
                                    <th>Pilih</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $index => $item)
                                    <tr class="@if($loop->iteration % 2 == 1) bg-body @endif border-bottom">
                                        <td class="text-center text-secondary fw-bold">{{ $data->firstItem() + $index }}</td>
                                        <td class="text-wrap fw-medium">{{ $item->nama }}</td>
                                        <td>
                                            <span class="badge {{ $item->jenis_peserta_id == 1 ? 'bg-primary' : 'bg-info' }}">
                                                {{ $item->jenisPeserta->jenis_peserta ?? '-' }}
                                            </span>
                                        </td>
                                        <td>
                                            @if ($item->jenis_peserta_id == 1)
                                                {{ $item->nip }}
                                                <br>
                                                @if (!empty($item->golPangkat?->pangkat) && !empty($item->golPangkat?->golongan))
                                                    <span class="badge bg-secondary-subtle text-dark mt-1">
                                                        {{ $item->golPangkat->pangkat . ' - ' . $item->golPangkat->golongan }}
                                                    </span>
                                                @else
                                                    <span class="text-muted d-block mt-1"></span>
                                                @endif
                                            @elseif ($item->jenis_peserta_id == 2)
                                                {{ $item->nik }}
                                            @endif
                                        </td>
                                        <td class="text-wrap">
                                            <span class="badge bg-info-subtle text-dark fw-normal">{{ $item->jabatan }}</span>
                                        </td>
                                        <td class="text-wrap">
                                            <span class="fw-medium text-dark">{{ $item->unit_kerja ?? '-' }}</span>
                                            <br>
                                            <span class="text-muted small">{{ $item->instansi ?? '-' }}</span>
                                        </td>
                                        <td>
                                            <input
                                                type="checkbox"
                                                wire:model="check.{{ $item->id }}"
                                                wire:change="toggleCheck({{ $item->id }}, $event.target.checked)"
                                                class="form-check-input"
                                                id="checkInline1"
                                            >
                                        </td>
                                    </tr>
                                @endforeach

                                @if($data->count() === 0)
                                    <tr>
                                        <td colspan="9" class="text-center text-muted py-4">
                                            <i class="link-icon" data-feather="inbox" style="font-size: 24px; opacity: 0.7;"></i>
                                            <div class="mt-2 fw-semibold">Tidak ada data asessee...</div>
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