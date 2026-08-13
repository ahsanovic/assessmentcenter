<div wire:poll.visible.30s>
    <x-breadcrumb :breadcrumbs="[
        ['url' => route('admin.dashboard'), 'title' => 'Dashboard'],
        ['url' => route('admin.tes-berlangsung'), 'title' => 'Monitoring Tes Potensi'],
        ['url' => null, 'title' => 'Peserta Tes Pengembangan Diri']
    ]" />
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <x-monitoring.event-header :nama="$event->nama_event" />
                    <x-monitoring.filter-panel>
                            <div class="row g-2 align-items-end">
                                <div class="col-sm-4">
                                    <div class="input-group">
                                        <span class="input-group-text bg-white"><i data-feather="search" style="width:16px;height:16px;"></i></span>
                                        <input wire:model.live.debounce="search" class="form-control" placeholder="cari peserta..." autocomplete="off">
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <select wire:model.live="filterSoalBelumDijawab" class="form-select">
                                        <option value="">Semua Peserta</option>
                                        <option value="ada">Ada Soal Belum Dijawab</option>
                                        <option value="semua_terjawab">Semua Soal Terjawab</option>
                                    </select>
                                </div>
                                <div class="col-sm-4">
                                    <x-btn-reset :text="'Reset'" />
                                </div>
                            </div>
                    </x-monitoring.filter-panel>
                    <div class="table-responsive">
                        <table class="table ac-data-table table-hover align-middle" style="overflow:hidden;">
                            <thead class="table-light border-bottom">
                                <tr>
                                    <th class="text-center" style="width: 45px;">#</th>
                                    <th>Nama Peserta</th>
                                    <th>Jabatan</th>
                                    <th>Instansi<br><small class="text-muted">Unit Kerja</small></th>
                                    <th>Mulai Tes</th>
                                    <th class="text-center">Jumlah Soal /<br><small class="text-muted">Soal Dijawab</small></th>
                                    <th class="text-center">Status Tes</th>
                                    <th class="text-center"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $index => $item)
                                    <tr class="@if($loop->iteration % 2 == 1) bg-body @endif border-bottom">
                                        <td class="text-center text-secondary fw-bold">{{ $data->firstItem() + $index }}</td>
                                        <td>
                                            <span class="fw-medium">{{ $item->nama }}</span><br>
                                            <span class="text-muted small">
                                            @if ($item->jenis_peserta_id == 1)
                                                <div class="fw-medium">{{ $item->nip }}</div>
                                                @if (!empty($item->golPangkat?->pangkat) && !empty($item->golPangkat?->golongan))
                                                    <span class="badge bg-secondary-subtle text-dark mt-1">
                                                        {{ $item->golPangkat->pangkat . ' - ' . $item->golPangkat->golongan }}
                                                    </span>
                                                @else
                                                    <span class="text-muted d-block mt-1"></span>
                                                @endif
                                            @elseif ($item->jenis_peserta_id == 2)
                                                <div class="fw-medium">{{ $item->nik }}</div>
                                            @endif
                                            </span>
                                        </td>
                                        <td class="text-wrap">
                                            <span class="badge bg-info-subtle text-dark fw-normal">{{ $item->jabatan }}</span>
                                        </td>
                                        <td class="text-wrap">
                                            <span class="fw-medium text-dark">{{ $item->instansi }}</span>
                                            <br>
                                            <span class="text-muted small">{{ $item->unit_kerja }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark border-1 border">
                                                {{ \Carbon\Carbon::parse($item->mulai_tes)->translatedFormat('d F Y') }}
                                                <span class="text-muted px-1">/</span>
                                                {{ \Carbon\Carbon::parse($item->mulai_tes)->translatedFormat('H:i:s') }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            @php
                                                $_totalSoal = count(explode(',', $item->soal_id));
                                                $_terjawab = collect(explode(',', $item->jawaban))
                                                    ->filter(fn($jawab) => $jawab !== '0' && $jawab !== 0 && $jawab !== null && $jawab !== '')
                                                    ->count();
                                            @endphp
                                            <x-monitoring.progress-answer :answered="$_terjawab" :total="$_totalSoal" />
                                        </td>
                                        <td class="text-center">
                                            @if ($item->is_finished == 'false')
                                                <span class="badge bg-danger">
                                                    ✖ Belum Selesai
                                                </span>
                                            @else
                                                <span class="badge bg-success">
                                                    ✔ Selesai
                                                </span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if ($item->is_finished == 'false')
                                                <x-table.btn-delete :id="$item->ujian_pengembangan_diri_id" />
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach

                                @if($data->count() === 0)
                                    <tr>
                                        <td colspan="9" class="text-center text-muted py-5 ac-empty-state">
                                            <i class="link-icon" data-feather="inbox" style="font-size: 24px; opacity: 0.7;"></i>
                                            <div class="mt-2 fw-semibold">Tidak ada data peserta...</div>
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