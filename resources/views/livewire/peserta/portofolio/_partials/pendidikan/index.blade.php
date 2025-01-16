<div>
    <x-breadcrumb :breadcrumbs="[
        ['url' => route('peserta.dashboard'), 'title' => 'Dashboard'],
        ['url' => route('peserta.portofolio'), 'title' => 'Portofolio'],
        ['url' => null, 'title' => 'Pendidikan'],
    ]" />
    <x-alert :type="'danger'" :teks="'Lengkapi portofolio Anda dengan detail yang mencerminkan perjalanan profesional dan pribadi Anda secara menyeluruh.
        Mulai dari biodata, pendidikan, pelatihan yang telah diikuti, riwayat karir, pengalaman kerja, hingga penilaian pribadi. 
        Tunjukkan potensi terbaik Anda dan ciptakan kesan mendalam melalui portofolio yang informatif dan menarik!'" />
    <div class="row">
        <div class="row">
            <x-tab-nav :nav="[
                ['url' => route('peserta.biodata'), 'title' => 'Biodata', 'active' => null],
                ['url' => route('peserta.pendidikan'), 'title' => 'Pendidikan', 'active' => 'active'],
                ['url' => route('peserta.pelatihan'), 'title' => 'Pelatihan', 'active' => null],
                ['url' => route('peserta.karir'), 'title' => 'Karir', 'active' => null],
                ['url' => route('peserta.pengalaman'), 'title' => 'Pengalaman Spesifik', 'active' => null],
                ['url' => route('peserta.penilaian'), 'title' => 'Penilaian Pribadi', 'active' => null],
            ]" />
            <div class="col-8 col-md-10 ps-0">
                <div class="tab-content tab-content-vertical border p-3" id="v-tabContent">
                    <div class="tab-pane fade show active" role="tabpanel">
                        <h5 class="mb-4">Pendidikan</h5>
                        <x-alert :type="'success'" :teks="'Pendidikan formal dari Tingkat SMA/SLTA/Sederajat'" />
                        <a href="{{ route('peserta.pendidikan.create') }}" wire:navigate
                            class="btn btn-xs btn-outline-primary mt-2 mb-3">Tambah</a>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Jenjang Pendidikan</th>
                                        <th>Nama Sekolah</th>
                                        <th>Tahun Masuk / Lulus</th>
                                        <th>Jurusan</th>
                                        <th>IPK / Nilai</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($pendidikan as $index => $item)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $item->jenjangPendidikan->jenjang ?? '' }}</td>
                                            <td class="text-wrap">{{ $item->nama_sekolah }}</td>
                                            <td>{{ $item->thn_masuk }} - {{ $item->thn_lulus }}</td>
                                            <td class="text-wrap">{{ $item->jurusan }}</td>
                                            <td>{{ $item->ipk }}</td>
                                            <td>
                                                <div class="btn-group dropstart">
                                                    <a class="btn btn-xs btn-outline-warning" wire:navigate
                                                        href="{{ route('peserta.pendidikan.edit', $item->id) }}">
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
