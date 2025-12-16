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
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th style="width: 5%">#</th>
                                    <th style="width: 20%">Nama Peserta</th>
                                    <th>Jawaban Responden</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $index => $item)
                                    <tr>
                                        <td>{{ $data->firstItem() + $index }}</td>
                                        <td>{{ $item->nama }}</td>
                                        <td class="text-wrap">
                                            @php
                                                $jawaban = $item->jawabanResponden->first();
                                                $kuesioner_id = explode(',', $jawaban->kuesioner_id ?? '');
                                                $skor = explode(',', $jawaban->skor ?? '');
                                            @endphp
                                            @foreach ($kuesioner_id as $i => $id)
                                                <p><strong>Pertanyaan:</strong> {{ $pertanyaan[$id] ?? '-' }}</p>
                                                <p>
                                                    <strong>Skor:</strong>
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
                                                    @endswitch
                                                </p>
                                                <hr>
                                            @endforeach
                                            @if (!empty($jawaban->jawaban_esai))
                                                <p><strong>Kritik & Saran:</strong> {{ $jawaban->jawaban_esai }}</p>
                                            @endif
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
