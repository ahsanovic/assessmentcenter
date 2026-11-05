<?php

namespace App\Models\LiterasiDigital;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HasilLiterasiDigital extends Model
{
    protected $table = 'hasil_literasi_digital';

    protected $guarded = ['id'];

    public function ujian(): BelongsTo
    {
        return $this->belongsTo(UjianLiterasiDigital::class, 'ujian_id');
    }
}
