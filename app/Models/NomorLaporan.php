<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NomorLaporan extends Model
{
    protected $table = 'nomor_laporan';
    protected $fillable = [
        'event_id',
        'nomor',
        'tanggal',
        'created_at',
        'updated_at'
    ];

    protected function setTanggalAttribute($value)
    {
        $this->attributes['tanggal'] = date('Y-m-d', strtotime($value));
    }

    protected function getTanggalAttribute($value)
    {
        return date('d-m-Y', strtotime($value));
    }

    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }
}
