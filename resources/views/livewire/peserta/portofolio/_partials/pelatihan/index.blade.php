<div>
    <x-breadcrumb :breadcrumbs="[
        ['url' => route('peserta.dashboard'), 'title' => 'Dashboard'],
        ['url' => route('peserta.portofolio'), 'title' => 'Portofolio'],
        ['url' => null, 'title' => 'Pelatihan'],
    ]" />
    <x-alert :type="'danger'" :teks="'Lengkapi portofolio Anda dengan detail yang mencerminkan perjalanan profesional dan pribadi Anda secara menyeluruh.
        Mulai dari biodata, pendidikan, pelatihan yang telah diikuti, riwayat karir, pengalaman kerja, hingga penilaian pribadi. 
        Tunjukkan potensi terbaik Anda dan ciptakan kesan mendalam melalui portofolio yang informatif dan menarik!'" />
    <div class="row">
        <div class="row">
            <x-tab-nav :nav="[
                ['url' => route('peserta.biodata'), 'title' => 'Biodata', 'active' => null],
                ['url' => route('peserta.pendidikan'), 'title' => 'Pendidikan', 'active' => null],
                ['url' => route('peserta.pelatihan'), 'title' => 'Pelatihan', 'active' => 'active'],
                ['url' => route('peserta.karir'), 'title' => 'Karir', 'active' => null],
                ['url' => route('peserta.pengalaman'), 'title' => 'Pengalaman Spesifik', 'active' => null],
                ['url' => route('peserta.penilaian'), 'title' => 'Penilaian Pribadi', 'active' => null],
            ]" />
            <div class="col-8 col-md-10 ps-0">
                <div class="tab-content tab-content-vertical border p-3" id="v-tabContent">
                    <div class="tab-pane fade show active" role="tabpanel">
                        <h5 class="mb-4">Pelatihan</h5>
                        <x-alert :type="'success'" :teks="'Riwayat pelatihan 5 tahun terakhir'" />
                        <a href="{{ route('peserta.pelatihan.create') }}" wire:navigate
                            class="btn btn-xs btn-outline-primary mt-2 mb-3">Tambah</a>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Nama Institusi</th>
                                        <th>Tanggal Pelatihan</th>
                                        <th>Subjek Pelatihan</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($pelatihan as $index => $item)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td class="text-wrap">{{ $item->nama_institusi }}</td>
                                            <td>{{ \Carbon\Carbon::parse($item->tgl_mulai)->translatedFormat('d M Y') }}
                                                -
                                                {{ \Carbon\Carbon::parse($item->tgl_selesai)->translatedFormat('d M Y') }}
                                            </td>
                                            <td class="text-wrap">{{ $item->subjek_pelatihan }}</td>
                                            <td>
                                                <div class="btn-group dropstart">
                                                    <a class="btn btn-xs btn-outline-warning" wire:navigate
                                                        href="{{ route('peserta.pelatihan.edit', $item->id) }}">
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
