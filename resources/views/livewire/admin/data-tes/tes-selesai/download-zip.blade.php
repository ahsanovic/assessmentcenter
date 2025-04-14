<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Laporan Penilaian Potensi Individu</title>
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

        .page-break {
            page-break-after: always;
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
        
        .deskripsi-title {
            font-weight: bold;
        }
        
        .ttd-section {
            width: 100%;
            margin-top: 50px;
        }
        
        .ttd-box {
            width: 50%;
            float: right;
        }
        
        .ttd-jabatan {
            margin-bottom: 80px;
            font-size: 13px;
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
        LAPORAN PENILAIAN POTENSI INDIVIDU
    </div>
    
    <div class="nomor-surat">
        NOMOR : {{ $data->nomorLaporan->first()->nomor ?? '' }}
    </div>
    
    <!-- Tujuan -->
    <table class="tujuan-table">
        <tr>
            <td width="35">Tujuan :</td>
            <td>Pemetaan Potensi</td>
            <td width="200" style="text-align: right">
                Tanggal Pemeriksaan : {{ \Carbon\Carbon::parse($data->nomorLaporan->first()->tanggal)->format('d F Y') ?? '-' }}
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
            <td>{{ $peserta->nip ?? '-' }}</td>
        </tr>
        <tr>
            <td>3</td>
            <td>Jabatan Saat ini</td>
            <td><center>:</center></td>
            <td>{{ $peserta->jabatan ?? '-' }}</td>
        </tr>
        <tr>
            <td>4</td>
            <td>Unit Kerja</td>
            <td><center>:</center></td>
            <td>{{ $peserta->unit_kerja ?? '-' }}</td>
        </tr>
    </table>
    
    <!-- Aspek Potensi -->
    <table class="aspek-table">
        <tr>
            <th colspan="2"><b>ASPEK POTENSI</b></th>
            <th><b>CAPAIAN LEVEL</b></th>
            <th><b>KETERANGAN</b></th>
        </tr>
        @php
            $no = 1;    
        @endphp
        @foreach ($aspek_potensi as $item)
            <tr>
                <td>{{  $no++ }}</td>
                <td>
                    {{ $item->alatTes->alat_tes }} <br/>
                    <i>
                    ({{ $item->alatTes->definisi_aspek_potensi }})
                    </i>
                </td>
                @switch($item->alatTes->alat_tes)
                    @case('Kemampuan Interpersonal')
                        <td style="text-align: center">{{ $capaian_level_interpersonal ?? '-' }}</td>
                        <td style="text-align: center">{{ $data->hasilInterpersonal[0]->kualifikasi_total ?? '-' }}</td>
                        @break
                    @case('Kecerdasan Emosi')
                        <td style="text-align: center">{{ $capaian_level_kecerdasan_emosi ?? '-' }}</td>
                        <td style="text-align: center">{{ $data->hasilKecerdasanEmosi[0]->kualifikasi_total ?? '-' }}</td>
                        @break
                    @case('Belajar Cepat dan Pengembangan Diri')
                        <td style="text-align: center">{{ $capaian_level_pengembangan_diri ?? '-' }}</td>
                        <td style="text-align: center">{{ $data->hasilPengembanganDiri[0]->kualifikasi_total ?? '-' }}</td>
                        @break
                    @case('Problem Solving')
                        <td style="text-align: center">{{ $capaian_level_problem_solving ?? '-' }}</td>
                        <td style="text-align: center">{{ $data->hasilProblemSolving[0]->kualifikasi_total ?? '-' }}</td>
                        @break
                    @case('Motivasi dan Komitmen')
                        <td  style="text-align: center">{{ $capaian_level_motivasi_komitmen ?? '-' }}</td>
                        <td style="text-align: center">{{ $data->hasilMotivasiKomitmen[0]->kualifikasi_total ?? '-' }}</td>
                        @break
                    @case('Berpikir Kritis dan Strategis')
                        <td style="text-align: center">{{ $capaian_level_berpikir_kritis ?? '-' }}</td>
                        <td style="text-align: center">{{ $data->hasilBerpikirKritis[0]->kualifikasi_total ?? '-' }}</td>
                        @break
                    @case('Kesadaran Diri')
                        <td style="text-align: center">{{ $capaian_level_kesadaran_diri ?? '-' }}</td>
                        <td style="text-align: center">{{ $data->hasilKesadaranDiri[0]->kualifikasi_total ?? '-' }}</td>
                        @break
                @endswitch
            </tr>
        @endforeach
        <tr>
            <td colspan="2"><b>Job Person Match (JPM)</b></td>
            <td style="text-align: center">
                @php
                    $capaian_level = [
                        $capaian_level_interpersonal,
                        $capaian_level_kecerdasan_emosi,
                        $capaian_level_pengembangan_diri,
                        $capaian_level_problem_solving,
                        $capaian_level_motivasi_komitmen,
                        $capaian_level_berpikir_kritis,
                        $capaian_level_kesadaran_diri
                    ];

                    $jpm = array_sum(array_map('floatval', $capaian_level)) / (3 * 7);
                @endphp
                <b>
                    {{ number_format($jpm * 100, 2) }} %
                </b>
            </td>
            <td></td>
        </tr>
        <tr>
            <td colspan="2"><b>Kategori</b></td>
            <td style="text-align: center">
                <b>
                    @if ($jpm >= 0.9)
                        {{ 'Optimal' }}
                    @elseif ($jpm >= 0.78)
                        {{ 'Cukup Optimal' }}
                    @else
                        {{ 'Kurang Optimal' }}
                    @endif
                </b>
            </td>
            <td></td>
        </tr>
    </table>
    
    <div class="page-break"></div>
    
    <!-- Deskripsi Kompetensi -->
    <div class="deskripsi-header"><b>C. DESKRIPSI KOMPETENSI</b></div>
    <table class="deskripsi-table">
        @php
            $no = 1;    
        @endphp
        @foreach ($aspek_potensi as $item)
        <tr>
            <td width="20">{{ $no++ }}</td>
            <td>
                <div class="deskripsi-title">{{ $item->alatTes->alat_tes }}</div>
                <p>
                    @switch($item->alatTes->alat_tes)
                        @case('Kemampuan Interpersonal')
                            {{ json_decode($data->hasilInterpersonal[0]->uraian_potensi_1)->uraian_potensi ?? '-' }}
                            {{ json_decode($data->hasilInterpersonal[0]->uraian_potensi_2)->uraian_potensi ?? '-' }}
                            {{ json_decode($data->hasilInterpersonal[0]->uraian_potensi_3)->uraian_potensi ?? '-' }}
                            {{ json_decode($data->hasilInterpersonal[0]->uraian_potensi_4)->uraian_potensi ?? '-' }}
                            {{ json_decode($data->hasilInterpersonal[0]->uraian_potensi_5)->uraian_potensi ?? '-' }}
                            @break
                        @case('Kecerdasan Emosi')
                            {{ json_decode($data->hasilKecerdasanEmosi[0]->uraian_potensi_1)->uraian_potensi ?? '-' }}
                            {{ json_decode($data->hasilKecerdasanEmosi[0]->uraian_potensi_2)->uraian_potensi ?? '-' }}
                            {{ json_decode($data->hasilKecerdasanEmosi[0]->uraian_potensi_3)->uraian_potensi ?? '-' }}
                            {{ json_decode($data->hasilKecerdasanEmosi[0]->uraian_potensi_4)->uraian_potensi ?? '-' }}
                            @break
                        @case('Belajar Cepat dan Pengembangan Diri')
                            {{ json_decode($data->hasilPengembanganDiri[0]->uraian_potensi_1)->uraian_potensi ?? '-' }}
                            {{ json_decode($data->hasilPengembanganDiri[0]->uraian_potensi_2)->uraian_potensi ?? '-' }}
                            {{ json_decode($data->hasilPengembanganDiri[0]->uraian_potensi_3)->uraian_potensi ?? '-' }}
                            {{ json_decode($data->hasilPengembanganDiri[0]->uraian_potensi_4)->uraian_potensi ?? '-' }}
                            {{ json_decode($data->hasilPengembanganDiri[0]->uraian_potensi_5)->uraian_potensi ?? '-' }}
                            @break
                        @case('Problem Solving')
                            {{ json_decode($data->hasilProblemSolving[0]->uraian_potensi_1)->deskripsi ?? '-' }}
                            {{ json_decode($data->hasilProblemSolving[0]->uraian_potensi_2)->deskripsi ?? '-' }}
                            {{ json_decode($data->hasilProblemSolving[0]->uraian_potensi_3)->deskripsi ?? '-' }}
                            {{ json_decode($data->hasilProblemSolving[0]->uraian_potensi_4)->deskripsi ?? '-' }}
                            {{ json_decode($data->hasilProblemSolving[0]->uraian_potensi_5)->deskripsi ?? '-' }}
                            {{ json_decode($data->hasilProblemSolving[0]->uraian_potensi_6)->deskripsi ?? '-' }}
                            {{ json_decode($data->hasilProblemSolving[0]->uraian_potensi_7)->deskripsi ?? '-' }}
                            {{ json_decode($data->hasilProblemSolving[0]->uraian_potensi_8)->deskripsi ?? '-' }}
                            @break
                        @case('Motivasi dan Komitmen')
                            {{ $data->hasilMotivasiKomitmen[0]->deskripsi ?? '-' }}
                            @break
                        @case('Berpikir Kritis dan Strategis')
                            {{ json_decode($data->hasilBerpikirKritis[0]->uraian_potensi_1)->deskripsi ?? '-' }}
                            {{ json_decode($data->hasilBerpikirKritis[0]->uraian_potensi_2)->deskripsi ?? '-' }}
                            {{ json_decode($data->hasilBerpikirKritis[0]->uraian_potensi_3)->deskripsi ?? '-' }}
                            {{ json_decode($data->hasilBerpikirKritis[0]->uraian_potensi_4)->deskripsi ?? '-' }}
                            {{ json_decode($data->hasilBerpikirKritis[0]->uraian_potensi_5)->deskripsi ?? '-' }}
                            {{ json_decode($data->hasilBerpikirKritis[0]->uraian_potensi_6)->deskripsi ?? '-' }}
                            {{ json_decode($data->hasilBerpikirKritis[0]->uraian_potensi_7)->deskripsi ?? '-' }}
                            {{ json_decode($data->hasilBerpikirKritis[0]->uraian_potensi_8)->deskripsi ?? '-' }}
                            @break
                        @case('Kesadaran Diri')
                            {{ json_decode($data->hasilKesadaranDiri[0]->uraian_potensi_1)->uraian_potensi ?? '-' }}
                            {{ json_decode($data->hasilKesadaranDiri[0]->uraian_potensi_2)->uraian_potensi ?? '-' }}
                            {{ json_decode($data->hasilKesadaranDiri[0]->uraian_potensi_3)->uraian_potensi ?? '-' }}
                            @break
                    @endswitch
                </p>
            </td>
        </tr>
        @endforeach
    </table>
    
    <!-- Tanda Tangan -->
    <div class="ttd-section">
        <div class="ttd-box">
            <div class="ttd-jabatan">
                Kepala Badan Kepegawaian Daerah<br>
                Provinsi Jawa Timur
            </div>
            <div class="ttd-nama">INDAH WAHYUNI, S.H., M.Si.</div>
            <div class="ttd-nip">NIP. 19670409 199202 2 003</div>
        </div>
        <div class="clear"></div>
    </div>
</body>
</html>