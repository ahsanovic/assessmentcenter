<?php

namespace App\Models\Intelektual;

use Illuminate\Database\Eloquent\Model;
use App\Models\Intelektual\RefModelIntelektual;

class SoalIntelektual extends Model
{
    protected $table = 'soal_intelektual';
    protected $guarded = ['id'];

    public function modelSoal()
    {
        return $this->belongsTo(RefModelIntelektual::class, 'model_id', 'id');
    }
}
