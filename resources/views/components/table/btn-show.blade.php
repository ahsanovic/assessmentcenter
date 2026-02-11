@props([
    'id',
    'action' => 'showDetail',
    'icon' => 'eye',
    'tooltip' => 'Detail',
    'color' => 'warning'
])

<button 
    wire:click="{{ $action }}('{{ $id }}')"
    class="btn btn-sm btn-outline-{{ $color }} btn-icon rounded-circle border-0 shadow-sm"
    data-bs-toggle="tooltip" 
    data-bs-placement="top" 
    title="{{ $tooltip }}"
    style="transition: background 0.2s;"
>
    <i class="link-icon" data-feather="{{ $icon }}"></i>
</button>
