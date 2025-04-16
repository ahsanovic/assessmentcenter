<?php

namespace App\Livewire\Admin\Auth;

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
        
        if (auth()->guard('admin')->attempt($this->only('username', 'password'))) {
            request()->session()->regenerate();
            return $this->redirect(route('admin.dashboard'), navigate: true);
        }
        
        $this->addError('username', 'username atau password salah.');
    }
    
    public function render()
    {
        return view('livewire.admin.auth.login');
    }
}
