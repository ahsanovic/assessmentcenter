<div class="card border-0 shadow-sm mb-4" 
        style="background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);">
    <div class="card-body p-4">
        <div class="d-flex align-items-center">
            <div class="rounded-circle p-3 me-3"
                style="background: rgba(102, 126, 234, 0.13); color: #667eea;" wire:ignore>
                <i data-feather="folder" style="width: 32px; height: 32px;"></i>
            </div>
            <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center w-100">
                <div>
                    <h3 class="mb-1" style="color: #3c3264; font-weight: 700;">
                        Kelengkapan Portofolio
                    </h3>
                    <p class="mb-0" style="color: #585e74; opacity: .85; font-weight: 500;">
                        Lengkapi data diri Anda dengan teliti
                    </p>
                </div>
                <a href="{{ route('peserta.portofolio') }}" class="ms-md-4 mt-3 mt-md-0 btn"
                   style="background: linear-gradient(90deg, #667eea 0%, #764ba2 100%); color: #fff; font-weight: 600; box-shadow: 0px 4px 14px rgba(102,126,234,0.15); border-radius: 24px; padding: 0.5rem 1.5rem; transition: background 0.3s, box-shadow 0.3s;"
                   onmouseover="this.style.background='linear-gradient(90deg, #5a67d8 0%, #553c9a 100%)';this.style.boxShadow='0px 6px 18px rgba(102,126,234,0.2)';"
                   onmouseout="this.style.background='linear-gradient(90deg, #667eea 0%, #764ba2 100%)';this.style.boxShadow='0px 4px 14px rgba(102,126,234,0.15)';"
                   wire:navigate
                >
                    <i data-feather="eye" class="me-2"></i>
                    Lihat Ringkasan Portofolio
                </a>
            </div>
        </div>
    </div>
</div>