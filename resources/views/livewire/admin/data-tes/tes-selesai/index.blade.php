<div>
    <x-breadcrumb :breadcrumbs="[
        ['url' => route('admin.dashboard'), 'title' => 'Dashboard'],
        ['url' => null, 'title' => 'Data Tes Selesai']
    ]" />
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">Data Tes Selesai</h6>
                    <h6 class="mt-4 text-danger"><i class="link-icon" data-feather="filter"></i> Filter</h6>
                    <div class="row mt-2">
                        <div class="col-sm-3">
                            <div class="mb-3">
                                <select wire:model.live="jabatan_diuji" class="form-select form-select-sm" id="jabatan-diuji">
                                    <option value="">pilih jabatan diuji</option>
                                    @foreach ($option_jabatan_diuji as $key => $item)
                                        <option value="{{ $key }}">{{ $item }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="mb-3">
                                <div class="input-group flatpickr" id="flatpickr-date">
                                    <input type="text" wire:model.live="tgl_mulai"
                                        class="form-control flatpickr-input" placeholder="tgl mulai pelaksanaan"
                                        data-input="" readonly="readonly">
                                    <span class="input-group-text input-group-addon" data-toggle="">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round"
                                            class="feather feather-calendar">
                                            <rect x="3" y="4" width="18" height="18" rx="2"
                                                ry="2"></rect>
                                            <line x1="16" y1="2" x2="16" y2="6"></line>
                                            <line x1="8" y1="2" x2="8" y2="6"></line>
                                            <line x1="3" y1="10" x2="21" y2="10"></line>
                                        </svg>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="mb-3">
                                <input wire:model.live.debounce="search" class="form-control form-control-sm" placeholder="cari event" />
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="mb-3">
                                <button wire:click="resetFilters" class="btn btn-sm btn-inverse-danger">Reset</button>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Nama Event</th>
                                    <th class="text-wrap">Jumlah Peserta</th>
                                    <th>Interpersonal</th>
                                    <th class="text-wrap">Kesadaran Diri</th>
                                    <th class="text-wrap">Berpikir Kritis dan Strategis</th>
                                    <th class="text-wrap">Problem Solving</th>
                                    <th class="text-wrap">Kecerdasan Emosi</th>
                                    <th class="text-wrap">Pengembangan Diri</th>
                                    <th class="text-wrap">Motivasi Komitmen</th>
                                    <th class="text-wrap">Semua Tes</th>
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
                                                href="{{ route('admin.tes-selesai.show-peserta-interpersonal', ['idEvent' => $item->id]) }}">
                                                    {{ $item->hasil_interpersonal_count ?? 0 }} orang
                                            </a>
                                        </td>
                                        <td>
                                            <a class="btn btn-xs btn-warning" wire:navigate
                                                href="{{ route('admin.tes-selesai.show-peserta-kesadaran-diri', ['idEvent' => $item->id]) }}">
                                                    {{ $item->hasil_kesadaran_diri_count ?? 0 }} orang
                                            </a>
                                        </td>
                                        <td>
                                            <a class="btn btn-xs btn-warning" wire:navigate
                                                href="{{ route('admin.tes-selesai.show-peserta-berpikir-kritis', ['idEvent' => $item->id]) }}">
                                                    {{ $item->hasil_berpikir_kritis_count ?? 0 }} orang
                                            </a>
                                        </td>
                                        <td>
                                            <a class="btn btn-xs btn-warning" wire:navigate
                                                href="{{ route('admin.tes-selesai.show-peserta-problem-solving', ['idEvent' => $item->id]) }}">
                                                    {{ $item->hasil_problem_solving_count ?? 0 }} orang
                                            </a>
                                        </td>
                                        <td>
                                            <a class="btn btn-xs btn-warning" wire:navigate
                                                href="{{ route('admin.tes-selesai.show-peserta-kecerdasan-emosi', ['idEvent' => $item->id]) }}">
                                                    {{ $item->hasil_kecerdasan_emosi_count ?? 0 }} orang
                                            </a>
                                        </td>
                                        <td>
                                            <a class="btn btn-xs btn-warning" wire:navigate
                                                href="{{ route('admin.tes-selesai.show-peserta-pengembangan-diri', ['idEvent' => $item->id]) }}">
                                                    {{ $item->hasil_pengembangan_diri_count ?? 0 }} orang
                                            </a>
                                        </td>
                                        <td>
                                            <a class="btn btn-xs btn-warning" wire:navigate
                                                href="{{ route('admin.tes-selesai.show-peserta-motivasi-komitmen', ['idEvent' => $item->id]) }}">
                                                    {{ $item->hasil_motivasi_komitmen_count ?? 0 }} orang
                                            </a>
                                        </td>
                                        <td>
                                            <a class="btn btn-xs btn-warning" wire:navigate
                                                href="{{ route('admin.tes-selesai.show-peserta', ['idEvent' => $item->id]) }}">
                                                    {{ $item->peserta_selesai_count ?? 0 }} orang
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
