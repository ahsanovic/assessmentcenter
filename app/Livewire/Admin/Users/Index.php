<?php

namespace App\Livewire\Admin\Users;

use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.admin.app', ['title' => 'Users'])]
class Index extends Component
{
    use WithPagination;

    public $selected_id;
    public $is_active;
    public $status;
    public $role;
    public $nama;
    public $username;
    public $password;
    public $showModal = false;
    public $isUpdate = false;

    #[Url(as: 'q')]
    public ?string $search =  '';

    #[Locked]
    public $editId;

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedIsActive()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->reset();
        $this->resetPage();
        $this->render();
    }

    public function openModal()
    {
        $this->resetValidation();
        $this->nama = '';
        $this->username = '';
        $this->role = '';
        $this->status = '';
        $this->showModal = true;
        $this->isUpdate = false;
        $this->editId = null;
        $this->dispatch('modalOpened');
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetValidation();
        $this->nama = '';
        $this->username = '';
        $this->role = '';
        $this->status = '';
        $this->isUpdate = false;
        $this->editId = null;
    }

    public function edit($id)
    {
        try {
            $data = User::findOrFail($id);
            $this->editId = $data->id;
            $this->nama = $data->nama;
            $this->username = $data->username;
            $this->role = $data->role;
            $this->status = $data->is_active;
            $this->isUpdate = true;
            $this->showModal = true;
            $this->resetValidation();
            $this->dispatch('modalOpened');
        } catch (\Throwable $th) {
            $this->dispatch('toast', ['type' => 'error', 'message' => 'terjadi kesalahan']);
        }
    }

    protected function rules()
    {
        $rules = [
            'nama' => ['required'],
            'username' => ['required', 'unique:users,username,' . $this->editId, 'min:6'],
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
                $data = User::whereId($this->editId)->first();
                $old_data = $data->getOriginal();

                $data->nama = $this->nama;
                $data->username = $this->username;
                $data->role = $this->role;
                $data->is_active = $this->status;
                $data->password = $this->password != '' ? bcrypt($this->password) : $data->password;
                $data->save();

                $this->reset('password');

                activity_log($data, 'update', 'users', $old_data);

                $this->dispatch('toast', ['type' => 'success', 'message' => 'berhasil ubah data']);
            } else {
                $data = User::create([
                    'nama' => $this->nama,
                    'username' => $this->username,
                    'role' => $this->role,
                    'password' => bcrypt($this->password)
                ]);

                $this->reset('password');

                activity_log($data, 'create', 'users');

                $this->dispatch('toast', ['type' => 'success', 'message' => 'berhasil tambah data']);
            }

            $this->closeModal();
            $this->resetPage();
        } catch (\Throwable $th) {
            // throw $th;
            $this->dispatch('toast', ['type' => 'error', 'message' => 'terjadi kesalahan']);
        }
    }

    public function render()
    {
        $data = User::when($this->search, function ($query) {
            $query->where('nama', 'like', '%' . $this->search . '%')
                ->orWhere('username', 'like', '%' . $this->search . '%');
        })
            ->when($this->role, function ($query) {
                $query->where('role', $this->role);
            })
            ->when($this->is_active, function ($query) {
                $query->where('is_active', $this->is_active);
            })
            ->orderByDesc('id')
            ->paginate(10);

        return view('livewire.admin.users.index', compact('data'));
    }

    public function deleteConfirmation($id)
    {
        $this->selected_id = $id;
        $this->dispatch('show-delete-confirmation');
    }

    #[On('delete')]
    public function destroy()
    {
        try {
            $data = User::find($this->selected_id);
            $old_data = $data->getOriginal();

            activity_log($data, 'delete', 'users', $old_data);

            $data->delete();

            $this->dispatch('toast', ['type' => 'success', 'message' => 'berhasil menghapus data']);
        } catch (\Throwable $th) {
            // throw $th;
            $this->dispatch('toast', ['type' => 'error', 'message' => 'gagal menghapus data']);
        }
    }
}
