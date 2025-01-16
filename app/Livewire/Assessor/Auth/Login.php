<?php

namespace App\Livewire\Assessor\Auth;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.assessor.guest', ['title' => 'Login Page'])]
class Login extends Component
{
    public $nip;
    public $password;

    public function login()
    {
        $this->validate([
            'nip' => 'required',
            'password' => 'required',
        ], [
            'nip.required' => 'wajib diisi.',
            'password.required' => 'wajib diisi.',
        ]);
        
        if (auth()->guard('assessor')->attempt($this->only('nip', 'password'))) {
            request()->session()->regenerate();
            return $this->redirect(route('assessor.dashboard'), navigate: true);
        }
        
        $this->addError('nip', 'NIP/NIK atau password salah.');
    }
    
    public function render()
    {
        return view('livewire.assessor.auth.login');
    }
}
