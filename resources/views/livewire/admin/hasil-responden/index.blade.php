<div>
    <x-breadcrumb :breadcrumbs="[
        ['url' => route('admin.dashboard'), 'title' => 'Dashboard'],
        ['url' => null, 'title' => 'Hasil Responden']
    ]" />
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="card mt-4 mb-4 bg-light-subtle">
                        <div class="card-body py-3">
                            <h6 class="text-danger mb-2" wire:ignore><i class="link-icon" data-feather="filter"></i> Filter</h6>
                            <div class="row align-items-center gy-2 gx-2 mt-1">
                                <div class="col-lg-6 col-md-12">
                                    <div class="input-group" wire:ignore>
                                        <span class="input-group-text bg-white"><i data-feather="search"></i></span>
                                        <input wire:model.live.debounce="search" class="form-control" placeholder="cari event...">
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <x-btn-reset :text="'Reset'" />
                                </div>
                                <div class="col-lg ms-lg-auto">
                                    <div class="d-flex justify-content-lg-end justify-content-start">
                                        <div class="hasil-responden-total-stat d-inline-flex align-items-center gap-2 rounded-3 border shadow-sm px-2 py-1"
                                            style="max-width: 100%; background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 48%, #ccfbf1 100%); border-color: rgba(13, 148, 136, 0.22) !important;">
                                            <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                                                style="width: 32px; height: 32px; background: rgba(13, 148, 136, 0.18);">
                                                <i data-feather="users" class="text-success" style="width: 15px; height: 15px;"></i>
                                            </div>
                                            <div class="text-start lh-sm min-w-0">
                                                <div class="d-flex flex-wrap align-items-baseline gap-x-2 gap-y-0">
                                                    <span class="small fw-semibold" style="color: #0f766e;">Total isi kuesioner</span>
                                                    @if(filled($search))
                                                        <span class="small text-muted">· sesuai pencarian</span>
                                                    @else
                                                        <span class="small text-muted">· {{ $metodeKuesionerLabel ?? 'Lainnya' }}</span>
                                                    @endif
                                                </div>
                                                <div class="d-flex align-items-baseline gap-1">
                                                    <span class="fs-6 fw-bold text-dark">{{ number_format($totalResponden ?? 0, 0, ',', '.') }}</span>
                                                    <span class="small text-muted">orang</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle shadow-sm border rounded" style="overflow:hidden;">
                            <thead class="table-light border-bottom">
                                <tr>
                                    <th class="text-center" style="width: 45px;">#</th>
                                    <th class="text-wrap">Event</th>
                                    <th class="text-nowrap">Metode Tes</th>
                                    <th>Jumlah dan Detail Responden</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $index => $item)
                                    <tr class="@if($loop->iteration % 2 == 1) bg-body @endif border-bottom">
                                        <td class="text-center text-secondary fw-bold">{{ $data->firstItem() + $index }}</td>
                                        <td class="text-wrap">{{ $item->nama_event }}</td>
                                        <td>{{ $item->metodeTes->metode_tes ?? '-' }}</td>
                                        <td>
                                            <a class="btn btn-xs btn-warning" wire:navigate
                                                href="{{ route('admin.hasil-responden.show', ['idEvent' => $item->id ]) }}">
                                                    {{ $item->jawaban_responden_count ?? 0 }} orang
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach

                                @if($data->count() === 0)
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4">
                                            <i class="link-icon" data-feather="inbox" style="font-size: 24px; opacity: 0.7;"></i>
                                            <div class="mt-2 fw-semibold">Tidak ada data event...</div>
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
