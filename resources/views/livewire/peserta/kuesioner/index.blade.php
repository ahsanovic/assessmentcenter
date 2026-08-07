@push('css')
<style>
    .form-check-input[type="checkbox"] {
        border: 2px solid #dee2e6;
    }
    .question-card {
        transition: all 0.3s ease;
        border-left: 4px solid transparent;
    }
    .question-card:hover {
        border-left-color: #0d6efd;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }
    .question-card.has-error {
        border-left-color: #dc3545;
    }
    .rating-label {
        font-size: 0.85rem;
        color: #6c757d;
    }
    .star-rating {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
    }
    .star-btn {
        appearance: none;
        border: 0;
        background: transparent;
        padding: 0;
        line-height: 1;
        cursor: pointer;
        color: #d1d5db;
        font-size: 2.5rem;
        transition: color 0.15s ease, transform 0.15s ease;
    }
    .star-btn:hover,
    .star-btn:focus-visible {
        color: #fbbf24;
        transform: scale(1.12);
        outline: none;
    }
    .star-btn.active {
        color: #f59e0b;
    }
    .star-rating:hover .star-btn {
        color: #fbbf24;
    }
    .star-rating:hover .star-btn:hover ~ .star-btn {
        color: #d1d5db;
    }
</style>
@endpush

<div>
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm bg-gradient" 
                style="
                    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
                    color: #23272f;
                "
                >
                <div class="card-body p-4" 
                    style="
                        color: #23272f; 
                        background: transparent;
                    ">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle p-3 me-3"
                            style="
                                background: rgba(103, 126, 234, 0.13);
                                color: #5f39af;
                                display: flex;
                                align-items: center;
                                justify-content: center;
                                box-shadow: 0 2px 8px rgba(110,88,213,.07);
                            "
                        >
                            <i data-feather="clipboard" style="width: 32px; height: 32px;"></i>
                        </div>
                        <div>
                            <h3 class="mb-1" style="color: #3c3264; font-weight: 700;">
                                Kuesioner Evaluasi
                            </h3>
                            <p class="mb-0" style="color: #585e74; opacity: .85; font-weight: 500;">
                                Penilaian Kompetensi
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert Info -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm border-start border-4 border-info">
                <div class="card-body p-4">
                    <div class="d-flex align-items-start">
                        <div class="me-3">
                            <i class="text-info" data-feather="info" style="width: 24px; height: 24px;"></i>
                        </div>
                        <div>
                            <h6 class="mb-2 text-info">Petunjuk Pengisian</h6>
                            <p class="mb-0 text-muted">Berikan penilaian Anda terhadap pelaksanaan Penilaian Kompetensi (Uji Kompetensi) yang telah diikuti dengan cara memilih jumlah bintang (1–5). Semua pertanyaan wajib diisi sebelum mengirim jawaban.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Kuesioner Form -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <form wire:submit="submit">
                @foreach ($kuesioner as $key => $item)
                    @php
                        $hasError = $item->is_esai === 'f'
                            ? $errors->has("jawaban_responden.{$item->id}.skor")
                            : $errors->has("jawaban_responden.{$item->id}.jawaban_esai");
                        $selectedSkor = (int) ($jawaban_responden[$item->id]['skor'] ?? 0);
                    @endphp
                    <div class="question-card card mb-4 border-0 bg-light rounded-3 {{ $hasError ? 'has-error' : '' }}">
                        <div class="card-body p-4">
                            @if ($item->is_esai == 'f')
                                <!-- Rating Question -->
                                <div class="d-flex align-items-start mb-3">
                                    <span class="badge bg-primary rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                        {{ $loop->iteration }}
                                    </span>
                                    <h6 class="mb-0 flex-grow-1">
                                        {{ $item->deskripsi }}
                                        <span class="text-danger">*</span>
                                    </h6>
                                </div>
                                <div class="d-flex flex-column flex-md-row align-items-center justify-content-center mt-4 gap-2">
                                    <span class="rating-label me-md-2">Sangat Tidak Setuju</span>
                                    <div class="star-rating" role="radiogroup" aria-label="Penilaian pertanyaan {{ $loop->iteration }}">
                                        @for ($i = 1; $i <= 5; $i++)
                                            <button
                                                type="button"
                                                class="star-btn {{ $selectedSkor >= $i ? 'active' : '' }}"
                                                wire:click="setRating({{ $item->id }}, {{ $i }})"
                                                aria-label="{{ $i }} bintang"
                                                title="{{ $i }} bintang"
                                            >★</button>
                                        @endfor
                                    </div>
                                    <span class="rating-label ms-md-2">Sangat Setuju</span>
                                </div>
                                @error("jawaban_responden.{$item->id}.skor")
                                    <div class="text-danger small text-center mt-3">{{ $message }}</div>
                                @enderror
                            @else
                                <!-- Essay Question -->
                                <div class="d-flex align-items-start mb-3">
                                    <span class="badge bg-success rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                        {{ $loop->iteration }}
                                    </span>
                                    <h6 class="mb-0 flex-grow-1">
                                        {{ $item->deskripsi }}
                                        <span class="text-danger">*</span>
                                    </h6>
                                </div>
                                <div class="mt-3">
                                    <textarea 
                                        class="form-control border-0 shadow-sm @error("jawaban_responden.{$item->id}.jawaban_esai") is-invalid @enderror" 
                                        wire:model.defer="jawaban_responden.{{ $item->id }}.jawaban_esai" 
                                        rows="4"
                                        placeholder="Tulis jawaban Anda di sini..."
                                    ></textarea>
                                    @error("jawaban_responden.{$item->id}.jawaban_esai")
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach

                <!-- Confirmation Checkbox -->
                <div class="card border-0 bg-warning bg-opacity-10 rounded-3 mb-4">
                    <div class="card-body p-4">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input @error('konfirmasi') is-invalid @enderror" id="konfirmasi" wire:model.live="konfirmasi" style="width: 1.25em; height: 1.25em;">
                            <label class="form-check-label ms-2" for="konfirmasi">
                                <strong>Saya telah mengisi semua pertanyaan dengan jujur</strong>
                                <span class="text-danger">*</span>
                            </label>
                            @error('konfirmasi')
                                <div class="invalid-feedback d-block mt-2">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <button class="btn btn-primary btn-lg px-5" type="submit" wire:loading.attr="disabled" @disabled(!$konfirmasi)>
                        <span wire:loading.remove wire:target="submit">
                            <i class="me-2" data-feather="send"></i>
                            Kirim Jawaban
                        </span>
                        <span wire:loading wire:target="submit">Mengirim...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
