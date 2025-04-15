<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kuesioner extends Model
{
    protected $table = 'kuesioner';
    protected $fillable = [
        'deskripsi',
        'is_active'
    ];
}
