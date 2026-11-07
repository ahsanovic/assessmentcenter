<?php

namespace App\Models\LiterasiDigital;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RefDeskripsiLiterasiDigitalVersi extends Model
{
    protected $table = 'ref_deskripsi_literasi_digital_versi';

    protected $guarded = ['id'];

    protected $casts = [
        'level' => 'integer',
    ];

    public function pilar(): BelongsTo
    {
        return $this->belongsTo(RefPilarLiterasiDigital::class, 'pilar_id');
    }
}
