<div>
    <x-breadcrumb :breadcrumbs="[
        ['url' => route('admin.dashboard'), 'title' => 'Dashboard'],
        ['url' => null, 'title' => 'Intelektual'],
        ['url' => null, 'title' => 'Soal Sub Tes 1']
    ]" />
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">Data Soal Sub Tes 1</h6>
                    <a href="{{ route('admin.soal-intelektual-subtes1.create') }}" wire:navigate class="btn btn-xs btn-outline-primary mt-3">Tambah</a>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Model Soal</th>
                                    <th>Soal</th>
                                    <th>Opsi A</th>
                                    <th>Opsi B</th>
                                    <th>Opsi C</th>
                                    <th>Opsi D</th>
                                    <th>Opsi E</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $index => $item)
                                    <tr>
                                        <td>{{ $data->firstItem() + $index }}</td>
                                        <td>{{ $item->modelSoal->jenis }}</td>
                                        <td class="text-wrap">{{ $item->soal }}</td>
                                        <td>{{ $item->opsi_a }}</td>
                                        <td>{{ $item->opsi_b }}</td>
                                        <td>{{ $item->opsi_c }}</td>
                                        <td>{{ $item->opsi_d }}</td>
                                        <td>{{ $item->opsi_e }}</td>
                                        <td>
                                            <div class="btn-group">
                                                <a
                                                    class="btn btn-xs btn-outline-warning"
                                                    wire:navigate
                                                    href="{{ route('admin.soal-intelektual-subtes1.edit', $item->id) }}"
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
