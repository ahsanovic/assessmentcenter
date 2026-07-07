<div>
    <x-breadcrumb :breadcrumbs="[
        ['url' => route('admin.dashboard'), 'title' => 'Dashboard'],
        ['url' => null, 'title' => 'Berita Acara'],
    ]" />

    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <x-modal.btn-add text="Tambah Berita Acara" icon="plus-circle" />

                    <div class="card mt-4 mb-4 bg-light-subtle">
                        <div class="card-body">
                            <h6 class="text-danger" wire:ignore><i class="link-icon" data-feather="filter"></i> Filter</h6>
                            <div class="row mt-2 g-2 align-items-center">
                                <div class="col-sm-3">
                                    <div class="input-group" wire:ignore>
                                        <span class="input-group-text bg-white"><i data-feather="search"></i></span>
                                        <input wire:model.live.debounce="search" class="form-control" placeholder="cari judul / event...">
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="input-group flatpickr" data-filter-model="tanggal">
                                        <input type="text"
                                            class="form-control flatpickr-input" placeholder="tanggal"
                                            data-input="" readonly="readonly">
                                        <span class="input-group-text input-group-addon" data-toggle="">
                                            <i class="link-icon" data-feather="calendar"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <x-btn-reset :text="'Reset'" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle shadow-sm border rounded" style="overflow:hidden;">
                            <thead class="table-light border-bottom">
                                <tr>
                                    <th class="text-center" style="width: 45px;">#</th>
                                    <th>Event</th>
                                    <th>Judul Berita Acara</th>
                                    <th>Tanggal</th>
                                    <th>Peserta Hadir</th>
                                    <th>Dibuat Oleh</th>
                                    <th class="text-center" style="width: 140px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $index => $item)
                                    <tr class="@if($loop->iteration % 2 == 1) bg-body @endif border-bottom">
                                        <td class="text-center text-secondary fw-bold">{{ $data->firstItem() + $index }}</td>
                                        <td class="text-wrap">
                                            <div class="fw-semibold">{{ $item->event?->nama_event }}</div>
                                            <small class="text-muted">{{ $item->event?->metodeTes?->metode_tes }}</small>
                                        </td>
                                        <td class="text-wrap">{{ $item->judul }}</td>
                                        <td>{{ $item->tanggal ? \Carbon\Carbon::parse($item->tanggal)->format('d F Y') : '-' }}</td>
                                        <td>{{ $item->jumlah_peserta_hadir }} / {{ $item->jumlah_peserta_seharusnya ?? '-' }}</td>
                                        <td>{{ $item->creator?->nama ?? '-' }}</td>
                                        <td class="text-center">
                                            <x-table.btn-link
                                                :route="'admin.dokumen.berita-acara.download'"
                                                :params="['id' => $item->id]"
                                                icon="download"
                                                tooltip="Unduh PDF"
                                                target="_blank"
                                            />
                                            <x-table.btn-edit :id="$item->id" />
                                            <x-table.btn-delete :id="$item->id" />
                                        </td>
                                    </tr>
                                @endforeach

                                @if($data->count() === 0)
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">
                                            <i class="link-icon" data-feather="inbox" style="font-size: 24px; opacity: 0.7;"></i>
                                            <div class="mt-2 fw-semibold">Belum ada berita acara...</div>
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <x-pagination :items="$data" />
    </div>

    @if($showModal)
    <div class="modal fade show" style="display: block; background: rgba(0,0,0,0.5);" tabindex="-1"
         wire:key="modal-{{ $isUpdate ? 'edit-'.$editId : 'create' }}"
         x-data="{ init() { setTimeout(() => { if (typeof feather !== 'undefined') feather.replace(); }, 50); } }"
         x-init="init()">
        <div class="modal-dialog modal-dialog-centered modal-xl" style="animation: slideDown 0.3s ease-out;">
            <div class="modal-content" style="border: none; border-radius: 16px; overflow: hidden; box-shadow: 0 20px 60px rgba(0,0,0,0.3);">
                <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; padding: 24px 32px;">
                    <div class="d-flex align-items-center gap-3">
                        <div class="d-flex align-items-center justify-content-center"
                             style="width: 48px; height: 48px; background: rgba(255,255,255,0.2); border-radius: 12px;">
                            @if($isUpdate)
                                <i class="link-icon text-white" data-feather="edit-3" style="width: 24px; height: 24px;"></i>
                            @else
                                <i class="link-icon text-white" data-feather="plus-circle" style="width: 24px; height: 24px;"></i>
                            @endif
                        </div>
                        <div>
                            <h5 class="modal-title text-white fw-bold mb-0" style="font-size: 1.5rem;">
                                {{ $isUpdate ? 'Edit Berita Acara' : 'Tambah Berita Acara' }}
                            </h5>
                            <p class="text-white-50 mb-0 mt-1" style="font-size: 0.875rem;">
                                Lengkapi data pelaksanaan uji kompetensi untuk dokumen berita acara
                            </p>
                        </div>
                    </div>
                    <button type="button" wire:click="closeModal" class="btn-close btn-close-white"></button>
                </div>

                <div class="modal-body" style="padding: 28px 32px; background: #f8f9fa; max-height: 72vh; overflow-y: auto;">
                    <div class="card mb-3 border-0 shadow-sm">
                        <div class="card-body">
                            <h6 class="text-danger mb-3"><i class="link-icon" data-feather="calendar"></i> Konteks Dokumen</h6>
                            <div class="row">
                                <div class="col-md-12">
                                    <label class="form-label fw-semibold mb-2" style="color: #344054; font-size: 0.875rem;">
                                        <span class="d-flex align-items-center gap-2">
                                            <i class="link-icon" data-feather="git-branch" style="width: 16px; height: 16px;"></i>
                                            Mekanisme Penkom <span class="text-danger">*</span>
                                        </span>
                                    </label>
                                    <div class="d-flex gap-3 align-items-center flex-wrap mb-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" id="mekanisme-tusi" value="tusi" wire:model.live="mekanisme_penkom" @disabled($isUpdate)>
                                            <label class="form-check-label @if($isUpdate) text-muted @endif" for="mekanisme-tusi">Tusi</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" id="mekanisme-retribusi" value="retribusi" wire:model.live="mekanisme_penkom" @disabled($isUpdate)>
                                            <label class="form-check-label @if($isUpdate) text-muted @endif" for="mekanisme-retribusi">Retribusi</label>
                                        </div>
                                        @if($isUpdate)
                                            <small class="text-muted">Mekanisme penkom tidak dapat diubah saat edit.</small>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <x-form.select2
                                        label="Event"
                                        icon="calendar"
                                        model="event_id_modal"
                                        placeholder="cari / pilih event"
                                        :required="true"
                                    >
                                        @foreach ($options_event as $key => $item)
                                            <option value="{{ $key }}">{{ $item }}</option>
                                        @endforeach
                                    </x-form.select2>
                                </div>
                                @if($mekanisme_penkom === 'tusi')
                                <div class="col-md-6">
                                    <x-form.select label="Jenis Penkom" icon="tag" model="jenis_penkom" placeholder="- pilih jenis penkom -">
                                        <option value="Uji Kompetensi">Uji Kompetensi</option>
                                        <option value="Uji Potensi">Uji Potensi</option>
                                        <option value="Uji Kompetensi dan Potensi">Uji Kompetensi dan Potensi</option>
                                    </x-form.select>
                                </div>
                                @endif
                                <div class="col-md-6">
                                    <x-form.input
                                        label="Judul Berita Acara"
                                        icon="type"
                                        model="judul"
                                        placeholder="contoh: PENYELENGGARAAN UJI KOMPETENSI"
                                        :required="true"
                                    />
                                </div>
                                @if($mekanisme_penkom === 'retribusi')
                                <div class="col-md-6">
                                    <x-form.input label="Nama Kegiatan" icon="clipboard" model="nama_kegiatan" placeholder="mis. Asesmen Seleksi Jabatan Pimpinan Tinggi Pratama" />
                                </div>
                                <div class="col-md-6">
                                    <x-form.input label="Nomor Surat" icon="hash" model="nomor_surat" placeholder="mis. 800.1.14 / 3951 / 204.6 / 2026" />
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    @if($mekanisme_penkom === 'retribusi')
                    {{-- ============ FORM RETRIBUSI ============ --}}
                    <div class="card mb-3 border-0 shadow-sm">
                        <div class="card-body">
                            <h6 class="text-danger mb-3"><i class="link-icon" data-feather="clock"></i> Waktu &amp; Tempat</h6>
                            <div class="row">
                                <div class="col-md-4">
                                    <x-form.date label="Tanggal" icon="calendar" model="tanggal_modal" placeholder="pilih tanggal" />
                                </div>
                                <div class="col-md-3">
                                    <x-form.time label="Pukul (Mulai)" icon="clock" model="waktu_mulai" placeholder="pilih waktu" />
                                </div>
                                <div class="col-md-3">
                                    <x-form.time label="Pukul (Selesai)" icon="clock" model="waktu_selesai" placeholder="pilih waktu" />
                                </div>
                                <div class="col-md-2">
                                    <x-form.select label="Zona Waktu" icon="globe" model="zona_waktu" placeholder="- pilih -">
                                        <option value="WIB">WIB</option>
                                        <option value="WITA">WITA</option>
                                        <option value="WIT">WIT</option>
                                    </x-form.select>
                                </div>
                                <div class="col-md-6">
                                    <x-form.input label="Di Lingkungan Pemerintah" icon="home" model="di_lingkungan_pemerintah" placeholder="mis. Kabupaten Manokwari" />
                                </div>
                                <div class="col-md-6">
                                    <x-form.input label="Bertempat Di" icon="map-pin" model="tempat" placeholder="mis. Universitas Papua Manokwari" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card mb-3 border-0 shadow-sm">
                        <div class="card-body">
                            <h6 class="text-danger mb-3"><i class="link-icon" data-feather="info"></i> Rincian Pelaksanaan</h6>
                            <div class="row">
                                <div class="col-md-3">
                                    <x-form.input label="Pejabat yang Dinilai" icon="user" model="pejabat_dinilai" type="number" placeholder="0" />
                                </div>
                                <div class="col-md-3">
                                    <x-form.input label="Seharusnya Hadir" icon="users" model="jumlah_peserta_seharusnya" type="number" placeholder="0" />
                                </div>
                                <div class="col-md-3">
                                    <x-form.input label="Tidak Hadir" icon="user-x" model="jumlah_peserta_tidak_hadir" type="number" placeholder="0" />
                                </div>
                                <div class="col-md-3" wire:key="display-jumlah-peserta-hadir-retribusi">
                                    <div class="mb-4">
                                        <label class="form-label fw-semibold mb-2" style="color: #344054; font-size: 0.875rem;">
                                            <span class="d-flex align-items-center gap-2">
                                                <i class="link-icon" data-feather="user-check" style="width: 16px; height: 16px;"></i>
                                                Peserta Hadir
                                            </span>
                                        </label>
                                        <input type="text" class="form-control" style="padding: 12px 16px; border-radius: 10px; border: 2px solid #e0e0e0; background:#eef2ff;" value="{{ $this->jumlahPesertaHadir }}" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <x-form.markdown label="Nomor Peserta (tidak hadir)" icon="hash" model="nomor_tidak_hadir" placeholder="nomor peserta yang tidak hadir" minHeight="90px" />
                                </div>
                                <div class="col-md-6">
                                    <x-form.markdown label="Alasan Ketidakhadiran" icon="help-circle" model="alasan_tidak_hadir" placeholder="alasan ketidakhadiran peserta" minHeight="90px" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card mb-3 border-0 shadow-sm">
                        <div class="card-body">
                            <h6 class="text-danger mb-3"><i class="link-icon" data-feather="file-text"></i> Penyerahan Hasil</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <x-form.date label="Tanggal Penyerahan Rekapitulasi Level" icon="calendar" model="tanggal_penyerahan_rekap" placeholder="pilih tanggal" />
                                </div>
                                <div class="col-md-6">
                                    <x-form.date label="Tanggal Penyerahan Laporan Individu" icon="calendar" model="tanggal_penyerahan_laporan" placeholder="pilih tanggal" />
                                </div>
                            </div>
                        </div>
                    </div>
                    @else
                    {{-- ============ FORM TUSI ============ --}}
                    <div class="card mb-3 border-0 shadow-sm">
                        <div class="card-body">
                            <h6 class="text-danger mb-3"><i class="link-icon" data-feather="clock"></i> Waktu Pelaksanaan</h6>
                            <div class="row">
                                <div class="col-md-3">
                                    <x-form.date label="Tanggal" icon="calendar" model="tanggal_modal" placeholder="pilih tanggal" />
                                </div>
                                <div class="col-md-3">
                                    <x-form.time label="Pukul (Mulai)" icon="clock" model="waktu_mulai" placeholder="pilih waktu" />
                                </div>
                                <div class="col-md-3">
                                    <x-form.time label="Pukul (Selesai)" icon="clock" model="waktu_selesai" placeholder="pilih waktu" />
                                </div>
                                <div class="col-md-3">
                                    <x-form.select label="Zona Waktu" icon="globe" model="zona_waktu" placeholder="- pilih -">
                                        <option value="WIB">WIB</option>
                                        <option value="WITA">WITA</option>
                                        <option value="WIT">WIT</option>
                                    </x-form.select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card mb-3 border-0 shadow-sm">
                        <div class="card-body">
                            <h6 class="text-danger mb-3"><i class="link-icon" data-feather="info"></i> Detail Pelaksanaan</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <x-form.input label="Pejabat" icon="user" model="pejabat" placeholder="mis. JF Ahli Pertama" />
                                </div>
                                <div class="col-md-6">
                                    <x-form.input label="Di Lingkungan Pemerintah" icon="home" model="di_lingkungan_pemerintah" placeholder="mis. Provinsi Jawa Timur" />
                                </div>
                                <div class="col-md-6">
                                    <x-form.input label="Ruang" icon="grid" model="ruang" placeholder="nama/nomor ruang" />
                                </div>
                                <div class="col-md-2">
                                    <x-form.input label="Total Peserta" icon="users" model="jumlah_peserta_seharusnya" type="number" placeholder="0" />
                                </div>
                                <div class="col-md-2">
                                    <x-form.input label="Tidak Hadir" icon="user-x" model="jumlah_peserta_tidak_hadir" type="number" placeholder="0" />
                                </div>
                                <div class="col-md-2" wire:key="display-jumlah-peserta-hadir-tusi">
                                    <div class="mb-4">
                                        <label class="form-label fw-semibold mb-2" style="color: #344054; font-size: 0.875rem;">
                                            <span class="d-flex align-items-center gap-2">
                                                <i class="link-icon" data-feather="user-check" style="width: 16px; height: 16px;"></i>
                                                Peserta Hadir
                                            </span>
                                        </label>
                                        <input type="text" class="form-control" style="padding: 12px 16px; border-radius: 10px; border: 2px solid #e0e0e0; background:#eef2ff;" value="{{ $this->jumlahPesertaHadir }}" readonly>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <x-form.markdown label="Nomor peserta tidak hadir" icon="hash" model="nomor_tidak_hadir" placeholder="nomor peserta yang tidak hadir" minHeight="90px" />
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <div class="card mb-3 border-0 shadow-sm">
                        <div class="card-body">
                            <x-form.markdown label="Catatan Selama Pelaksanaan" icon="edit-3" model="catatan" placeholder="ketidaksesuaian kondisi, ketidakhadiran, pelanggaran tata tertib, dll (mendukung Markdown, opsional)" minHeight="120px" />
                        </div>
                    </div>

                    @if($mekanisme_penkom === 'retribusi')
                    {{-- ============ PANITIA RETRIBUSI ============ --}}
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <h6 class="text-danger mb-3"><i class="link-icon" data-feather="award"></i> Yang Membuat Berita Acara</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="fw-semibold mb-2 text-secondary">1. Panitia Penyelenggara</div>
                                    <x-form.input label="Instansi/Unit" icon="briefcase" model="panitia1_instansi" placeholder="mis. UPT Pusat Penilaian Pegawai BKD Prov. Jatim" />
                                    <x-form.input label="Nama" icon="user" model="admin_nama" placeholder="nama panitia" />
                                    <x-form.input label="NIP" icon="hash" model="admin_nip" placeholder="NIP panitia" />
                                </div>
                                <div class="col-md-6">
                                    <div class="fw-semibold mb-2 text-secondary">2. Panitia Instansi</div>
                                    <x-form.input label="Instansi/Unit" icon="briefcase" model="panitia2_instansi" placeholder="mis. BKPSDM Kabupaten Manokwari" />
                                    <x-form.input label="Nama" icon="user" model="tester_nama" placeholder="nama panitia" />
                                    <x-form.input label="NIP" icon="hash" model="tester_nip" placeholder="NIP panitia" />
                                </div>
                            </div>
                        </div>
                    </div>
                    @else
                    {{-- ============ PANITIA TUSI ============ --}}
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <h6 class="text-danger mb-3"><i class="link-icon" data-feather="award"></i> Yang Membuat Berita Acara</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="fw-semibold mb-2 text-secondary">Admin</div>
                                    <x-form.select2 label="Pilih Pegawai" icon="users" model="admin_pegawai_id" placeholder="pilih pegawai admin" :live="true">
                                        @foreach ($options_pegawai as $id => $label)
                                            <option value="{{ $id }}">{{ $label }}</option>
                                        @endforeach
                                    </x-form.select2>
                                    <x-form.input label="Nama" icon="user" model="admin_nama" placeholder="nama admin" :readonly="true" />
                                    <x-form.input label="NIP" icon="hash" model="admin_nip" placeholder="NIP admin" :readonly="true" />
                                </div>
                                <div class="col-md-6">
                                    <div class="fw-semibold mb-2 text-secondary">Tester/Mdt</div>
                                    <x-form.select2 label="Pilih Pegawai" icon="users" model="tester_pegawai_id" placeholder="pilih pegawai tester" :live="true">
                                        @foreach ($options_pegawai as $id => $label)
                                            <option value="{{ $id }}">{{ $label }}</option>
                                        @endforeach
                                    </x-form.select2>
                                    <x-form.input label="Nama" icon="user" model="tester_nama" placeholder="nama tester" :readonly="true" />
                                    <x-form.input label="NIP" icon="hash" model="tester_nip" placeholder="NIP tester" :readonly="true" />
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>

                <div class="modal-footer" style="background: white; border-top: 2px solid #f0f0f0; padding: 20px 32px; gap: 12px;">
                    <x-modal.btn-cancel />
                    <x-modal.btn-save :isUpdate="$isUpdate" />
                </div>
            </div>
        </div>
    </div>

    <style>
        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
    @endif
</div>

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/easymde/dist/easymde.min.css">
    <style>
        .EasyMDEContainer .CodeMirror {
            border-radius: 10px;
            border: 2px solid #e0e0e0;
            font-size: 0.95rem;
        }
        .editor-toolbar {
            border-radius: 10px 10px 0 0;
            border: 2px solid #e0e0e0;
            border-bottom: none;
        }
    </style>
@endpush

@push('js')
    <script src="https://unpkg.com/easymde/dist/easymde.min.js"></script>
@endpush

@push('js')
    @script()
        <script>
            const markdownEditors = {};

            function initMarkdownEditors() {
                if (typeof EasyMDE === 'undefined') return;

                document.querySelectorAll('[data-markdown-editor]').forEach((el) => {
                    if (el._easymde) return;

                    const model = el.dataset.markdownModel;
                    const minHeight = el.dataset.minHeight || '100px';

                    const editor = new EasyMDE({
                        element: el,
                        spellChecker: false,
                        status: false,
                        minHeight,
                        placeholder: el.getAttribute('placeholder') || '',
                        toolbar: ['bold', 'italic', 'heading', '|', 'unordered-list', 'ordered-list', '|', 'link', 'quote', '|', 'preview'],
                    });

                    el._easymde = editor;
                    markdownEditors[model] = editor;

                    editor.value($wire.get(model) || '');

                    editor.codemirror.on('change', () => {
                        $wire.set(model, editor.value(), false);
                    });
                });
            }

            function setMarkdownEditorValue(model, value) {
                const editor = markdownEditors[model]
                    || document.querySelector(`[data-markdown-model="${model}"]`)?._easymde;

                if (editor) {
                    editor.value(value ?? '');
                }
            }

            function initFeatherIcons() {
                if (typeof feather !== 'undefined') {
                    feather.replace();
                }
            }

            function initModalFlatpickr() {
                document.querySelectorAll('[data-flatpickr]').forEach((input) => {
                    input._flatpickr?.destroy();

                    const model = input.dataset.model;
                    const hiddenInput = input
                        .closest('.mb-4')
                        ?.querySelector(`input[type="hidden"][wire\\:model="${model}"]`);

                    const value = hiddenInput?.value || null;

                    input._flatpickr = flatpickr(input, {
                        dateFormat: 'd-m-Y',
                        allowInput: false,
                        defaultDate: value || null,
                        onChange: (_, dateStr) => {
                            if (!hiddenInput) return;
                            hiddenInput.value = dateStr;
                            hiddenInput.dispatchEvent(new Event('input', { bubbles: true }));
                        },
                    });
                });
            }

            function initModalTimeFlatpickr() {
                document.querySelectorAll('[data-flatpickr-time]').forEach((input) => {
                    input._flatpickr?.destroy();

                    const model = input.dataset.model;
                    const hiddenInput = input
                        .closest('.mb-4')
                        ?.querySelector(`input[type="hidden"][wire\\:model="${model}"]`);

                    const value = hiddenInput?.value || null;

                    input._flatpickr = flatpickr(input, {
                        enableTime: true,
                        noCalendar: true,
                        dateFormat: 'H.i',
                        time_24hr: true,
                        allowInput: false,
                        defaultDate: value || null,
                        onChange: (_, timeStr) => {
                            if (!hiddenInput) return;
                            hiddenInput.value = timeStr;
                            hiddenInput.dispatchEvent(new Event('input', { bubbles: true }));
                        },
                    });
                });
            }

            function initFilterSelect2() {
                const $filter = $('#filter-event');
                if (!$filter.length) return;

                if ($filter.hasClass('select2-hidden-accessible')) {
                    $filter.select2('destroy');
                }

                $filter.select2({
                    placeholder: 'cari / pilih event',
                    allowClear: true,
                    width: '100%',
                    minimumResultsForSearch: 0,
                }).on('change', function () {
                    @this.set('event_id', $(this).val());
                });
            }

            $(document).ready(function () {
                initFilterSelect2();
                initFeatherIcons();

                Livewire.on('reset-select2', () => {
                    $('#filter-event').val(null).trigger('change');
                });

                Livewire.on('modalOpened', () => {
                    setTimeout(() => {
                        initModalFlatpickr();
                        initModalTimeFlatpickr();
                        initMarkdownEditors();
                        initFeatherIcons();
                    }, 150);
                });

                Livewire.on('set-flatpickr-time', (payload) => {
                    const data = Array.isArray(payload) ? payload[0] : payload;
                    const input = document.querySelector(`[data-flatpickr-time][data-model="${data.model}"]`);
                    if (input?._flatpickr && data.value) {
                        input._flatpickr.setDate(data.value, true, 'H.i');
                    }
                });

                Livewire.on('set-markdown', (payload) => {
                    const data = Array.isArray(payload) ? payload[0] : payload;
                    setTimeout(() => {
                        setMarkdownEditorValue(data?.model, data?.value ?? '');
                    }, 200);
                });

                Livewire.on('set-flatpickr', (payload) => {
                    const data = Array.isArray(payload) ? payload[0] : payload;
                    const input = document.querySelector(`[data-flatpickr][data-model="${data.model}"]`);
                    if (input?._flatpickr && data.value) {
                        input._flatpickr.setDate(data.value, true, 'd-m-Y');
                    }
                });
            });

            document.addEventListener('livewire:initialized', () => {
                initFeatherIcons();
                Livewire.hook('morph.updated', () => {
                    requestAnimationFrame(() => {
                        initFeatherIcons();
                        initMarkdownEditors();
                    });
                });
            });
        </script>
    @endscript
@endpush
