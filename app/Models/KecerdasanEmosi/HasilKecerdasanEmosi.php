<?php

namespace App\Models\KecerdasanEmosi;

use Illuminate\Database\Eloquent\Model;

class HasilKecerdasanEmosi extends Model
{
    protected $table = 'hasil_kecerdasan_emosi';
    protected $guarded = ['id'];
    protected $casts = ['nilai' => 'array'];
}
