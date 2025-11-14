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

        .aspek-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        
        .aspek-table th, .aspek-table td {
            border: 1px solid #000;
            padding: 5px;
            text-align: left;
            vertical-align: middle;
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
        
        .deskripsi-table th {
            font-weight: bold;
            border: 1px solid #000;
            padding: 5px;
            text-align: left;
            background-color: #f2f2f2;
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
    </div>
    
    <!-- Tujuan -->
    <table class="tujuan-table">
        <tr>
            <td width="20">Tujuan</td>
            <td width="5">:</td>
            <td>Pemetaan Kompetensi Mansoskul dan Teknis</td>
            <td width="200" style="text-align: right">
                Tanggal : {{ \Carbon\Carbon::parse($peserta->test_started_at)->format('d F Y') ?? '-' }}
            </td>
        </tr>
        <tr>
            <td width="20">Level</td>
            <td width="5">:</td>
            <td>{{ $data->metode_tes_id == 5 ? '1' : '2' }}</td>
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
    
    <!-- Capaian Kompetensi -->
    <div class="identitas-header"><b>B. CAPAIAN KOMPETENSI</b></div>
    <table class="aspek-table" border="1">
        <tr>
            <th colspan="2" rowspan="2"><b>UNIT KOMPETENSI</b></th>
            <th colspan="2"><b>NILAI CAPAIAN</b></th>
            <th rowspan="2"><b>KETERANGAN</b></th>
        </tr>
        <tr>
            <th width="8%"><b>0</b></th>
            <th width="8%"><b>1</b></th>
        </tr>
        @php
            $no = 1;    
        @endphp
        @php
            $nilaiCapaian = $data->hasilPspk[0]->nilai_capaian; // array dari JSON
        @endphp
        @foreach ($aspek_potensi as $item)
            @php
                $nilai = $nilaiCapaian[$loop->index] ?? null;
            @endphp
            <tr>
                <td width="3%">{{  $no++ }}</td>
                <td width="60%">
                    {{ $item->nama_aspek }} <br/>
                    <i>
                        {{ $item->deskripsi_aspek }}
                    </i>
                </td>
                {{-- Kolom Nilai Capaian 1 --}}
                <td style="text-align: center">
                    {{ $nilai == 0.5 ? '0.5' : '' }}
                </td>

                {{-- Kolom Nilai Capaian 2 --}}
                <td style="text-align: center">
                    {{ in_array($nilai, [1, 1.5]) ? $nilai : '' }}
                </td>

                {{-- Kategori --}}
                <td style="text-align: center">
                    @switch($nilai)
                        @case(0.5)
                            Cukup Mampu
                            @break
                        @case(1)
                            Mampu
                            @break
                        @case(1.5)
                            Sangat Mampu
                            @break
                        @default
                            '';
                    @endswitch
                </td>
            </tr>
        @endforeach
    </table>

    <div class="page-break"></div>

    @php
        $jumlah_nilai_capaian = collect($data->hasilPspk[0]->nilai_capaian)->filter(function($n) {
            return in_array($n, [1, 1.5]);
        })->count();
    @endphp
    <table class="aspek-table" border="1">
    <tr>
        <td colspan="5" style="padding-left: 10px;">
            <b>REKOMENDASI:</b> <br/>
            Berdasarkan grafik capaian kompetensi, jumlah nilai capaian kompetensi
            Sdr/i {{ $peserta->nama }} adalah {{ $jumlah_nilai_capaian }} dari 9 kompetensi yang disyaratkan jabatan, atau dinyatakan dengan : 
        </td>
    </tr>
    <tr>
        <td colspan="2"><b>Job Person Match (JPM):</b></td>
        <td colspan="3"><b>{{ $data->hasilPspk[0]->jpm }}%</b></td>
    </tr>
    <tr>
        <td colspan="2"><b>Kategori:</b></td>
        <td colspan="3"><b>{{ $data->hasilPspk[0]->kategori }}</b></td>
    </tr>
</table>

    <!-- Deskripsi Kompetensi -->
    <div class="deskripsi-header"><b>C. DESKRIPSI KOMPETENSI</b></div>
    <table class="deskripsi-table">
        @php
            $no = 1;    
        @endphp
        @foreach ($aspek_potensi as $item)
        <tr>
            <td width="3">{{ $no++ }}</td>
            <td>
                <div class="deskripsi-title"><b>{{ $item->nama_aspek }}</b></div>
                <p>
                    {{ $data->hasilPspk[0]->deskripsi[$item->kode_aspek] ?? '' }}
                </p>
            </td>
        </tr>
        @endforeach
    </table>

    <div class="page-break"></div>

    <!-- Saran Pengembangan Kompetensi -->
    <div class="deskripsi-header"><b>D. SARAN PENGEMBANGAN KOMPETENSI</b></div>
    <table class="aspek-table" border="1">
        <tr>
            <th colspan="2"><b>AREA PENGEMBANGAN</b></th>
            <th><b>CAPAIAN</b></th>
            <th><b>LANGKAH-LANGKAH YANG DILAKUKAN</b></th>
        </tr>
        @php
            $no = 1;
        @endphp
        @foreach ($aspek_potensi as $item)
        <tr>
            <td width="3">{{ $no++ }}</td>
            <td>
                {{ $item->nama_aspek }}
            </td>
            <td>
                @php
                    $nilai = $data->hasilPspk[0]->nilai_capaian[$loop->index] ?? null;
                @endphp
                {{ in_array($nilai, [1, 1.5]) ? 'Memenuhi Standar Kompetensi' : 'Belum Memenuhi Standar Kompetensi' }}
            </td>
            <td>
                @php
                    $ref = \App\Models\Pspk\RefSaranPengembangan::first();
                    $saran_pengembangan = $ref->{$item->kode_aspek} ?? '';
                    $items = preg_split('/-\s+/', trim($saran_pengembangan), -1, PREG_SPLIT_NO_EMPTY);
                @endphp
                @if (in_array($nilai, [1, 1.5]))
                    {{ 'Dapat diberikan tantangan di level kompetensi yang lebih tinggi' }}
                @else
                    <div>
                        @foreach ($items as $text)
                            - {{ trim($text) }}<br>
                        @endforeach
                    </div>
                @endif
            </td>
        </tr>
        @endforeach
    </table>

    <div class="page-break"></div>

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