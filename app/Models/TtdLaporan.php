<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TtdLaporan extends Model
{
    protected $table = 'ttd_laporan';
    protected $fillable = [
        'id',
        'nama',
        'nip',
        'ttd',
        'is_active'
    ];
}
