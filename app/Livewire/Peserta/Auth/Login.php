<?php

namespace App\Livewire\Peserta\Auth;

use App\Models\Peserta;
use App\Services\PesertaParticipationService;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

#[Layout('components.layouts.peserta.guest', ['title' => 'Login Page'])]
class Login extends Component
{
    public $id_number;
    public $password;

    public function login(PesertaParticipationService $participationService)
    {
        $this->validate([
            'id_number' => 'required|numeric',
            'password' => 'required',
        ], [
            'id_number.required' => 'wajib diisi.',
            'id_number.numeric' => 'NIP/NIK harus berupa angka.',
            'password.required' => 'wajib diisi.',
        ]);

        $length = strlen($this->id_number);

        if ($length === 18) {
            $exists = Peserta::where('jenis_peserta_id', 1)->where('nip', $this->id_number)->exists();
            if (!$exists) {
                $this->addError('id_number', 'NIP tidak ditemukan atau bukan peserta ASN.');
                return;
            }
        } elseif ($length === 16) {
            $exists = Peserta::where('jenis_peserta_id', 2)->where('nik', $this->id_number)->exists();
            if (!$exists) {
                $this->addError('id_number', 'NIK tidak ditemukan atau bukan peserta Non-ASN.');
                return;
            }
        } else {
            $this->addError('id_number', 'NIP atau NIK harus 16 atau 18 digit.');
            return;
        }

        $participations = $participationService->findActiveParticipationsByIdNumber($this->id_number);

        if ($participations->isEmpty()) {
            $this->addError('id_number', 'Tes sudah selesai / akun tidak ditemukan.');
            return;
        }

        $matchedParticipations = $participationService->participationsMatchingPassword($participations, $this->password);

        if ($matchedParticipations->isEmpty()) {
            $this->addError('id_number', 'NIP/NIK atau password salah.');
            return;
        }

        $peserta = $participationService->resolveLoginParticipation($matchedParticipations);

        if (!$peserta) {
            $this->addError('id_number', 'Tes sudah selesai / akun tidak ditemukan.');
            return;
        }

        Auth::guard('peserta')->login($peserta);

        request()->session()->regenerate();

        return $this->redirect(route('peserta.dashboard'));
    }

    public function render()
    {
        return view('livewire.peserta.auth.login');
    }
}
