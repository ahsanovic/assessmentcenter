<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Absensi {{ $event->nama_event }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 14mm 12mm 16mm 12mm;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10.5px;
            color: #000;
            margin: 0;
            padding: 0;
            line-height: 1.35;
        }

        .document-title {
            text-align: center;
            font-size: 12.5px;
            font-weight: bold;
            text-transform: uppercase;
            margin: 0 0 14px 0;
            line-height: 1.45;
            padding: 0 8px;
        }

        .meta {
            margin-bottom: 12px;
        }

        .meta table {
            border-collapse: collapse;
            width: 100%;
        }

        .meta td {
            padding: 2px 0;
            vertical-align: top;
            font-size: 10.5px;
        }

        .meta .label {
            width: 88px;
            font-weight: bold;
        }

        .meta .separator {
            width: 10px;
            text-align: center;
            font-weight: bold;
        }

        table.absensi {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        table.absensi thead {
            display: table-header-group;
        }

        table.absensi tbody tr {
            page-break-inside: avoid;
        }

        table.absensi th,
        table.absensi td {
            border: 1px solid #000;
            vertical-align: middle;
        }

        table.absensi th {
            text-align: center;
            font-weight: bold;
            font-size: 10.5px;
            padding: 6px 4px;
            background-color: #fff;
        }

        table.absensi td {
            padding: 5px 6px;
            font-size: 10px;
        }

        .col-no {
            width: 7%;
            text-align: center;
            font-weight: bold;
            padding: 5px 2px !important;
            font-size: 9.5px;
        }

        .col-nama {
            width: 33%;
            text-align: left;
            word-wrap: break-word;
        }

        .col-unit {
            width: 35%;
            text-align: left;
            word-wrap: break-word;
        }

        .col-ttd {
            width: 25%;
            padding: 0 !important;
            vertical-align: bottom;
        }

        table.ttd-inner {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        table.ttd-inner td {
            border: none;
            height: 28px;
            vertical-align: bottom;
            font-size: 9.5px;
            padding: 0 3px 5px;
            line-height: 1;
        }

        .ttd-slot-left {
            width: 50%;
            text-align: left;
        }

        .ttd-slot-right {
            width: 50%;
            text-align: right;
        }

        .ttd-sign-line {
            white-space: nowrap;
            display: inline-block;
        }

        .ttd-sign-line .ttd-number {
            display: inline-block;
            margin-right: 2px;
        }

        .row-empty td.col-nama,
        .row-empty td.col-unit {
            background-color: #fff;
        }
    </style>
</head>
<body>
    <h1 class="document-title">DAFTAR HADIR PESERTA <br />{!! nl2br(e($judul)) !!}</h1>

    <div class="meta">
        <table>
            <tr>
                <td class="label">Hari, Tanggal</td>
                <td class="separator">:</td>
                <td>{{ $tanggal }}</td>
            </tr>
            <tr>
                <td class="label">Sesi</td>
                <td class="separator">:</td>
                <td>{{ $sesi }}</td>
            </tr>
            <tr>
                <td class="label">Waktu</td>
                <td class="separator">:</td>
                <td>{{ $waktu }}</td>
            </tr>
            <tr>
                <td class="label">Tempat</td>
                <td class="separator">:</td>
                <td>{{ $tempat }}</td>
            </tr>
        </table>
    </div>

    <table class="absensi">
        <colgroup>
            <col style="width: 7%;">
            <col style="width: 33%;">
            <col style="width: 35%;">
            <col style="width: 25%;">
        </colgroup>
        <thead>
            <tr>
                <th class="col-no">NO</th>
                <th class="col-nama">NAMA</th>
                <th class="col-unit">UNIT KERJA</th>
                <th class="col-ttd">TANDA TANGAN</th>
            </tr>
        </thead>
        <tbody>
            @php
                $rows = $event->peserta;
                $pesertaCount = $rows->count();
                $totalRows = $pesertaCount + (int) $extraRows;
            @endphp

            @for ($i = 0; $i < $totalRows; $i++)
                @php
                    $nomor = $i + 1;
                    $peserta = $rows->get($i);
                    $isEmpty = $i >= $pesertaCount;
                    $isOdd = $nomor % 2 === 1;
                @endphp
                <tr @class(['row-empty' => $isEmpty])>
                    <td class="col-no">{{ $nomor }}</td>
                    <td class="col-nama">{{ $isEmpty ? '' : $peserta->nama }}</td>
                    <td class="col-unit">{{ $isEmpty ? '' : $peserta->unit_kerja }}</td>
                    <td class="col-ttd">
                        <table class="ttd-inner">
                            <tr>
                                @if ($isOdd)
                                    <td class="ttd-slot-left">
                                        <span class="ttd-sign-line">
                                            <span class="ttd-number">{{ $nomor }}.</span><span>.............................</span>
                                        </span>
                                    </td>
                                    <td class="ttd-slot-right">&nbsp;</td>
                                @else
                                    <td class="ttd-slot-left">&nbsp;</td>
                                    <td class="ttd-slot-right">
                                        <span class="ttd-sign-line">
                                            <span class="ttd-number">{{ $nomor }}.</span><span>..............................</span>
                                        </span>
                                    </td>
                                @endif
                            </tr>
                        </table>
                    </td>
                </tr>
            @endfor
        </tbody>
    </table>
</body>
</html>
