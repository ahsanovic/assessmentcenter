<div>
    <x-breadcrumb :breadcrumbs="[
        ['url' => route('assessor.dashboard'), 'title' => 'Dashboard'],
        ['url' => route('assessor.event'), 'title' => 'Event'],
        ['url' => route('assessor.event.show-peserta', $id_event), 'title' => 'Asesi'],
        ['url' => null, 'title' => 'Portofolio'],
    ]" />
    <div class="row">
        <div class="col-8 col-md-10 ps-0">
            <div>
                <h4 class="text-center">Portofolio</h4>
                <div class="mt-3">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="mb-3 text-primary">Biodata</h5>
                            <div class="row mb-2">
                                <div class="col-sm-3">
                                    @if ($biodata->foto)
                                        <img src="{{ asset('storage/' . $biodata->foto) }}" class="img-fluid rounded mb-3" width="100">
                                    @endif
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-sm-2">
                                    <label>Nama</label>
                                </div>
                                <div class="col-sm-9">
                                    <label>: {{ $biodata->nama }}</label>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-sm-2">
                                    <label>NIP / NIK</label>
                                </div>
                                <div class="col-sm-9">
                                    <label>: {{ $biodata->nip }}
                                        {{ $biodata->nik ? '/ ' . $biodata->nik : '' }}</label>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-sm-2">
                                    <label>Tempat / Tanggal Lahir</label>
                                </div>
                                <div class="col-sm-9">
                                    <label>: {{ $biodata->tempat_lahir }}, {{ $biodata->tgl_lahir }}</label>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-sm-2">
                                    <label>Agama</label>
                                </div>
                                <div class="col-sm-9">
                                    <label>: {{ $biodata->agama->agama ?? '' }}</label>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-sm-2">
                                    <label>Jenis Kelamin</label>
                                </div>
                                <div class="col-sm-9">
                                    <label>: {{ $biodata->jk == 'L' ? 'Laki-Laki' : 'Perempuan' }}</label>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-sm-2">
                                    <label>Pangkat / Golongan</label>
                                </div>
                                <div class="col-sm-9">
                                    <label>: {{ $biodata->golPangkat->pangkat ?? '' }} -
                                        {{ $biodata->golPangkat->golongan ?? '' }}</label>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-sm-2">
                                    <label>Jabatan</label>
                                </div>
                                <div class="col-sm-9">
                                    <label>: {{ $biodata->jabatan }}</label>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-sm-2">
                                    <label>Unit Kerja</label>
                                </div>
                                <div class="col-sm-9">
                                    <label>: {{ $biodata->unit_kerja }}</label>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-sm-2">
                                    <label>Instansi</label>
                                </div>
                                <div class="col-sm-9">
                                    <label>: {{ $biodata->instansi }}</label>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-sm-2">
                                    <label>Alamat</label>
                                </div>
                                <div class="col-sm-9">
                                    <label>: {{ $biodata->alamat }}</label>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-sm-2">
                                    <label>Nomor HP</label>
                                </div>
                                <div class="col-sm-9">
                                    <label>: {{ $biodata->no_hp }}</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="mb-2 text-primary">Pendidikan Formal</h5>
                            <div class="table-responsive pt-2">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Jenjang</th>
                                            <th>Nama Sekolah</th>
                                            <th>Tahun Masuk / Lulus</th>
                                            <th>Jurusan</th>
                                            <th>IPK / Nilai</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($pendidikan as $index => $item)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $item->jenjangPendidikan->jenjang ?? '' }}</td>
                                                <td class="text-wrap">{{ $item->nama_sekolah }}</td>
                                                <td>{{ $item->thn_masuk }} - {{ $item->thn_lulus }}</td>
                                                <td class="text-wrap">{{ $item->jurusan }}</td>
                                                <td>{{ $item->ipk }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center">
                                                    <i class="link-icon text-secondary" data-feather="inbox"></i>
                                                    <br />
                                                    <span class="text-secondary">tidak ada data</span>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="mb-2 text-primary">Pelatihan / Kursus</h5>
                            <div class="table-responsive pt-2">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Nama Institusi</th>
                                            <th>Tanggal Pelatihan</th>
                                            <th>Subjek Pelatihan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($pelatihan as $index => $item)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td class="text-wrap">{{ $item->nama_institusi }}</td>
                                                <td>{{ $item->tgl_mulai }} - {{ $item->tgl_selesai }}</td>
                                                <td class="text-wrap">{{ $item->subjek_pelatihan }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center">
                                                    <i class="link-icon text-secondary" data-feather="inbox"></i>
                                                    <br />
                                                    <span class="text-secondary">tidak ada data</span>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="mb-2 text-primary">Riwayat Karir</h5>
                            <div class="table-responsive pt-2">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Jangka Waktu</th>
                                            <th>Instansti</th>
                                            <th>Jabatan</th>
                                            <th>Uraian Tugas / Tanggung Jawab</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($karir as $index => $item)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>
                                                    {{ \Carbon\Carbon::create()->month($item->bulan_mulai)->translatedFormat('F') }}
                                                    - {{ $item->tahun_mulai }} <br />
                                                    s/d <br />
                                                    {{ \Carbon\Carbon::create()->month($item->bulan_selesai)->translatedFormat('F') }}
                                                    - {{ $item->tahun_selesai }}
                                                </td>
                                                <td class="text-wrap">{{ $item->instansi }}</td>
                                                <td class="text-wrap">{{ $item->jabatan }}</td>
                                                <td class="text-wrap">{{ $item->uraian_tugas }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center">
                                                    <i class="link-icon text-secondary" data-feather="inbox"></i>
                                                    <br />
                                                    <span class="text-secondary">tidak ada data</span>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="mb-3 text-primary">Pengalaman Spesifik</h5>
                            @foreach ($pertanyaan as $item)
                                <div class="row mb-2">
                                    <div class="col">
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <label
                                                    class="form-label"><strong>{{ $item->pertanyaan }}</strong></label>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <label class="form-label">{!! $item->jawaban->first()->jawaban ?? '' !!}</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="mb-3 text-primary">Penilaian Pribadi</h5>
                            @foreach ($penilaian as $item)
                                <div class="row mb-2">
                                    <div class="col">
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <label
                                                    class="form-label"><strong>{{ $item->pertanyaan }}</strong></label>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <label class="form-label">{!! $item->jawaban->first()->jawaban ?? '' !!}</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
