<div>
    <x-breadcrumb :breadcrumbs="[
        ['url' => route('admin.dashboard'), 'title' => 'Dashboard'],
        ['url' => route('admin.jawaban-peserta.literasi-digital'), 'title' => 'Jawaban Peserta — Tes Literasi Digital'],
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
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle">Literasi Digital</span>
                                <span class="badge bg-light text-dark border">
                                    <i data-feather="calendar" style="width:12px;height:12px;" class="me-1"></i>
                                    {{ $event->tgl_mulai }}
                                </span>
                                @if ($event->metodeTes)
                                    <span class="badge bg-secondary-subtle text-secondary border">{{ $event->metodeTes->metode_tes ?? 'Literasi Digital' }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="d-flex flex-wrap gap-2 align-items-center">
                            <span class="badge rounded-pill bg-success-subtle text-success border border-success-subtle px-3 py-2">
                                <i data-feather="check-circle" style="width:14px;height:14px;" class="me-1"></i> Hijau = benar
                            </span>
                            <span class="badge rounded-pill bg-danger-subtle text-danger border border-danger-subtle px-3 py-2">
                                <i data-feather="x-circle" style="width:14px;height:14px;" class="me-1"></i> Merah = salah
                            </span>
                        </div>
                    </div>

                    <div class="border rounded bg-white p-3 mb-3">
                        <div class="d-flex flex-wrap align-items-start justify-content-between gap-3">
                            <div>
                                <div class="small text-muted text-uppercase fw-semibold">Reliabilitas tes</div>
                                <div class="d-flex flex-wrap align-items-center gap-2 mt-1">
                                    <span class="fs-3 fw-semibold lh-sm">
                                        {{ $stratified['alpha'] !== null ? number_format($stratified['alpha'], 2) : '–' }}
                                    </span>
                                    <span class="badge {{ match ($stratified['key']) {
                                        'reliabel' => 'bg-success',
                                        'cukup' => 'bg-primary',
                                        'rendah' => 'bg-danger',
                                        default => 'bg-secondary',
                                    } }}">{{ $stratified['label'] }}</span>
                                </div>
                                <div class="small text-muted mt-2">α berlapis · N uji {{ $stratified['n_uji'] }}</div>
                            </div>
                            <p class="small text-muted mb-0" style="max-width: 680px;">{{ $stratified['kalimat'] }}</p>
                        </div>
                    </div>

                    <ul class="nav nav-tabs nav-tabs-line mb-3 flex-wrap" role="tablist">
                        @foreach ($pillarTabs as $tab)
                            <li class="nav-item" wire:key="tab-literasi-{{ $tab['kode'] }}">
                                <button type="button"
                                    class="nav-link {{ $pilar === $tab['kode'] ? 'active' : '' }}"
                                    wire:click="setPilar('{{ $tab['kode'] }}')">
                                    {{ $tab['label'] }}
                                </button>
                            </li>
                        @endforeach
                    </ul>

                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-2">
                        <h6 class="fw-semibold mb-0">Kesimpulan uji butir — {{ $activePillar['label'] }}</h6>
                        <button type="button" wire:click="downloadExcel" wire:loading.attr="disabled"
                            class="btn btn-sm btn-success btn-icon-text">
                            <i class="btn-icon-prepend" data-feather="download"></i>
                            <span wire:loading.remove wire:target="downloadExcel">Download Excel</span>
                            <span wire:loading wire:target="downloadExcel">Mengunduh...</span>
                        </button>
                    </div>

                    @php $conclusion = $activePillar['conclusion']; @endphp

                    @if ($conclusion['n_uji'] < 3)
                        <div class="alert alert-danger py-2 small mb-3">
                            Belum cukup peserta yang menjawab seluruh butir pilar. Reliabilitas dan keputusan butir belum dapat dihitung.
                        </div>
                    @elseif ($conclusion['indikatif'])
                        <div class="alert alert-danger py-2 small mb-3">
                            N uji = {{ $conclusion['n_uji'] }}, di bawah 30. Angka reliabilitas dan keputusan butir bersifat indikatif, belum stabil untuk menggugurkan soal.
                        </div>
                    @endif

                    <div class="row g-2 mb-3" wire:key="kesimpulan-{{ $pilar }}">
                        <div class="col-6 col-lg-3">
                            <div class="border rounded bg-white h-100 p-3">
                                <div class="small text-muted text-uppercase fw-semibold">Reliabilitas pilar</div>
                                <div class="fs-3 fw-semibold lh-sm mt-1">
                                    {{ $conclusion['alpha'] !== null ? number_format($conclusion['alpha'], 2) : '–' }}
                                </div>
                                <span class="badge mt-2 {{ match ($conclusion['reliabilitas_key']) {
                                    'reliabel' => 'bg-success',
                                    'cukup' => 'bg-primary',
                                    'rendah' => 'bg-danger',
                                    default => 'bg-secondary',
                                } }}">{{ $conclusion['reliabilitas_label'] }}</span>
                                <div class="small text-muted mt-2">
                                    Cronbach's α
                                    @if ($conclusion['r_tabel'] !== null)
                                        · r tabel {{ number_format($conclusion['r_tabel'], 3) }}
                                    @endif
                                    · N uji {{ $conclusion['n_uji'] }}
                                    @if ($conclusion['alpha'] !== null)
                                        · {{ $conclusion['n_butir_alpha'] }} butir
                                    @endif
                                </div>
                            </div>
                        </div>
                        @foreach ([
                            'layak' => ['label' => 'Layak', 'hint' => 'Dipertahankan', 'tone' => 'success'],
                            'ditinjau' => ['label' => 'Ditinjau', 'hint' => 'Perlu dicek', 'tone' => 'warning'],
                            'lemah' => ['label' => 'Lemah', 'hint' => 'Prioritas revisi', 'tone' => 'danger'],
                        ] as $key => $meta)
                            <div class="col-6 col-lg-3" wire:key="kartu-{{ $pilar }}-{{ $key }}">
                                <button type="button"
                                    class="btn text-start border rounded bg-white h-100 p-3 w-100 {{ $filterKeputusan === $key ? 'border-'.$meta['tone'].' border-2' : '' }}"
                                    wire:click="setFilterKeputusan('{{ $key }}')">
                                    <div class="small text-muted text-uppercase fw-semibold">{{ $meta['label'] }}</div>
                                    <div class="fs-3 fw-semibold lh-sm mt-1 {{ $meta['tone'] === 'warning' ? 'text-warning-emphasis' : 'text-'.$meta['tone'] }}">{{ $conclusion['counts'][$key] }}</div>
                                    <div class="small text-muted mt-2">{{ $meta['hint'] }} · dari {{ $conclusion['n_butir'] }} butir</div>
                                </button>
                            </div>
                        @endforeach
                    </div>

                    <p class="mb-1">{{ $conclusion['kalimat'] }}</p>
                    <p class="text-muted small mb-3">
                        Layak bila r ≥ 0,30 dan r lebih besar dari r tabel. Ditinjau bila daya beda sedang atau belum signifikan. Lemah bila r di bawah 0,20, termasuk korelasi negatif.
                        r memakai peserta yang menjawab semua butir pilar yang diterimanya. Butir kosong tidak dihitung sebagai salah.
                        P di atas 0,70 mudah, 0,30 sampai 0,70 sedang, di bawah 0,30 sukar. Bintang pada persentase menandai kunci.
                    </p>

                    <div class="d-flex flex-wrap gap-2 mb-3">
                        @foreach (['semua' => 'Semua butir', 'layak' => 'Layak', 'ditinjau' => 'Ditinjau', 'lemah' => 'Lemah'] as $key => $label)
                            <button type="button"
                                class="btn btn-sm {{ $filterKeputusan === $key ? 'btn-dark' : 'btn-outline-secondary' }}"
                                wire:click="setFilterKeputusan('{{ $key }}')">
                                {{ $label }}
                            </button>
                        @endforeach
                    </div>

                    <h6 class="fw-semibold mb-2">Analisa butir — {{ $activePillar['label'] }}</h6>

                    @php
                        $rowsAnalisa = collect($activePillar['items']);
                        if ($filterKeputusan !== 'semua') {
                            $rowsAnalisa = $rowsAnalisa->where('keputusan_key', $filterKeputusan)->values();
                        }
                    @endphp

                    <div class="table-responsive mb-4">
                        <table class="table table-sm table-hover align-middle shadow-sm border rounded mb-0">
                            <thead class="table-light border-bottom">
                                <tr>
                                    <th class="text-center" style="width: 40px;">#</th>
                                    <th style="min-width: 260px;">Soal</th>
                                    <th style="min-width: 140px;">Kunci</th>
                                    <th class="text-center">N</th>
                                    <th class="text-center">Terjawab</th>
                                    <th class="text-center" title="Peserta yang masuk hitungan r">N uji</th>
                                    @foreach ($optionLetters as $letter)
                                        <th class="text-center" style="width: 52px;">% {{ $letter }}</th>
                                    @endforeach
                                    <th class="text-center">P</th>
                                    <th class="text-center">Kesukaran</th>
                                    <th class="text-center">r</th>
                                    <th class="text-center">r tabel</th>
                                    <th class="text-center" style="min-width: 120px;">Keputusan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($rowsAnalisa as $item)
                                    <tr wire:key="analisa-{{ $pilar }}-{{ $item['soal_id'] }}">
                                        <td class="text-center fw-bold text-secondary">{{ $item['nomor'] }}</td>
                                        <td class="small text-wrap" title="{{ $item['pertanyaan'] }}">{{ \Illuminate\Support\Str::limit($item['pertanyaan'], 140) }}</td>
                                        <td class="small text-wrap" title="{{ $item['kunci'] }}">{{ \Illuminate\Support\Str::limit($item['kunci'], 80) }}</td>
                                        <td class="text-center">{{ $item['n_peserta'] }}</td>
                                        <td class="text-center">{{ $item['n_terjawab'] }}</td>
                                        <td class="text-center">{{ $item['n_uji_butir'] }}</td>
                                        @foreach ($optionLetters as $letter)
                                            <td class="text-center small {{ ($item['kunci_letter'] ?? null) === $letter ? 'fw-bold text-success' : '' }}">
                                                {{ $item['percentages'][$letter] ?? 0 }}%
                                                @if (($item['kunci_letter'] ?? null) === $letter)
                                                    *
                                                @endif
                                            </td>
                                        @endforeach
                                        <td class="text-center">{{ $item['p_label'] }}</td>
                                        <td class="text-center">
                                            @if ($item['kesukaran_key'] === 'none')
                                                -
                                            @else
                                                <span class="badge {{ match ($item['kesukaran_key']) {
                                                    'mudah' => 'bg-primary',
                                                    'sukar' => 'bg-dark',
                                                    default => 'bg-secondary',
                                                } }}">{{ $item['kesukaran_label'] }}</span>
                                            @endif
                                        </td>
                                        <td class="text-center font-monospace small {{ match ($item['keputusan_key']) {
                                            'layak' => 'text-success fw-semibold',
                                            'ditinjau' => 'text-warning-emphasis fw-semibold',
                                            'lemah' => 'text-danger fw-semibold',
                                            default => 'text-muted',
                                        } }}">
                                            {{ $item['r_item_total'] !== null ? number_format($item['r_item_total'], 3) : '-' }}
                                        </td>
                                        <td class="text-center font-monospace small">
                                            {{ $item['r_tabel'] !== null ? number_format($item['r_tabel'], 3) : '-' }}
                                        </td>
                                        <td class="text-center">
                                            <span class="badge {{ match ($item['keputusan_key']) {
                                                'layak' => 'bg-success',
                                                'ditinjau' => 'bg-warning text-dark',
                                                'lemah' => 'bg-danger',
                                                default => 'bg-secondary',
                                            } }}">{{ $item['keputusan_label'] }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ 11 + count($optionLetters) }}" class="text-center text-muted py-4">
                                            @if ($filterKeputusan !== 'semua')
                                                Tidak ada butir dengan status ini.
                                            @else
                                                Belum ada data ujian untuk pilar ini.
                                            @endif
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <h6 class="fw-semibold mb-2">Jawaban per peserta</h6>
                    <p class="text-muted small">Analisa butir memakai seluruh peserta event. Lembar jawaban pada Excel mengikuti pencarian yang sedang aktif.</p>

                    <x-monitoring.filter-panel>
                        <div class="row g-2 align-items-end">
                            <div class="col-md-6 col-lg-5">
                                <label class="form-label small text-muted mb-1">Cari Peserta</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white"><i data-feather="search" style="width:16px;height:16px;"></i></span>
                                    <input wire:model.live.debounce.300ms="search" class="form-control" placeholder="Nama atau NIP..." autocomplete="off">
                                </div>
                            </div>
                            <div class="col-md-3 col-lg-2">
                                <x-btn-reset :text="'Reset'" />
                            </div>
                        </div>
                    </x-monitoring.filter-panel>

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
                                    @endphp
                                    <tr wire:key="ujian-literasi-{{ $ujian->id }}">
                                        <td class="text-center text-secondary fw-bold">{{ $data->firstItem() + $index }}</td>
                                        <td class="text-nowrap font-monospace small">{{ $peserta?->nip ?? '-' }}</td>
                                        <td class="fw-medium">{{ $peserta?->nama ?? '-' }}</td>
                                        <td class="text-center">
                                            @if ($ujian->is_finished === 'true')
                                                <span class="badge bg-success-subtle text-success border">Selesai</span>
                                            @else
                                                <span class="badge bg-warning-subtle text-warning-emphasis border">Berlangsung</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <button type="button"
                                                class="btn btn-sm btn-outline-warning btn-icon rounded-circle border-0 shadow-sm"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modalJawaban{{ $ujian->id }}"
                                                title="Lihat jawaban ({{ $terjawab }}/{{ $totalSoal }})">
                                                <i class="link-icon" data-feather="eye"></i>
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
                            aria-labelledby="modalJawabanLabel{{ $ujian->id }}" aria-hidden="true" wire:ignore.self
                            wire:key="modal-jawaban-literasi-{{ $ujian->id }}">
                            <div class="modal-dialog modal-xl modal-dialog-scrollable">
                                <div class="modal-content">
                                    <div class="modal-header border-bottom">
                                        <div>
                                            <h5 class="modal-title mb-1" id="modalJawabanLabel{{ $ujian->id }}">
                                                {{ $peserta?->nama ?? '-' }}
                                            </h5>
                                            <div class="small text-muted">
                                                NIP: <span class="font-monospace">{{ $peserta?->nip ?? '-' }}</span>
                                            </div>
                                        </div>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                                    </div>
                                    <div class="modal-body bg-body-tertiary">
                                        @forelse ($rows as $row)
                                            @php
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
                                            @endphp
                                            <div class="card mb-3 border shadow-sm {{ $statusClass }}">
                                                <div class="card-body">
                                                    <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-2">
                                                        <span class="badge bg-dark rounded-pill">Soal {{ $row['nomor'] }}</span>
                                                        <div class="d-flex flex-wrap gap-1">
                                                            <span class="badge bg-light text-dark border">{{ $row['pilar'] }}</span>
                                                            @if ($row['status'] === 'correct')
                                                                <span class="badge bg-success">Benar</span>
                                                            @elseif ($row['status'] === 'wrong')
                                                                <span class="badge bg-danger">Salah</span>
                                                            @elseif ($row['status'] === 'unanswered')
                                                                <span class="badge bg-secondary">Belum dijawab</span>
                                                            @else
                                                                <span class="badge bg-secondary">Tidak ada kunci</span>
                                                            @endif
                                                        </div>
                                                    </div>

                                                    <p class="mb-3 fw-medium text-wrap">{{ $row['pertanyaan'] }}</p>

                                                    <div class="row g-3">
                                                        <div class="col-md-6">
                                                            <div class="p-3 rounded border bg-white h-100">
                                                                <div class="small text-muted text-uppercase fw-semibold mb-1">Jawaban Peserta</div>
                                                                <div class="fw-semibold {{ $textClass }}">{{ $row['jawaban_peserta'] }}</div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="p-3 rounded border bg-white h-100">
                                                                <div class="small text-muted text-uppercase fw-semibold mb-1">Kunci Jawaban</div>
                                                                <div class="fw-semibold">{{ $row['kunci'] ?? '-' }}</div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="mt-3 p-2 rounded bg-light border">
                                                        <span class="small text-muted fw-semibold">Skor:</span>
                                                        <span class="small ms-1">{{ $row['skor_label'] }}</span>
                                                    </div>
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
