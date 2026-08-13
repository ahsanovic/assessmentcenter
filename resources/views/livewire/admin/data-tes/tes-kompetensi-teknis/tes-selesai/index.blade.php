<div>
    <x-breadcrumb :breadcrumbs="[
        ['url' => route('admin.dashboard'), 'title' => 'Dashboard'],
        ['url' => null, 'title' => 'Hasil Tes Kompetensi Teknis']
    ]" />
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <x-monitoring.page-header
                        icon="bar-chart-2"
                        color="success"
                        title="Hasil Tes Kompetensi Teknis"
                        description="Lihat dan unduh hasil ujian kompetensi teknis peserta per event."
                    />
                    <x-monitoring.filter-panel :badge="$data->total().' event'">
                            <div class="row g-2 align-items-end">
                                <div class="col-sm-3">
                                    <select wire:model.live="jabatan_diuji" class="form-select" id="jabatan-diuji">
                                        <option value="">pilih jabatan diuji</option>
                                        @foreach ($option_jabatan_diuji as $key => $item)
                                            <option value="{{ $key }}">{{ $item }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-2">
                                    <div class="input-group flatpickr" id="flatpickr-date">
                                        <input type="text" wire:model.live="tgl_mulai"
                                            class="form-control flatpickr-input" placeholder="tgl mulai pelaksanaan"
                                            data-input="" readonly="readonly">
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
                                </div>
                                <div class="col-sm-4">
                                    <div class="input-group">
                                        <span class="input-group-text bg-white"><i data-feather="search" style="width:16px;height:16px;"></i></span>
                                        <input wire:model.live.debounce.400ms="search" class="form-control" placeholder="Ketik nama event…" autocomplete="off">
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <x-btn-reset :text="'Reset'" />
                                </div>
                            </div>
                    </x-monitoring.filter-panel>
                    <div class="table-responsive">
                        <table class="table ac-data-table table-hover align-middle" style="overflow:hidden;">
                            <thead class="table-light border-bottom">
                                <tr>
                                    <th class="text-center" style="width: 45px;">#</th>
                                    <th>Nama Event</th>
                                    <th>Jumlah Peserta</th>
                                    <th>Detail</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $index => $item)
                                    <tr>
                                        <td class="text-center text-secondary fw-bold">{{ $data->firstItem() + $index }}</td>
                                        <td class="text-wrap">{{ $item->nama_event }}</td>
                                        <td>{{ $item->jumlah_peserta }}</td>
                                        <td>
                                            <a class="btn btn-xs btn-warning {{ $item->hasil_kompetensi_teknis_count == 0 ? 'disabled' : '' }}" wire:navigate
                                                href="{{ route('admin.tes-selesai.kompetensi-teknis.show-peserta', ['idEvent' => $item->id]) }}">
                                                    {{ $item->hasil_kompetensi_teknis_count ?? 0 }} orang
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
