<div>
    <x-breadcrumb :breadcrumbs="[
        ['url' => route('admin.dashboard'), 'title' => 'Dashboard'],
        ['url' => null, 'title' => 'Tes Intelektual Berlangsung']
    ]" />
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle shadow-sm border rounded" style="overflow:hidden;">
                            <thead class="table-light border-bottom">
                                <tr>
                                    <th class="text-center" style="width: 45px;">#</th>
                                    <th class="text-wrap">Nama Event</th>
                                    <th>Jumlah Peserta</th>
                                    <th>Sub Tes 1</th>
                                    <th>Sub Tes 2</th>
                                    <th>Sub Tes 3</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $index => $item)
                                    <tr class="@if($loop->iteration % 2 == 1) bg-body @endif border-bottom">
                                        <td class="text-center text-secondary fw-bold">{{ $data->firstItem() + $index }}</td>
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
