<div>
    <x-breadcrumb :breadcrumbs="[
        ['url' => route('peserta.dashboard'), 'title' => 'Dashboard'],
        ['url' => route('peserta.portofolio'), 'title' => 'Portofolio'],
        ['url' => null, 'title' => 'Karir'],
    ]" />
    <x-alert :type="'danger'" :teks="'Lengkapi portofolio Anda dengan detail yang mencerminkan perjalanan profesional dan pribadi Anda secara menyeluruh.
        Mulai dari biodata, pendidikan, pelatihan yang telah diikuti, riwayat karir, pengalaman kerja, hingga penilaian pribadi. 
        Tunjukkan potensi terbaik Anda dan ciptakan kesan mendalam melalui portofolio yang informatif dan menarik!'" />
    <div class="row">
        <div class="row">
            <x-tab-nav :nav="[
                ['url' => route('peserta.biodata'), 'title' => 'Biodata', 'active' => null],
                ['url' => route('peserta.pendidikan'), 'title' => 'Pendidikan', 'active' => null],
                ['url' => route('peserta.pelatihan'), 'title' => 'Pelatihan', 'active' => null],
                ['url' => route('peserta.karir'), 'title' => 'Karir', 'active' => 'active'],
                ['url' => route('peserta.pengalaman'), 'title' => 'Pengalaman Spesifik', 'active' => null],
                ['url' => route('peserta.penilaian'), 'title' => 'Penilaian Pribadi', 'active' => null],
            ]" />
            <div class="col-8 col-md-10 ps-0">
                <div class="tab-content tab-content-vertical border p-3" id="v-tabContent">
                    <div class="tab-pane fade show active" role="tabpanel">
                        <h5 class="mb-4">Karir</h5>
                        <x-alert :type="'success'" :teks="'Riwayat karir 5 tahun terakhir'" />
                        <a href="{{ route('peserta.karir.create') }}" wire:navigate
                            class="btn btn-xs btn-outline-primary mt-2 mb-3">Tambah</a>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Jangka Waktu</th>
                                        <th>Instansi</th>
                                        <th>Jabatan</th>
                                        <th>Uraian Tugas / Tanggung Jawab</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($karir as $index => $item)
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
                                            <td>
                                                <div class="btn-group dropstart">
                                                    <a class="btn btn-xs btn-outline-warning" wire:navigate
                                                        href="{{ route('peserta.karir.edit', $item->id) }}">
                                                        Edit
                                                    </a>
                                                    <button wire:click="deleteConfirmation('{{ $item->id }}')"
                                                        class="btn btn-xs btn-outline-danger">
                                                        Hapus
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
