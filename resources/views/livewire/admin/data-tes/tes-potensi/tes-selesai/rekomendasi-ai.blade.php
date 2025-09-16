<div>
    <x-breadcrumb :breadcrumbs="[
        ['url' => route('admin.dashboard'), 'title' => 'Dashboard'],
        ['url' => route('admin.tes-selesai'), 'title' => 'Data Tes Selesai'],
        ['url' => route('admin.tes-selesai.show-peserta', ['idEvent' => $id_event]), 'title' => 'Peserta'],
        ['url' => null, 'title' => 'Rekomendasi AI'],
    ]" />
    
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="text-center mt-4 mb-4">REKOMENDASI PENGEMBANGAN DIRI DAN POTENSI JABATAN</h4>
                    <div class="row mb-2">
                        <label class="col-md-2 fw-semibold">Nama</label>
                        <div class="col-md-7">: {{ $peserta->nama }}</div>
                    </div>
                    <div class="row mb-2">
                        <label class="col-md-2 fw-semibold">NIP</label>
                        <div class="col-md-7">: {{ $peserta->nip ?: $peserta->nik }}</div>
                    </div>
                    <div class="row mb-2">
                        <label class="col-md-2 fw-semibold">Jabatan Saat Ini</label>
                        <div class="col-md-7">: {{ $peserta->jabatan }}</div>
                    </div>
                    <div class="row mb-2">
                        <label class="col-md-2 fw-semibold">Unit Kerja</label>
                        <div class="col-md-7">: {{ $peserta->instansi . ' - ' . $peserta->unit_kerja }}</div>
                    </div>
                    <div class="row mb-2">
                        <label class="col-md-2 fw-semibold">JPM (Job Person Match)</label>
                        <div class="col-md-7">: {{ $persentase }}%</div>
                    </div>
                    <div class="row mb-2">
                        <label class="col-md-2 fw-semibold">Kategori</label>
                        <div class="col-md-7">: {{ $kategori }}</div>
                    </div>
                    <div class="row mt-4">
                        <div class="col-12">
                            <button class="btn btn-sm btn-warning" wire:click="generateRekomendasi" wire:loading.attr="disabled" wire:target="generateRekomendasi">
                                <span wire:loading.remove wire:target="generateRekomendasi">Generate Rekomendasi oleh AI</span>
                                <span wire:loading wire:target="generateRekomendasi">
                                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                    AI is analyzing...
                                </span>
                            </button>
                        </div>
                    </div>
                    @if ($hasil_rekomendasi)
                        <div 
                            x-data="{ copied: false }" 
                            class="mt-4 p-3 bg-light border rounded position-relative">

                            {{-- Tombol Copy --}}
                            <button 
                                class="btn btn-sm btn-outline-secondary position-absolute top-0 end-0 mt-2 me-2"
                                @click="
                                    let range = document.createRange();
                                    range.selectNode($refs.rekomendasi);
                                    window.getSelection().removeAllRanges();
                                    window.getSelection().addRange(range);

                                    try {
                                        copied = document.execCommand('copy');
                                        window.getSelection().removeAllRanges();
                                    } catch (err) {
                                        copied = false;
                                    }

                                    setTimeout(() => copied = false, 2000);
                                ">
                                <template x-if="!copied">
                                    <span><i class="bi bi-clipboard"></i> Copy</span>
                                </template>
                                <template x-if="copied">
                                    <span><i class="bi bi-check2"></i> Tersalin!</span>
                                </template>
                            </button>

                            <h5 class="fw-semibold text-dark mb-3">Hasil Rekomendasi:</h5>
                            <div x-ref="rekomendasi">
                                {!! $hasil_rekomendasi !!}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>    
</div>
