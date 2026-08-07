<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Laporan Penilaian Literasi Digital dan Emerging Skill</title>
    <style>
        @page {
            margin-left: 1.5cm;
            margin-top: 1cm;
            margin-right: 1.5cm;
            margin-bottom: 2cm;
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
            margin-bottom: 22px;
        }

        .aspek-table th,
        .aspek-table td {
            border: 1px solid #000;
            padding: 5px;
            text-align: left;
            vertical-align: middle;
        }

        .aspek-table th {
            font-weight: bold;
            text-align: center;
        }

        .deskripsi-header {
            font-weight: bold;
            margin-bottom: 5px;
        }

        .deskripsi-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        .deskripsi-table td {
            border: 1px solid #000;
            padding: 6px;
            text-align: justify;
            vertical-align: top;
        }

        .deskripsi-table tr {
            page-break-inside: avoid;
        }

        .deskripsi-table p {
            margin: 0;
            padding: 0;
        }

        .ttd-section {
            width: 100%;
            margin-top: 30px;
            border-collapse: collapse;
            page-break-inside: avoid;
            page-break-before: auto;
        }

        .ttd-section td {
            vertical-align: top;
            border: none;
            padding: 0;
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
            font-weight: bold;
            text-decoration: underline;
        }

        .ttd-nip {
            margin: 0;
            font-size: 14px;
        }

        .footer {
            position: fixed;
            bottom: -1.2cm;
            left: 0;
            right: 0;
            text-align: center;
        }

        .footer img {
            width: 100%;
            height: 65px;
        }
    </style>
</head>
<body>
    @php
        $hasil = $data->hasilCakapDigital->first();
    @endphp

    <!-- Footer -->
    <div class="footer">
        <img src="{{ public_path('assets/images/tte-footer.png') }}" alt="footer" />
    </div>

    <!-- Header -->
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

    <!-- Title -->
    <div class="title-box">
        LAPORAN PENILAIAN LITERASI DIGITAL DAN EMERGING SKILL
    </div>

    <div class="nomor-surat">
        NOMOR : {{ $nomor_laporan ?? '' }}
    </div>

    <!-- Tujuan -->
    <table class="tujuan-table">
        <tr>
            <td width="20">Tujuan</td>
            <td width="5">:</td>
            <td>Pengukuran Literasi Digital dan Emerging Skill</td>
            <td width="200" style="text-align: right">
                Tanggal : {{ $peserta->test_started_at ? \Carbon\Carbon::parse($peserta->test_started_at)->translatedFormat('d F Y') : '-' }}
            </td>
        </tr>
    </table>

    <!-- Identitas -->
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

    <!-- Capaian Hasil Penilaian -->
    <div class="identitas-header"><b>B. CAPAIAN HASIL PENILAIAN</b></div>
    <table class="aspek-table" border="1">
        <tr>
            <th width="8%">NO</th>
            <th width="32%">ASPEK</th>
            <th width="20%">JPM</th>
            <th width="40%">KATEGORI</th>
        </tr>
        <tr>
            <td style="text-align: center;">1</td>
            <td>Literasi Digital</td>
            <td style="text-align: center;">{{ $hasil->jpm_literasi !== null ? number_format((float) $hasil->jpm_literasi, 2) . '%' : '-' }}</td>
            <td style="text-align: center;">{{ $hasil->kesimpulan_literasi ?? '-' }}</td>
        </tr>
        <tr>
            <td style="text-align: center;">2</td>
            <td>Emerging Skill</td>
            <td style="text-align: center;">{{ $hasil->jpm_emerging !== null ? number_format((float) $hasil->jpm_emerging, 2) . '%' : '-' }}</td>
            <td style="text-align: center;">{{ $hasil->kesimpulan_emerging ?? '-' }}</td>
        </tr>
    </table>

    <!-- Deskripsi Aspek -->
    <div class="deskripsi-header"><b>C. DESKRIPSI ASPEK</b></div>
    <table class="deskripsi-table" border="1">
        <tr>
            <td width="28%">
                <b>1. Literasi Digital</b>
            </td>
            <td>
                <p>{{ $hasil->deskripsi_literasi ?? '-' }}</p>
            </td>
        </tr>
        <tr>
            <td>
                <b>2. Emerging Skill</b>
            </td>
            <td>
                <p>{{ $hasil->deskripsi_emerging ?? '-' }}</p>
            </td>
        </tr>
    </table>

    <!-- Tanda Tangan: table + page-break-inside:avoid agar utuh di halaman yang sama bila muat -->
    <table class="ttd-section">
        <tr>
            <td width="50%"></td>
            <td width="50%">
                <div class="ttd-jabatan">
                    Kepala Badan Kepegawaian Daerah<br>
                    Provinsi Jawa Timur
                </div>
                <div class="tte">
                    @if ($tte?->ttd)
                        <img src="{{ public_path('storage/' . $tte->ttd) }}" height="50" width="50" alt="tte">
                    @endif
                </div>
                <div class="ttd-nama">{{ $tte->nama ?? '' }}</div>
                @php
                    $nipRaw = $tte->nip ?? '';
                    $nip = strlen($nipRaw) >= 18
                        ? substr($nipRaw, 0, 8) . ' ' . substr($nipRaw, 8, 6) . ' ' . substr($nipRaw, 14, 1) . ' ' . substr($nipRaw, 15, 3)
                        : $nipRaw;
                @endphp
                <div class="ttd-nip">NIP. {{ $nip }}</div>
            </td>
        </tr>
    </table>
</body>
</html>
