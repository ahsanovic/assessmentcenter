<?php

namespace App\Models\ProblemSolving;

use Illuminate\Database\Eloquent\Model;

class UjianProblemSolving extends Model
{
    protected $table = 'ujian_problem_solving';
    protected $guarded = ['id'];
    protected $casts = ['waktu_tes_berakhir' => 'datetime'];
}
