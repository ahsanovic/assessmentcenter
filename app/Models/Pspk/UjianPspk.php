<?php

namespace App\Models\Pspk;

use App\Models\Peserta;
use Illuminate\Database\Eloquent\Model;

class UjianPspk extends Model
{
    protected $table = 'ujian_pspk';
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
