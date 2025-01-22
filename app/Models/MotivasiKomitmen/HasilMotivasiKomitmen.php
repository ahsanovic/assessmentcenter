<?php

namespace App\Models\MotivasiKomitmen;

use Illuminate\Database\Eloquent\Model;

class HasilMotivasiKomitmen extends Model
{
    protected $table = 'hasil_motivasi_komitmen';
    protected $guarded = ['id'];
    protected $casts = ['nilai' => 'array'];
}
