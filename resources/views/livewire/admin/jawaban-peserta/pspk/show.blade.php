<div>
    <x-breadcrumb :breadcrumbs="[
        ['url' => route('admin.dashboard'), 'title' => 'Dashboard'],
        ['url' => route('admin.jawaban-peserta.pspk'), 'title' => 'Jawaban Peserta — Tes PSPK'],
        ['url' => null, 'title' => 'Detail Event']
    ]" />

    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex flex-wrap align-items-start justify-content-between gap-3 mb-3">
                        <div>
                            <h5 class="card-title mb-2">{{ $event->nama_event }}</h5>
                            <div class="d-flex flex-wrap gap-2">
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle">{{ $levelLabel }}</span>
                                <span class="badge bg-light text-dark border">
                                    <i data-feather="calendar" style="width:12px;height:12px;" class="me-1"></i>
                                    {{ $event->tgl_mulai }}
                                </span>
                                @if ($event->metodeTes)
                                    <span class="badge bg-secondary-subtle text-secondary border">{{ $event->metodeTes->metode_tes ?? 'PSPK' }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="d-flex flex-wrap gap-2 align-items-center">
                            <span class="badge rounded-pill bg-success-subtle text-success border border-success-subtle px-3 py-2">
                                <i data-feather="check-circle" style="width:14px;height:14px;" class="me-1"></i> Hijau = benar / skor tertinggi
                            </span>
                            <span class="badge rounded-pill bg-danger-subtle text-danger border border-danger-subtle px-3 py-2">
                                <i data-feather="x-circle" style="width:14px;height:14px;" class="me-1"></i> Merah = salah / skor bukan tertinggi
                            </span>
                        </div>
                    </div>

                    <div class="card mt-2 mb-4 bg-light-subtle border-0 shadow-sm">
                        <div class="card-body">
                            <div class="row g-2 align-items-end">
                                <div class="col-md-6 col-lg-5">
                                    <label class="form-label small text-muted mb-1">Cari Peserta</label>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text bg-white"><i data-feather="search" style="width:14px;height:14px;"></i></span>
                                        <input wire:model.live.debounce.300ms="search" class="form-control" placeholder="Nama atau NIP...">
                                    </div>
                                </div>
                                <div class="col-md-3 col-lg-2">
                                    <x-btn-reset :text="'Reset'" />
                                </div>
                                <div class="col-md-12 col-lg-5 text-lg-end">
                                    <button wire:click="downloadExcel" wire:loading.attr="disabled"
                                        class="btn btn-sm btn-success btn-icon-text">
                                        <i class="btn-icon-prepend" data-feather="download"></i>
                                        <span wire:loading.remove wire:target="downloadExcel">Download Excel</span>
                                        <span wire:loading wire:target="downloadExcel">Mengunduh...</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle shadow-sm border rounded mb-0">
                            <thead class="table-light border-bottom">
                                <tr>
                                    <th class="text-center" style="width: 45px;">#</th>
                                    <th>NIP</th>
                                    <th>Nama Peserta</th>
                                    <th class="text-center">Status Tes</th>
                                    <th class="text-center" style="width: 140px;">Jawaban</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($data as $index => $ujian)
                                    @php
                                        $peserta = $ujian->peserta;
                                        $rows = $jawabanByUjian[$ujian->id] ?? [];
                                        $totalSoal = count($rows);
                                        $terjawab = collect($rows)->where('status', '!=', 'unanswered')->count();
                                        $benar = collect($rows)->where('status', 'correct')->count();
                                        $salah = collect($rows)->where('status', 'wrong')->count();
                                    @endphp
                                    <tr>
                                        <td class="text-center text-secondary fw-bold">{{ $data->firstItem() + $index }}</td>
                                        <td class="text-nowrap font-monospace small">{{ $peserta->nip ?? '-' }}</td>
                                        <td class="fw-medium">{{ $peserta->nama }}</td>
                                        <td class="text-center">
                                            @if ($ujian->is_finished === 'true')
                                                <span class="badge bg-success-subtle text-success border">Selesai</span>
                                            @else
                                                <span class="badge bg-warning-subtle text-warning-emphasis border">Berlangsung</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <button type="button"
                                                class="btn btn-sm btn-outline-primary rounded-pill px-3"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modalJawaban{{ $ujian->id }}">
                                                Lihat ({{ $terjawab }}/{{ $totalSoal }})
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-5">
                                            <i class="link-icon" data-feather="users" style="font-size: 28px; opacity: 0.6;"></i>
                                            <div class="mt-2 fw-semibold">Tidak ada peserta ditemukan.</div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @foreach ($data as $ujian)
                        @php
                            $peserta = $ujian->peserta;
                            $rows = $jawabanByUjian[$ujian->id] ?? [];
                        @endphp
                        <div class="modal fade" id="modalJawaban{{ $ujian->id }}" tabindex="-1"
                            aria-labelledby="modalJawabanLabel{{ $ujian->id }}" aria-hidden="true" wire:ignore.self>
                            <div class="modal-dialog modal-xl modal-dialog-scrollable">
                                <div class="modal-content">
                                    <div class="modal-header border-bottom">
                                        <div>
                                            <h5 class="modal-title mb-1" id="modalJawabanLabel{{ $ujian->id }}">
                                                {{ $peserta->nama }}
                                            </h5>
                                            <div class="small text-muted">
                                                NIP: <span class="font-monospace">{{ $peserta->nip ?? '-' }}</span>
                                            </div>
                                        </div>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                                    </div>
                                    <div class="modal-body bg-body-tertiary">
                                        @forelse ($rows as $row)
                                            @php
                                                $isSkorOpsi = ($row['jenis_kunci'] ?? null) === 'skor_opsi';
                                                $isKunciJawaban = ($row['jenis_kunci'] ?? null) === 'kunci_jawaban';

                                                if ($isSkorOpsi && $row['status'] !== 'unanswered') {
                                                    $statusClass = $row['status'] === 'correct'
                                                        ? 'border-success bg-success-subtle'
                                                        : 'border-danger bg-danger-subtle';
                                                    $textClass = $row['status'] === 'correct' ? 'text-success' : 'text-danger';
                                                } elseif ($isKunciJawaban) {
                                                    $statusClass = match ($row['status']) {
                                                        'correct' => 'border-success bg-success-subtle',
                                                        'wrong' => 'border-danger bg-danger-subtle',
                                                        default => 'border-light bg-white',
                                                    };
                                                    $textClass = match ($row['status']) {
                                                        'correct' => 'text-success',
                                                        'wrong' => 'text-danger',
                                                        default => 'text-dark',
                                                    };
                                                } else {
                                                    $statusClass = 'border-light bg-white';
                                                    $textClass = 'text-dark';
                                                }
                                            @endphp
                                            <div class="card mb-3 border shadow-sm {{ $statusClass }}">
                                                <div class="card-body">
                                                    <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-2">
                                                        <span class="badge bg-dark rounded-pill">Soal {{ $row['nomor'] }}</span>
                                                        <div class="d-flex flex-wrap gap-1">
                                                            <span class="badge bg-light text-dark border">{{ $row['aspek'] }}</span>
                                                            @if ($isLevel34 && $row['jenis_soal'] === \App\Models\Pspk\SoalPspk::JENIS_ANKAS)
                                                                <span class="badge bg-info-subtle text-info border">Ankas</span>
                                                            @elseif ($isLevel34 && $row['jenis_soal'] === \App\Models\Pspk\SoalPspk::JENIS_SJT)
                                                                <span class="badge bg-purple-subtle text-purple border" style="background:#f3e8ff!important;color:#7c3aed!important;">SJT</span>
                                                            @endif
                                                            @if ($isKunciJawaban)
                                                                @if ($row['status'] === 'correct')
                                                                    <span class="badge bg-success">Benar</span>
                                                                @elseif ($row['status'] === 'wrong')
                                                                    <span class="badge bg-danger">Salah</span>
                                                                @elseif ($row['status'] === 'unanswered')
                                                                    <span class="badge bg-secondary">Belum dijawab</span>
                                                                @endif
                                                            @elseif ($isSkorOpsi)
                                                                @if ($row['status'] === 'unanswered')
                                                                    <span class="badge bg-secondary">Belum dijawab</span>
                                                                @else
                                                                    <span class="badge {{ $row['status'] === 'correct' ? 'bg-success' : 'bg-danger' }}">
                                                                        Skor: {{ $row['skor_jawaban'] }}
                                                                    </span>
                                                                @endif
                                                            @elseif ($row['status'] === 'unanswered')
                                                                <span class="badge bg-secondary">Belum dijawab</span>
                                                            @endif
                                                        </div>
                                                    </div>

                                                    <p class="mb-3 fw-medium">{{ $row['pertanyaan'] }}</p>

                                                    <div class="row g-3">
                                                        <div class="{{ $row['kunci'] ? 'col-md-6' : 'col-12' }}">
                                                            <div class="p-3 rounded border bg-white h-100">
                                                                <div class="small text-muted text-uppercase fw-semibold mb-1">Jawaban Peserta</div>
                                                                <div class="fw-semibold {{ $textClass }}">{{ $row['jawaban_peserta'] }}</div>
                                                            </div>
                                                        </div>
                                                        @if ($row['kunci'])
                                                            <div class="col-md-6">
                                                                <div class="p-3 rounded border bg-white h-100">
                                                                    <div class="small text-muted text-uppercase fw-semibold mb-1">Kunci Jawaban</div>
                                                                    <div class="fw-semibold {{ $isKunciJawaban ? $textClass : 'text-dark' }}">{{ $row['kunci'] }}</div>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    </div>

                                                    @if ($row['skor_opsi'])
                                                        <div class="mt-3 p-2 rounded bg-light border">
                                                            <span class="small text-muted fw-semibold">Skor per opsi:</span>
                                                            <span class="small ms-1">{{ $row['skor_opsi'] }}</span>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        @empty
                                            <div class="text-center text-muted py-4">Belum ada data jawaban.</div>
                                        @endforelse
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
        <x-pagination :items="$data" />
    </div>
</div>
