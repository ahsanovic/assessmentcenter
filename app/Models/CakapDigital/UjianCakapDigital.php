<?php

namespace App\Models\CakapDigital;

use App\Models\Peserta;
use Illuminate\Database\Eloquent\Model;

class UjianCakapDigital extends Model
{
    protected $table = 'ujian_cakap_digital';
    protected $guarded = ['id'];
    protected $casts = ['waktu_tes_berakhir' => 'datetime'];

    public function peserta()
    {
        return $this->belongsTo(Peserta::class, 'peserta_id', 'id');
    }
}
