<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $table = 'activity_logs';
    protected $fillable = [
        'id',
        'user_id',
        'modul',
        'action',
        'model_id',
        'old_data',
        'new_data',
        'ip_address',
        'user_agent'
    ];

    public $incrementing = false;

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
