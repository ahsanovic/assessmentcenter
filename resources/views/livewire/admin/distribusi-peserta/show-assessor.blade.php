<div>
    <x-breadcrumb :breadcrumbs="[
        ['url' => route('admin.dashboard'), 'title' => 'Dashboard'],
        ['url' => route('admin.distribusi-peserta'), 'title' => 'Distribusi Peserta'],
        ['url' => null, 'title' => 'Data Assessor Event ' . $event->nama_event]
    ]" />
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">Data Assessor Event: <span class="badge bg-warning text-dark"> {{ $event->nama_event }}</span></h6>
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
                                    <th>Nama Assessor</th>
                                    <th>NIP - Pangkat/Gol</th>
                                    <th>Jabatan</th>
                                    <th>Instansi</th>
                                    <th>Jumlah Asessee</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $index => $item)
                                    <tr class="@if($loop->iteration % 2 == 1) bg-body @endif border-bottom">
                                        <td class="text-center text-secondary fw-bold">{{ $data->firstItem() + $index }}</td>
                                        <td>{{ $item->nama }}</td>
                                        <td>{{ $item->nip }} <br/> {{ $item->golPangkat->pangkat ?? '' }}  -  {{  $item->golPangkat->golongan ?? '' }}</td>
                                        <td>{{ $item->jabatan }}</td>
                                        <td class="text-wrap">{{ $item->instansi }}</td>
                                        <td>{{ $item->peserta_count }}</td>
                                        <td>
                                            <a class="btn btn-xs btn-primary" wire:navigate
                                                href="{{ 
                                                    route('admin.distribusi-peserta.list-asessee', [
                                                        'idEvent' => $item->pivot->event_id,
                                                        'idAssessor' => $item->id
                                                    ]) 
                                                }}">
                                                Pilih Asessee
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