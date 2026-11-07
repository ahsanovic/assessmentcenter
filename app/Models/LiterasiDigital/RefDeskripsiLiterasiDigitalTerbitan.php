<?php

namespace App\Models\LiterasiDigital;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RefDeskripsiLiterasiDigitalTerbitan extends Model
{
    protected $table = 'ref_deskripsi_literasi_digital_terbitan';

    protected $guarded = ['id'];

    public function item(): HasMany
    {
        return $this->hasMany(RefDeskripsiLiterasiDigitalTerbitanItem::class, 'terbitan_id');
    }
}
