@props([
    'id',
    'action' => 'deleteConfirmation',
    'icon' => 'trash',
    'tooltip' => 'Hapus',
    'disabled' => false
])

<button 
    wire:click="{{ $action }}('{{ $id }}')"
    class="btn btn-sm btn-outline-danger btn-icon rounded-circle border-0 shadow-sm"
    data-bs-toggle="tooltip" 
    data-bs-placement="top" 
    title="{{ $tooltip }}"
    style="transition: background 0.2s;"
    @if($disabled) disabled @endif
>
    <i class="link-icon" data-feather="{{ $icon }}"></i>
</button>
