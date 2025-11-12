<?php

namespace App\Models\Pspk;

use App\Models\RefAspekPspk;
use Illuminate\Database\Eloquent\Model;

class RefDescPspk extends Model
{
    protected $table = 'ref_deskripsi_pspk';
    protected $guarded = ['id'];

    public function aspek()
    {
        return $this->belongsTo(RefAspekPspk::class, 'aspek_id', 'id');
    }
}
