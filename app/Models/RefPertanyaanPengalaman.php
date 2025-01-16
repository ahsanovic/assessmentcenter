<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RefPertanyaanPengalaman extends Model
{
    protected $table = 'ref_pertanyaan_pengalaman';
    protected $fillable = ['pertanyaan', 'kode'];

    public function jawaban()
    {
        return $this->hasMany(JawabanPengalaman::class, 'pertanyaan_id', 'id');
    }
}
