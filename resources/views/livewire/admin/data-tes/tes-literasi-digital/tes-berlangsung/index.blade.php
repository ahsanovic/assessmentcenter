<div>
    <x-breadcrumb :breadcrumbs="[
        ['url' => route('admin.dashboard'), 'title' => 'Dashboard'],
        ['url' => null, 'title' => 'Monitoring Tes Literasi Digital']
    ]" />
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <x-monitoring.page-header
                        icon="activity"
                        color="primary"
                        title="Monitoring Tes Literasi Digital"
                        description="Pantau progres ujian literasi digital setiap peserta secara langsung."
                    />
                    <div class="table-responsive">
                        <table class="table ac-data-table table-hover align-middle" style="overflow:hidden;">
                            <thead class="table-light border-bottom">
                                <tr>
                                    <th class="text-center" style="width: 45px;">#</th>
                                    <th class="text-wrap ac-col-event-name">Nama Event</th>
                                    <th class="text-wrap">Peserta Terdaftar/Mulai Ujian</th>
                                    <th class="text-wrap">Detail</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $index => $item)
                                    <tr wire:key="monitoring-literasi-event-{{ $item->id }}" class="@if($loop->iteration % 2 == 1) bg-body @endif border-bottom">
                                        <td class="text-center text-secondary fw-bold">{{ $data->firstItem() + $index }}</td>
                                        <td class="text-wrap ac-col-event-name"><x-monitoring.event-name-cell :nama="$item->nama_event" :nama-grup="$item->eventGroup?->nama" /></td>
                                        <td><x-monitoring.peserta-terdaftar-mulai :terdaftar="$item->peserta_count" :mulai="$item->peserta_mulai_ujian_count" /></td>
                                        <td>
                                            <a class="btn btn-xs btn-warning {{ $item->ujian_literasi_digital_count == 0 ? 'disabled' : '' }}" wire:navigate
                                                href="{{ route('admin.tes-berlangsung.literasi-digital.show-peserta', ['idEvent' => $item->id]) }}">
                                                    {{ $item->ujian_literasi_digital_count }} orang
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach

                                @if ($data->count() === 0)
                                    <tr>
                                        <td colspan="9" class="text-center text-muted py-5 ac-empty-state">
                                            <i class="link-icon" data-feather="inbox" style="font-size: 24px; opacity: 0.7;"></i>
                                            <div class="mt-2 fw-semibold">Tidak ada data ujian literasi digital berlangsung...</div>
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
