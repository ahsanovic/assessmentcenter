<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RefPertanyaanPenilaian extends Model
{
    protected $table = 'ref_pertanyaan_penilaian';
    protected $fillable = ['pertanyaan', 'urutan'];

    public function jawaban()
    {
        return $this->hasMany(JawabanPenilaian::class, 'pertanyaan_id', 'id');
    }
}
