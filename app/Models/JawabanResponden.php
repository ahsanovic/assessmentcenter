<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JawabanResponden extends Model
{
    protected $table = 'jawaban_responden';
    protected $fillable = [
        'event_id',
        'peserta_id',
        'skor',
        'kuesioner_id',
        'jawaban_esai'
    ];

    public function peserta()
    {
        return $this->belongsTo(Peserta::class, 'peserta_id', 'id');
    }
}
