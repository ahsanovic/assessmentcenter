<div>
    <x-breadcrumb :breadcrumbs="[
        ['url' => route('admin.dashboard'), 'title' => 'Dashboard'],
        ['url' => route('admin.tes-selesai'), 'title' => 'Data Tes Selesai'],
        // ['url' => route('admin.tes-selesai.show-peserta'), 'title' => 'Peserta'],
        ['url' => null, 'title' => 'Report'],
    ]" />

    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="text-center mt-4 mb-3">LAPORAN HASIL PENILAIAN POTENSI</h4>
                    <div class="table-responsive">
                        <table class="table">
                            <tbody>
                                <tr>
                                    <td width="4%">Nomor Peserta</td>
                                    <td width="1%">:</td>
                                    <td>XXXXX</td>
                                </tr>
                                <tr>
                                    <td>Nama Peserta</td>
                                    <td>:</td>
                                    <td>{{ $peserta->nama }}</td>
                                </tr>
                                <tr>
                                    <td>Jabatan</td>
                                    <td>:</td>
                                    <td>{{ $peserta->jabatan }}</td>
                                </tr>
                                <tr>
                                    <td>Instansi</td>
                                    <td>:</td>
                                    <td>{{ $peserta->instansi . ' - ' . $peserta->unit_kerja }}</td>
                                </tr>
                                <tr>
                                    <td>Tanggal Pemeriksaan</td>
                                    <td>:</td>
                                    <td>XXXXX</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <br><br>
                    <h5>GRAFIK POTENSI</h5>
                    <div class="table-responsive col-12 col-md-12 col-sm-12">
                        <table class="table">
                            <thead class="text-center align-middle">
                                <tr rowspan="2">
                                    <th rowspan="2">NO</th>
                                    <th rowspan="2">ASPEK POTENSI</th>
                                    <th rowspan="2">DEFINISI ASPEK POTENSI</th>
                                    <th colspan="5">LEVEL POTENSI</th>
                                </tr>
                                <tr>
                                    <th>1</th>
                                    <th>2</th>
                                    <th>3</th>
                                    <th>4</th>
                                    <th>5</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $no = 1;    
                                @endphp
                                @foreach ($aspek_potensi as $item)
                                <tr>
                                    <td>{{ $no++ }}</td>
                                    <td class="text-wrap">{{ $item->alat_tes }}</td>
                                    <td class="text-wrap">{{ $item->definisi_aspek_potensi }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <br>
                    Keterangan : Level 3 adalah standar seharusnya dicapai
                    <br><br>

                    <h5>DESKRIPSI POTENSI</h5>
                    
                    {{-- grafik --}}

                    <div class="table-responsive col-12 col-md-12 col-sm-12">
                        <table class="table">
                            <tr>
                                <td>Potensi Belajar Cepat dan Pengembangan diri</td>
                                <td>:</td>
                                <td>Lorem ipsum dolor sit amet, consectetur adipisicing elit. At, possimus! Vel
                                    voluptatibus dolorem, dolorum necessitatibus nisi, quae, sint earum voluptate at
                                    beatae accusantium recusandae! Officia voluptate reiciendis odit qui? Illo?</td>
                            </tr>
                            <tr>
                                <td>Interpersonal</td>
                                <td>:</td>
                                <td>Lorem ipsum dolor sit amet, consectetur adipisicing elit. At, possimus! Vel
                                    voluptatibus dolorem, dolorum necessitatibus nisi, quae, sint earum voluptate at
                                    beatae accusantium recusandae! Officia voluptate reiciendis odit qui? Illo?</td>
                            </tr>
                            <tr>
                                <td>Kecerdasan Emosi</td>
                                <td>:</td>
                                <td>Lorem ipsum dolor sit amet, consectetur adipisicing elit. At, possimus! Vel
                                    voluptatibus dolorem, dolorum necessitatibus nisi, quae, sint earum voluptate at
                                    beatae accusantium recusandae! Officia voluptate reiciendis odit qui? Illo?</td>
                            </tr>
                            <tr>
                                <td>Motivasi dan Komitmen</td>
                                <td>:</td>
                                <td>Lorem ipsum dolor sit amet, consectetur adipisicing elit. At, possimus! Vel
                                    voluptatibus dolorem, dolorum necessitatibus nisi, quae, sint earum voluptate at
                                    beatae accusantium recusandae! Officia voluptate reiciendis odit qui? Illo?</td>
                            </tr>
                            <tr>
                                <td>Berpikir Kritis dan Strategis</td>
                                <td>:</td>
                                <td>Lorem ipsum dolor sit amet, consectetur adipisicing elit. At, possimus! Vel
                                    voluptatibus dolorem, dolorum necessitatibus nisi, quae, sint earum voluptate at
                                    beatae accusantium recusandae! Officia voluptate reiciendis odit qui? Illo?</td>
                            </tr>
                            <tr>
                                <td>Problem Solving</td>
                                <td>:</td>
                                <td>Lorem ipsum dolor sit amet, consectetur adipisicing elit. At, possimus! Vel
                                    voluptatibus dolorem, dolorum necessitatibus nisi, quae, sint earum voluptate at
                                    beatae accusantium recusandae! Officia voluptate reiciendis odit qui? Illo?</td>
                            </tr>
                            <tr>
                                <td>Kesadaran Diri</td>
                                <td>:</td>
                                <td>Lorem ipsum dolor sit amet, consectetur adipisicing elit. At, possimus! Vel
                                    voluptatibus dolorem, dolorum necessitatibus nisi, quae, sint earum voluptate at
                                    beatae accusantium recusandae! Officia voluptate reiciendis odit qui? Illo?</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
