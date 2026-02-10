@props([
    'label',
    'icon' => 'check-circle',
    'model',
    'options' => [],
    'required' => false
])

<div class="mb-4">
    <label class="form-label fw-semibold mb-2" style="color: #344054; font-size: 0.875rem; letter-spacing: 0.01em;">
        <span class="d-flex align-items-center gap-2">
            <i class="link-icon" data-feather="{{ $icon }}" style="width: 16px; height: 16px;"></i>
            {{ $label }}
            @if($required) <span class="text-danger">*</span> @endif
        </span>
    </label>
    <div class="d-flex gap-3 align-items-center flex-wrap">
        {{ $slot }}
    </div>
    @error($model)
    <div class="text-danger d-flex align-items-center gap-2 mt-2" style="font-size: 0.875rem;">
        <i class="link-icon" data-feather="alert-circle" style="width: 14px; height: 14px;"></i>
        {{ $message }}
    </div>
    @enderror
</div>
