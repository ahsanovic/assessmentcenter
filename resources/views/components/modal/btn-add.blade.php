@props([
    'text' => 'Tambah Data',
    'icon' => 'plus-circle',
    'action' => 'openModal'
])

<button 
    wire:click="{{ $action }}" 
    class="btn btn-sm btn-primary d-flex align-items-center gap-2 shadow-sm"
    style="border-radius: 6px; padding: 6px 14px; font-size: 0.875rem; transition: all 0.3s ease;"
    onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(13, 110, 253, 0.3)'"
    onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow=''"
>
    <i class="link-icon" data-feather="{{ $icon }}" style="width: 16px; height: 16px;"></i>
    <span class="fw-semibold">{{ $text }}</span>
</button>
