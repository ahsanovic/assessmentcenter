<?php

namespace App\Models\ProblemSolving;

use Illuminate\Database\Eloquent\Model;
use App\Models\ProblemSolving\RefIndikatorProblemSolving;

class SoalProblemSolving extends Model
{
    protected $table = 'soal_problem_solving';
    protected $guarded = ['id'];

    public function indikator()
    {
        return $this->belongsTo(RefIndikatorProblemSolving::class, 'indikator_nomor', 'id');
    }

    public function aspek()
    {
        return $this->belongsTo(RefAspekProblemSolving::class, 'aspek_id', 'id');
    }
}
