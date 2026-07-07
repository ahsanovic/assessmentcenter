@props([
    'text' => null,
    'isUpdate' => false,
    'action' => 'save',
    'icon' => 'save'
])

<button 
    type="button" 
    wire:click="{{ $action }}"
    class="btn btn-primary d-flex align-items-center gap-2" 
    style="padding: 10px 24px; border-radius: 10px; font-weight: 600; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3); transition: all 0.2s ease;"
    onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 16px rgba(102, 126, 234, 0.4)'"
    onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(102, 126, 234, 0.3)'"
    wire:loading.attr="disabled" wire:target="{{ $action }}"
    {{ $attributes }}
>
    <span wire:loading wire:target="{{ $action }}" class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
    <i class="link-icon" data-feather="{{ $icon }}" style="width: 18px; height: 18px;"></i>
    {{ $text ?? ($isUpdate ? 'Update' : 'Simpan') }}
</button>
