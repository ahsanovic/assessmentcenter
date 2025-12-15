@props([
    'cancelUrl' => '#',
    'isUpdate' => false,
    'cancelText' => 'Batal',
    'submitText' => null,
    'updateText' => 'Ubah',
    'createText' => 'Simpan',
])

<div class="mt-3" wire:ignore>
    <a href="{{ $cancelUrl }}" wire:navigate class="btn btn-sm btn-inverse-danger me-2 btn-icon-text">
        <i class="btn-icon-prepend" data-feather="x"></i>
        {{ $cancelText }}
    </a>
    <button type="submit" class="btn btn-sm btn-inverse-success btn-icon-text">
        <i class="btn-icon-prepend" data-feather="save"></i>
        {{ $submitText ?? ($isUpdate ? $updateText : $createText) }}
    </button>
</div>

