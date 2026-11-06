<div>
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

    @if ($aspekTabs === [])
        <div class="alert alert-danger py-2 small mb-3">
            Belum ada jawaban peserta pada event ini. Reliabilitas dan keputusan butir belum dapat dihitung.
        </div>
    @else
        <ul class="nav nav-tabs nav-tabs-line mb-3 flex-wrap" role="tablist">
            @foreach ($aspekTabs as $tab)
                <li class="nav-item" wire:key="tab-pspk-{{ $tab['kode'] }}">
                    <button type="button"
                        class="nav-link {{ $activeKode === $tab['kode'] ? 'active' : '' }}"
                        wire:click="setAspek('{{ $tab['kode'] }}')">
                        {{ $tab['label'] }}
                    </button>
                </li>
            @endforeach
        </ul>

        <h6 class="fw-semibold mb-2">Kesimpulan uji butir — {{ $activeAspek['label'] }}</h6>

        @php $conclusion = $activeAspek['conclusion']; @endphp

        @if ($conclusion['n_uji'] < 3)
            <div class="alert alert-danger py-2 small mb-3">
                Belum cukup peserta yang menjawab seluruh butir aspek. Reliabilitas dan keputusan butir belum dapat dihitung.
            </div>
        @elseif ($conclusion['indikatif'])
            <div class="alert alert-danger py-2 small mb-3">
                N uji = {{ $conclusion['n_uji'] }}, di bawah 30. Angka reliabilitas dan keputusan butir bersifat indikatif, belum stabil untuk menggugurkan soal.
            </div>
        @endif

        <div class="row g-2 mb-3" wire:key="kesimpulan-pspk-{{ $activeKode }}">
            <div class="col-6 col-lg-3">
                <div class="border rounded bg-white h-100 p-3">
                    <div class="small text-muted text-uppercase fw-semibold">Reliabilitas aspek</div>
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
                <div class="col-6 col-lg-3" wire:key="kartu-pspk-{{ $activeKode }}-{{ $key }}">
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
            r memakai peserta yang menjawab semua butir aspek yang diterimanya. Butir kosong tidak dihitung sebagai salah.
            @if ($skorModel === 'kunci_jawaban')
                P di atas 0,70 mudah, 0,30 sampai 0,70 sedang, di bawah 0,30 sukar. Bintang pada persentase menandai kunci.
                Skor laporan level ini memberi 5 untuk jawaban benar dan 1 untuk jawaban salah. α dan r dihitung dari pola benar-salah karena transformasi itu tidak mengubah keduanya.
            @else
                P adalah rata-rata poin dibagi poin tertinggi opsi. Di atas 0,70 mudah, 0,30 sampai 0,70 sedang, di bawah 0,30 sukar. Bintang menandai opsi bernilai tertinggi.
                α dan r memakai poin opsi, sama seperti skor aspek pada laporan.
                @if ($showJenis)
                    Ankas dan SJT dihitung bersama pada aspek yang sama.
                @endif
            @endif
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

        <h6 class="fw-semibold mb-2">Analisa butir — {{ $activeAspek['label'] }}</h6>

        @php
            $rowsAnalisa = collect($activeAspek['items']);
            if ($filterKeputusan !== 'semua') {
                $rowsAnalisa = $rowsAnalisa->where('keputusan_key', $filterKeputusan)->values();
            }
            $columnCount = 11 + count($optionLetters) + ($showJenis ? 1 : 0);
        @endphp

        <div class="table-responsive mb-4">
            <table class="table table-sm table-hover align-middle shadow-sm border rounded mb-0">
                <thead class="table-light border-bottom">
                    <tr>
                        <th class="text-center" style="width: 40px;">#</th>
                        <th style="min-width: 260px;">Soal</th>
                        <th style="min-width: 140px;">{{ $skorModel === 'kunci_jawaban' ? 'Kunci' : 'Opsi tertinggi' }}</th>
                        @if ($showJenis)
                            <th class="text-center">Jenis</th>
                        @endif
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
                        <tr wire:key="analisa-pspk-{{ $activeKode }}-{{ $item['soal_id'] }}">
                            <td class="text-center fw-bold text-secondary">{{ $item['nomor'] }}</td>
                            <td class="small text-wrap" title="{{ $item['pertanyaan'] }}">{{ \Illuminate\Support\Str::limit($item['pertanyaan'], 140) }}</td>
                            <td class="small text-wrap" title="{{ $item['kunci'] }}">{{ \Illuminate\Support\Str::limit($item['kunci'], 80) }}</td>
                            @if ($showJenis)
                                <td class="text-center">
                                    @if ($item['jenis_label'] === 'Ankas')
                                        <span class="badge bg-info-subtle text-info border">Ankas</span>
                                    @elseif ($item['jenis_label'] === 'SJT')
                                        <span class="badge border" style="background:#f3e8ff;color:#7c3aed;">SJT</span>
                                    @else
                                        -
                                    @endif
                                </td>
                            @endif
                            <td class="text-center">{{ $item['n_peserta'] }}</td>
                            <td class="text-center">{{ $item['n_terjawab'] }}</td>
                            <td class="text-center">{{ $item['n_uji_butir'] }}</td>
                            @foreach ($optionLetters as $letter)
                                <td class="text-center small {{ in_array($letter, $item['kunci_letters'], true) ? 'fw-bold text-success' : '' }}">
                                    {{ $item['percentages'][$letter] ?? 0 }}%
                                    @if (in_array($letter, $item['kunci_letters'], true))
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
                            <td colspan="{{ $columnCount }}" class="text-center text-muted py-4">
                                @if ($filterKeputusan !== 'semua')
                                    Tidak ada butir dengan status ini.
                                @else
                                    Belum ada data ujian untuk aspek ini.
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @endif
</div>
