<div>
    <x-breadcrumb :breadcrumbs="[
        ['url' => route('admin.dashboard'), 'title' => 'Dashboard'],
        ['url' => null, 'title' => 'Jawaban Peserta — Tes PSPK']
    ]" />

    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                        <div>
                            <h5 class="card-title mb-1">Jawaban Peserta — Tes PSPK</h5>
                            <p class="text-muted small mb-0">Pilih event untuk melihat detail jawaban setiap peserta.</p>
                        </div>
                    </div>

                    <div class="card mt-2 mb-4 bg-light-subtle border-0 shadow-sm">
                        <div class="card-body">
                            <h6 class="text-danger mb-3" wire:ignore>
                                <i class="link-icon" data-feather="filter"></i> Filter
                            </h6>
                            <div class="row g-2 align-items-end">
                                <div class="col-sm-6 col-md-3">
                                    <label class="form-label small text-muted mb-1">Level PSPK</label>
                                    <select wire:model.live="level_pspk" class="form-select form-select-sm" id="level-pspk">
                                        <option value="">Semua level</option>
                                        @foreach ($option_level_pspk as $key => $item)
                                            <option value="{{ $key }}">{{ $item }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-6 col-md-2">
                                    <label class="form-label small text-muted mb-1">Tanggal Tes</label>
                                    <div class="input-group input-group-sm flatpickr" id="flatpickr-date">
                                        <input type="text" wire:model.live="tgl_mulai"
                                            class="form-control flatpickr-input" placeholder="Pilih tanggal"
                                            data-input="" readonly="readonly">
                                        <span class="input-group-text input-group-addon" data-toggle="">
                                            <i data-feather="calendar" style="width:14px;height:14px;"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-md-3">
                                    <label class="form-label small text-muted mb-1">Cari Event</label>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text bg-white"><i data-feather="search" style="width:14px;height:14px;"></i></span>
                                        <input wire:model.live.debounce.300ms="search" class="form-control" placeholder="Nama event...">
                                    </div>
                                </div>
                                <div class="col-12 col-md-auto">
                                    <x-btn-reset :text="'Reset'" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle shadow-sm border rounded mb-0">
                            <thead class="table-light border-bottom">
                                <tr>
                                    <th class="text-center" style="width: 45px;">#</th>
                                    <th>Nama Event</th>
                                    <th>Level</th>
                                    <th>Tanggal Tes</th>
                                    <th class="text-center">Peserta</th>
                                    <th class="text-center" style="width: 120px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($data as $index => $item)
                                    @php
                                        $levelMap = [5 => 'Level 1', 6 => 'Level 2', 7 => 'Level 3', 8 => 'Level 4'];
                                        $levelName = $levelMap[$item->metode_tes_id] ?? '-';
                                    @endphp
                                    <tr>
                                        <td class="text-center text-secondary fw-bold">{{ $data->firstItem() + $index }}</td>
                                        <td class="text-wrap fw-medium">{{ $item->nama_event }}</td>
                                        <td>
                                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle">{{ $levelName }}</span>
                                        </td>
                                        <td class="text-nowrap">{{ $item->tgl_mulai }}</td>
                                        <td class="text-center">
                                            <span class="badge rounded-pill bg-secondary-subtle text-dark border">
                                                {{ $item->peserta_ujian_count ?? 0 }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <a class="btn btn-xs btn-primary btn-icon-text"
                                                wire:navigate
                                                href="{{ route('admin.jawaban-peserta.pspk.show', ['idEvent' => $item->id]) }}">
                                                <i class="btn-icon-prepend" data-feather="eye"></i>
                                                Lihat
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-5">
                                            <i class="link-icon" data-feather="inbox" style="font-size: 28px; opacity: 0.6;"></i>
                                            <div class="mt-2 fw-semibold">Tidak ada event dengan data jawaban.</div>
                                            <div class="small">Coba ubah filter atau pastikan peserta sudah mengerjakan tes.</div>
                                        </td>
                                    </tr>
                                @endforelse
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
    @script()
        <script>
            $(document).ready(function() {
                $('#event').select2({
                    width: '100%',
                    placeholder: 'Semua event'
                }).on('change', function(e) {
                    @this.set('event', $(this).val());
                });

                Livewire.on('reset-select2', () => {
                    $('#event').val(null).trigger('change');
                });
            });
        </script>
    @endscript
@endpush
