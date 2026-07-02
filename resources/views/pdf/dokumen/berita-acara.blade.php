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

        .content { margin-top: 16px; text-align: justify; }

        .dots { border-bottom: 1px dotted #000; }

        table.detail { width: 100%; border-collapse: collapse; margin-top: 4px; }
        table.detail td { vertical-align: top; padding: 2px 0; }
        table.detail td.label { width: 230px; }
        table.detail td.sep { width: 12px; }

        .item { margin-top: 10px; }
        .item-label { font-weight: normal; }

        .catatan-line { border-bottom: 1px solid #000; height: 16px; }

        .ttd-table { width: 100%; border-collapse: collapse; margin-top: 10px; table-layout: fixed; }
        .ttd-table td { width: 50%; vertical-align: top; padding-top: 6px; }
        .ttd-table td.admin-col { padding-right: 20px; }
        .ttd-table td.tester-col { padding-left: 20px; }
        .ttd-table .role { font-weight: bold; }
        .ttd-table .qrcode { width: 70px; height: 70px; vertical-align: middle; display: inline-block; }
        .ttd-table .ttd-sign-row td { vertical-align: middle; }
        .ttd-table .ttd-sign-label { white-space: nowrap; padding-right: 6px; }
        .ttd-table .ttd-sign-qr { padding-left: 4px; }

        .footnote { margin-top: 20px; font-size: 12px; }
        .footnote ol { margin: 4px 0 0 18px; padding: 0; }
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
        <div class="t2">{{ $judul }}</div>
    </div>

    <div class="content">
        <p>
            Pada hari ini
            <span>&nbsp;{{ $hari ?: '.....................' }}&nbsp;</span>
            tanggal <span>&nbsp;{{ $tanggal_angka ?: '.....................' }}&nbsp;</span>
            bulan <span>&nbsp;{{ $bulan_teks ?: '.................' }}&nbsp;</span>
            tahun <span>&nbsp;{{ $tahun }}</span>
        </p>

        <table class="detail">
            <tr>
                <td colspan="3" style="padding-top:8px;">
                    a.&nbsp; Telah diselenggarakan Uji Kompetensi dari pukul
                    <b>{{ $waktu_mulai ?: '............' }}</b> sampai dengan pukul
                    <b>{{ $waktu_selesai ?: '............' }}</b>
                </td>
            </tr>
            <tr>
                <td class="label" style="padding-left:18px;">Pejabat</td>
                <td class="sep">:</td>
                <td>{{ $pejabat ?: '-' }}</td>
            </tr>
            <tr>
                <td class="label" style="padding-left:18px;">Di Lingkungan Pemerintah</td>
                <td class="sep">:</td>
                <td>{{ $di_lingkungan_pemerintah ?: '-' }}</td>
            </tr>
            <tr>
                <td class="label" style="padding-left:18px;">Ruang</td>
                <td class="sep">:</td>
                <td>{{ $ruang ?: '-' }}</td>
            </tr>
            <tr>
                <td class="label" style="padding-left:18px;">Jumlah Peserta Seharusnya</td>
                <td class="sep">:</td>
                <td>{{ $jumlah_peserta_seharusnya !== null && $jumlah_peserta_seharusnya !== '' ? $jumlah_peserta_seharusnya : '-' }} orang</td>
            </tr>
            <tr>
                <td class="label" style="padding-left:18px;">Jumlah Peserta yang Tidak Hadir</td>
                <td class="sep">:</td>
                <td>{{ (int) $jumlah_peserta_tidak_hadir }} orang</td>
            </tr>
            <tr>
                <td class="label" style="padding-left:18px;">Yaitu Nomor</td>
                <td class="sep">:</td>
                <td>{{ $nomor_tidak_hadir ?: '-' }}</td>
            </tr>
            <tr>
                <td class="label" style="padding-left:18px; padding-top:8px;">Jumlah Peserta yang Hadir</td>
                <td class="sep" style="padding-top:8px;">:</td>
                <td style="padding-top:8px;">{{ (int) $jumlah_peserta_hadir }} orang</td>
            </tr>
        </table>

        <div class="item">
            <div>b.&nbsp; Catatan selama pelaksanaan Ujian *)</div>
            <div style="padding-left:18px; margin-top:4px; text-align:justify;">
                @if (!empty(trim((string) $catatan)))
                    {!! nl2br(e($catatan)) !!}
                @else
                    <div class="catatan-line"></div>
                    <div class="catatan-line"></div>
                    <div class="catatan-line"></div>
                @endif
            </div>
        </div>

        <p style="margin-top:14px; padding-left:18px;">Berita acara ini dibuat dengan sesungguhnya.</p>

        <p style="text-align:center; margin-top:6px;">Yang membuat berita acara</p>

        <table class="ttd-table">
            <tr>
                <td class="role admin-col">Admin</td>
                <td class="role tester-col">Tester/Mdt</td>
            </tr>
            <tr class="ttd-sign-row">
                <td class="admin-col">
                    <table style="border-collapse: collapse;">
                        <tr>
                            <td class="ttd-sign-label">1.&nbsp; Tanda tangan&nbsp; :</td>
                            <td class="ttd-sign-qr">
                                @if (!empty($admin_qrcode))
                                    <img src="{{ $admin_qrcode }}" alt="QR Admin" class="qrcode">
                                @endif
                            </td>
                        </tr>
                    </table>
                </td>
                <td class="tester-col">
                    <table style="border-collapse: collapse;">
                        <tr>
                            <td class="ttd-sign-label">1.&nbsp; Tanda tangan&nbsp; :</td>
                            <td class="ttd-sign-qr">
                                @if (!empty($tester_qrcode))
                                    <img src="{{ $tester_qrcode }}" alt="QR Tester" class="qrcode">
                                @endif
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td class="admin-col" style="padding-top:24px;">2.&nbsp; N a m a&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : {{ $admin_nama ?: '' }}</td>
                <td class="tester-col" style="padding-top:24px;">2.&nbsp; N a m a&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : {{ $tester_nama ?: '' }}</td>
            </tr>
            <tr>
                <td class="admin-col">3.&nbsp; N I P&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : {{ $admin_nip ?: '' }}</td>
                <td class="tester-col">3.&nbsp; N I P&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : {{ $tester_nip ?: '' }}</td>
            </tr>
        </table>

        <div class="footnote">
            *) Diisi apabila terjadi antara lain hal-hal sebagai berikut :
            <ol>
                <li>Ketidaksesuaian kondisi yang terjadi di lapangan.</li>
                <li>Ketidaksesuaian jumlah peserta yang hadir (alasan ketidakhadiran)</li>
                <li>Pelanggaran tata tertib oleh peserta ujian, dan lain-lain.</li>
            </ol>
        </div>
    </div>
</body>
</html>
