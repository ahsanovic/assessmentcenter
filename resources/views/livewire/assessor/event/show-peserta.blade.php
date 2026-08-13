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
                    <x-monitoring.filter-panel>
                            <div class="row g-2 align-items-end">
                                <div class="col-sm-4">
                                    <div class="input-group">
                                        <span class="input-group-text bg-white"><i data-feather="search" style="width:16px;height:16px;"></i></span>
                                        <input wire:model.live.debounce="search" class="form-control" placeholder="cari berdasar nama / nip / jabatan / instansi" />
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <x-btn-reset :text="'Reset'" />
                                </div>
                            </div>
                    </x-monitoring.filter-panel>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Nama Asesi</th>
                                    <th>NIP - Pangkat/Gol</th>
                                    <th>Jabatan</th>
                                    <th>Instansi</th>
                                    <th>Unit Kerja</th>
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
                                        <td class="text-wrap">{{ $item->unit_kerja }}</td>
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