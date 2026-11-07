<?php

namespace App\Models\LiterasiDigital;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RefDeskripsiLevelLiterasiDigital extends Model
{
    protected $table = 'ref_deskripsi_level_literasi_digital';

    protected $guarded = ['id'];

    public function pilar(): BelongsTo
    {
        return $this->belongsTo(RefPilarLiterasiDigital::class, 'pilar_id');
    }
}
