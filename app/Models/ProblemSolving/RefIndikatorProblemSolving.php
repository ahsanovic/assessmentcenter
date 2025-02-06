<?php

namespace App\Models\ProblemSolving;

use Illuminate\Database\Eloquent\Model;

class RefIndikatorProblemSolving extends Model
{
    protected $table = 'ref_indikator_problem_solving';
    protected $guarded = ['id'];
    protected $casts = [
        'kualifikasi_deskripsi' => 'array',
    ];
}
