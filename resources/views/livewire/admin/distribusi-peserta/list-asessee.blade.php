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
                    <h6 class="card-title">Data Asessee Assessor {{ $assessor->nama }}</h6>
                    <h6 class="mt-4 text-danger"><i class="link-icon" data-feather="filter"></i> Filter</h6>
                    <div class="row mt-2">
                        <div class="col-sm-4">
                            <div class="mb-3">
                                <input wire:model.live.debounce="search" class="form-control form-control-sm" placeholder="cari peserta berdasar nama / nip / jabatan / instansi" />
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="mb-3">
                                <button wire:click="resetFilters" class="btn btn-sm btn-inverse-danger me-2">Reset</button>
                                <a href="{{ route('admin.distribusi-peserta.show-assessor', ['idEvent' => $event_id]) }}" wire:navigate class="btn btn-sm btn-inverse-success">
                                    Kembali ke List Assessor
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
                                    <th>NIP - Pangkat/Gol</th>
                                    <th>Jabatan</th>
                                    <th>Instansi</th>
                                    <th>Unit Kerja</th>
                                    <th>Pilih</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $index => $item)
                                    <tr>
                                        <td>{{ $data->firstItem() + $index }}</td>
                                        <td>{{ $item->nama }}</td>
                                        <td>{{ $item->nip }} <br/> {{ $item->golPangkat->pangkat ?? '' }}  -  {{  $item->golPangkat->golongan ?? '' }}</td>
                                        <td>{{ $item->jabatan }}</td>
                                        <td class="text-wrap">{{ $item->instansi }}</td>
                                        <td class="text-wrap">{{ $item->unit_kerja }}</td>
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
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>        
        </div>
        <x-pagination :items="$data" />
    </div>
</div>