<?php

namespace App\Livewire\Peserta\Auth;

use App\Models\Event;
use App\Models\Peserta;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.peserta.guest', ['title' => 'Login Page'])]
class Login extends Component
{
    public $id_number;
    public $password;

    public function login()
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
            // Cek apakah peserta ASN
            $peserta = Peserta::where('jenis_peserta_id', 1)->where('nip', $this->id_number)->first(['id']);
            if (!$peserta) {
                $this->addError('id_number', 'NIP tidak ditemukan atau bukan peserta ASN.');
                return;
            }
        } elseif ($length === 16) {
            // Cek apakah peserta Non-ASN
            $peserta = Peserta::where('jenis_peserta_id', 2)->where('nik', $this->id_number)->first(['id']);
            if (!$peserta) {
                $this->addError('id_number', 'NIK tidak ditemukan atau bukan peserta Non-ASN.');
                return;
            }
        } else {
            $this->addError('id_number', 'NIP atau NIK harus 16 atau 18 digit.');
            return;
        }

        // Cari event yang belum selesai
        // $event = Event::where('is_finished', 'false')->first();

        // if (!$event) {
        //     $this->addError('id_number', 'Tidak ada event yang sedang berlangsung.');
        //     return;
        // }

        $peserta = Peserta::where(function ($query) {
            $query->where(function ($q) {
                $q->where('jenis_peserta_id', 1)
                    ->where('nip', $this->id_number);
            })->orWhere(function ($q) {
                $q->where('jenis_peserta_id', 2)
                    ->where('nik', $this->id_number);
            });
        })
            ->whereHas('event', function ($query) {
                $query->where('is_finished', 'false');
            })
            ->where('is_active', 'true')
            ->first();

        if (!$peserta) {
            $this->addError('id_number', 'Tes sudah selesai / akun tidak ditemukan.');
        }

        if ($peserta) {
            $credentials = [
                'password' => $this->password,
            ];

            if ($peserta->jenis_peserta_id == 1) {
                // ASN
                $credentials['nip'] = $this->id_number;
            } elseif ($peserta->jenis_peserta_id == 2) {
                // Non-ASN
                $credentials['nik'] = $this->id_number;
            }

            if (auth()->guard('peserta')->attempt($credentials)) {
                request()->session()->regenerate();
                return $this->redirect(route('peserta.dashboard'));
            }
        }

        // if ($peserta && auth()->guard('peserta')->attempt($this->only('nip', 'nik', 'password'))) {
        //     request()->session()->regenerate();
        //     return $this->redirect(route('peserta.dashboard'), navigate: true);
        // }

        $this->addError('id_number', 'NIP/NIK atau password salah.');
    }

    public function render()
    {
        return view('livewire.peserta.auth.login');
    }
}
