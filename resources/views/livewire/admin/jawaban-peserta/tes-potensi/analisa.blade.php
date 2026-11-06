<div x-data="{ filter: 'semua' }">
    <h6 class="fw-semibold mb-2">Kesimpulan uji butir — {{ $subtesLabel }}</h6>

    @if ($conclusion['n_uji'] < 3)
        <div class="alert alert-danger py-2 small mb-3">
            Belum cukup peserta yang menjawab seluruh butir subtes. Reliabilitas dan keputusan butir belum dapat dihitung.
        </div>
    @elseif ($conclusion['indikatif'])
        <div class="alert alert-danger py-2 small mb-3">
            N uji = {{ $conclusion['n_uji'] }}, di bawah 30. Angka reliabilitas dan keputusan butir bersifat indikatif, belum stabil untuk menggugurkan soal.
        </div>
    @endif

    <div class="row g-2 mb-3">
        <div class="col-6 col-lg-3">
            <div class="border rounded bg-white h-100 p-3">
                <div class="small text-muted text-uppercase fw-semibold">Reliabilitas</div>
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
                </div>
            </div>
        </div>
        @foreach ([
            'layak' => ['label' => 'Layak', 'hint' => 'Dipertahankan', 'tone' => 'success'],
            'ditinjau' => ['label' => 'Ditinjau', 'hint' => 'Perlu dicek', 'tone' => 'warning'],
            'lemah' => ['label' => 'Lemah', 'hint' => 'Prioritas revisi', 'tone' => 'danger'],
        ] as $key => $meta)
            <div class="col-6 col-lg-3">
                <button type="button"
                    class="btn text-start border rounded bg-white h-100 p-3 w-100"
                    x-bind:class="filter === '{{ $key }}' ? 'border-{{ $meta['tone'] }} border-2' : ''"
                    x-on:click="filter = filter === '{{ $key }}' ? 'semua' : '{{ $key }}'">
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
        r dan keputusan memakai peserta yang menjawab semua butir.
    </p>

    <div class="d-flex flex-wrap gap-2 mb-3">
        @foreach (['semua' => 'Semua butir', 'layak' => 'Layak', 'ditinjau' => 'Ditinjau', 'lemah' => 'Lemah'] as $key => $label)
            <button type="button"
                class="btn btn-sm"
                x-bind:class="filter === '{{ $key }}' ? 'btn-dark' : 'btn-outline-secondary'"
                x-on:click="filter = '{{ $key }}'">
                {{ $label }}
            </button>
        @endforeach
    </div>

    <h6 class="fw-semibold mb-2">Analisa butir — {{ $subtesLabel }}</h6>

    <div class="table-responsive mb-4">
        <table class="table table-sm table-hover align-middle shadow-sm border rounded mb-0">
            <thead class="table-light border-bottom">
                <tr>
                    <th class="text-center" style="width: 40px;">#</th>
                    <th style="min-width: 160px;">Indikator</th>
                    <th style="min-width: 280px;">Soal</th>
                    <th style="min-width: 120px;">Skor opsi</th>
                    <th class="text-center">N</th>
                    <th class="text-center">Terjawab</th>
                    @foreach ($optionLetters as $letter)
                        <th class="text-center" style="width: 52px;">% {{ $letter }}</th>
                    @endforeach
                    <th class="text-center">Rata</th>
                    <th class="text-center">Max</th>
                    <th class="text-center">r</th>
                    <th class="text-center" style="min-width: 120px;">Keputusan</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($items as $item)
                    @php
                        $rClass = match ($item['keputusan_key']) {
                            'layak' => 'text-success fw-semibold',
                            'ditinjau' => 'text-warning-emphasis fw-semibold',
                            'lemah' => 'text-danger fw-semibold',
                            default => 'text-muted',
                        };
                        $badgeClass = match ($item['keputusan_key']) {
                            'layak' => 'bg-success',
                            'ditinjau' => 'bg-warning text-dark',
                            'lemah' => 'bg-danger',
                            default => 'bg-secondary',
                        };
                    @endphp
                    <tr x-show="filter === 'semua' || filter === '{{ $item['keputusan_key'] }}'">
                        <td class="text-center fw-bold text-secondary">{{ $item['nomor'] }}</td>
                        <td class="small">{{ Str::limit($item['indikator'], 80) }}</td>
                        <td class="small text-wrap">{{ Str::limit($item['pertanyaan'], 120) }}</td>
                        <td class="small">{{ $item['opsi_skor'] }}</td>
                        <td class="text-center">{{ $item['n_peserta'] }}</td>
                        <td class="text-center">{{ $item['n_terjawab'] }}</td>
                        @foreach ($optionLetters as $letter)
                            <td class="text-center small">{{ $item['percentages'][$letter] ?? 0 }}%</td>
                        @endforeach
                        <td class="text-center">{{ $item['rata_poin'] ?? '-' }}</td>
                        <td class="text-center">{{ $item['poin_max'] ?? '-' }}</td>
                        <td class="text-center font-monospace small {{ $rClass }}">{{ $item['r_item_total'] !== null ? number_format($item['r_item_total'], 3) : '-' }}</td>
                        <td class="text-center"><span class="badge {{ $badgeClass }}">{{ $item['keputusan_label'] }}</span></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ 10 + count($optionLetters) }}" class="text-center text-muted py-4">
                            Belum ada data ujian untuk subtes ini.
                        </td>
                    </tr>
                @endforelse
                <tr x-show="(filter === 'layak' && {{ (int) $conclusion['counts']['layak'] }} === 0) || (filter === 'ditinjau' && {{ (int) $conclusion['counts']['ditinjau'] }} === 0) || (filter === 'lemah' && {{ (int) $conclusion['counts']['lemah'] }} === 0)" x-cloak>
                    <td colspan="{{ 10 + count($optionLetters) }}" class="text-center text-muted py-4">Tidak ada butir dengan status ini.</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
