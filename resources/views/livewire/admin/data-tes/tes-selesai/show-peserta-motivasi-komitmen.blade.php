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
                    <h6 class="card-title">Hasil Tes Motivasi Komitmen - {{ $event->nama_event }}</h6>
                    <h6 class="mt-4 text-danger"><i class="link-icon" data-feather="filter"></i> Filter</h6>
                    <div class="row mt-2">
                        <div class="col-sm-4">
                            <div class="mb-3">
                                <input wire:model.live.debounce="search" class="form-control form-control-sm" placeholder="cari peserta berdasar nama / nip / jabatan / instansi" />
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
                                    <th>Nama Peserta</th>
                                    <th>NIP - Pangkat/Gol</th>
                                    <th>Jabatan</th>
                                    <th>Instansi / Unit Kerja</th>
                                    <th>Selesai Tes</th>
                                    <th>Aksi</th>
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
                                        <td class="text-wrap">{{ \Carbon\Carbon::parse($item->waktu_selesai)->translatedFormat('d F Y / H:i:s') }}</td>
                                        <td>
                                            @if ($item->is_finished == 'true')
                                                <button wire:click="deleteConfirmation('{{ $item->hasil_motivasi_komitmen_id }}')" tabindex="0" class="btn btn-xs btn-outline-danger">
                                                    Hapus
                                                </button>
                                            @endif
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