<div class="row w-100 mx-0 auth-page">
    <div class="col-12 col-md-10 col-lg-9 col-xl-8 mx-auto">
        <div class="card border-0 shadow-lg overflow-hidden" style="border-radius: 1.5rem;">
            <div class="row g-0">
                <!-- Left Side - Illustration -->
                <div class="col-lg-5 d-none d-lg-block">
                    <div class="h-100 position-relative" style="background: linear-gradient(135deg, #0d6efd 0%, #01a7f7 50%, #0dcaf0 100%); min-height: 500px;">
                        <!-- Decorative Elements -->
                        <div class="position-absolute" style="top: 20px; left: 20px; width: 60px; height: 60px; border: 3px solid rgba(255,255,255,0.2); border-radius: 50%;"></div>
                        <div class="position-absolute" style="bottom: 40px; right: 30px; width: 100px; height: 100px; border: 3px solid rgba(255,255,255,0.15); border-radius: 50%;"></div>
                        <div class="position-absolute" style="top: 50%; left: 10%; width: 20px; height: 20px; background: rgba(255,255,255,0.2); border-radius: 50%;"></div>
                        <div class="position-absolute" style="top: 30%; right: 20%; width: 15px; height: 15px; background: rgba(255,255,255,0.25); border-radius: 50%;"></div>
                        
                        <div class="d-flex flex-column align-items-center justify-content-center h-100 p-4 text-white position-relative">
                            <!-- Logo -->
                            <div class="mb-4 bg-white rounded-circle p-3 shadow" style="width: 150px; height: 150px; display: flex; align-items: center; justify-content: center;">
                                <img src="{{ asset('assets/images/main-logo.png') }}" class="img-fluid" style="max-width: 100px;" alt="Logo">
                            </div>
                            
                            <!-- Icon -->
                            <div class="mb-4">
                                <div class="rounded-circle bg-white bg-opacity-25 p-4" wire:ignore>
                                    <i data-feather="shield" style="width: 48px; height: 48px;"></i>
                                </div>
                            </div>
                            
                            <!-- Text -->
                            <h3 class="fw-bold mb-2 text-center">Admin Panel</h3>
                            <p class="text-center opacity-75 mb-0 px-3">
                                Kelola sistem assessment center dengan mudah dan efisien
                            </p>
                            
                            <!-- Dots -->
                            <div class="mt-4 d-flex gap-2">
                                <span style="width: 10px; height: 10px; background: rgba(255,255,255,0.8); border-radius: 50%;"></span>
                                <span style="width: 10px; height: 10px; background: rgba(255,255,255,0.4); border-radius: 50%;"></span>
                                <span style="width: 10px; height: 10px; background: rgba(255,255,255,0.4); border-radius: 50%;"></span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Right Side - Form -->
                <div class="col-lg-7">
                    <div class="p-4 p-md-5">
                        <!-- Mobile Logo -->
                        <div class="d-lg-none text-center mb-4">
                            <img src="{{ asset('assets/images/main-logo.png') }}" class="img-fluid mb-3" style="max-width: 80px;" alt="Logo">
                        </div>
                        
                        <!-- Header -->
                        <div class="text-center text-lg-start mb-4">
                            <h2 class="fw-bold mb-1" style="color: #01a7f7;">
                                <img src="{{ asset('assets/images/small-logo.png') }}" class="img-fluid mb-2" style="max-width: 30px;" alt="Logo">
                                SIKMA
                            </h2>
                            <p class="text-muted mb-0 small">Sistem Kompetensi Mandiri dan Adaptif</p>
                        </div>
                        
                        <div class="mb-4">
                            <div class="d-flex align-items-center">
                                <div class="rounded-circle p-2 me-3" style="background: rgba(1, 167, 247, 0.1);" wire:ignore>
                                    <i data-feather="user-check" style="width: 24px; height: 24px; color: #01a7f7;"></i>
                                </div>
                                <div>
                                    <h5 class="fw-semibold mb-0">Selamat Datang, Admin!</h5>
                                    <small class="text-muted">Masuk untuk mengelola sistem</small>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Form -->
                        <form class="forms-sample" wire:submit="login">
                            <div class="mb-4">
                                <label for="username" class="form-label fw-medium" wire:ignore>
                                    <i data-feather="user" style="width: 16px; height: 16px;" class="me-1"></i>
                                    Username
                                </label>
                                <div class="input-group input-group-lg">
                                    <span class="input-group-text bg-light border-end-0" wire:ignore>
                                        <i data-feather="at-sign" style="width: 18px; height: 18px; color: #6c757d;"></i>
                                    </span>
                                    <input type="text" 
                                        wire:model="username" 
                                        class="form-control border-start-0 ps-0 @error('username') is-invalid @enderror" 
                                        id="username" 
                                        placeholder=" Masukkan username"
                                        style="font-size: 1rem;">
                                </div>
                                @error('username') 
                                    <small class="text-danger mt-1 d-block" wire:ignore>
                                        <i data-feather="alert-circle" style="width: 14px; height: 14px;" class="me-1"></i>
                                        {{ $message }}
                                    </small>
                                @enderror
                            </div>
                            
                            <div class="mb-4">
                                <label for="password" class="form-label fw-medium" wire:ignore>
                                    <i data-feather="lock" style="width: 16px; height: 16px;" class="me-1"></i>
                                    Password
                                </label>
                                <div class="input-group input-group-lg" id="password-input-group">
                                    <span class="input-group-text bg-light border-end-0" wire:ignore>
                                        <i data-feather="key" style="width: 18px; height: 18px; color: #6c757d;"></i>
                                    </span>
                                    <input type="password" 
                                        wire:model="password" 
                                        class="form-control border-start-0 ps-0 @error('password') is-invalid @enderror" 
                                        id="password" 
                                        autocomplete="current-password" 
                                        placeholder=" Masukkan password"
                                        style="font-size: 1rem;">
                                    <span class="input-group-text bg-light border-start-0" style="cursor: pointer;" id="toggle-password-visibility" wire:ignore>
                                        <i data-feather="eye" id="password-eye-icon"></i>
                                    </span>
                                </div>
                                @error('password') 
                                    <small class="text-danger mt-1 d-block" wire:ignore>
                                        <i data-feather="alert-circle" style="width: 14px; height: 14px;" class="me-1"></i>
                                        {{ $message }}
                                    </small>
                                @enderror
                            </div>
                            <script>
                                document.addEventListener('DOMContentLoaded', function() {
                                    const input = document.getElementById('password');
                                    const toggle = document.getElementById('toggle-password-visibility');
                                    const eyeIcon = document.getElementById('password-eye-icon');
                                    let isShown = false;

                                    function updateIcon() {
                                        if (isShown) {
                                            eyeIcon.setAttribute('data-feather', 'eye-off');
                                        } else {
                                            eyeIcon.setAttribute('data-feather', 'eye');
                                        }
                                        if (window.feather) window.feather.replace();
                                    }

                                    toggle.addEventListener('click', function() {
                                        isShown = !isShown;
                                        input.type = isShown ? 'text' : 'password';
                                        updateIcon();
                                    });

                                    updateIcon();
                                });
                            </script>
                            
                            <div class="d-grid mt-4" wire:ignore>
                                <button type="submit" class="btn btn-lg text-white d-flex align-items-center justify-content-center gap-2" style="background: linear-gradient(135deg, #0d6efd 0%, #01a7f7 100%); border: none; border-radius: 0.75rem; padding: 0.875rem;">
                                    <i data-feather="log-in" style="width: 20px; height: 20px;"></i>
                                    <span class="fw-semibold">Masuk</span>
                                </button>
                            </div>
                        </form>
                        
                        <!-- Footer -->
                        <div class="mt-4 pt-3 border-top">
                            <p class="text-center text-muted small mb-0" wire:ignore>
                                <i data-feather="info" style="width: 14px; height: 14px;" class="me-1"></i>
                                Hubungi administrator jika mengalami kendala
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
