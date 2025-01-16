<?php

namespace App\Models\KecerdasanEmosi;

use Illuminate\Database\Eloquent\Model;

class RefKecerdasanEmosi extends Model
{
    protected $table = 'ref_kecerdasan_emosi';
    protected $guarded = ['id'];
    protected $casts = [
        'kualifikasi' => 'array',
    ];

    public function setIndikatorNamaAttribute($value)
    {
        $this->attributes['indikator_nama'] = strtoupper($value);
    }
}
