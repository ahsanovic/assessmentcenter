<?php

namespace App\Models\Pspk;

use App\Models\RefAspekPspk;
use Illuminate\Database\Eloquent\Model;

class SoalPspk extends Model
{
    protected $table = 'soal_pspk';
    protected $guarded = ['id'];

    public function aspek()
    {
        return $this->belongsTo(RefAspekPspk::class, 'aspek_id', 'id');
    }

    public function levelPspk()
    {
        return $this->belongsTo(RefLevelPspk::class, 'level_pspk_id', 'id');
    }
}
