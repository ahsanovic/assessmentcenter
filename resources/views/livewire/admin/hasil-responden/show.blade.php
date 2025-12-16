<div>
    <x-breadcrumb :breadcrumbs="[
        ['url' => route('admin.dashboard'), 'title' => 'Dashboard'],
        ['url' => route('admin.hasil-responden'), 'title' => 'Hasil Responden'],
        ['url' => null, 'title' => 'Detail Hasil Responden']
    ]" />
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="card mt-4 mb-4 bg-light-subtle">
                        <div class="card-body">
                            <h6 class="text-danger" wire:ignore><i class="link-icon" data-feather="filter"></i> Filter</h6>
                            <div class="row mt-2">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <div class="input-group" wire:ignore>
                                            <span class="input-group-text bg-white"><i data-feather="search"></i></span>
                                            <input wire:model.live.debounce="search" class="form-control" placeholder="cari peserta...">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <x-btn-reset :text="'Reset'" />
                                </div>
                                <div class="col-md-4 text-end">
                                    <button wire:click="downloadExcel" wire:loading.attr="disabled" class="btn btn-success btn-icon-text">
                                        <i class="btn-icon-prepend" data-feather="download"></i>
                                        <span wire:loading.remove wire:target="downloadExcel">Download Semua Jawaban</span>
                                        <span wire:loading wire:target="downloadExcel">Downloading...</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th style="width: 5%">#</th>
                                    <th style="width: 30%">Nama Peserta</th>
                                    <th style="width: 20%">Jawaban Responden</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $index => $item)
                                    <tr>
                                        <td>{{ $data->firstItem() + $index }}</td>
                                        <td>{{ $item->nama }}</td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalJawaban{{ $item->id }}">
                                                <i class="link-icon" data-feather="eye" style="width: 14px; height: 14px;"></i> Lihat Jawaban
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        {{-- Modal Jawaban Responden --}}
                        @foreach ($data as $item)
                            @php
                                $jawaban = $item->jawabanResponden->first();
                                $kuesioner_id = explode(',', $jawaban->kuesioner_id ?? '');
                                $skor = explode(',', $jawaban->skor ?? '');
                            @endphp
                            <div class="modal fade" id="modalJawaban{{ $item->id }}" tabindex="-1" aria-labelledby="modalJawabanLabel{{ $item->id }}" aria-hidden="true" wire:ignore.self>
                                <div class="modal-dialog modal-lg modal-dialog-scrollable">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="modalJawabanLabel{{ $item->id }}">Jawaban: {{ $item->nama }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            @foreach ($kuesioner_id as $i => $id)
                                                <div class="card mb-3">
                                                    <div class="card-body">
                                                        <p class="mb-2"><strong>Pertanyaan:</strong></p>
                                                        <p class="text-muted">{{ $pertanyaan[$id] ?? '-' }}</p>
                                                        <p class="mb-0">
                                                            <strong>Skor:</strong>
                                                            <span class="badge bg-primary">
                                                                @switch($skor[$i])
                                                                    @case(1)
                                                                        Sangat Tidak Setuju
                                                                        @break
                                                                    @case(2)
                                                                        Tidak Setuju
                                                                        @break
                                                                    @case(3)
                                                                        Netral
                                                                        @break
                                                                    @case(4)
                                                                        Setuju
                                                                        @break
                                                                    @case(5)
                                                                        Sangat Setuju
                                                                        @break
                                                                    @default
                                                                        -
                                                                @endswitch
                                                            </span>
                                                        </p>
                                                    </div>
                                                </div>
                                            @endforeach
                                            @if (!empty($jawaban->jawaban_esai))
                                                <div class="card border-warning">
                                                    <div class="card-body">
                                                        <p class="mb-2"><strong>Kritik & Saran:</strong></p>
                                                        <p class="text-muted mb-0">{{ $jawaban->jawaban_esai }}</p>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-sm btn-secondary btn-icon-text" data-bs-dismiss="modal">
                                                <i class="btn-icon-prepend" data-feather="x"></i>
                                                Tutup
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>        
        </div>
        <x-pagination :items="$data" />
    </div>
</div>
