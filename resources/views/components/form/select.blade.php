@props([
    'label',
    'icon' => 'list',
    'model',
    'required' => false,
    'placeholder' => '- pilih -',
    'width' => '100%',
])

@php
    $id = 'select-' . str_replace('.', '-', $model);
    $fieldKey = 'form-field-' . str_replace(['.', '[', ']'], '-', $model);
@endphp

<div class="mb-4" wire:key="{{ $fieldKey }}">
    <label class="form-label fw-semibold mb-2" style="color: #344054; font-size: 0.875rem; letter-spacing: 0.01em;">
        <span class="d-flex align-items-center gap-2">
            <i class="link-icon" data-feather="{{ $icon }}" style="width: 16px; height: 16px;"></i>
            {{ $label }}
            @if($required) <span class="text-danger">*</span> @endif
        </span>
    </label>
    <select
        class="form-select @error($model) is-invalid @enderror"
        wire:model="{{ $model }}"
        style="padding: 12px 16px; border-radius: 10px; border: 2px solid #e0e0e0; transition: all 0.3s ease; font-size: 0.95rem;"
        onfocus="this.style.borderColor='#667eea'; this.style.boxShadow='0 0 0 4px rgba(102, 126, 234, 0.1)'"
        onblur="this.style.borderColor='#e0e0e0'; this.style.boxShadow='none'" {{ $attributes }}
    >
        @if($placeholder)
            <option value="" selected>{{ $placeholder }}</option>
        @endif
        {{ $slot }}
    </select>
    @error($model)
        <div class="invalid-feedback d-flex align-items-center gap-2" style="font-size: 0.875rem;">
            <i class="link-icon" data-feather="alert-circle" style="width: 14px; height: 14px;"></i>
            {{ $message }}
        </div>
    @enderror
</div>