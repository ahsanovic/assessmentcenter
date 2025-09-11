<div>
    <x-breadcrumb :breadcrumbs="[
        ['url' => route('admin.dashboard'), 'title' => 'Dashboard'],
        ['url' => null, 'title' => 'Data Tes Sedang Berlangsung']
    ]" />
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">Data Tes Intelektual (Sedang Berlangsung)</h6>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th class="text-wrap">Nama Event</th>
                                    <th>Jumlah Peserta</th>
                                    <th>Sub Tes 1</th>
                                    <th>Sub Tes 2</th>
                                    <th>Sub Tes 3</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $index => $item)
                                    <tr>
                                        <td>{{ $data->firstItem() + $index }}</td>
                                        <td class="text-wrap">{{ $item->nama_event }}</td>
                                        <td>{{ $item->jumlah_peserta }}</td>
                                        <td>
                                            <a class="btn btn-xs btn-warning {{ $item->subtes1_berlangsung_count == 0 ? 'disabled' : '' }}" wire:navigate
                                                href="{{ route('admin.tes-berlangsung.intelektual.show-peserta-subtes-1', ['idEvent' => $item->id]) }}">
                                                    {{ $item->subtes1_berlangsung_count }} orang
                                            </a>
                                        </td>
                                        <td>
                                            <a class="btn btn-xs btn-warning {{ $item->subtes2_berlangsung_count == 0 ? 'disabled' : '' }}" wire:navigate
                                                href="{{ route('admin.tes-berlangsung.intelektual.show-peserta-subtes-2', ['idEvent' => $item->id]) }}">
                                                    {{ $item->subtes2_berlangsung_count }} orang
                                            </a>
                                        </td>
                                        <td>
                                            <a class="btn btn-xs btn-warning {{ $item->subtes3_berlangsung_count == 0 ? 'disabled' : '' }}" wire:navigate
                                                href="{{ route('admin.tes-berlangsung.intelektual.show-peserta-subtes-3', ['idEvent' => $item->id]) }}">
                                                    {{ $item->subtes3_berlangsung_count }} orang
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
