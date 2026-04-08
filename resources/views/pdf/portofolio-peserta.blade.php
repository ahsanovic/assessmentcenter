<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Portofolio Peserta</title>
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
            margin: 14px 0 8px 0;
            text-align: center;
            padding: 5px;
            font-weight: bold;
            font-size: 14px;
        }

        .section-title {
            font-weight: bold;
            margin-top: 16px;
            margin-bottom: 6px;
            font-size: 13px;
        }

        .identitas-table {
            width: 100%;
            border: 1px solid #000;
            border-collapse: collapse;
            margin-bottom: 12px;
        }

        .identitas-table td {
            padding: 4px 6px;
            vertical-align: top;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }

        .data-table th, .data-table td {
            border: 1px solid #000;
            padding: 5px;
            text-align: left;
            vertical-align: top;
        }

        .data-table th {
            background-color: #f2f2f2;
            font-weight: bold;
            text-align: center;
        }

        .qa-block {
            margin-bottom: 10px;
            padding: 6px;
            border: 1px solid #ccc;
        }

        .qa-q {
            font-weight: bold;
            margin-bottom: 4px;
        }

        .qa-a {
            text-align: justify;
        }

        .page-break {
            page-break-after: always;
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

        /* Baris judul + foto: foto di kanan atas area (tidak overlap kotak); judul kiri mepet border tabel (valign bottom) */
        .identitas-head-row {
            width: 100%;
            border-collapse: collapse;
            margin-top: 16px;
            margin-bottom: 0;
        }

        .identitas-head-row td {
            padding: 0;
        }

        .identitas-heading {
            margin: 0;
            padding: 0;
            font-weight: bold;
            font-size: 13px;
        }

        .identitas-head-no-foto {
            margin-top: 16px;
            margin-bottom: 0;
        }

        .identitas-foto-img {
            width: 56px;
            height: 56px;
            object-fit: cover;
            border-radius: 50%;
            display: block;
            margin: 0;
        }

        .identitas-table.identitas-table--first {
            margin-top: 0;
        }

        .event-name {
            text-align: center;
            margin-bottom: 10px;
            font-size: 12px;
        }
    </style>
</head>
<body>
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
        FORM PORTOFOLIO
    </div>

    <div class="event-name">
        <h8>{{ $event->nama_event ?? '' }}</h8>
    </div>

    @if($fotoDataUri)
        <table class="identitas-head-row" border="0" cellpadding="0" cellspacing="0">
            <tr style="height:56px;">
                <td valign="bottom" style="width:auto; padding-right:8px;">
                    <div class="identitas-heading">A. IDENTITAS / BIODATA</div>
                </td>
                <td valign="top" align="right" style="width:56px; white-space:nowrap; padding-left:8px;">
                    <img src="{{ $fotoDataUri }}" alt="foto" class="identitas-foto-img">
                </td>
            </tr>
        </table>
    @else
        <div class="identitas-head-no-foto">
            <div class="identitas-heading">A. IDENTITAS / BIODATA</div>
        </div>
    @endif
    <table class="identitas-table identitas-table--first" border="1" style="width:100%; table-layout:fixed;">
        <colgroup>
            <col style="width:28%;">
            <col>
        </colgroup>
        <tr>
            <td><b>Nama</b></td>
            <td>{{ $peserta->nama }}</td>
        </tr>
        @if($peserta->jenis_peserta_id == 1)
        <tr>
            <td><b>NIP</b></td>
            <td>{{ $peserta->nip ?? '-' }}</td>
        </tr>
        @endif
        <tr>
            <td><b>NIK</b></td>
            <td>{{ $peserta->nik ?? '-' }}</td>
        </tr>
        <tr>
            <td><b>Tempat / Tanggal Lahir</b></td>
            <td>{{ $peserta->tempat_lahir ?? '-' }}, {{ $peserta->tgl_lahir ?? '-' }}</td>
        </tr>
        <tr>
            <td><b>Jenis Kelamin</b></td>
            <td>{{ ($peserta->jk ?? '') === 'L' ? 'Laki-Laki' : (($peserta->jk ?? '') === 'P' ? 'Perempuan' : '-') }}</td>
        </tr>
        <tr>
            <td><b>Agama</b></td>
            <td>{{ $peserta->agama->agama ?? '-' }}</td>
        </tr>
        @if($peserta->jenis_peserta_id == 1)
        <tr>
            <td><b>Pangkat / Golongan</b></td>
            <td>{{ trim(($peserta->golPangkat->pangkat ?? '') . (isset($peserta->golPangkat->golongan) && $peserta->golPangkat->golongan ? ' - ' . $peserta->golPangkat->golongan : '')) ?: '-' }}</td>
        </tr>
        <tr>
            <td><b>Jabatan</b></td>
            <td>{{ $peserta->jabatan ?? '-' }}</td>
        </tr>
        @endif
        <tr>
            <td><b>Unit Kerja</b></td>
            <td>{{ $peserta->unit_kerja ?? '-' }}</td>
        </tr>
        <tr>
            <td><b>Instansi</b></td>
            <td>{{ $peserta->instansi ?? '-' }}</td>
        </tr>
        <tr>
            <td><b>Alamat</b></td>
            <td>{{ $peserta->alamat ?? '-' }}</td>
        </tr>
        <tr>
            <td><b>Nomor HP</b></td>
            <td>{{ $peserta->no_hp ?? '-' }}</td>
        </tr>
    </table>

    <div class="section-title">B. PENDIDIKAN FORMAL</div>
    <table class="data-table" border="1">
        <thead>
        <tr>
            <th style="width:4%;">#</th>
            <th>Jenjang</th>
            <th>Nama Sekolah</th>
            <th>Tahun</th>
            <th>Jurusan</th>
            <th>IPK / Nilai</th>
        </tr>
        </thead>
        <tbody>
        @forelse ($pendidikan as $index => $item)
            <tr>
                <td style="text-align:center;">{{ $index + 1 }}</td>
                <td>{{ $item->jenjangPendidikan->jenjang ?? '' }}</td>
                <td>{{ $item->nama_sekolah }}</td>
                <td>{{ $item->thn_masuk }} - {{ $item->thn_lulus }}</td>
                <td>{{ $item->jurusan }}</td>
                <td style="text-align:center;">{{ $item->ipk }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="6" style="text-align:center;">Tidak ada data</td>
            </tr>
        @endforelse
        </tbody>
    </table>

    <div class="section-title">C. PELATIHAN / KURSUS</div>
    <table class="data-table" border="1">
        <thead>
        <tr>
            <th style="width:4%;">#</th>
            <th>Nama Institusi</th>
            <th>Tanggal</th>
            <th>Subjek Pelatihan</th>
        </tr>
        </thead>
        <tbody>
        @forelse ($pelatihan as $index => $item)
            <tr>
                <td style="text-align:center;">{{ $index + 1 }}</td>
                <td>{{ $item->nama_institusi }}</td>
                <td>{{ $item->tgl_mulai }} - {{ $item->tgl_selesai }}</td>
                <td>{{ $item->subjek_pelatihan }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="4" style="text-align:center;">Tidak ada data</td>
            </tr>
        @endforelse
        </tbody>
    </table>

    <div class="page-break"></div>

    <div class="section-title">D. RIWAYAT KARIR</div>
    <table class="data-table" border="1">
        <thead>
        <tr>
            <th style="width:4%;">#</th>
            <th>Jangka Waktu</th>
            <th>Instansi</th>
            <th>Jabatan</th>
            <th>Uraian Tugas</th>
        </tr>
        </thead>
        <tbody>
        @forelse ($karir as $index => $item)
            <tr>
                <td style="text-align:center;">{{ $index + 1 }}</td>
                <td>
                    @php
                        $mulai = \Carbon\Carbon::create()->month((int) $item->bulan_mulai)->translatedFormat('F') . ' ' . $item->tahun_mulai;
                        $selesai = \Carbon\Carbon::create()->month((int) $item->bulan_selesai)->translatedFormat('F') . ' ' . $item->tahun_selesai;
                    @endphp
                    {{ $mulai }} s/d {{ $selesai }}
                </td>
                <td>{{ $item->instansi }}</td>
                <td>{{ $item->jabatan }}</td>
                <td>{{ $item->uraian_tugas }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="5" style="text-align:center;">Tidak ada data</td>
            </tr>
        @endforelse
        </tbody>
    </table>

    <div class="section-title">E. PENGALAMAN SPESIFIK</div>
    @forelse ($pertanyaan as $index => $item)
        @php
            $jawab = optional($item->jawaban->first())->jawaban;
        @endphp
        <div class="qa-block">
            <div class="qa-q">{{ $index + 1 }}. {{ $item->pertanyaan }}</div>
            <div class="qa-a">{!! $jawab ? strip_tags($jawab, '<p><br><strong><b><i><em><u>') : '<em>Belum dijawab</em>' !!}</div>
            @php
                $option_kode = [
                    1 => 'OPH',
                    2 => 'MP',
                    3 => 'INT',
                    4 => 'KS',
                    5 => 'PP',
                    6 => 'KOM',
                    7 => 'PB',
                    8 => 'PDOL',
                    9 => 'PK',
                ];
                $kode = collect(json_decode($item->kode, true))->mapWithKeys(fn($item) => [$item => true])->toArray();
            @endphp
            <div style="text-align: right; margin-top: 10px; font-size: 10px;">
                @php
                    $kodeList = collect($kode)
                        ->keys()
                        ->map(function($key) use ($option_kode) {
                            return strtolower($option_kode[$key] ?? $key);
                        })
                        ->toArray();
                @endphp
                @if(count($kodeList) > 0)
                    <span>({{ implode(',', $kodeList) }})</span>
                @endif
            </div>
       
        </div>
    @empty
        <p>Tidak ada pertanyaan.</p>
    @endforelse

    <div class="page-break"></div>

    <div class="section-title">F. PENILAIAN PRIBADI</div>
    @forelse ($penilaian as $index => $item)
        @php
            $jawab = optional($item->jawaban->first())->jawaban;
        @endphp
        <div class="qa-block">
            <div class="qa-q">{{ $index + 1 }}. {{ $item->pertanyaan }}</div>
            <div class="qa-a">{!! $jawab ? strip_tags($jawab, '<p><br><strong><b><i><em><u>') : '<em>Belum dijawab</em>' !!}</div>
        </div>
    @empty
        <p>Tidak ada pertanyaan.</p>
    @endforelse
</body>
</html>
