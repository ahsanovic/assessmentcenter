<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Laporan Penilaian Kompetensi Individu</title>
    <style>
        @page {
            margin-left: 1.5cm;
            margin-top: 1cm;
            margin-right: 1.5cm;
            margin-bottom: 1cm;
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
        
        .page-break {
            page-break-after: always;
        }

        .custom-list {
            padding-left: 10;
        }
        
        .ttd-section {
            width: 100%;
            margin-top: 40px;
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
            font-size: 14px;;
        }
        
        .clear {
            clear: both;
        }
        
        .italic {
            font-style: italic;
        }

        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: 50px;
            text-align: center;
        }

        .footer img {
            width: 100%;
            height: auto;
        }
    </style>
</head>
<body>
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
        LAPORAN PENILAIAN KOMPETENSI INDIVIDU
    </div>
    
    <div class="nomor-surat">
        NOMOR : {{ $data->nomorLaporan[0]->nomor ?? '' }}
        {{-- NOMOR : {{ $nomor_laporan ?? '-' }} --}}
    </div>
    
    <!-- Tujuan -->
    <table class="tujuan-table">
        <tr>
            <td width="20">Tujuan</td>
            <td width="5">:</td>
            <td>Pemetaan Kompetensi Mansoskul dan Teknis</td>
            <td width="200" style="text-align: right">
                {{-- Tanggal Pemeriksaan : {{ \Carbon\Carbon::parse($data->nomorLaporan[0]->tanggal)->format('d F Y') ?? '-' }} --}}
                Tanggal : {{ \Carbon\Carbon::parse($peserta->test_started_at)->format('d F Y') ?? '-' }}
            </td>
        </tr>
        <tr>
            <td width="20">Level</td>
            <td width="5">:</td>
            <td>{{ $data->jabatan_diuji_id }}</td>
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
            <td>Tempat/Tgl lahir</td>
            <td><center>:</center></td>
            <td>{{ $peserta->tempat_lahir . ', ' . $peserta->tgl_lahir ?? '-' }}</td>
        </tr>
        <tr>
            <td>4</td>
            <td>Jenis Kelamin</td>
            <td><center>:</center></td>
            <td>{{ $peserta->jk == 'L' ? 'Laki - Laki' : ($peserta->jk == 'P' ? 'Perempuan' : '-') }}</td>
        </tr>
        <tr>
            <td>5</td>
            <td>Pangkat</td>
            <td><center>:</center></td>
            <td>{{ $peserta->golPangkat?->pangkat . ' (' . $peserta->golPangkat?->golongan . ')' ?? '-' }}</td>
        </tr>
        <tr>
            <td>6</td>
            <td>Jabatan</td>
            <td><center>:</center></td>
            <td>{{ $peserta->jabatan ?? '-' }}</td>
        </tr>
        <tr>
            <td>7</td>
            <td>Unit Kerja</td>
            <td><center>:</center></td>
            <td>{{ $peserta->unit_kerja ?? '-' }}</td>
        </tr>
    </table>
    
    <!-- Capaian Kompetensi Teknis -->
    <div class="identitas-header"><b>B. CAPAIAN KOMPETENSI TEKNIS</b></div>
    <table class="identitas-table" border="1">
        <tr>
            <td width="135">Job Person Match (JPM)</td>
            <td width="10"><center>:</center></td>
            <td><strong>{{ $data->hasilKompetensiTeknis[0]->jpm ? $data->hasilKompetensiTeknis[0]->jpm . ' %' : '' }}</strong></td>
        </tr>
        <tr>
            <td width="135">Kategori</td>
            <td width="10"><center>:</center></td>
            <td><strong>{{ $data->hasilKompetensiTeknis[0]->kategori ?? '' }}</strong></td>
        </tr>
        <tr>
            <td width="135">Deskripsi</td>
            <td width="10"><center>:</center></td>
            <td>{{ $data->hasilKompetensiTeknis[0]->deskripsi ?? '' }}</td>
        </tr>
    </table>

    <!-- Tanda Tangan -->
    <div class="ttd-section">
        <div class="ttd-box">
            <div class="ttd-jabatan">
                Kepala Badan Kepegawaian Daerah<br>
                Provinsi Jawa Timur
            </div>
            <div class="tte">
                <img src="{{ public_path('storage/' . $tte->ttd) }}" height="50" width="50" alt="tte">
            </div>
            <div class="ttd-nama">{{ $tte->nama }}</div>
            @php
                $nip = substr($tte->nip, 0, 8) . ' ' . substr($tte->nip, 8, 6) . ' ' . substr($tte->nip, 14, 1) . ' ' . substr($tte->nip, 15, 3);
            @endphp
            <div class="ttd-nip">NIP. {{ $nip }}</div>
        </div>
        <div class="clear"></div>
    </div>
</body>
</html>