<div>
    <x-breadcrumb :breadcrumbs="[
        ['url' => route('admin.dashboard'), 'title' => 'Dashboard'],
        ['url' => null, 'title' => 'Data Tes Sedang Berlangsung']
    ]" />
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">Data Tes Potensi (Sedang Berlangsung)</h6>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th class="text-wrap">Nama Event</th>
                                    <th class="text-wrap">Jumlah Peserta</th>
                                    <th class="text-wrap">Interpersonal</th>
                                    <th class="text-wrap">Kesadaran Diri</th>
                                    <th class="text-wrap">Berpikir Kritis dan Strategis</th>
                                    <th class="text-wrap">Problem Solving</th>
                                    <th class="text-wrap">Kecerdasan Emosi</th>
                                    <th class="text-wrap">Pengembangan Diri</th>
                                    <th class="text-wrap">Motivasi Komitmen</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $index => $item)
                                    <tr>
                                        <td>{{ $data->firstItem() + $index }}</td>
                                        <td class="text-wrap">{{ $item->nama_event }}</td>
                                        <td>{{ $item->jumlah_peserta }}</td>
                                        <td>
                                            <a class="btn btn-xs btn-warning" wire:navigate
                                                href="{{ route('admin.tes-berlangsung.show-peserta-interpersonal', ['idEvent' => $item->id]) }}">
                                                    {{ $item->ujian_interpersonal_count }} orang
                                            </a>
                                        </td>
                                        <td>
                                            <a class="btn btn-xs btn-warning" wire:navigate
                                                href="{{ route('admin.tes-berlangsung.show-peserta-kesadaran-diri', ['idEvent' => $item->id]) }}">
                                                    {{ $item->ujian_kesadaran_diri_count }} orang
                                            </a>
                                        </td>
                                        <td>
                                            <a class="btn btn-xs btn-warning" wire:navigate
                                                href="{{ route('admin.tes-berlangsung.show-peserta-berpikir-kritis', ['idEvent' => $item->id]) }}">
                                                    {{ $item->ujian_berpikir_kritis_count }} orang
                                            </a>
                                        </td>
                                        <td>
                                            <a class="btn btn-xs btn-warning" wire:navigate
                                                href="{{ route('admin.tes-berlangsung.show-peserta-problem-solving', ['idEvent' => $item->id]) }}">
                                                    {{ $item->ujian_problem_solving_count }} orang
                                            </a>
                                        </td>
                                        <td>
                                            <a class="btn btn-xs btn-warning" wire:navigate
                                                href="{{ route('admin.tes-berlangsung.show-peserta-kecerdasan-emosi', ['idEvent' => $item->id]) }}">
                                                    {{ $item->ujian_kecerdasan_emosi_count }} orang
                                            </a>
                                        </td>
                                        <td>
                                            <a class="btn btn-xs btn-warning" wire:navigate
                                                href="{{ route('admin.tes-berlangsung.show-peserta-pengembangan-diri', ['idEvent' => $item->id]) }}">
                                                    {{ $item->ujian_pengembangan_diri_count }} orang
                                            </a>
                                        </td>
                                        <td>
                                            <a class="btn btn-xs btn-warning" wire:navigate
                                                href="{{ route('admin.tes-berlangsung.show-peserta-motivasi-komitmen', ['idEvent' => $item->id]) }}">
                                                    {{ $item->ujian_motivasi_komitmen_count }} orang
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <x-pagination :items="$data" />
    </div>
</div>
