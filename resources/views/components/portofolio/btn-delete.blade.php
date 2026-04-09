@props([
    'id',
    'action' => 'deleteConfirmation',
    'icon' => 'trash-2',
    'tooltip' => 'Hapus'
])

<button wire:click="{{ $action }}('{{ $id }}')"
    class="btn btn-outline-danger"
    data-bs-toggle="tooltip"
    data-bs-placement="top"
    title="{{ $tooltip }}"
>
    <span wire:ignore><i data-feather="{{ $icon }}" style="width: 14px; height: 14px;"></i></span>
</button>