<?php

namespace App\Livewire\Assessor\Auth;

use App\Models\Assessor;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.assessor.guest', ['title' => 'Login Page'])]
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
            'id_number.numeric' => 'harus angka.',
            'password.required' => 'wajib diisi.',
        ]);

        if (strlen($this->id_number) == 18) {
            $assessor = Assessor::where('is_asn', 'true')->where('nip', $this->id_number)->first(['id']);
            if (!$assessor) {
                $this->addError('id_number', 'NIP tidak ditemukan atau bukan assessor ASN.');
                return;
            }
        } else if (strlen($this->id_number) == 16) {
            $assessor = Assessor::where('is_asn', 'false')->where('nik', $this->id_number)->first(['id']);
            if (!$assessor) {
                $this->addError('id_number', 'NIK tidak ditemukan atau bukan assessor Non ASN.');
                return;
            }
        } else {
            $this->addError('id_number', 'NIP atau NIK harus 16 atau 18 digit.');
                return;
        }

        $assessor = Assessor::where(function ($query) {
            $query->where(function ($q) {
                $q->where('is_asn', 'true')
                    ->where('nip', $this->id_number);
            })->orWhere(function ($q) {
                $q->where('is_asn', 'false')
                    ->where('nik', $this->id_number);
            });
        })
        ->where('is_active', 'true')
        ->first();
    
        if (!$assessor) {
            $this->addError('id_number', 'Tes sudah selesai / akun tidak ditemukan.');
        }

        if ($assessor) {
            $credentials = [
                'password' => $this->password,
            ];
        
            if ($assessor->is_asn == 'true') {
                $credentials['nip'] = $this->id_number;
            } elseif ($assessor->is_asn == 'false') {
                $credentials['nik'] = $this->id_number;
            }
        
            if (auth()->guard('assessor')->attempt($credentials)) {
                request()->session()->regenerate();
                return $this->redirect(route('assessor.dashboard'), navigate: true);
            }
        }
        
        $this->addError('id_number', 'NIP/NIK atau password salah.');
    }
    
    public function render()
    {
        return view('livewire.assessor.auth.login');
    }
}
