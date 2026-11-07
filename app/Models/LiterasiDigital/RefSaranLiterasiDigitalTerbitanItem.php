<?php

namespace App\Models\LiterasiDigital;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RefSaranLiterasiDigitalTerbitanItem extends Model
{
    protected $table = 'ref_saran_literasi_digital_terbitan_item';

    protected $guarded = ['id'];

    protected $casts = [
        'level' => 'integer',
        'versi_id' => 'integer',
    ];

    public function terbitan(): BelongsTo
    {
        return $this->belongsTo(RefSaranLiterasiDigitalTerbitan::class, 'terbitan_id');
    }

    public function pilar(): BelongsTo
    {
        return $this->belongsTo(RefPilarLiterasiDigital::class, 'pilar_id');
    }

    public function versi(): BelongsTo
    {
        return $this->belongsTo(RefSaranLiterasiDigitalVersi::class, 'versi_id');
    }
}
