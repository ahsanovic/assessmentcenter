<?php

namespace App\Models\ProblemSolving;

use Illuminate\Database\Eloquent\Model;

class HasilProblemSolving extends Model
{
    protected $table = 'hasil_problem_solving';
    protected $guarded = ['id'];
    protected $casts = ['nilai' => 'array'];
}
