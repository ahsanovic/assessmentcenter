<?php

namespace App\Livewire\Admin\Auth;

use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.admin.guest', ['title' => 'Login Page'])]
class Login extends Component
{
    public $username;
    public $password;

    public function login()
    {
        $this->validate([
            'username' => 'required',
            'password' => 'required',
        ], [
            'username.required' => 'wajib diisi.',
            'password.required' => 'wajib diisi.',
        ]);

        $active_user = User::where('username', $this->username)->where('is_active', 't')->first();
        if (!$active_user) {
            $this->addError('username', 'Akun tidak aktif');
            return;
        }
        
        if ($active_user && auth()->guard('admin')->attempt($this->only('username', 'password'))) {
            request()->session()->regenerate();
            return $this->redirect(route('admin.dashboard'));
        }
        
        $this->addError('username', 'username atau password salah.');
    }
    
    public function render()
    {
        return view('livewire.admin.auth.login');
    }
}
