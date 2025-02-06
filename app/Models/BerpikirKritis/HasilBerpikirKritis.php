<?php

namespace App\Models\BerpikirKritis;

use Illuminate\Database\Eloquent\Model;

class HasilBerpikirKritis extends Model
{
    protected $table = 'hasil_berpikir_kritis';
    protected $guarded = ['id'];
    protected $casts = ['nilai' => 'array'];
}
