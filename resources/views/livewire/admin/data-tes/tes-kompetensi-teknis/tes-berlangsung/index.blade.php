<div>
    <x-breadcrumb :breadcrumbs="[
        ['url' => route('admin.dashboard'), 'title' => 'Dashboard'],
        ['url' => null, 'title' => 'Tes Kompetensi Teknis Berlangsung']
    ]" />
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th class="text-wrap">Nama Event</th>
                                    <th class="text-wrap">Jumlah Peserta</th>
                                    <th class="text-wrap">Detail</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $index => $item)
                                    <tr>
                                        <td>{{ $data->firstItem() + $index }}</td>
                                        <td class="text-wrap">{{ $item->nama_event }}</td>
                                        <td>{{ $item->jumlah_peserta }}</td>
                                        <td>
                                            <a class="btn btn-xs btn-warning {{ $item->ujian_kompetensi_teknis_count == 0 ? 'disabled' : '' }}" wire:navigate
                                                href="{{ route('admin.tes-berlangsung.kompetensi-teknis.show-peserta', ['idEvent' => $item->id]) }}">
                                                    {{ $item->ujian_kompetensi_teknis_count }} orang
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
