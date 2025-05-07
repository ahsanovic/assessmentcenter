<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NilaiJpm extends Model
{
    protected $table = 'nilai_jpm';
    protected $fillable = [
        'event_id',
        'peserta_id',
        'jpm',
        'kategori'
    ];

    public function peserta()
    {
        return $this->belongsTo(Peserta::class, 'peserta_id', 'id');
    }
}
