<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Laporan Penilaian Literasi Digital</title>
    <style>
        @page {
            margin-left: 1.5cm;
            margin-top: 1cm;
            margin-right: 1.5cm;
            margin-bottom: 2.6cm;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 0;
        }

        .header {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        .header td:first-child {
            width: 80px;
            text-align: center;
            vertical-align: middle;
        }

        .header td:last-child {
            padding-left: 10px;
            text-align: center;
        }

        .header h4 {
            font-size: 24px;
            margin: 5px 0;
        }

        .header h5 {
            margin: 5px 0;
            font-size: 16px;
        }

        .header h8 {
            font-size: 14px;
            margin: 2px 0;
        }

        .header p {
            margin: 2px 0;
            font-size: 12px;
        }

        .title-box {
            margin: 10px 0;
            text-align: center;
            padding: 5px;
            font-weight: bold;
            font-size: 14px;
        }

        .nomor-surat {
            text-align: center;
            margin-top: -15px;
            margin-bottom: 20px;
        }

        .tujuan-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        .tujuan-table td {
            padding: 3px;
        }

        .identitas-table {
            width: 100%;
            border: 1px solid #000;
            border-collapse: collapse;
            margin-bottom: 22px;
        }

        .identitas-header {
            font-weight: bold;
            margin-bottom: 5px;
        }

        .identitas-table td {
            padding: 3px;
        }

        .aspek-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        .aspek-table thead,
        .saran-table thead {
            display: table-header-group;
        }

        .saran-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            page-break-inside: auto;
        }

        .saran-table th {
            border: 1px solid #000;
            padding: 5px;
            font-weight: bold;
            text-align: center;
            vertical-align: middle;
        }

        .saran-table td {
            border-left: 1px solid #000;
            border-right: 1px solid #000;
            border-top: none;
            border-bottom: none;
            padding: 4px 5px;
            text-align: left;
            vertical-align: top;
        }

        .saran-table tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }

        .saran-table tr.awal td {
            border-top: 1px solid #000;
        }

        .saran-table tr.akhir td {
            border-bottom: 1px solid #000;
        }

        .saran-table td.kolom-tetap {
            text-align: center;
            vertical-align: top;
        }

        .aspek-table th, .aspek-table td {
            border: 1px solid #000;
            padding: 5px;
            text-align: left;
            vertical-align: top;
        }

        .aspek-table td {
            text-align: justify;
        }

        .aspek-table th {
            font-weight: bold;
            text-align: center;
        }

        .deskripsi-header {
            font-weight: bold;
            margin-top: 20px;
            margin-bottom: 5px;
        }

        .deskripsi-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .deskripsi-table td {
            border: 1px solid #000;
            padding: 5px;
            text-align: justify;
            vertical-align: top;
        }

        .deskripsi-table p {
            margin: 0;
            padding: 0;
        }

        .ttd-section {
            width: 100%;
            margin-top: 40px;
            page-break-inside: avoid;
        }

        .ttd-box {
            width: 50%;
            float: right;
        }

        .ttd-jabatan {
            margin-bottom: 10px;
            font-size: 13px;
        }

        .tte {
            margin-bottom: 6px;
        }

        .ttd-nama {
            margin: 0;
            font-size: 14px;
        }

        .ttd-nip {
            margin: 0;
            font-size: 14px;
        }

        .clear {
            clear: both;
        }

        .footer {
            position: fixed;
            bottom: -2.15cm;
            left: 0;
            right: 0;
            height: 1.8cm;
            overflow: hidden;
            text-align: center;
        }

        .footer img {
            width: 100%;
            height: 1.8cm;
        }

        @include('pdf.partials.markdown-styles')

        .aspek-table .md,
        .deskripsi-table .md,
        .saran-table .md {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }

        .aspek-table .md ul,
        .aspek-table .md ol,
        .aspek-table .md p,
        .aspek-table .md h1,
        .aspek-table .md h2,
        .aspek-table .md h3,
        .aspek-table .md h4,
        .deskripsi-table .md p {
            margin: 0;
        }

        .saran-table .md ul,
        .saran-table .md ol,
        .saran-table .md p,
        .saran-table .md h1,
        .saran-table .md h2,
        .saran-table .md h3,
        .saran-table .md h4 {
            margin: 0;
        }
    </style>
</head>
<body>
    <div class="footer">
        <img src="{{ public_path('assets/images/tte-footer.png') }}" alt="footer" />
    </div>

    <table class="header">
        <tr>
            <td>
                <img src="{{ public_path('assets/images/logo.png') }}" height="100" width="80" alt="logo">
            </td>
            <td>
                <h5>PEMERINTAH PROVINSI JAWA TIMUR</h5>
                <h4><b>BADAN KEPEGAWAIAN DAERAH</b></h4>
                <h8><b>Jl. Jemur Andayani I Surabaya Telp. 0318477551 Kode Pos 60236</b></h8>
                <p>Laman : bkd.jatimprov.go.id Pos-el : bkdjatim@gmail.com</p>
            </td>
        </tr>
    </table>

    <div style="border-bottom: 3px solid #000; margin: 1px 0;"></div>
    <div style="border-bottom: 1px solid #000; margin: 0;"></div>

    <div class="title-box">
        LAPORAN PENILAIAN LITERASI DIGITAL
    </div>

    <div class="nomor-surat">
        NOMOR : {{ $nomor_laporan ?? '' }}
    </div>

    <table class="tujuan-table">
        <tr>
            <td width="20">Tujuan</td>
            <td width="5">:</td>
            <td>Pemetaan Literasi Digital</td>
            <td width="200" style="text-align: right">
                Tanggal : {{ $peserta->test_started_at ? \Carbon\Carbon::parse($peserta->test_started_at)->translatedFormat('d M Y') : '-' }}
            </td>
        </tr>
    </table>

    <div class="identitas-header"><b>A. IDENTITAS</b></div>
    <table class="identitas-table" border="1">
        <tr>
            <td width="10">1</td>
            <td width="120">Nama</td>
            <td width="10"><center>:</center></td>
            <td>{{ $peserta->nama ?? '-' }}</td>
        </tr>
        <tr>
            <td>2</td>
            <td>NIP</td>
            <td><center>:</center></td>
            <td>{{ $peserta->nip ?: $peserta->nik }}</td>
        </tr>
        <tr>
            <td>3</td>
            <td>Jabatan</td>
            <td><center>:</center></td>
            <td>{{ $peserta->jabatan ?? '-' }}</td>
        </tr>
        <tr>
            <td>4</td>
            <td>Unit Kerja</td>
            <td><center>:</center></td>
            <td>{{ $peserta->unit_kerja ?? '-' }}</td>
        </tr>
        <tr>
            <td>5</td>
            <td>Instansi</td>
            <td><center>:</center></td>
            <td>{{ $peserta->instansi ?? '-' }}</td>
        </tr>
    </table>

    <div class="identitas-header"><b>B. CAPAIAN</b></div>
    <table class="aspek-table" border="1">
        <thead>
            <tr>
                <th><b>NO</b></th>
                <th><b>ASPEK</b></th>
                <th><b>STANDAR</b></th>
                <th><b>CAPAIAN</b></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($baris as $item)
                <tr>
                    <td width="3%" style="text-align: center;">{{ $loop->iteration }}</td>
                    <td width="60%">
                        {{ $item['nama'] }}
                        @if (filled($item['deskripsi_pilar']))
                            <br/>
                            <i>{!! nl2br(e($item['deskripsi_pilar'])) !!}</i>
                        @endif
                    </td>
                    <td width="10%" style="text-align: center;">{{ $item['standar'] }}</td>
                    <td width="10%" style="text-align: center;">{{ $item['capaian'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="aspek-table" border="1">
        <tr>
            <td colspan="5" style="padding-left: 10px;">
                Berdasarkan hasil penilaian, total nilai capaian adalah {{ $total_capaian }}
                dari total nilai standar yaitu {{ $total_standar }} atau setara dengan {{ number_format($jpm, 2) }}%
                dan masuk dalam kategori {{ $kategori !== '' ? $kategori : '-' }}.
            </td>
        </tr>
        <tr>
            <td colspan="2"><b>Job Person Match (JPM):</b></td>
            <td colspan="3"><b>{{ number_format($jpm, 2) }}%</b></td>
        </tr>
        <tr>
            <td colspan="2"><b>Kategori:</b></td>
            <td colspan="3"><b>{{ $kategori !== '' ? $kategori : '-' }}</b></td>
        </tr>
    </table>

    <div class="deskripsi-header"><b>C. DESKRIPSI</b></div>
    <table class="deskripsi-table">
        <tbody>
            @foreach ($baris as $item)
                <tr>
                    <td width="4%" style="text-align: center;">{{ $loop->iteration }}</td>
                    <td>
                        <div><b>{{ $item['nama'] }}</b></div>
                        @foreach ($item['deskripsi_pecahan'] as $bagian)
                            <p>{{ $bagian }}</p>
                        @endforeach
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="deskripsi-header"><b>D. SARAN PENGEMBANGAN</b></div>
    <table class="saran-table">
        <thead>
            <tr>
                <th><b>NO</b></th>
                <th><b>ASPEK</b></th>
                <th><b>CAPAIAN</b></th>
                <th><b>SARAN PENGEMBANGAN</b></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($baris as $item)
                @php
                    $pecahanSaran = $item['saran_pecahan'] !== [] ? $item['saran_pecahan'] : [''];
                @endphp
                @foreach ($pecahanSaran as $bagian)
                    <tr class="{{ $loop->first ? 'awal' : 'sambungan' }} {{ $loop->last ? 'akhir' : '' }}">
                        <td class="kolom-tetap" width="3%">{{ $loop->first ? $loop->parent->iteration : '' }}</td>
                        <td width="30%">{{ $loop->first ? $item['nama'] : '' }}</td>
                        <td class="kolom-tetap" width="10%">{{ $loop->first ? $item['capaian'] : '' }}</td>
                        <td>
                            @include('pdf.partials.markdown', ['html' => $bagian, 'empty' => ''])
                        </td>
                    </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>

    @if ($tte)
        <div class="ttd-section">
            <div class="ttd-box">
                <div class="ttd-jabatan">
                    Kepala Badan Kepegawaian Daerah<br>
                    Provinsi Jawa Timur
                </div>
                @php
                    $berkasTtd = filled($tte->ttd) ? public_path('storage/'.$tte->ttd) : null;
                @endphp
                @if ($berkasTtd && is_file($berkasTtd))
                    <div class="tte">
                        <img src="{{ $berkasTtd }}" height="50" width="50" alt="tte">
                    </div>
                @endif
                <div class="ttd-nama">{{ $tte->nama }}</div>
                @php
                    $nip = (string) ($tte->nip ?? '');
                    if (strlen($nip) >= 18) {
                        $nip = substr($nip, 0, 8).' '.substr($nip, 8, 6).' '.substr($nip, 14, 1).' '.substr($nip, 15, 3);
                    }
                @endphp
                <div class="ttd-nip">NIP. {{ $nip }}</div>
            </div>
            <div class="clear"></div>
        </div>
    @endif
</body>
</html>
