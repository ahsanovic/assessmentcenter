<?php

namespace App\Models\LiterasiDigital;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RefSaranPengembanganLiterasiDigital extends Model
{
    protected $table = 'ref_saran_pengembangan_literasi_digital';

    protected $guarded = ['id'];

    protected $casts = [
        'level' => 'integer',
    ];

    public function pilar(): BelongsTo
    {
        return $this->belongsTo(RefPilarLiterasiDigital::class, 'pilar_id');
    }
}
