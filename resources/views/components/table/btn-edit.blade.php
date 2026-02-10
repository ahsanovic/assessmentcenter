@props([
    'id',
    'action' => 'edit',
    'icon' => 'edit-3',
    'tooltip' => 'Edit'
])

<button
    wire:click="{{ $action }}('{{ $id }}')"
    class="btn btn-sm btn-outline-success btn-icon rounded-circle border-0 shadow-sm"
    data-bs-toggle="tooltip" 
    data-bs-placement="top" 
    title="{{ $tooltip }}"
    style="transition: background 0.2s;"
>
    <i class="link-icon" data-feather="{{ $icon }}"></i>
</button>
