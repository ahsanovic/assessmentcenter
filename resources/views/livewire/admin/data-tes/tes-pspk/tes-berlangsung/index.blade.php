<div>
    <x-breadcrumb :breadcrumbs="[
        ['url' => route('admin.dashboard'), 'title' => 'Dashboard'],
        ['url' => null, 'title' => 'Monitoring Tes PSPK']
    ]" />
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <x-monitoring.page-header
                        icon="activity"
                        color="primary"
                        title="Monitoring Tes PSPK"
                        description="Pantau progres ujian PSPK setiap peserta secara langsung."
                    />
                    <div class="table-responsive">
                        <table class="table ac-data-table table-hover align-middle" style="overflow:hidden;">
                            <thead class="table-light border-bottom">
                                <tr>
                                    <th class="text-center" style="width: 45px;">#</th>
                                    <th class="text-wrap">Nama Event</th>
                                    <th class="text-wrap">Jumlah Peserta</th>
                                    <th class="text-wrap">Detail</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $index => $item)
                                    <tr>
                                        <td class="text-center text-secondary fw-bold">{{ $data->firstItem() + $index }}</td>
                                        <td class="text-wrap">{{ $item->nama_event }}</td>
                                        <td>{{ $item->jumlah_peserta }}</td>
                                        <td>
                                            <a class="btn btn-xs btn-warning {{ $item->ujian_pspk_count == 0 ? 'disabled' : '' }}" wire:navigate
                                                href="{{ route('admin.tes-berlangsung.pspk.show-peserta', ['idEvent' => $item->id]) }}">
                                                    {{ $item->ujian_pspk_count }} orang
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach

                                @if ($data->count() === 0)
                                    <tr>
                                        <td colspan="9" class="text-center text-muted py-5 ac-empty-state">
                                            <i class="link-icon" data-feather="inbox" style="font-size: 24px; opacity: 0.7;"></i>
                                            <div class="mt-2 fw-semibold">Tidak ada data ujian PSPK berlangsung...</div>
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
