<div class="row w-100 mx-0 auth-page">
    <div class="col-md-10 col-lg-8 col-xl-6 mx-auto">
        <div class="card">
            <div class="row">
                <div class="col-md-4 pe-md-0">
                    <div class="auth-side-wrapper">
                        <div class="d-flex align-items-center justify-content-center h-100">
                            <img src="{{ asset('assets/images/logo-upt.png') }}" class="img-fluid" />
                        </div>
                    </div>
                </div>
                <div class="col-md-8 ps-md-0">
                    <div class="auth-form-wrapper px-4 py-5">
                        <a href="#" class="nobleui-logo d-block mb-2">
                            SIKMA
                            <span class="ml-3 text-muted"><small class="text-muted">(Sistem Kompetensi Mandiri dan Adaptif)</small></span>
                        </a>
                        <h5 class="text-secondary fw-normal mb-4">Welcome Admin!</h5>
                        <form class="forms-sample" wire:submit="login">
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" wire:model="username" class="form-control @error('username') is-invalid @enderror" id="username" placeholder="Username">
                            @error('username') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" wire:model="password" class="form-control @error('password') is-invalid @enderror" id="password" autocomplete="current-password" placeholder="Password">
                            @error('password') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div wire:ignore>
                            <button type="submit" class="btn btn-primary me-2 mb-2 mb-md-0 text-white btn-icon-text">
                                <i class="btn-icon-prepend" data-feather="log-in"></i>
                                Login
                            </button>
                        </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>