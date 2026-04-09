@props([
    'id',
    'action' => 'edit',
    'icon' => 'edit-2',
    'tooltip' => 'Edit',
    'route' => ''
])

<a class="btn btn-outline-success" wire:navigate
    data-bs-toggle="tooltip"
    data-bs-placement="top"
    title="{{ $tooltip }}"
    href="{{ route($route, $id) }}">
    <span wire:ignore><i data-feather="{{ $icon }}" style="width: 14px; height: 14px;"></i></span>
</a>