<?php

namespace App\Livewire\Admin\Users;

use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Component;

#[Layout('components.layouts.admin.app', ['title' => 'Users'])]
class Form extends Component
{
    public $isUpdate = false;
    public $nama;
    public $username;
    public $role;
    public $is_active;
    public $password;

    #[Locked]
    public $id;

    public function mount($id = null)
    {
        try {
            if ($id) {
                $this->isUpdate = true;

                $data = User::findOrFail($id);
                $this->nama = $data->nama;
                $this->username = $data->username;
                $this->role = $data->role;
                $this->is_active = $data->is_active;
            }
        } catch (\Throwable $th) {
            // throw $th;
            $this->dispatch('toast', ['type' => 'error', 'message' => 'terjadi kesalahan']);
        }
    }

    public function render()
    {
        return view('livewire.admin.users.form');
    }

    protected function rules()
    {
        $rules = [
            'nama' => ['required'],
            'username' => ['required', 'unique:users,username,' . $this->id, 'min:6'],
            'role' => ['required'],
            'password' => $this->isUpdate ? ['nullable', 'min:8'] : ['required', 'min:8'],
        ];

        return $rules;
    }

    protected function messages()
    {
        return [
            'nama.required' => 'harus diisi',
            'username.required' => 'harus diisi',
            'username.min' => 'minimal 6 karakter',
            'username.unique' => 'sudah terdaftar',
            'password.required' => 'harus diisi',
            'password.min' => 'minimal 8 karakter',
            'role.required' => 'harus dipilih',
        ];
    }

    public function save()
    {
        $this->validate();
        try {
            if ($this->isUpdate) {
                $data = User::whereId($this->id)->first();
                $old_data = $data->getOriginal();

                $data->nama = $this->nama;
                $data->username = $this->username;
                $data->role = $this->role;
                $data->is_active = $this->is_active;
                $data->password = $this->password != '' ? bcrypt($this->password) : $data->password;
                $data->save();

                $this->reset('password');

                activity_log($data, 'update', 'users', $old_data);

                session()->flash('toast', [
                    'type' => 'success',
                    'message' => 'berhasil ubah data'
                ]);

                $this->redirect(route('admin.user'), true);
            } else {
                $data = User::create([
                    'nama' => $this->nama,
                    'username' => $this->username,
                    'role' => $this->role,
                    'password' => bcrypt($this->password)
                ]);

                $this->reset('password');

                activity_log($data, 'create', 'users');

                session()->flash('toast', [
                    'type' => 'success',
                    'message' => 'berhasil tambah data'
                ]);

                $this->redirect(route('admin.user'), true);
            }
        } catch (\Throwable $th) {
            // throw $th;
            $this->dispatch('toast', ['type' => 'error', 'message' => 'terjadi kesalahan']);
        }
    }
}
