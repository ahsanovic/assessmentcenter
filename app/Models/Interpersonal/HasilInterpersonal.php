<?php

namespace App\Models\Interpersonal;

use Illuminate\Database\Eloquent\Model;

class HasilInterpersonal extends Model
{
    protected $table = 'hasil_interpersonal';
    protected $guarded = ['id'];
    protected $casts = ['nilai' => 'array'];
}
