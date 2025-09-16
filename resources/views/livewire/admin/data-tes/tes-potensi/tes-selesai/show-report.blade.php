<div>
    <x-breadcrumb :breadcrumbs="[
        ['url' => route('admin.dashboard'), 'title' => 'Dashboard'],
        ['url' => route('admin.tes-selesai'), 'title' => 'Data Tes Selesai'],
        ['url' => route('admin.tes-selesai.show-peserta', ['idEvent' => $id_event]), 'title' => 'Peserta'],
        ['url' => null, 'title' => 'Report'],
    ]" />

    <div class="container">
        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="text-center mt-4 mb-4">LAPORAN PENILAIAN POTENSI INDIVIDU</h4>
                        <div class="d-flex justify-content-between mb-4 mx-3">
                            <div>Tujuan: Pemetaan Potensi</div>
                            <div>Tanggal Pemeriksaan : {{ \Carbon\Carbon::parse($peserta->test_started_at)->format('d F Y') ?? '-' }}</div>
                        </div>
                        <h6>A. IDENTITAS</h6>
                        <div class="table">
                            <table class="table">
                                <tbody>
                                    <tr>
                                        <td>Nama</td>
                                        <td>:</td>
                                        <td>{{ $peserta->nama }}</td>
                                    </tr>
                                    <tr>
                                        <td>NIP</td>
                                        <td>:</td>
                                        <td>{{ $peserta->nip ?: $peserta->nik }}</td>
                                    </tr>
                                    <tr>
                                        <td>Jabatan Saat Ini</td>
                                        <td>:</td>
                                        <td>{{ $peserta->jabatan }}</td>
                                    </tr>
                                        <td>Unit Kerja</td>
                                        <td>:</td>
                                        <td>{{ $peserta->instansi . ' - ' . $peserta->unit_kerja }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <br><br>
                        <h6>B. POTENSI</h6>
                        <div class="table col-12 col-md-12 col-sm-12 mb-5">
                            <table class="table">
                                <thead class="text-center align-middle">
                                    <tr rowspan="2">
                                        <th rowspan="2" colspan="2">ASPEK POTENSI</th>
                                        <th>CAPAIAN LEVEL</th>
                                        <th>KETERANGAN</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $no = 1;    
                                    @endphp
                                    @foreach ($aspek_potensi as $item)
                                    <tr>
                                        <td>{{ $no++ }}</td>
                                        <td class="text-wrap">
                                            {{ $item->alat_tes }} <br/>
                                            <i>
                                            ({{ $item->definisi_aspek_potensi }})
                                            </i>
                                        </td>
                                        @switch($item->alat_tes)
                                            @case('Intelektual')
                                                <td>{{ $capaian_level_intelektual ?? '-' }}</td>
                                                <td>{{ $data->hasilIntelektual[0]->kategori ?? '-' }}</td>
                                            @break
                                            @case('Kemampuan Interpersonal')
                                                <td>{{ $data->hasilInterpersonal[0]->level_total ?? '-' }}</td>
                                                <td>{{ $data->hasilInterpersonal[0]->kualifikasi_total ?? '-' }}</td>
                                                @break
                                            @case('Kecerdasan Emosi')
                                                <td>{{ $data->hasilKecerdasanEmosi[0]->level_total ?? '-' }}</td>
                                                <td>{{ $data->hasilKecerdasanEmosi[0]->kualifikasi_total ?? '-' }}</td>
                                                @break
                                            @case('Belajar Cepat dan Pengembangan Diri')
                                                <td>{{ $data->hasilPengembanganDiri[0]->level_total ?? '-' }}</td>
                                                <td>{{ $data->hasilPengembanganDiri[0]->kualifikasi_total ?? '-' }}</td>
                                                @break
                                            @case('Problem Solving')
                                                <td>{{ $data->hasilProblemSolving[0]->level_total ?? '-' }}</td>
                                                <td>{{ $data->hasilProblemSolving[0]->kualifikasi_total ?? '-' }}</td>
                                                @break
                                            @case('Motivasi dan Komitmen')
                                                <td>{{ $data->hasilMotivasiKomitmen[0]->level_total ?? '-' }}</td>
                                                <td>{{ $data->hasilMotivasiKomitmen[0]->kualifikasi_total ?? '-' }}</td>
                                                @break
                                            @case('Berpikir Kritis dan Strategis')
                                                <td>{{ $data->hasilBerpikirKritis[0]->level_total ?? '-' }}</td>
                                                <td>{{ $data->hasilBerpikirKritis[0]->kualifikasi_total ?? '-' }}</td>
                                                @break
                                            @case('Kesadaran Diri')
                                                <td>{{ $data->hasilKesadaranDiri[0]->level_total ?? '-' }}</td>
                                                <td>{{ $data->hasilKesadaranDiri[0]->kualifikasi_total ?? '-' }}</td>
                                                @break
                                            @default
                                        @endswitch
                                    </tr>
                                    @endforeach
                                    <tr>
                                        <td colspan="2"><strong>Job Person Match (JPM)</strong></td>
                                        <td>
                                            @php
                                                $capaian_level = [
                                                    $capaian_level_intelektual,
                                                    $capaian_level_interpersonal,
                                                    $capaian_level_kecerdasan_emosi,
                                                    $capaian_level_pengembangan_diri,
                                                    $capaian_level_problem_solving,
                                                    $capaian_level_motivasi_komitmen,
                                                    $capaian_level_berpikir_kritis,
                                                    $capaian_level_kesadaran_diri
                                                ];
                                            @endphp
                                            <b>
                                                @php
                                                    $jpm = countJpm($capaian_level);
                                                @endphp
                                                {{ number_format($jpm * 100, 2) }} %
                                            </b>
                                        </td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td colspan="2"><strong>Kategori</strong></td>
                                        <td>
                                            <b>
                                                {{ getKategori($jpm) }}
                                            </b>
                                        </td>
                                        <td></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <h6>C. DESKRIPSI POTENSI</h6>
                        <div class="table col-12 col-md-12 col-sm-12">
                            <table class="table">
                                @php
                                    $no = 1;    
                                @endphp
                                @foreach ($aspek_potensi as $item)
                                <tr>
                                    <td>{{ $no++ }}</td>
                                    <td class="text-wrap">
                                        <strong>{{ $item->alat_tes }}</strong> <br/>
                                        @switch($item->alat_tes)
                                            @case('Intelektual')
                                            <ul>
                                                <li>{{ $data->hasilIntelektual[0]->uraian_potensi_subtes_1 ?? '-' }}</li>
                                                <li>{{ $data->hasilIntelektual[0]->uraian_potensi_subtes_2 ?? '-' }}</li>
                                                <li>{{ $data->hasilIntelektual[0]->uraian_potensi_subtes_3 ?? '-' }}</li>
                                            </ul>
                                            @break
                                            @case('Kemampuan Interpersonal')
                                                <ul>
                                                    <li>{{ json_decode($data->hasilInterpersonal[0]->uraian_potensi_1)->uraian_potensi ?? '-' }}</li>
                                                    <li>{{ json_decode($data->hasilInterpersonal[0]->uraian_potensi_2)->uraian_potensi ?? '-' }}</li>
                                                    <li>{{ json_decode($data->hasilInterpersonal[0]->uraian_potensi_3)->uraian_potensi ?? '-' }}</li>
                                                    <li>{{ json_decode($data->hasilInterpersonal[0]->uraian_potensi_4)->uraian_potensi ?? '-' }}</li>
                                                    <li>{{ json_decode($data->hasilInterpersonal[0]->uraian_potensi_5)->uraian_potensi ?? '-' }}</li>
                                                </ul>
                                                @break
                                            @case('Kecerdasan Emosi')
                                                <ul>
                                                    <li>{{ json_decode($data->hasilKecerdasanEmosi[0]->uraian_potensi_1)->uraian_potensi ?? '-' }}</li>
                                                    <li>{{ json_decode($data->hasilKecerdasanEmosi[0]->uraian_potensi_2)->uraian_potensi ?? '-' }}</li>
                                                    <li>{{ json_decode($data->hasilKecerdasanEmosi[0]->uraian_potensi_3)->uraian_potensi ?? '-' }}</li>
                                                    <li>{{ json_decode($data->hasilKecerdasanEmosi[0]->uraian_potensi_4)->uraian_potensi ?? '-' }}</li>
                                                </ul>
                                                @break
                                            @case('Belajar Cepat dan Pengembangan Diri')
                                                <ul>
                                                    <li>{{ json_decode($data->hasilPengembanganDiri[0]->uraian_potensi_1)->uraian_potensi ?? '-' }}</li>
                                                    <li>{{ json_decode($data->hasilPengembanganDiri[0]->uraian_potensi_2)->uraian_potensi ?? '-' }}</li>
                                                    <li>{{ json_decode($data->hasilPengembanganDiri[0]->uraian_potensi_3)->uraian_potensi ?? '-' }}</li>
                                                    <li>{{ json_decode($data->hasilPengembanganDiri[0]->uraian_potensi_4)->uraian_potensi ?? '-' }}</li>
                                                    <li>{{ json_decode($data->hasilPengembanganDiri[0]->uraian_potensi_5)->uraian_potensi ?? '-' }}</li>
                                                </ul>
                                                @break
                                            @case('Problem Solving')
                                                @if ($data->hasilproblemSolving[0]->uraian_potensi == null)
                                                    <ul>
                                                        <li>{{ json_decode($data->hasilProblemSolving[0]->uraian_potensi_1)->deskripsi ?? '-' }}</li>
                                                        <li>{{ json_decode($data->hasilProblemSolving[0]->uraian_potensi_2)->deskripsi ?? '-' }}</li>
                                                        <li>{{ json_decode($data->hasilProblemSolving[0]->uraian_potensi_3)->deskripsi ?? '-' }}</li>
                                                        <li>{{ json_decode($data->hasilProblemSolving[0]->uraian_potensi_4)->deskripsi ?? '-' }}</li>
                                                        <li>{{ json_decode($data->hasilProblemSolving[0]->uraian_potensi_5)->deskripsi ?? '-' }}</li>
                                                        <li>{{ json_decode($data->hasilProblemSolving[0]->uraian_potensi_6)->deskripsi ?? '-' }}</li>
                                                        <li>{{ json_decode($data->hasilProblemSolving[0]->uraian_potensi_7)->deskripsi ?? '-' }}</li>
                                                        <li>{{ json_decode($data->hasilProblemSolving[0]->uraian_potensi_8)->deskripsi ?? '-' }}</li>
                                                    </ul>
                                                @else
                                                    {{ $data->hasilProblemSolving[0]->uraian_potensi ?? '-' }}                                                
                                                @endif
                                                @break
                                            @case('Motivasi dan Komitmen')
                                                {{ $data->hasilMotivasiKomitmen[0]->deskripsi ?? '-' }}
                                                @break
                                            @case('Berpikir Kritis dan Strategis')
                                                @if ($data->hasilBerpikirKritis[0]->uraian_potensi == null)
                                                    <ul>
                                                        <li>{{ json_decode($data->hasilBerpikirKritis[0]->uraian_potensi_1)->deskripsi ?? '-' }}</li>
                                                        <li>{{ json_decode($data->hasilBerpikirKritis[0]->uraian_potensi_2)->deskripsi ?? '-' }}</li>
                                                        <li>{{ json_decode($data->hasilBerpikirKritis[0]->uraian_potensi_3)->deskripsi ?? '-' }}</li>
                                                        <li>{{ json_decode($data->hasilBerpikirKritis[0]->uraian_potensi_4)->deskripsi ?? '-' }}</li>
                                                        <li>{{ json_decode($data->hasilBerpikirKritis[0]->uraian_potensi_5)->deskripsi ?? '-' }}</li>
                                                        <li>{{ json_decode($data->hasilBerpikirKritis[0]->uraian_potensi_6)->deskripsi ?? '-' }}</li>
                                                        <li>{{ json_decode($data->hasilBerpikirKritis[0]->uraian_potensi_7)->deskripsi ?? '-' }}</li>
                                                        <li>{{ json_decode($data->hasilBerpikirKritis[0]->uraian_potensi_8)->deskripsi ?? '-' }}</li>
                                                    </ul>
                                                @else
                                                    {{ $data->hasilBerpikirKritis[0]->uraian_potensi ?? '-' }}                                                
                                                @endif
                                                @break
                                            @case('Kesadaran Diri')
                                                <ul>
                                                    <li>{{ json_decode($data->hasilKesadaranDiri[0]->uraian_potensi_1)->uraian_potensi ?? '-' }}</li>
                                                    <li>{{ json_decode($data->hasilKesadaranDiri[0]->uraian_potensi_2)->uraian_potensi ?? '-' }}</li>
                                                    <li>{{ json_decode($data->hasilKesadaranDiri[0]->uraian_potensi_3)->uraian_potensi ?? '-' }}</li>
                                                </ul>
                                                @break
                                            @default
                                        @endswitch
                                    </td>
                                </tr>
                                @endforeach
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
