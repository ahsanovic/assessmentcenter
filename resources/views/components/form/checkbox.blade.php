@props([
    'label',
    'icon' => 'check-square',
    'model',
    'value',
    'text',
    'required' => false
])

<div class="mb-4">
    <div class="form-check">
        <label class="form-check-label d-flex align-items-center gap-2" style="color: #344054; font-size: 0.95rem; cursor: pointer;">
            <input 
                type="checkbox" 
                wire:model="{{ $model }}"
                value="{{ $value }}"
                class="form-check-input @error($model) is-invalid @enderror"
                style="width: 20px; height: 20px; border-radius: 6px; border: 2px solid #e0e0e0; cursor: pointer;"
                {{ $attributes }}
            >
            <i class="link-icon" data-feather="{{ $icon }}" style="width: 16px; height: 16px;"></i>
            {{ $label ?? $text }}
            @if($required) <span class="text-danger">*</span> @endif
        </label>
        @error($model)
        <div class="text-danger d-flex align-items-center gap-2 mt-2" style="font-size: 0.875rem;">
            <i class="link-icon" data-feather="alert-circle" style="width: 14px; height: 14px;"></i>
            {{ $message }}
        </div>
        @enderror
    </div>
</div>
