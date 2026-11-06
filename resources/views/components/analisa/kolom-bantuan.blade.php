@props([
    'label',
    'kolom',
    'huruf' => null,
])

@php
    $huruf = $huruf ? strtoupper((string) $huruf) : null;

    $teks = match ($kolom) {
        'n-subtes' => 'Jumlah peserta yang memiliki data ujian pada subtes ini. Angkanya sama untuk setiap butir.',
        'n-butir' => 'Jumlah peserta yang menerima butir ini. Angkanya bisa berbeda antar butir bila tidak semua peserta mendapat soal yang sama.',
        'terjawab' => 'Jumlah peserta yang memilih salah satu opsi. Jawaban kosong tidak dihitung, dan tidak dianggap salah.',
        'persen' => 'Persentase peserta yang memilih opsi '.$huruf.', dihitung dari kolom Terjawab, bukan dari N.',
        'persen-kunci' => 'Persentase peserta yang memilih opsi '.$huruf.', dihitung dari kolom Terjawab. Tanda bintang menandai kunci jawaban.',
        'persen-tertinggi' => 'Persentase peserta yang memilih opsi '.$huruf.', dihitung dari kolom Terjawab. Tanda bintang menandai opsi bernilai tertinggi.',
        'rata' => 'Rata-rata poin butir dari jawaban yang memiliki skor.',
        'max' => 'Poin tertinggi di antara opsi pada butir ini, bukan poin tertinggi yang dicapai peserta.',
        'n-uji-aspek' => 'Jumlah peserta yang masuk hitungan r pada baris ini. Mereka menjawab semua butir aspek yang diterimanya, dan butir ini termasuk di dalamnya.',
        'n-uji-pilar' => 'Jumlah peserta yang masuk hitungan r pada baris ini. Mereka menjawab semua butir pilar yang diterimanya, dan butir ini termasuk di dalamnya.',
        'p-benar' => 'Indeks kesukaran: jumlah jawaban benar dibagi kolom Terjawab. Makin tinggi, butir makin mudah.',
        'p-poin' => 'Indeks kesukaran: rata-rata poin dibagi poin tertinggi opsi. Makin tinggi, butir makin mudah.',
        'kesukaran' => 'Kategori dari P. Mudah bila P di atas 0,70, sedang bila 0,30 sampai 0,70, sukar bila di bawah 0,30. Tanda minus berarti P belum bisa dihitung.',
        'r-subtes' => 'Korelasi Pearson antara skor butir dan jumlah skor butir lain pada subtes. Hanya peserta yang menjawab semua butir yang dihitung, minimal 3 orang. Makin tinggi, butir makin sejalan dengan skor subtes.',
        'r-aspek-benar' => 'Korelasi Pearson antara skor benar-salah butir dan jumlah skor butir lain pada aspek. Hanya peserta pada kolom N uji yang dihitung. Skor laporan 5 untuk benar dan 1 untuk salah tidak mengubah angka ini.',
        'r-aspek-poin' => 'Korelasi Pearson antara poin opsi butir dan jumlah poin butir lain pada aspek. Hanya peserta pada kolom N uji yang dihitung.',
        'r-pilar' => 'Korelasi Pearson antara skor benar-salah butir (1 atau 0) dan jumlah skor butir lain pada pilar. Hanya peserta pada kolom N uji yang dihitung. Jawaban kosong tidak dihitung sebagai salah.',
        'r-tabel' => 'Batas signifikansi korelasi, uji dua arah 5%, dengan df = N uji pada baris ini dikurangi 2. Agar berstatus Layak, r harus lebih besar dari angka ini dan juga minimal 0,30.',
        'keputusan' => 'Layak bila r minimal 0,30 dan lebih besar dari r tabel. Ditinjau bila daya beda sedang atau belum signifikan. Lemah bila r di bawah 0,20, termasuk korelasi negatif. Belum cukup data bila r atau r tabel tidak terhitung.',
        default => '',
    };
@endphp

<span {{ $attributes->class(['d-inline-flex', 'align-items-center', 'justify-content-center', 'gap-1', 'text-nowrap']) }}>
    <span>{{ $label }}</span>
    @if ($teks !== '')
        <button
            type="button"
            class="butir-kolom-help"
            data-butir-help="{{ $teks }}"
            aria-expanded="false"
            aria-label="Penjelasan kolom {{ $label }}"
        >?</button>
    @endif
</span>
