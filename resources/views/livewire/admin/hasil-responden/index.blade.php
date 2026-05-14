<div>
    <x-breadcrumb :breadcrumbs="[
        ['url' => route('admin.dashboard'), 'title' => 'Dashboard'],
        ['url' => null, 'title' => 'Hasil Responden']
    ]" />
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="card mt-3 mb-3 bg-light-subtle">
                        <div class="card-body py-2">
                            <h6 class="text-danger mb-1" wire:ignore><i class="link-icon" data-feather="filter"></i> Filter</h6>
                            <div class="row align-items-center gy-2 gx-2 mt-1">
                                <div class="col-xl-4 col-lg-12 col-md-12">
                                    <div class="input-group input-group-sm" wire:ignore>
                                        <span class="input-group-text bg-white"><i data-feather="search"></i></span>
                                        <input wire:model.live.debounce="search" class="form-control" placeholder="cari event...">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <x-btn-reset :text="'Reset'" />
                                </div>
                                <div class="col-12 col-xl ms-xl-auto mt-1 mt-xl-0">
                                    <div class="d-flex flex-column flex-xl-row flex-xl-nowrap justify-content-xl-end align-items-xl-center gap-2 gap-xl-3">
                                        <div class="rounded-2 bg-white shadow-sm border px-2 py-1 flex-grow-0" style="max-width: fit-content;">
                                            <label for="downloadPeriod" class="form-label visually-hidden">Rentang unduhan</label>
                                            <div class="d-flex flex-wrap align-items-center gap-2">
                                                <span class="small fw-semibold text-nowrap text-dark mb-0">Unduh</span>
                                                <span class="small text-muted text-nowrap mb-0 d-none d-sm-inline">
                                                    {{ $downloadSinceLabel ?? '' }}
                                                </span>
                                                <select id="downloadPeriod" class="form-select form-select-sm" wire:model.live="downloadPeriod" style="width: auto; min-width: 8.5rem; max-width: 11rem;">
                                                    <option value="1m">1 bulan</option>
                                                    <option value="3m">3 bulan</option>
                                                    <option value="6m">6 bulan</option>
                                                    <option value="1y">1 tahun</option>
                                                </select>
                                                <button type="button"
                                                    title="Unduh laporan sebagai file Excel sesuai periode"
                                                    class="btn btn-success btn-sm btn-icon-text text-nowrap"
                                                    wire:click="downloadKuesioner"
                                                    wire:loading.attr="disabled"
                                                    wire:target="downloadKuesioner"
                                                    @disabled(($downloadPreviewCount ?? 0) === 0)>
                                                    <i class="btn-icon-prepend" data-feather="download"></i>
                                                    <span wire:loading.remove wire:target="downloadKuesioner">Excel</span>
                                                    <span wire:loading wire:target="downloadKuesioner">Menyiapkan...</span>
                                                </button>
                                                @if(($downloadPreviewCount ?? 0) > 0)
                                                    <span class="small text-success text-nowrap mb-0">
                                                        {{ number_format($downloadPreviewCount, 0, ',', '.') }} jawaban
                                                    </span>
                                                @else
                                                    <span class="small text-muted text-nowrap mb-0 align-middle">
                                                        Tidak ada data pada periode ini
                                                    </span>
                                                @endif
                                            </div>
                                            @error('downloadKuesioner')
                                                <div class="small text-danger mt-1 mb-0">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="d-none d-xl-block flex-shrink-0 px-2" aria-hidden="true">
                                            <div class="rounded-pill mx-auto" style="width:3px;height:2rem;background:linear-gradient(180deg,transparent,rgba(13,148,136,.25),transparent);"></div>
                                        </div>

                                        <div class="hasil-responden-total-stat d-inline-flex align-items-center rounded-2 border shadow-sm px-2 py-1 align-self-xl-center flex-shrink-0"
                                            style="max-width: 100%; background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 48%, #ccfbf1 100%); border-color: rgba(13, 148, 136, 0.22) !important;">
                                            <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 me-2"
                                                style="width: 28px; height: 28px; background: rgba(13, 148, 136, 0.18);">
                                                <i data-feather="users" class="text-success" style="width: 14px; height: 14px;"></i>
                                            </div>
                                            <div class="text-start lh-1 small min-w-0">
                                                <span class="fw-semibold" style="color: #0f766e;">Total isi</span>
                                                @if(filled($search))
                                                    <span class="text-muted"> · pencarian</span>
                                                @else
                                                    <span class="text-muted"> · {{ $metodeKuesionerLabel ?? 'Lainnya' }}</span>
                                                @endif
                                                <span class="fw-bold text-dark ms-1">{{ number_format($totalResponden ?? 0, 0, ',', '.') }}</span>
                                                <span class="text-muted"> orang</span>
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
