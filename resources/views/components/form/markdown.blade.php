@props([
    'label',
    'icon' => 'edit-3',
    'model',
    'placeholder' => '',
    'required' => false,
    'minHeight' => '100px',
])

@php
    $fieldKey = 'markdown-field-' . str_replace(['.', '[', ']'], '-', $model);
    $editorId = 'markdown-editor-' . str_replace(['.', '[', ']'], '-', $model);
@endphp

<div class="mb-4" wire:key="{{ $fieldKey }}">
    <label class="form-label fw-semibold mb-2" style="color: #344054; font-size: 0.875rem; letter-spacing: 0.01em;">
        <span class="d-flex align-items-center gap-2">
            <i class="link-icon" data-feather="{{ $icon }}" style="width: 16px; height: 16px;"></i>
            {{ $label }}
            @if($required) <span class="text-danger">*</span> @endif
        </span>
    </label>
    <div class="mb-1" wire:ignore>
        <textarea
            id="{{ $editorId }}"
            data-markdown-editor
            data-markdown-model="{{ $model }}"
            data-min-height="{{ $minHeight }}"
            placeholder="{{ $placeholder }}"
        ></textarea>
    </div>
    @error($model)
        <div class="text-danger d-flex align-items-center gap-2 mt-2" style="font-size: 0.875rem;">
            <i class="link-icon" data-feather="alert-circle" style="width: 14px; height: 14px;"></i>
            {{ $message }}
        </div>
    @enderror
</div>
