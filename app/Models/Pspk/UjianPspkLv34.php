<?php

namespace App\Models\Pspk;

use App\Models\Peserta;
use Illuminate\Database\Eloquent\Model;

class UjianPspkLv34 extends Model
{
    protected $table = 'ujian_pspk_lv3_4';

    protected $guarded = ['id'];

    protected $casts = [
        'waktu_tes_berakhir' => 'datetime',
        'skor_aspek' => 'array',
    ];

    public function peserta()
    {
        return $this->belongsTo(Peserta::class, 'peserta_id', 'id');
    }
}
