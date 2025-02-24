<div>
    <x-breadcrumb :breadcrumbs="[
        ['url' => route('admin.dashboard'), 'title' => 'Dashboard'],
        ['url' => null, 'title' => 'Berpikir Kritis'],
        ['url' => null, 'title' => 'Data Referensi Aspek'],
    ]" />
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">Data Referensi Aspek Berpikir Kritis</h6>
                    <a href="{{ route('admin.ref-aspek-berpikir-kritis.create') }}" wire:navigate class="btn btn-xs btn-outline-primary mt-3">Tambah</a>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Nama Aspek</th>
                                    <th>Nomor Aspek</th>
                                    <th>Nomor Indikator</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $index => $item)
                                    <tr>
                                        <td>{{ $data->firstItem() + $index }}</td>
                                        <td>{{ $item->aspek }}</td>
                                        <td>{{ $item->aspek_nomor }}</td>
                                        <td class="text-wrap">
                                            @php $split_indikator = explode(',', $item->indikator_nomor); @endphp
                                            @foreach ($split_indikator as $indikator)
                                                <span class="badge bg-danger">{{ $indikator }}</span>
                                            @endforeach
                                        </td>
                                        <td>
                                            <div class="btn-group dropstart">
                                                <a
                                                    class="btn btn-xs btn-outline-warning"
                                                    wire:navigate
                                                    href="{{ route('admin.ref-aspek-berpikir-kritis.edit', $item->id) }}"
                                                >
                                                    Edit
                                                </a>
                                                <button wire:click="deleteConfirmation('{{ $item->id }}')" tabindex="0" class="btn btn-xs btn-outline-danger">
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
