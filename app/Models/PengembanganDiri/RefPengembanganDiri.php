<?php

namespace App\Models\PengembanganDiri;

use Illuminate\Database\Eloquent\Model;

class RefPengembanganDiri extends Model
{
    protected $table = 'ref_pengembangan_diri';
    protected $guarded = ['id'];
    protected $casts = [
        'kualifikasi' => 'array',
    ];

    public function setIndikatorNamaAttribute($value)
    {
        $this->attributes['indikator_nama'] = strtoupper($value);
    }
}
