<div>
    <x-breadcrumb :breadcrumbs="[
        ['url' => route('admin.dashboard'), 'title' => 'Dashboard'],
        ['url' => route('admin.tes-berlangsung'), 'title' => 'Data Tes Sedang Berlangsung'],
        ['url' => null, 'title' => 'Peserta']
    ]" />
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">Peserta Tes Motivasi dan Komitmen - {{ $event->nama_event }}</h6>
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
                                    <th>NIK / NIP - Pangkat/Gol</th>
                                    <th>Jabatan</th>
                                    <th>Instansi</th>
                                    <th>Mulai Tes</th>
                                    <th>Status Tes</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $index => $item)
                                    <tr>
                                        <td>{{ $data->firstItem() + $index }}</td>
                                        <td>{{ $item->nama }}</td>
                                        <td>
                                            @if ($item->jenis_peserta_id == 1)
                                                {{ $item->nip }} <br/> {{ $item->golPangkat->pangkat . ' - ' . $item->golPangkat->golongan }}
                                            @elseif ($item->jenis_peserta_id ==2)
                                                {{ $item->nik }}
                                            @endif
                                        </td>
                                        <td class="text-wrap">{{ $item->jabatan }}</td>
                                        <td class="text-wrap">{{ $item->instansi }} <br/> {{ $item->unit_kerja }}</td>
                                        <td class="text-wrap">{{ \Carbon\Carbon::parse($item->mulai_tes)->translatedFormat('d F Y / H:i:s') }}</td>
                                        <td>
                                            @if ($item->is_finished == 'false')
                                                <span class="badge bg-danger text-white">Belum Selesai</span>
                                            @else
                                                <span class="badge bg-success text-white">Selesai</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($item->is_finished == 'false')
                                                <button wire:click="deleteConfirmation('{{ $item->ujian_motivasi_komitmen_id }}')" tabindex="0" class="btn btn-xs btn-outline-danger">
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