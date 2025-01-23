<div>
    <x-breadcrumb :breadcrumbs="[
        ['url' => route('assessor.dashboard'), 'title' => 'Dashboard'],
        ['url' => route('assessor.event'), 'title' => 'Event'],
        ['url' => null, 'title' => 'Asesi']
    ]" />
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">Data Asesi Event {{ $event->nama_event }}</h6>
                    <h6 class="mt-4 text-danger"><i class="link-icon" data-feather="filter"></i> Filter</h6>
                    <div class="row mt-2">
                        <div class="col-sm-4">
                            <div class="mb-3">
                                <input wire:model.live.debounce="search" class="form-control form-control-sm" placeholder="cari berdasar nama / nip / jabatan / instansi" />
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="mb-3">
                                <button wire:click="resetFilters" class="btn btn-sm btn-inverse-danger">Reset</button>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Nama Asesi</th>
                                    <th>NIP - Pangkat/Gol</th>
                                    <th>Jabatan</th>
                                    <th>Instansi</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $index => $item)
                                    <tr>
                                        <td>{{ $data->firstItem() + $index }}</td>
                                        <td>{{ $item->nama }}</td>
                                        <td>{{ $item->nip }} <br/> {{ $item->golPangkat->pangkat ?? '' }}  -  {{  $item->golPangkat->golongan ?? '' }}</td>
                                        <td class="text-wrap">{{ $item->jabatan }}</td>
                                        <td class="text-wrap">{{ $item->instansi }}</td>
                                        <td>
                                            <a class="btn btn-xs btn-primary" wire:navigate
                                                href="{{ route('assessor.peserta.portofolio', ['idEvent' => $item->event_id, 'idPeserta' => $item->id]) }}">
                                                Portofolio
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