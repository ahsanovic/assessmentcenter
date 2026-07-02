<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Berita Acara</title>
    <style>
        @page {
            margin: 1.5cm 2cm;
        }

        body {
            font-family: "Times New Roman", Times, serif;
            font-size: 13px;
            color: #000;
            margin: 0;
            padding: 0;
            line-height: 1.5;
        }

        .header {
            width: 100%;
            border-collapse: collapse;
        }

        .header td:first-child {
            width: 80px;
            text-align: center;
            vertical-align: middle;
        }

        .header td:last-child {
            text-align: center;
            padding-left: 6px;
        }

        .header h5 { margin: 2px 0; font-size: 14px; }
        .header h3 { margin: 2px 0; font-size: 18px; }
        .header p { margin: 1px 0; font-size: 11px; }

        .line-thick { border-bottom: 3px solid #000; margin-top: 2px; }
        .line-thin { border-bottom: 1px solid #000; margin-top: 1px; }

        .title { text-align: center; font-weight: bold; margin-top: 14px; }
        .title .t1 { font-size: 15px; }
        .title .t2 { font-size: 15px; text-transform: uppercase; }
        .title .nomor { font-weight: bold; margin-top: 6px; font-size: 13px; }

        .content { margin-top: 16px; text-align: justify; }

        table.detail { width: 100%; border-collapse: collapse; margin-top: 4px; }
        table.detail td { vertical-align: top; padding: 2px 0; }
        table.detail td.no { width: 20px; }
        table.detail td.label { width: 280px; }
        table.detail td.sep { width: 12px; }
        table.detail td.sub-label { padding-left: 26px; }

        .catatan-box { padding-left: 8px; margin-top: 4px; text-align: justify; }
        .catatan-line { border-bottom: 1px solid #000; height: 16px; }

        @include('pdf.partials.markdown-styles')

        table.penyerahan { width: 100%; border-collapse: collapse; margin-top: 8px; }
        table.penyerahan td { vertical-align: top; padding: 2px 0; }
        table.penyerahan td.label { width: 420px; }
        table.penyerahan td.sep { width: 12px; }

        .ttd-block { margin-top: 6px; }
        table.ttd-detail { border-collapse: collapse; margin-top: 2px; }
        table.ttd-detail td { vertical-align: top; padding: 1px 0; }
        table.ttd-detail td.label { width: 120px; padding-left: 18px; }
        table.ttd-detail td.sep { width: 12px; }
        .ttd-space { height: 48px; }
    </style>
</head>
<body>
    <table class="header">
        <tr>
            <td>
                <img src="{{ public_path('assets/images/logo.png') }}" height="90" width="72" alt="logo">
            </td>
            <td>
                <h5>PEMERINTAH PROVINSI JAWA TIMUR</h5>
                <h3><b>BADAN KEPEGAWAIAN DAERAH</b></h3>
                <p>Jl. Jemur Andayani No. 1 - Telp. (031) 8477551 Fax. (031) 8477404</p>
                <p><b>SURABAYA&nbsp;&nbsp;60174</b></p>
            </td>
        </tr>
    </table>
    <div class="line-thick"></div>
    <div class="line-thin"></div>

    <div class="title">
        <div class="t1">BERITA ACARA</div>
        <div class="t2">{{ $judul }} TAHUN {{ $tahun }}</div>
        <div class="nomor">Nomor: {{ $nomor_surat ?: '.................................' }}</div>
    </div>

    <div class="content">
        <p>
            Pada hari ini {{ $hari ?: '.............' }} tanggal {{ $tanggal_angka ?: '.....' }}
            bulan {{ $bulan_teks ?: '.............' }} tahun {{ $tahun }}, telah diselenggarakan kegiatan
            <b>{{ $nama_kegiatan ?: '.............................' }}</b> di lingkungan Pemerintah
            {{ $di_lingkungan_pemerintah ?: '.............................' }}, yang dilaksanakan dari pukul
            {{ $waktu_mulai ?: '........' }} {{ $zona_waktu }} sampai dengan pukul
            {{ $waktu_selesai ?: '........' }} {{ $zona_waktu }}, bertempat di
            {{ $tempat ?: '.............................' }}.
        </p>

        <p style="margin-top:10px;">Adapun rincian pelaksanaan kegiatan sebagai berikut:</p>

        <table class="detail">
            <tr>
                <td class="no">a.</td>
                <td class="label">Pejabat yang dinilai</td>
                <td class="sep">:</td>
                <td>{{ $pejabat_dinilai !== null && $pejabat_dinilai !== '' ? $pejabat_dinilai : '-' }} orang</td>
            </tr>
            <tr>
                <td class="no">b.</td>
                <td class="label">Jumlah peserta yang seharusnya hadir</td>
                <td class="sep">:</td>
                <td>{{ $jumlah_peserta_seharusnya !== null && $jumlah_peserta_seharusnya !== '' ? $jumlah_peserta_seharusnya : '-' }} orang</td>
            </tr>
            <tr>
                <td class="no">c.</td>
                <td class="label">Jumlah peserta yang tidak hadir</td>
                <td class="sep">:</td>
                <td>{{ (int) $jumlah_peserta_tidak_hadir }} orang</td>
            </tr>
            <tr>
                <td class="no"></td>
                <td class="sub-label" colspan="1">-&nbsp; Nomor peserta</td>
                <td class="sep">:</td>
                <td>
                    @include('pdf.partials.markdown', ['html' => $nomor_tidak_hadir_html ?? null, 'fallback' => $nomor_tidak_hadir ?? null])
                </td>
            </tr>
            <tr>
                <td class="no"></td>
                <td class="sub-label" colspan="1">-&nbsp; Alasan ketidakhadiran</td>
                <td class="sep">:</td>
                <td>
                    @include('pdf.partials.markdown', ['html' => $alasan_tidak_hadir_html ?? null, 'fallback' => $alasan_tidak_hadir ?? null])
                </td>
            </tr>
            <tr>
                <td class="no">d.</td>
                <td class="label">Jumlah peserta yang hadir</td>
                <td class="sep">:</td>
                <td>{{ (int) $jumlah_peserta_hadir }} orang</td>
            </tr>
        </table>

        <p style="margin-top:12px;">Catatan selama pelaksanaan kegiatan Uji Kompetensi :</p>
        <div class="catatan-box">
            @include('pdf.partials.markdown', ['html' => $catatan_html ?? null, 'fallback' => $catatan ?? null, 'empty' => ''])
            @if (empty($catatan_html ?? '') && empty(trim((string) ($catatan ?? ''))))
                <div class="catatan-line"></div>
                <div class="catatan-line"></div>
            @endif
        </div>

        <table class="penyerahan">
            <tr>
                <td class="label">Tanggal penyerahan Rekapitulasi Level hasil penilaian Uji Kompetensi</td>
                <td class="sep">:</td>
                <td>{{ $tanggal_penyerahan_rekap_teks ?: '-' }}</td>
            </tr>
            <tr>
                <td class="label">Tanggal penyerahan Laporan Individu hasil penilaian Uji Kompetensi</td>
                <td class="sep">:</td>
                <td>{{ $tanggal_penyerahan_laporan_teks ?: '-' }}</td>
            </tr>
        </table>

        <p style="margin-top:12px;">Demikian berita acara ini dibuat dengan sesungguhnya untuk digunakan sebagaimana mestinya.</p>

        <p style="margin-top:10px;">Yang membuat berita acara:</p>

        <div class="ttd-block">
            <div>1.&nbsp; {{ $panitia1_instansi ?: '-' }}</div>
            <table class="ttd-detail">
                <tr>
                    <td class="label">Nama</td>
                    <td class="sep">:</td>
                    <td>{{ $admin_nama ?: '' }}</td>
                </tr>
                <tr>
                    <td class="label">NIP</td>
                    <td class="sep">:</td>
                    <td>{{ $admin_nip ?: '' }}</td>
                </tr>
                <tr>
                    <td class="label">Tanda tangan</td>
                    <td class="sep">:</td>
                    <td class="ttd-space"></td>
                </tr>
            </table>
        </div>

        <div class="ttd-block" style="margin-top:12px;">
            <div>2.&nbsp; {{ $panitia2_instansi ?: '-' }}</div>
            <table class="ttd-detail">
                <tr>
                    <td class="label">Nama</td>
                    <td class="sep">:</td>
                    <td>{{ $tester_nama ?: '' }}</td>
                </tr>
                <tr>
                    <td class="label">NIP</td>
                    <td class="sep">:</td>
                    <td>{{ $tester_nip ?: '' }}</td>
                </tr>
                <tr>
                    <td class="label">Tanda tangan</td>
                    <td class="sep">:</td>
                    <td class="ttd-space"></td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>
