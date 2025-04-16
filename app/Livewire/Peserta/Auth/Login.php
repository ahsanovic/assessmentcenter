<?php

namespace App\Livewire\Peserta\Auth;

use App\Models\Event;
use App\Models\Peserta;
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

        // Cari event yang belum selesai
        $event = Event::where('is_finished', 'false')->first();

        if (!$event) {
            $this->addError('nip', 'Tidak ada event yang sedang berlangsung.');
            return;
        }

        $peserta = Peserta::where('nip', $this->nip)
            ->whereHas('event', function ($query) {
                $query->where('is_finished', 'false');
            })
            ->where('is_active', 'true')
            ->first();
        
        if (!$peserta) {
            $this->addError('nip', 'Tes sudah selesai / akun tidak ditemukan.');
        }

        if ($peserta && auth()->guard('peserta')->attempt($this->only('nip', 'password'))) {
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
