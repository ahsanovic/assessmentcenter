<?php

namespace App\Models\LiterasiDigital;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RefSaranLiterasiDigitalTerbitan extends Model
{
    protected $table = 'ref_saran_literasi_digital_terbitan';

    protected $guarded = ['id'];

    public function item(): HasMany
    {
        return $this->hasMany(RefSaranLiterasiDigitalTerbitanItem::class, 'terbitan_id');
    }
}
