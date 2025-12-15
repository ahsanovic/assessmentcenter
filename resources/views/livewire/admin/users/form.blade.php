<div>
    <x-breadcrumb :breadcrumbs="[
        ['url' => route('admin.dashboard'), 'title' => 'Dashboard'],
        ['url' => null, 'title' => 'Data User']
    ]" />
    <div class="row">
        <div class="col-md-12 stretch-card">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">Form User</h6>
                    <form wire:submit="save">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="mb-3">
                                    <label class="form-label">Nama</label>
                                    <input
                                        type="text"
                                        wire:model="nama"
                                        class="form-control @error('nama') is-invalid @enderror"
                                        placeholder="masukkan nama"
                                    >
                                    @error('nama')
                                    <label class="error invalid-feedback">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div><!-- Col -->
                        </div><!-- Row -->
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="mb-3">
                                    <label class="form-label">Username</label>
                                    <input
                                        type="text"
                                        wire:model="username"
                                        class="form-control @error('username') is-invalid @enderror"
                                        placeholder="masukkan username"
                                        {{ $isUpdate === true ? 'disabled' : '' }}
                                    >
                                    @error('username')
                                    <label class="error invalid-feedback">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div><!-- Col -->
                        </div><!-- Row -->
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="mb-3">
                                    <label class="form-label">Password</label>
                                    <input
                                        type="password"
                                        wire:model="password"
                                        class="form-control @error('password') is-invalid @enderror"
                                        placeholder="masukkan password"
                                    >
                                    @error('password')
                                    <label class="error invalid-feedback">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div><!-- Col -->
                        </div><!-- Row -->
                        <div class="row mb-4">
                            <div class="col-sm-12">
                                <div class="mb-1">
                                    <label class="form-label">Role</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input type="radio" class="form-check-input" wire:model="role" id="radioInline" value="admin" {{ $role == 'admin' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="radioInline">
                                            Admin
                                        </label>
                                    </div>
                                <div class="form-check form-check-inline">
                                    <input type="radio" class="form-check-input" wire:model="role" id="radioInline1" value="user" {{ $role == 'user' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="radioInline1">
                                        User
                                    </label>
                                </div>
                            </div><!-- Col -->
                            @error('role')
                                <label class="error invalid-feedback d-block">{{ $message }}</label>
                            @enderror
                        </div><!-- Row -->

                        @if ($isUpdate === true)
                        <div class="row mb-4">
                            <div class="col-sm-12">
                                <div class="mb-1">
                                    <label class="form-label">Status</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input type="radio" class="form-check-input" wire:model="is_active" id="radioInline2" value="t" {{ $is_active == 't' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="radioInline2">
                                            Aktif
                                        </label>
                                    </div>
                                <div class="form-check form-check-inline">
                                    <input type="radio" class="form-check-input" wire:model="is_active" id="radioInline3" value="f" {{ $is_active == 'f' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="radioInline3">
                                        Non Aktif
                                    </label>
                                </div>
                            </div><!-- Col -->
                        </div><!-- Row -->
                        @endif
                        
                        <x-form-action 
                            :cancelUrl="route('admin.user')" 
                            :isUpdate="$isUpdate === true" 
                        />
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>