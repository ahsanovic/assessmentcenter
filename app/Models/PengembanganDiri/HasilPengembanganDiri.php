<?php

namespace App\Models\PengembanganDiri;

use Illuminate\Database\Eloquent\Model;

class HasilPengembanganDiri extends Model
{
    protected $table = 'hasil_pengembangan_diri';
    protected $guarded = ['id'];
    protected $casts = ['nilai' => 'array'];
}
