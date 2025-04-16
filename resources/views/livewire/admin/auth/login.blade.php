<div class="row w-100 mx-0 auth-page">
    <div class="col-md-10 col-lg-8 col-xl-6 mx-auto">
        <div class="card">
            <div class="row">
                <div class="col-md-4 pe-md-0">
                    <div class="auth-side-wrapper">

                    </div>
                </div>
                <div class="col-md-8 ps-md-0">
                    <div class="auth-form-wrapper px-4 py-5">
                        <a href="#" class="nobleui-logo d-block mb-2">BKD<span> Asessment Center</span></a>
                        <h5 class="text-secondary fw-normal mb-4">Selamat Datang Admin!</h5>
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
                        <div>
                            <button type="submit" class="btn btn-primary me-2 mb-2 mb-md-0 text-white">Login</button>
                        </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>