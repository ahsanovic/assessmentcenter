<?php

namespace App\Models\KompetensiTeknis;

use App\Models\RefJabatanDiuji;
use Illuminate\Database\Eloquent\Model;

class SoalKompetensiTeknis extends Model
{
    protected $table = 'soal_kompetensi_teknis';
    protected $guarded = ['id'];

    public function jenisJabatan()
    {
        return $this->belongsTo(RefJabatanDiuji::class, 'jenis_jabatan_id', 'id');
    }
}
