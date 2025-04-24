<?php

namespace App\Livewire\Peserta\Portofolio;

use App\Models\Event;
use App\Models\Peserta;
use App\Models\RefAgama;
use App\Models\RefGolPangkat;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('components.layouts.peserta.app', ['title' => 'Portofolio'])]
class Biodata extends Component
{
    use WithFileUploads;

    public $alamat;
    public $no_hp;
    public $foto;
    public $foto_url;
    public $old_foto_path;
    public $nik;
    public $tempat_lahir;
    public $tgl_lahir;
    public $agama_id;
    public $jk;
    public $gol_pangkat_id;
    public $gelar_depan;
    public $gelar_belakang;

    #[Validate([
        'foto' => 'image|mimes:jpeg,png,jpg|max:200',
    ], message: [
        'foto.image' => 'File harus berupa gambar',
        'foto.mimes' => 'Foto harus berupa gambar dengan format jpeg, png, jpg',
        'foto.max' => 'Foto maksimal 200 KB'
    ])]

    public function render()
    {
        $biodata = Peserta::wherePesertaEvent(
            Auth::guard('peserta')->user()->id,
            Auth::guard('peserta')->user()->event_id
        )
        ->first();
        
        $option_agama = RefAgama::pluck('agama', 'id');
        $option_gol_pangkat = RefGolPangkat::all();
        $portofolio = Event::where('id', $biodata->event_id)->first(['metode_tes_id']);

        return view('livewire..peserta.portofolio._partials.biodata.index', [
            'biodata' => $biodata,
            'option_agama' => $option_agama,
            'option_gol_pangkat' => $option_gol_pangkat,
            'portofolio' => $portofolio
        ]);
    }

    public function mount()
    {
        $this->gelar_depan = Auth::guard('peserta')->user()->gelar_depan;
        $this->gelar_belakang = Auth::guard('peserta')->user()->gelar_belakang;
        $this->alamat = Auth::guard('peserta')->user()->alamat;
        $this->no_hp = Auth::guard('peserta')->user()->no_hp;
        $this->nik = Auth::guard('peserta')->user()->nik;
        $this->jk = Auth::guard('peserta')->user()->jk;
        $this->agama_id = Auth::guard('peserta')->user()->agama_id;
        $this->gol_pangkat_id = Auth::guard('peserta')->user()->gol_pangkat_id;
        $this->tempat_lahir = Auth::guard('peserta')->user()->tempat_lahir;
        $this->tgl_lahir = Auth::guard('peserta')->user()->tgl_lahir;
        $this->old_foto_path = Auth::guard('peserta')->user()->foto;
        if ($this->old_foto_path) {
            $this->foto_url = asset('storage/' . $this->old_foto_path);
        }
    }

    public function updatedFoto()
    {
        $this->validate();
        try {
            // Hapus file lama jika ada
            if ($this->old_foto_path && Storage::disk('public')->exists($this->old_foto_path)) {
                Storage::disk('public')->delete($this->old_foto_path);
            }

            $path = $this->foto->storeAs('foto', uniqid() . '.' . $this->foto->extension(), 'public');

            // update url foto untuk preview
            $this->foto_url = asset('storage/' . $path);

            // Simpan path baru sebagai path lama untuk penghapusan selanjutnya
            $this->old_foto_path = $path;

            Peserta::whereId(Auth::guard('peserta')->user()->id)->update([
                'foto' => 'foto/' . basename($path),
            ]);

            $this->dispatch('toast', [
                'type' => 'success',
                'message' => 'berhasil update foto'
            ]);
        } catch (\Throwable $th) {
            // throw $th;
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'gagal update foto'
            ]);
        }
    }

    public function updatedGelarDepan($value)
    {
        try {
            Peserta::whereId(Auth::guard('peserta')->user()->id)->update([
                'gelar_depan' => $value,
            ]);

            $this->dispatch('toast', [
                'type' => 'success',
                'message' => 'berhasil update data gelar depan'
            ]);
        } catch (\Throwable $th) {
            // throw $th;
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'gagal update data gelar depan'
            ]);
        }
    }
    
    public function updatedGelarBelakang($value)
    {
        try {
            Peserta::whereId(Auth::guard('peserta')->user()->id)->update([
                'gelar_belakang' => $value,
            ]);

            $this->dispatch('toast', [
                'type' => 'success',
                'message' => 'berhasil update data gelar belakang'
            ]);
        } catch (\Throwable $th) {
            // throw $th;
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'gagal update data gelar belakang'
            ]);
        }
    }

    public function updatedAlamat($value)
    {
        $this->validate([
            'alamat' => 'required',
        ], [
            'alamat.required' => 'alamat tidak boleh kosong',
        ]);

        try {
            Peserta::whereId(Auth::guard('peserta')->user()->id)->update([
                'alamat' => $value,
            ]);

            $this->dispatch('toast', [
                'type' => 'success',
                'message' => 'berhasil update data alamat'
            ]);
        } catch (\Throwable $th) {
            // throw $th;
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'gagal update data alamat'
            ]);
        }
    }

    public function updatedNoHp($value)
    {
        $this->validate([
            'no_hp' => 'required|numeric|digits_between:10,12',
        ], [
            'no_hp.required' => 'no hp tidak boleh kosong',
            'no_hp.numeric' => 'no hp harus berupa angka',
            'no_hp.digits_between' => 'no hp harus berjumlah 10-12 digit',
        ]);

        try {
            Peserta::whereId(Auth::guard('peserta')->user()->id)->update([
                'no_hp' => $value,
            ]);

            $this->dispatch('toast', [
                'type' => 'success',
                'message' => 'berhasil update data nomor hp'
            ]);
        } catch (\Throwable $th) {
            //throw $th;
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'gagal update data nomor hp'
            ]);
        }
    }

    public function updatedNik($value)
    {
        $this->validate([
            'nik' => 'required|numeric|digits:16',
        ], [
            'nik.required' => 'nik tidak boleh kosong',
            'nik.numeric' => 'nik harus berupa angka',
            'nik.digits' => 'nik harus berjumlah 16 digit',
        ]);

        try {
            Peserta::whereId(Auth::guard('peserta')->user()->id)->update([
                'nik' => $value,
            ]);

            $this->dispatch('toast', [
                'type' => 'success',
                'message' => 'berhasil update data nik'
            ]);
        } catch (\Throwable $th) {
            //throw $th;
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'gagal update data nik'
            ]);
        }
    }

    public function updatedTempatLahir($value)
    {
        $this->validate([
            'tempat_lahir' => 'required',
        ], [
            'tempat_lahir.required' => 'harus diisi',
        ]);

        try {
            Peserta::whereId(Auth::guard('peserta')->user()->id)->update([
                'tempat_lahir' => $value,
            ]);

            $this->dispatch('toast', [
                'type' => 'success',
                'message' => 'berhasil update data tempat lahir'
            ]);
        } catch (\Throwable $th) {
            //throw $th;
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'gagal update data tempat lahir'
            ]);
        }
    }

    public function updatedTglLahir($value)
    {
        $this->validate([
            'tgl_lahir' => 'required|date_format:d-m-Y',
        ], [
            'tgl_lahir.required' => 'harus diisi',
            'tgl_lahir.date_format' => 'format tanggal tidak valid',
        ]);

        try {
            $data = Peserta::whereId(Auth::guard('peserta')->user()->id)->first();
            $data->tgl_lahir = $value;
            $data->save();

            $this->dispatch('toast', [
                'type' => 'success',
                'message' => 'berhasil update data tanggal lahir'
            ]);
        } catch (\Throwable $th) {
            throw $th;
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'gagal update data tanggal lahir'
            ]);
        }
    }

    public function updatedAgamaId($value)
    {
        $this->validate([
            'agama_id' => 'required|numeric',
        ], [
            'agama_id.required' => 'harus diisi',
            'agama_id.numeric' => 'harus angka',
        ]);

        try {
            Peserta::whereId(Auth::guard('peserta')->user()->id)->update([
                'agama_id' => $value,
            ]);

            $this->dispatch('toast', [
                'type' => 'success',
                'message' => 'berhasil update data agama'
            ]);
        } catch (\Throwable $th) {
            //throw $th;
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'gagal update data agama'
            ]);
        }
    }

    public function updatedGolPangkatId($value)
    {
        $this->validate([
            'gol_pangkat_id' => 'required|numeric',
        ], [
            'gol_pangkat_id.required' => 'harus diisi',
            'gol_pangkat_id.numeric' => 'harus angka',
        ]);

        try {
            Peserta::whereId(Auth::guard('peserta')->user()->id)->update([
                'gol_pangkat_id' => $value,
            ]);

            $this->dispatch('toast', [
                'type' => 'success',
                'message' => 'berhasil update data pangkat'
            ]);
        } catch (\Throwable $th) {
            //throw $th;
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'gagal update data pangkat'
            ]);
        }
    }

    public function updatedJk($value)
    {
        $this->validate([
            'jk' => 'required',
        ], [
            'jk.required' => 'harus diisi',
        ]);

        try {
            Peserta::whereId(Auth::guard('peserta')->user()->id)->update([
                'jk' => $value,
            ]);

            $this->dispatch('toast', [
                'type' => 'success',
                'message' => 'berhasil update data jenis kelamin'
            ]);
        } catch (\Throwable $th) {
            //throw $th;
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'gagal update data jenis kelamin'
            ]);
        }
    }
}
