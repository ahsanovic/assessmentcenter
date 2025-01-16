<?php

namespace App\Models\KecerdasanEmosi;

use Illuminate\Database\Eloquent\Model;
use App\Models\KecerdasanEmosi\RefKecerdasanEmosi;

class SoalKecerdasanEmosi extends Model
{
    protected $table = 'soal_kecerdasan_emosi';
    protected $guarded = ['id'];

    public function jenisIndikator()
    {
        return $this->belongsTo(RefKecerdasanEmosi::class, 'jenis_indikator_id', 'id');
    }
}
