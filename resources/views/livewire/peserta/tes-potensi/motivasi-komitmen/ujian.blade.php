<div x-data
x-init="
    document.addEventListener('visibilitychange', () => {
        if (document.hidden) {
            $wire.laporPelanggaran();
        }
    });

    Livewire.on('toast', e => {
        toastr.options = {
            positionClass: 'toast-top-center',
            closeButton: true,
            timeOut: 0,
            extendedTimeOut: 0,
        };
        toastr[e.type](e.message);
    });
">
    <div class="row mb-4">
        <div class="col">
            <h3 class="text-center">Tes Motivasi dan Komitmen</h3>
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
                        Belum Dijawab: <span class="badge bg-danger text-white">{{ $jawaban_kosong ?? 0 }}</span>
                    </button>
                </div>
                <div class="col-2">
                    <button type="button" class="btn btn-inverse-info">
                        <strong><span class="time text-dark"></span></strong>
                    </button>
                </div>
                <div class="col-2">
                    <button type="button" class="btn btn-inverse-primary">
                        Tes ke- <span class="badge bg-primary text-white">{{ $current_sequence . ' / 7' }}</span>
                    </button>
                </div>
                <div class="col-2">
                    <button class="btn btn-inverse-dark"
                        x-data
                        @click="Swal.fire({
                            title: '{{ $current_sequence == 7 ? 'Apakah Anda yakin mengakhiri tes?' : 'Apakah Anda yakin melanjutkan tes berikutnya?' }}',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: '{{ $current_sequence == 7 ? 'Akhiri Tes!' : 'Tes Berikutnya' }}',
                            cancelButtonText: 'Batal',
                        }).then((result) => {
                            if (result.isConfirmed) {
                                $wire.finish();
                            }
                        })"
                        @disabled($jawaban_kosong != 0)
                    >
                    {{ $current_sequence == 7 ? 'Selesai' : 'Lanjut Tes Berikutnya' }}
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
                                    wire:model="jawaban_user.{{ $nomor_sekarang - 1 }}" value="B" id="opsi2"
                                >
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
                    <button
                        class="btn btn-success btn-sm"
                        wire:click="saveAndNext({{ $nomor_sekarang }})"
                        id="btn-simpan"
                        disabled
                    >
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
                    @for ($i = 0; $i < 5; $i++)
                        <div class="d-flex flex-wrap btn-group btn-group-sm" role="group" aria-label="Basic example">
                            @for ($j = 1; $j <= 11; $j++)
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
@push('js')
<script>
    $(document).ready(function() {
        $('.form-check-input').change(function() {
            $('#btn-simpan').removeAttr('disabled');
        })
    })

    function timeout(id) {
        Swal.fire({
            title: 'Waktu Habis',
            text: 'Waktu ujian telah habis, tetapi Anda masih dapat mengerjakan soal sampai selesai. Silahkan mengisi semua jawaban dan pastikan jangan ada yang terlewat!',
            icon: 'warning',
            allowOutsideClick: false,
        })
    }

    var waktuBerakhir = new Date({{ $timer }} * 1000).getTime();
    
    // Update the count down every 1 second
    var x = setInterval(function() {
        var now = new Date().getTime();
        
        // Find the distance between now an the count down date
        var distance = waktuBerakhir - now;
        
        // Time calculations for days, hours, minutes and seconds
        var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        var seconds = Math.floor((distance % (1000 * 60)) / 1000);
        
        // Output the result in an element
        $('.time').html(('0' + hours).slice(-2) + " : " + ('0' + minutes).slice(-2) + " : " + ('0' + seconds).slice(-2) + "");
        
        // If the count down is over, write some text
        if (distance < 0) {
            if (!localStorage.getItem("popup")) {
                clearInterval(x); 
                $('.time').html('Waktu Habis');
                timeout({{ $id_ujian }});
                localStorage.setItem("popup", 'viewed');
            } else {
                clearInterval(x); 
                $('.time').html('Waktu Habis');
                clearTimeout({{ $id_ujian }})
            }
        }
    }, 1000);
</script>
@endpush

