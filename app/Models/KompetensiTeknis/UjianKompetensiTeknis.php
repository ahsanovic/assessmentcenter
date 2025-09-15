<?php

namespace App\Models\KompetensiTeknis;

use App\Models\Peserta;
use Illuminate\Database\Eloquent\Model;

class UjianKompetensiTeknis extends Model
{
    protected $table = 'ujian_kompetensi_teknis';
    protected $guarded = ['id'];
    protected $casts = ['waktu_tes_berakhir' => 'datetime'];

    public function peserta()
    {
        return $this->belongsTo(Peserta::class, 'peserta_id', 'id');
    }
}
