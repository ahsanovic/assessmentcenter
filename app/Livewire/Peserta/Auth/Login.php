<?php

namespace App\Livewire\Peserta\Auth;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.peserta.guest', ['title' => 'Login Page'])]
class Login extends Component
{
    public $nip;
    public $password;

    public function login()
    {
        $this->validate([
            'nip' => 'required|digits:18',
            'password' => 'required',
        ], [
            'nip.required' => 'wajib diisi.',
            'nip.digits' => 'NIP tidak valid.',
            'password.required' => 'wajib diisi.',
        ]);
        
        if (auth()->guard('peserta')->attempt($this->only('nip', 'password'))) {
            request()->session()->regenerate();
            return $this->redirect(route('peserta.dashboard'), navigate: true);
        }
        
        $this->addError('nip', 'NIP atau password salah.');
    }
    
    public function render()
    {
        return view('livewire.peserta.auth.login');
    }
}
