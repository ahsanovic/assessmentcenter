<div>
    <div class="row mb-4">
        <div class="col">
            <h3 class="text-center">Tes Belajar Cepat dan Pengembangan Diri</h3>
        </div>
    </div>
    <div class="card mb-3">
        <div class="card-body">
            <div class="row d-flex justify-content-center">
                <div class="col-2">
                    <button type="button" class="btn btn-inverse-success">
                        Sudah Dijawab: <span class="badge bg-success text-white">{{ $jml_soal - $jawaban_kosong }}</span>
                    </button>
                </div>
                <div class="col-2">
                    <button type="button" class="btn btn-inverse-danger">
                        Belum Dijawab: <span class="badge bg-danger text-white">{{ $jawaban_kosong }}</span>
                    </button>
                </div>
                <div class="col-2">
                    <button class="btn btn-inverse-dark"
                        x-data
                        @click="Swal.fire({
                            title: 'Apakah Anda yakin mengakhiri tes?',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Akhiri Tes!',
                            cancelButtonText: 'Batal',
                        }).then((result) => {
                            if (result.isConfirmed) {
                                $wire.finish();
                            }
                        })"
                    >
                        Selesai
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="card mb-3">
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-6">
                    <h5>SOAL NO. {{ $nomor_sekarang }}</h5>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-md-12 col-lg-12 mb-3">
                    {{ $soal->soal }}
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 col-lg-12">
                    <div class="row mb-3">
                        <div class="col-12 me-2 mb-2">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio"
                                    wire:model="jawaban_user.{{ $nomor_sekarang - 1 }}" value="A" id="opsi1"
                                    checked
                                >
                                <label class="form-check-label" for="opsi1">
                                    A. {{ $soal->opsi_a }}
                                </label>
                            </div>
                        </div>
                        <div class="col-12 me-2 mb-2">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio"
                                    wire:model="jawaban_user.{{ $nomor_sekarang - 1 }}" value="B" id="opsi2">
                                <label class="form-check-label" for="opsi2">
                                    B. {{ $soal->opsi_b }}
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col md-6 lg-12 d-flex">
                    <button class="btn btn-warning btn-sm me-2" wire:click="navigate({{ $nomor_sekarang - 1 }})"
                        @if ($nomor_sekarang == 1) disabled @endif>
                        Sebelumnya
                    </button>
                    <button class="btn btn-success btn-sm" wire:click="saveAndNext({{ $nomor_sekarang }})">
                        Simpan & Lanjutkan
                    </button>
                </div>
            </div>
            <div class="row mt-5">
                @php
                    $nomor_soal = 1;
                    $kosong = 0;
                @endphp
                <h6 class="text-muted small">NAVIGASI SOAL</h6>
                <div class="col mt-2">
                    @for ($i = 0; $i < 4; $i++)
                        <div class="d-flex flex-wrap btn-group btn-group-sm" role="group" aria-label="Basic example">
                            @for ($j = 1; $j <= 13; $j++)
                                <button wire:click="navigate({{ $nomor_soal }})"
                                    class="btn btn-sm btn-<?php
                                    if ($jawaban[$nomor_soal - 1] === '0') {
                                        echo 'inverse-danger';
                                        $kosong++;
                                    } else {
                                        echo 'inverse-success';
                                    }
                                    ?> me-2 mb-2">
                                    {{ $nomor_soal++ }}
                                </button>
                            @endfor
                        </div>
                    @endfor
                </div>
            </div>
        </div>
    </div>
</div>
