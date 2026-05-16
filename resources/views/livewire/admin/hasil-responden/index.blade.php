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
                            <div class="row align-items-start gy-2 gx-2 mt-1">
                                <div class="col-xl-4 col-lg-6 col-md-12">
                                    <div class="input-group input-group-sm" wire:ignore>
                                        <span class="input-group-text bg-white"><i data-feather="search"></i></span>
                                        <input wire:model.live.debounce="search" class="form-control" placeholder="cari event...">
                                    </div>
                                    <div class="hasil-responden-total-stat d-inline-flex align-items-center rounded-2 border shadow-sm px-2 py-1 mt-2"
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
                                                <span class="text-muted"> · semua event</span>
                                            @endif
                                            <span class="fw-bold text-dark ms-1">{{ number_format($totalResponden ?? 0, 0, ',', '.') }}</span>
                                            <span class="text-muted"> orang</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-4 col-lg-6 col-md-12">
                                    <div class="input-group input-group-sm flatpickr" id="flatpickr-download-range" wire:ignore
                                        data-download-dari="{{ $downloadDari }}"
                                        data-download-sampai="{{ $downloadSampai }}">
                                        <input type="text" class="form-control flatpickr-input" placeholder="rentang tanggal unduhan"
                                            data-input readonly="readonly">
                                        <button type="button" class="input-group-text text-muted" id="btn-clear-download-range"
                                            title="Hapus tanggal terpilih">
                                            <i data-feather="x" style="width:14px;height:14px;"></i>
                                        </button>
                                        <span class="input-group-text input-group-addon" data-toggle="">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round"
                                                class="feather feather-calendar">
                                                <rect x="3" y="4" width="18" height="18" rx="2"
                                                    ry="2"></rect>
                                                <line x1="16" y1="2" x2="16" y2="6"></line>
                                                <line x1="8" y1="2" x2="8" y2="6"></line>
                                                <line x1="3" y1="10" x2="21" y2="10"></line>
                                            </svg>
                                        </span>
                                    </div>
                                    <div class="d-flex flex-wrap gap-1 mt-2">
                                        <button type="button"
                                            class="btn btn-xs {{ ($activeDownloadPreset ?? null) === 'today' ? 'btn-primary' : 'btn-outline-secondary' }}"
                                            wire:click="applyDownloadRangePreset('today')">Hari ini</button>
                                        <button type="button"
                                            class="btn btn-xs {{ ($activeDownloadPreset ?? null) === '7d' ? 'btn-primary' : 'btn-outline-secondary' }}"
                                            wire:click="applyDownloadRangePreset('7d')">7 hari</button>
                                        <button type="button"
                                            class="btn btn-xs {{ ($activeDownloadPreset ?? null) === '30d' ? 'btn-primary' : 'btn-outline-secondary' }}"
                                            wire:click="applyDownloadRangePreset('30d')">30 hari</button>
                                        <button type="button"
                                            class="btn btn-xs {{ ($activeDownloadPreset ?? null) === 'month' ? 'btn-primary' : 'btn-outline-secondary' }}"
                                            wire:click="applyDownloadRangePreset('month')">Bulan ini</button>
                                    </div>
                                </div>
                                <div class="col-xl-4 col-lg-12 col-md-12">
                                    <div class="d-flex gap-2 flex-wrap justify-content-xl-end justify-content-lg-start">
                                        <x-btn-reset :text="'Reset'" />
                                        <button type="button"
                                            title="Unduh laporan sebagai file Excel sesuai rentang tanggal"
                                            class="btn btn-success btn-sm btn-icon-text text-nowrap"
                                            wire:click="downloadKuesioner"
                                            wire:loading.attr="disabled"
                                            wire:target="downloadKuesioner"
                                            @disabled(!($downloadReady ?? false))>
                                            <i class="btn-icon-prepend" data-feather="download"></i>
                                            <span wire:loading.remove wire:target="downloadKuesioner">Download Excel</span>
                                            <span wire:loading wire:target="downloadKuesioner">Menyiapkan...</span>
                                        </button>
                                    </div>
                                    <div class="d-flex flex-wrap align-items-center gap-2 mt-2 justify-content-xl-end justify-content-lg-start">
                                            <span class="badge bg-light text-dark border">
                                                @if(!empty($downloadRangeLabel))
                                                    {{ $downloadRangeLabel }}
                                                @else
                                                    Belum pilih rentang
                                                @endif
                                            </span>
                                            <span class="small @if(($downloadReady ?? false)) text-success @else text-muted @endif">
                                                {{ $downloadStatusMessage ?? '' }}
                                            </span>
                                    </div>
                                    @error('downloadKuesioner')
                                        <div class="small text-danger mt-1 mb-0">{{ $message }}</div>
                                    @enderror
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
@push('js')
    @script
        <script>
            (function () {
                function parseDMY(s) {
                    const p = (s || '').split('-').map((n) => parseInt(n, 10));
                    if (p.length !== 3 || p.some((n) => Number.isNaN(n))) return null;
                    return new Date(p[2], p[1] - 1, p[0]);
                }

                function initFlatpickrDownloadRange() {
                    const wrap = document.getElementById('flatpickr-download-range');
                    if (!wrap || wrap._flatpickr) return;

                    const dari = wrap.getAttribute('data-download-dari') || '';
                    const sampai = wrap.getAttribute('data-download-sampai') || '';
                    const d1 = parseDMY(dari);
                    const d2 = parseDMY(sampai);
                    const defaultDate = d1 && d2 ? [d1, d2] : undefined;

                    flatpickr(wrap, {
                        wrap: true,
                        mode: 'range',
                        dateFormat: 'd-m-Y',
                        locale: { rangeSeparator: ' s.d. ' },
                        allowInput: false,
                        defaultDate: defaultDate,
                        onChange: (selectedDates, _dateStr, instance) => {
                            if (selectedDates.length === 2) {
                                $wire.set('downloadDari', instance.formatDate(selectedDates[0], 'd-m-Y'));
                                $wire.set('downloadSampai', instance.formatDate(selectedDates[1], 'd-m-Y'));
                            } else if (selectedDates.length === 1) {
                                const singleDate = instance.formatDate(selectedDates[0], 'd-m-Y');
                                $wire.set('downloadDari', singleDate);
                                $wire.set('downloadSampai', singleDate);
                            } else if (selectedDates.length === 0) {
                                $wire.set('downloadDari', null);
                                $wire.set('downloadSampai', null);
                            }
                        },
                    });
                }

                document.addEventListener('DOMContentLoaded', initFlatpickrDownloadRange);
                document.addEventListener('livewire:navigated', initFlatpickrDownloadRange);

                document.addEventListener('livewire:initialized', () => {
                    initFlatpickrDownloadRange();
                    if (!window.__hasilRespondenDownloadRangeClearBound) {
                        window.__hasilRespondenDownloadRangeClearBound = true;
                        Livewire.on('flatpickr-download-range-clear', () => {
                            document.getElementById('flatpickr-download-range')?._flatpickr?.clear();
                        });
                    }
                    if (!window.__hasilRespondenDownloadRangeSetBound) {
                        window.__hasilRespondenDownloadRangeSetBound = true;
                        Livewire.on('flatpickr-download-range-set', (payload) => {
                            const p = Array.isArray(payload) ? payload[0] : payload;
                            const fromDate = parseDMY(p?.from);
                            const toDate = parseDMY(p?.to);
                            const picker = document.getElementById('flatpickr-download-range')?._flatpickr;
                            if (picker && fromDate && toDate) {
                                picker.setDate([fromDate, toDate], true);
                            }
                        });
                    }
                });

                document.addEventListener('click', (e) => {
                    const clearBtn = e.target.closest('#btn-clear-download-range');
                    if (!clearBtn) return;
                    document.getElementById('flatpickr-download-range')?._flatpickr?.clear();
                    $wire.set('downloadDari', null);
                    $wire.set('downloadSampai', null);
                });
            })();
        </script>
    @endscript
@endpush
