@props([
    'label',
    'icon' => 'list',
    'model',
    'required' => false,
    'placeholder' => '- pilih -',
    'live' => false,
])

@php
    $id = 'select2-' . str_replace('.', '-', $model);
@endphp

@if($live)
<div
    class="mb-4"
    x-data="{ value: @entangle($model).live }"
    x-init="
        () => {
            const el = $refs.select;

            $(el).select2({
                placeholder: '{{ $placeholder }}',
                allowClear: true,
                width: '100%',
                minimumResultsForSearch: 0,
                dropdownParent: $(el).closest('.modal').length ? $(el).closest('.modal') : $('body'),
            });

            if (value) {
                $(el).val(value).trigger('change.select2');
            }

            $(el).on('change', () => {
                value = $(el).val() || null;
            });
        }
    "
>
@else
<div
    class="mb-4"
    x-data="{ value: @entangle($model) }"
    x-init="
        () => {
            const el = $refs.select;

            $(el).select2({
                placeholder: '{{ $placeholder }}',
                allowClear: true,
                width: '100%',
                minimumResultsForSearch: 0,
                dropdownParent: $(el).closest('.modal').length ? $(el).closest('.modal') : $('body'),
            });

            if (value) {
                $(el).val(value).trigger('change.select2');
            }

            $(el).on('change', () => {
                value = $(el).val() || null;
            });
        }
    "
>
@endif
    <label class="form-label fw-semibold mb-2" style="color: #344054; font-size: 0.875rem; letter-spacing: 0.01em;">
        <span class="d-flex align-items-center gap-2">
            <i class="link-icon" data-feather="{{ $icon }}" style="width: 16px; height: 16px;"></i>
            {{ $label }}
            @if($required) <span class="text-danger">*</span> @endif
        </span>
    </label>

    <div wire:ignore>
        <select
            x-ref="select"
            class="form-select @error($model) is-invalid @enderror"
            style="padding: 12px 16px; border-radius: 10px; border: 2px solid #e0e0e0; transition: all 0.3s ease; font-size: 0.95rem;"
            onfocus="this.style.borderColor='#667eea'; this.style.boxShadow='0 0 0 4px rgba(102, 126, 234, 0.1)'"
            onblur="this.style.borderColor='#e0e0e0'; this.style.boxShadow='none'"
            {{ $attributes }}
        >
            <option value="">{{ $placeholder }}</option>
            {{ $slot }}
        </select>
    </div>

    @error($model)
        <div class="invalid-feedback d-flex align-items-center gap-2" style="font-size: 0.875rem;">
            <i class="link-icon" data-feather="alert-circle" style="width: 14px; height: 14px;"></i>
            {{ $message }}
        </div>
    @enderror
</div>

