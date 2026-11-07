<?php

namespace App\Models\LiterasiDigital;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RefSaranLiterasiDigitalVersi extends Model
{
    protected $table = 'ref_saran_literasi_digital_versi';

    protected $guarded = ['id'];

    protected $casts = [
        'level' => 'integer',
    ];

    public function pilar(): BelongsTo
    {
        return $this->belongsTo(RefPilarLiterasiDigital::class, 'pilar_id');
    }
}
