@props([
    'label' => 'Upload Gambar',
    'model',
    'field' => null,
    'old' => null,
    'value' => null,
])

<div class="mb-3">
    @if ($label ?? false)
        <label class="form-label">{{ $label }}</label>
    @endif

    <input
        type="file"
        accept="image/*"
        class="form-control @error($model) is-invalid @enderror"
        @if($model) wire:model="{{ $model }}" @endif
    >

    @error('form.'.$field)
        <label class="error invalid-feedback">{{ $message }}</label>
    @enderror

    {{-- Preview file baru --}}
    @if($value && is_object($value) && method_exists($value, 'temporaryUrl'))
        {{-- preview upload baru --}}
        <div class="mt-2">
            <img src="{{ $value->temporaryUrl() }}" class="img-thumbnail mb-2" style="max-height:150px;">
            {{-- tombol hapus utk reset upload baru --}}
            <button type="button" 
                    class="btn btn-sm btn-inverse-danger" 
                    wire:click="$set('{{ $model }}', null)">
                hapus
            </button>
        </div>
    {{-- preview lama --}}
    @elseif(!empty($old))
        <div class="mt-2">
            <img src="{{ asset('storage/'.$old) }}" 
                 alt="Preview Lama" class="img-thumbnail mb-2 me-2" style="max-height: 150px;">
            
            <button type="button" 
                    class="btn btn-sm btn-inverse-danger" 
                    wire:click="deleteImage('{{ $field}}')">
                <i class="link-icon" data-feather="trash"></i>
            </button>
        </div>
    @endif
</div>
