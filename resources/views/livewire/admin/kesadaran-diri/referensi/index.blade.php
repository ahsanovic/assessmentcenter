<div>
    <x-breadcrumb :breadcrumbs="[
        ['url' => route('admin.dashboard'), 'title' => 'Dashboard'],
        ['url' => null, 'title' => 'Kesadaran Diri'],
        ['url' => null, 'title' => 'Data Referensi']
    ]" />
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">Data Referensi Instrumen Kesadaran Diri</h6>
                    <a href="{{ route('admin.ref-kesadaran-diri.create') }}" class="btn btn-xs btn-outline-primary mt-3">Tambah</a>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Nama Indikator</th>
                                    <th>Nomor Indikator</th>
                                    <th>Kualifikasi/Uraian Potensi</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $index => $item)
                                    <tr>
                                        <td>{{ $data->firstItem() + $index }}</td>
                                        <td class="text-wrap">{{ $item->indikator_nama }}</td>
                                        <td>{{ $item->indikator_nomor }}</td>
                                        <td class="text-wrap">
                                            @if(is_array($item->kualifikasi))
                                                @foreach($item->kualifikasi as $qual)
                                                    <b>{{ $qual['kualifikasi'] ?? '' }}:</b> <br />
                                                    {{ $qual['uraian_potensi'] ?? '' }} <br />
                                                @endforeach
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group dropstart">
                                                <a
                                                    class="btn bt-xs btn-outline-warning"
                                                    wire:navigate
                                                    href="{{ route('admin.ref-kesadaran-diri.edit', $item->id) }}"
                                                >
                                                    Edit
                                                </a>
                                                <button wire:click="deleteConfirmation('{{ $item->id }}')" class="btn btn-xs btn-outline-danger">
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
        <x-pagination :items="$data" />
    </div>
</div>