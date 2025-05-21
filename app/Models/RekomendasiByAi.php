<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RekomendasiByAi extends Model
{
    protected $table = 'rekomendasi_ai';
    protected $fillable = [
        'event_id',
        'peserta_id',
        'jpm_id',
        'rekomendasi'
    ];
}
