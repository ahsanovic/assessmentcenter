<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EventGroup extends Model
{
    protected $table = 'event_group';

    protected $guarded = ['id'];

    public function events(): HasMany
    {
        return $this->hasMany(Event::class, 'event_group_id', 'id');
    }
}
