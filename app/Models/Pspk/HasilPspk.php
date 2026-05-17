<?php

namespace App\Models\Pspk;

use App\Models\Event;
use App\Models\Peserta;
use Illuminate\Database\Eloquent\Model;

class HasilPspk extends Model
{
    protected $table = 'hasil_pspk';

    protected $guarded = ['id'];

    protected $casts = [
        'deskripsi' => 'array',
        'nilai_capaian' => 'array',
        'saran_pengembangan' => 'array',
    ];

    public function peserta()
    {
        return $this->belongsTo(Peserta::class, 'peserta_id');
    }

    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    public function ujian()
    {
        return $this->belongsTo(UjianPspk::class, 'ujian_id');
    }
}
