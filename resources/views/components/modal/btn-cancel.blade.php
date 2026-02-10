@props([
    'text' => 'Batal',
    'action' => 'closeModal',
    'icon' => 'x'
])

<button 
    type="button" 
    wire:click="{{ $action }}" 
    class="btn btn-light d-flex align-items-center gap-2" 
    style="padding: 10px 24px; border-radius: 10px; font-weight: 600; border: 2px solid #e0e0e0; transition: all 0.2s ease;"
    onmouseover="this.style.background='#f5f5f5'; this.style.borderColor='#d0d0d0'"
    onmouseout="this.style.background=''; this.style.borderColor='#e0e0e0'"
>
    <i class="link-icon" data-feather="{{ $icon }}" style="width: 18px; height: 18px;"></i>
    {{ $text }}
</button>
